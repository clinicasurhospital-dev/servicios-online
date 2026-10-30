@echo off
echo =========================================
echo  EJECUTANDO DESPLIEGUE AUTOMATICO LARAVEL
echo =========================================

cd /d C:\inetpub\wwwroot

:: 1. Limpieza de cachés antiguas
"C:\php\php.exe" artisan optimize:clear

:: 2. Regenerar caché de configuración y rutas para máximo rendimiento
"C:\php\php.exe" artisan config:cache
"C:\php\php.exe" artisan route:cache
"C:\php\php.exe" artisan view:cache

echo =========================================
echo  DESPLIEGUE COMPLETADO CON EXITO
echo =========================================