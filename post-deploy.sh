#!/usr/bin/env bash

# ==========================================================
# Script de Post-Despliegue para BanaHosting / cPanel
# Clínica Sur Hospital — Agenda de Citas
# ==========================================================

set -e

echo "=========================================================="
echo "🚀 Iniciando post-despliegue en BanaHosting..."
echo "=========================================================="

# 1. Determinar el binario de PHP (prioridad ea-php82 en cPanel)
if command -v ea-php82 &> /dev/null; then
    PHP_BIN="ea-php82"
elif [ -f "/usr/local/bin/ea-php82" ]; then
    PHP_BIN="/usr/local/bin/ea-php82"
else
    PHP_BIN="php"
fi

echo "📌 Utilizando ejecutable PHP: $PHP_BIN"

# 2. Ejecutar migraciones pendientes
echo ""
echo "📦 [1/3] Ejecutando migraciones de base de datos..."
$PHP_BIN artisan migrate --force

# 3. Limpiar y optimizar cachés de Laravel
echo ""
echo "🧹 [2/3] Limpiando y optimizando cachés..."
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan optimize

# 4. Asegurar permisos de almacenamiento y caché
echo ""
echo "🔒 [3/3] Aplicando permisos 775 en storage y bootstrap/cache..."
chmod -R 775 storage bootstrap/cache

echo ""
echo "=========================================================="
echo "✅ ¡Post-despliegue completado con éxito! Plataforma lista."
echo "=========================================================="
