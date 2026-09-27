# Inventory Management System - PowerShell Launcher
$ErrorActionPreference = "Stop"
Set-Location -Path $PSScriptRoot

Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "  Inventory Management System - PowerShell Launcher" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan

if (-not (Test-Path "$PSScriptRoot\vendor")) {
    Write-Host "[INFO] Installing Composer dependencies..." -ForegroundColor Yellow
    composer install --no-interaction
}

if (-not (Test-Path "$PSScriptRoot\.env")) {
    Write-Host "[INFO] Initializing .env configuration..." -ForegroundColor Yellow
    Copy-Item "$PSScriptRoot\.env.example" "$PSScriptRoot\.env"
    & php -c "$PSScriptRoot\php.ini" artisan key:generate
}

Write-Host "[INFO] Starting Laravel server at http://localhost:8000 ..." -ForegroundColor Green
Start-Process "http://localhost:8000"
& php -c "$PSScriptRoot\php.ini" -S localhost:8000 "$PSScriptRoot\index.php"
