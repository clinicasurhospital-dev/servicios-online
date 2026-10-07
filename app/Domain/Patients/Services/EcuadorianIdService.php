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
     * Ecuadorian provinces map by province code (digits 1-2).
     */
    public const PROVINCES = [
        1  => 'Azuay',
        2  => 'Bolívar',
        3  => 'Cañar',
        4  => 'Carchi',
        5  => 'Cotopaxi',
        6  => 'Chimborazo',
        7  => 'El Oro',
        8  => 'Esmeraldas',
        9  => 'Guayas',
        10 => 'Imbabura',
        11 => 'Loja',
        12 => 'Los Ríos',
        13 => 'Manabí',
        14 => 'Morona Santiago',
        15 => 'Napo',
        16 => 'Pastaza',
        17 => 'Pichincha',
        18 => 'Tungurahua',
        19 => 'Zamora Chinchipe',
        20 => 'Galápagos',
        21 => 'Sucumbíos',
        22 => 'Orellana',
        23 => 'Santo Domingo de los Tsáchilas',
        24 => 'Santa Elena',
        30 => 'Ecuatorianos en el Exterior',
    ];

    /**
     * Validates Ecuadorian Cédula using the official Modulo 10 algorithm.
     */
    public function validateCedula(string $cedula): array
    {
        $cedula = trim($cedula);

        // Must be exactly 10 numeric digits
        if (!preg_match('/^[0-9]{10}$/', $cedula)) {
            return [
                'is_valid' => false,
                'error' => 'La cédula debe contener exactamente 10 dígitos numéricos.',
                'province' => null,
            ];
        }

        // Validate province code (first 2 digits)
        $provinceCode = (int) substr($cedula, 0, 2);
        if (!isset(self::PROVINCES[$provinceCode])) {
            return [
                'is_valid' => false,
                'error' => "El código de provincia '{$provinceCode}' no corresponde a ninguna provincia válida de Ecuador.",
                'province' => null,
            ];
        }

        $provinceName = self::PROVINCES[$provinceCode];

        // Third digit must be < 6 for natural citizens
        $thirdDigit = (int) $cedula[2];
        if ($thirdDigit >= 6) {
            return [
                'is_valid' => false,
                'error' => 'El tercer dígito de la cédula no corresponde a una persona natural ecuatoriana.',
                'province' => $provinceName,
            ];
        }

        // Official Modulo 10 Algorithm
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
                'error' => 'El dígito verificador de la cédula es incorrecto según el algoritmo del Registro Civil.',
                'province' => $provinceName,
            ];
        }

        return [
            'is_valid' => true,
            'error' => null,
            'province' => $provinceName,
            'province_code' => $provinceCode,
        ];
    }

    /**
     * Consult and auto-complete patient data by Cedula.
     * Searches local database first, then tries Civil Registry / SRI public services.
     */
    public function lookup(string $cedula): array
    {
        $validation = $this->validateCedula($cedula);

        if (!$validation['is_valid']) {
            return [
                'success' => false,
                'is_valid' => false,
                'error' => $validation['error'],
                'province' => $validation['province'],
            ];
        }

        $province = $validation['province'];

        // 1. Check if patient already exists in local DB
        $localPatient = Patient::where('identification_number', $cedula)->first();
        if ($localPatient) {
            return [
                'success' => true,
                'is_valid' => true,
                'source' => 'LOCAL_DB',
                'province' => $province,
                'identification_number' => $cedula,
                'first_name' => $localPatient->first_name,
                'last_name' => $localPatient->last_name,
                'full_name' => $localPatient->full_name,
                'birth_date' => $localPatient->birth_date ? $localPatient->birth_date->format('Y-m-d') : null,
                'gender' => $localPatient->gender ? $localPatient->gender->value : 'OTHER',
                'email' => $localPatient->email,
                'phone' => $localPatient->phone,
                'already_registered' => $localPatient->user_id !== null,
                'message' => 'Datos obtenidos del registro clínico existente.',
            ];
        }

        // 2. Query external public APIs (DINARDAP / SRI / Registro Civil proxy)
        $externalData = $this->queryExternalCivilRegistry($cedula);

        if ($externalData['success']) {
            return array_merge([
                'success' => true,
                'is_valid' => true,
                'source' => 'REGISTRO_CIVIL',
                'province' => $province,
                'identification_number' => $cedula,
                'already_registered' => false,
                'message' => 'Cédula validada y consultada exitosamente en el Registro Civil.',
            ], $externalData);
        }

        // 3. Fallback: valid cedula mathematically verified
        return [
            'success' => true,
            'is_valid' => true,
            'source' => 'ALGORITHM',
            'province' => $province,
            'identification_number' => $cedula,
            'first_name' => '',
            'last_name' => '',
            'full_name' => '',
            'birth_date' => null,
            'gender' => 'OTHER',
            'already_registered' => false,
            'message' => "Cédula auténtica validada (Provincia de {$province}). Ingresa tus nombres.",
        ];
    }

    /**
     * Query public registry services for name / details lookup.
     */
    protected function queryExternalCivilRegistry(string $cedula): array
    {
        try {
            // Attempt public SRI consultation endpoint
            $url = "https://srienlinea.sri.gob.ec/sri-catastro-sujeto-servicio-internet/rest/Persona/obtenerPorNumeroIdentificacion?numeroIdentificacion={$cedula}";

            $response = Http::timeout(2.5)->get($url);

            if ($response->successful()) {
                $json = $response->json();
                $nombreCompleto = $json['nombreCompleto'] ?? $json['razonSocial'] ?? null;

                if (!empty($nombreCompleto)) {
                    $parsed = $this->parseFullName($nombreCompleto);
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
            Log::debug("External registry lookup notice for {$cedula}: " . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Parses a single full name string (e.g. "PEREZ LOPEZ JUAN CARLOS") into first and last names.
     */
    public function parseFullName(string $rawName): array
    {
        $rawName = trim(preg_replace('/\s+/', ' ', $rawName));
        $words = explode(' ', $rawName);
        $count = count($words);

        if ($count <= 1) {
            return [
                'first_name' => Str::title(mb_strtolower($rawName)),
                'last_name' => '',
                'full_name' => Str::title(mb_strtolower($rawName)),
            ];
        }

        // Ecuadorian convention in official registries: [APELLIDO 1] [APELLIDO 2] [NOMBRE 1] [NOMBRE 2...]
        if ($count === 2) {
            $lastName = Str::title(mb_strtolower($words[0]));
            $firstName = Str::title(mb_strtolower($words[1]));
        } elseif ($count === 3) {
            $lastName = Str::title(mb_strtolower($words[0] . ' ' . $words[1]));
            $firstName = Str::title(mb_strtolower($words[2]));
        } else {
            $lastName = Str::title(mb_strtolower($words[0] . ' ' . $words[1]));
            $firstName = Str::title(mb_strtolower(implode(' ', array_slice($words, 2))));
        }

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => "{$firstName} {$lastName}",
        ];
    }
}
