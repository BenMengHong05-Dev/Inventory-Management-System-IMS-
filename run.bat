@echo off
setlocal
cd /d "%~dp0"

echo ===================================================
echo   Inventory Management System - Windows Launcher
echo ===================================================

if not exist "%~dp0vendor" (
    echo [INFO] Installing Composer dependencies...
    call composer install --no-interaction
)

if not exist "%~dp0.env" (
    echo [INFO] Initializing .env configuration...
    copy "%~dp0.env.example" "%~dp0.env" >nul
    call php -c "%~dp0php.ini" artisan key:generate
)

echo [INFO] Starting Laravel server at http://localhost:8000 ...
start http://localhost:8000
php -c "%~dp0php.ini" -S localhost:8000 "%~dp0index.php"
pause
