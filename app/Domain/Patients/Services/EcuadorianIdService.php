<?php

namespace App\Domain\Patients\Services;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EcuadorianIdService
{
    /**
     * Validates Ecuadorian Cédula (for both natural citizens and foreign residents).
     */
    public function validateCedula(string $cedula): array
    {
        $cedula = trim(preg_replace('/\D/', '', $cedula));

        // Must be exactly 10 numeric digits
        if (strlen($cedula) !== 10) {
            return [
                'is_valid' => false,
                'error' => 'La cédula debe contener exactamente 10 dígitos numéricos.',
            ];
        }

        $provinceCode = (int) substr($cedula, 0, 2);

        // Province codes 01-24, 30 (Exterior/Extranjeros), and special series 50, 80
        if (($provinceCode < 1 || $provinceCode > 24) && !in_array($provinceCode, [30, 50, 80], true)) {
            return [
                'is_valid' => false,
                'error' => 'El código inicial de la cédula no es válido.',
            ];
        }

        // Modulo 10 algorithm
        $coefficients = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $val = ((int) $cedula[$i]) * $coefficients[$i];
            if ($val >= 10) {
                $val -= 9;
            }
            $sum += $val;
        }

        $verifierDigit = (10 - ($sum % 10)) % 10;
        $tenthDigit = (int) $cedula[9];

        if ($verifierDigit !== $tenthDigit) {
            return [
                'is_valid' => false,
                'error' => 'El dígito verificador de la cédula es incorrecto.',
            ];
        }

        return [
            'is_valid' => true,
            'error' => null,
        ];
    }

    /**
     * Consult and auto-complete patient data by Cedula.
     * Searches local database first, then queries national registry / SRI public services.
     */
    public function lookup(string $cedula): array
    {
        $cedula = trim(preg_replace('/\D/', '', $cedula));

        if (strlen($cedula) !== 10) {
            return [
                'success' => false,
                'is_valid' => false,
                'error' => 'La cédula debe contener exactamente 10 dígitos numéricos.',
            ];
        }

        // 1. Check if patient already exists in local DB
        try {
            $localPatient = Patient::where('identification_number', $cedula)->first();
            if ($localPatient) {
                return [
                    'success' => true,
                    'is_valid' => true,
                    'source' => 'LOCAL_DB',
                    'identification_number' => $cedula,
                    'first_name' => $localPatient->first_name,
                    'last_name' => $localPatient->last_name,
                    'full_name' => $localPatient->full_name,
                    'birth_date' => $localPatient->birth_date ? $localPatient->birth_date->format('Y-m-d') : null,
                    'gender' => $localPatient->gender ? $localPatient->gender->value : 'OTHER',
                    'email' => $localPatient->email,
                    'phone' => $localPatient->phone,
                    'already_registered' => $localPatient->user_id !== null,
                    'message' => '✓ Datos obtenidos del registro clínico existente.',
                ];
            }
        } catch (\Throwable $e) {
            Log::debug("Local DB patient query error for {$cedula}: " . $e->getMessage());
        }

        // 2. Query external public APIs (SRI / Registro Civil)
        $externalData = $this->queryExternalCivilRegistry($cedula);

        if ($externalData['success']) {
            return array_merge([
                'success' => true,
                'is_valid' => true,
                'source' => 'REGISTRO_CIVIL',
                'identification_number' => $cedula,
                'already_registered' => false,
                'message' => '✓ Identidad verificada con éxito.',
            ], $externalData);
        }

        // 3. Fallback: mathematical validation
        $validation = $this->validateCedula($cedula);

        if (!$validation['is_valid']) {
            return [
                'success' => false,
                'is_valid' => false,
                'error' => $validation['error'],
            ];
        }

        return [
            'success' => true,
            'is_valid' => true,
            'source' => 'ALGORITHM',
            'identification_number' => $cedula,
            'first_name' => '',
            'last_name' => '',
            'full_name' => '',
            'birth_date' => null,
            'gender' => 'OTHER',
            'already_registered' => false,
            'message' => '✓ Cédula válida. Por favor ingresa tus nombres.',
        ];
    }

    /**
     * Query public registry services for name / identity details lookup.
     */
    protected function queryExternalCivilRegistry(string $cedula): array
    {
        try {
            // 1. SRI Móvil Deudas endpoint (contains all natural citizens and foreign residents)
            $urlDeudas = "https://srienlinea.sri.gob.ec/movil-servicios/api/v1.0/deudas/porIdentificacion/{$cedula}/?tipoPersona=N";
            $response = Http::withoutVerifying()->timeout(4.5)->get($urlDeudas);

            if ($response->successful()) {
                $json = $response->json();
                $contribuyente = $json['contribuyente'] ?? null;
                $nombre = $contribuyente['nombreComercial'] ?? $contribuyente['denominacion'] ?? null;

                if (!empty($nombre)) {
                    $parsed = $this->parseFullName($nombre);
                    return [
                        'success' => true,
                        'first_name' => $parsed['first_name'],
                        'last_name' => $parsed['last_name'],
                        'full_name' => $parsed['full_name'],
                        'birth_date' => null,
                        'gender' => 'OTHER',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::debug("SRI deudas query error for {$cedula}: " . $e->getMessage());
        }

        try {
            // 2. SRI Catastro Personas endpoint
            $urlCatastro = "https://srienlinea.sri.gob.ec/sri-catastro-sujeto-servicio-internet/rest/Persona/obtenerPorNumeroIdentificacion?numeroIdentificacion={$cedula}";
            $response = Http::withoutVerifying()->timeout(3.5)->get($urlCatastro);

            if ($response->successful()) {
                $json = $response->json();
                $nombre = $json['nombreCompleto'] ?? $json['razonSocial'] ?? null;

                if (!empty($nombre)) {
                    $parsed = $this->parseFullName($nombre);
                    return [
                        'success' => true,
                        'first_name' => $parsed['first_name'],
                        'last_name' => $parsed['last_name'],
                        'full_name' => $parsed['full_name'],
                        'birth_date' => null,
                        'gender' => 'OTHER',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::debug("SRI catastro query error for {$cedula}: " . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Parses a single full name string (e.g. "AROCA REYES JONATHAN EDUARDO") into first and last names.
     */
    public function parseFullName(string $rawName): array
    {
        $rawName = trim(preg_replace('/\s+/', ' ', $rawName));
        if (empty($rawName)) {
            return ['first_name' => '', 'last_name' => '', 'full_name' => ''];
        }

        $words = explode(' ', $rawName);
        $count = count($words);

        if ($count === 1) {
            $name = Str::title(mb_strtolower($words[0]));
            return [
                'first_name' => $name,
                'last_name' => '',
                'full_name' => $name,
            ];
        }

        if ($count === 2) {
            // APELLIDO NOMBRE
            $lastName = Str::title(mb_strtolower($words[0]));
            $firstName = Str::title(mb_strtolower($words[1]));
            return [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => "{$firstName} {$lastName}",
            ];
        }

        // Check for compound prefixes
        $upper = array_map('strtoupper', $words);

        if ($count >= 4 && $upper[0] === 'DE' && in_array($upper[1], ['LA', 'LOS', 'LAS'], true)) {
            // DE LA CRUZ PEREZ JUAN CARLOS
            if ($count >= 5) {
                $lastName = Str::title(mb_strtolower(implode(' ', array_slice($words, 0, 4))));
                $firstName = Str::title(mb_strtolower(implode(' ', array_slice($words, 4))));
            } else {
                $lastName = Str::title(mb_strtolower(implode(' ', array_slice($words, 0, 3))));
                $firstName = Str::title(mb_strtolower(implode(' ', array_slice($words, 3))));
            }
        } elseif ($count >= 3 && in_array($upper[0], ['DE', 'DEL', 'SAN', 'SANTA', 'VON', 'VAN'], true)) {
            if ($count >= 4) {
                $lastName = Str::title(mb_strtolower(implode(' ', array_slice($words, 0, 3))));
                $firstName = Str::title(mb_strtolower(implode(' ', array_slice($words, 3))));
            } else {
                $lastName = Str::title(mb_strtolower(implode(' ', array_slice($words, 0, 2))));
                $firstName = Str::title(mb_strtolower(implode(' ', array_slice($words, 2))));
            }
        } elseif ($count === 3) {
            // APELLIDO1 APELLIDO2 NOMBRE
            $lastName = Str::title(mb_strtolower($words[0] . ' ' . $words[1]));
            $firstName = Str::title(mb_strtolower($words[2]));
        } else {
            // APELLIDO1 APELLIDO2 NOMBRE1 NOMBRE2...
            $lastName = Str::title(mb_strtolower($words[0] . ' ' . $words[1]));
            $firstName = Str::title(mb_strtolower(implode(' ', array_slice($words, 2))));
        }

        return [
            'first_name' => trim($firstName),
            'last_name' => trim($lastName),
            'full_name' => trim("{$firstName} {$lastName}"),
        ];
    }
}

