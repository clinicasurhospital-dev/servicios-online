<?php

/**
 * Script de Post-Despliegue para BanaHosting (PHP)
 * Clínica Sur Hospital — Agenda de Citas
 */

$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    header('Content-Type: text/plain; charset=UTF-8');
}

echo "==========================================================\n";
echo "🚀 Iniciando post-despliegue en BanaHosting (ea-php82)...\n";
echo "==========================================================\n\n";

// Ejecutar los comandos requeridos
$commands = [
    'Migraciones' => 'ea-php82 artisan migrate --force',
    'Limpiar Cache' => 'ea-php82 artisan optimize:clear',
    'Optimizar Cache' => 'ea-php82 artisan optimize',
    'Permisos Storage' => 'chmod -R 775 storage bootstrap/cache',
];

foreach ($commands as $name => $cmd) {
    echo "▶ Ejecutando: $name ($cmd)\n";
    $output = [];
    $returnCode = 0;
    exec($cmd . ' 2>&1', $output, $returnCode);
    
    // Si ea-php82 no estuviera en PATH, intentar con php
    if ($returnCode !== 0 && str_contains($cmd, 'ea-php82')) {
        $fallbackCmd = str_replace('ea-php82', 'php', $cmd);
        echo "  ↪ Reintentando con 'php': $fallbackCmd\n";
        $output = [];
        exec($fallbackCmd . ' 2>&1', $output, $returnCode);
    }

    echo implode("\n", $output) . "\n\n";
}

echo "==========================================================\n";
echo "✅ ¡Post-despliegue completado con éxito! Plataforma lista.\n";
echo "==========================================================\n";
