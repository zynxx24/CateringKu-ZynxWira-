@echo off
chcp 65001 >nul 2>&1
setlocal enabledelayedexpansion

REM ===========================================================
REM  CateringKu - Script Menjalankan Aplikasi (Windows)
REM  Penggunaan: run.bat [command]
REM
REM  Commands:
REM    dev      - Jalankan dev server (PHP + Vite)
REM    install  - Install semua dependency
REM    setup    - Setup awal (install + migrate + seed)
REM    migrate  - Jalankan migrasi database
REM    seed     - Seed database dengan data test
REM    fresh    - Fresh migrate + seed (HAPUS SEMUA DATA)
REM    build    - Build production assets
REM    clear    - Clear semua cache
REM    test     - Jalankan test suite
REM    stop     - Hentikan semua proses
REM ===========================================================

REM --- Auto-cd ke folder project (CateringKu/) ---
cd /d "%~dp0"

REM --- Deteksi PHP lokal dari tes-main\php83\ ---
REM     Struktur: tes-main\php83\php.exe
REM               tes-main\CateringKu\run.bat  (script ini)
set "PHP_LOCAL=%~dp0..\php83\php.exe"
set "PHP_LOCAL_DIR=%~dp0..\php83"

if exist "%PHP_LOCAL%" (
    set "PATH=%PHP_LOCAL_DIR%;%PATH%"
    set "PHP_SOURCE=lokal [%PHP_LOCAL%]"
) else (
    REM Fallback ke PHP sistem jika php83 tidak ditemukan
    set "PHP_SOURCE=sistem (php83 tidak ditemukan di %PHP_LOCAL%)"
)

REM --- ANSI Colors (Windows 10+) ---
for /F %%a in ('echo prompt $E ^| cmd') do set "ESC=%%a"
set "RED=%ESC%[0;31m"
set "GREEN=%ESC%[0;32m"
set "YELLOW=%ESC%[1;33m"
set "BLUE=%ESC%[0;34m"
set "CYAN=%ESC%[0;36m"
set "BOLD=%ESC%[1m"
set "NC=%ESC%[0m"

REM --- Log paths ---
set "LOG_DIR=%~dp0storage\logs\dev-runner"
set "PHP_DEV_LOG=%LOG_DIR%\php-dev.log"
set "VITE_DEV_LOG=%LOG_DIR%\vite-dev.log"
set "RUNNER_LOG=%LOG_DIR%\runner.log"

REM --- Main ---
set "COMMAND=%~1"
if "%COMMAND%"=="" set "COMMAND=help"

if "%COMMAND%"=="dev"     goto cmd_dev
if "%COMMAND%"=="install" goto cmd_install
if "%COMMAND%"=="setup"   goto cmd_setup
if "%COMMAND%"=="migrate" goto cmd_migrate
if "%COMMAND%"=="seed"    goto cmd_seed
if "%COMMAND%"=="fresh"   goto cmd_fresh
if "%COMMAND%"=="build"   goto cmd_build
if "%COMMAND%"=="clear"   goto cmd_clear
if "%COMMAND%"=="test"    goto cmd_test
if "%COMMAND%"=="stop"    goto cmd_stop
if "%COMMAND%"=="help"    goto cmd_help

call :error "Command tidak dikenal: %COMMAND%"
goto cmd_help

REM ===========================================================
REM  HELPERS
REM ===========================================================
:info
echo %BLUE%[INFO]%NC% %~1
goto :eof

:success
echo %GREEN%[OK]%NC% %~1
goto :eof

:warn
echo %YELLOW%[!]%NC% %~1
goto :eof

:error
echo %RED%[X]%NC% %~1
goto :eof

:debug
echo %CYAN%[DEBUG]%NC% %~1
goto :eof

:timestamp
set "_ts_date=%DATE:~6,4%-%DATE:~3,2%-%DATE:~0,2%"
set "_ts_time=%TIME: =0%"
set "TIMESTAMP=%_ts_date% %_ts_time:~0,8%"
goto :eof

:log_runner
call :timestamp
echo [!TIMESTAMP!] %~1 >> "%RUNNER_LOG%" 2>&1
goto :eof

:prepare_log_dir
if not exist "%LOG_DIR%" mkdir "%LOG_DIR%"
if not exist "%PHP_DEV_LOG%"  type nul > "%PHP_DEV_LOG%"
if not exist "%VITE_DEV_LOG%" type nul > "%VITE_DEV_LOG%"
if not exist "%RUNNER_LOG%"   type nul > "%RUNNER_LOG%"
if not exist "%~dp0storage\logs\laravel.log" (
    if not exist "%~dp0storage\logs" mkdir "%~dp0storage\logs"
    type nul > "%~dp0storage\logs\laravel.log"
)
goto :eof

REM ===========================================================
REM  BANNER
REM ===========================================================
:banner
echo.
echo %CYAN%  +=========================================+%NC%
echo %CYAN%  ^|   CateringKu Dev Server                ^|%NC%
echo %CYAN%  ^|   Platform Katering Online              ^|%NC%
echo %CYAN%  +=========================================+%NC%
echo.
goto :eof

REM ===========================================================
REM  CHECK PREREQUISITES
REM ===========================================================
:check_php
if exist "%PHP_LOCAL%" goto :eof
REM php83 tidak ada, cek PHP sistem
where php >nul 2>&1
if errorlevel 1 (
    call :error "PHP tidak ditemukan!"
    call :error "Diharapkan ada di: %PHP_LOCAL%"
    call :error "Pastikan folder php83\ ada di root repo (sejajar folder CateringKu\)"
    exit /b 1
)
call :warn "Menggunakan PHP dari sistem (bukan php83 lokal)"
goto :eof

:check_node
where node >nul 2>&1
if errorlevel 1 (
    call :error "Node.js tidak ditemukan! Install Node.js 18+ terlebih dahulu."
    exit /b 1
)
goto :eof

:check_composer
where composer >nul 2>&1
if errorlevel 1 (
    call :error "Composer tidak ditemukan! Install Composer terlebih dahulu."
    exit /b 1
)
goto :eof

:check_env
if not exist .env (
    call :warn "File .env tidak ditemukan. Membuat dari .env.example..."
    if exist .env.example (
        copy .env.example .env >nul
        php artisan key:generate --force
        call :success ".env berhasil dibuat dengan APP_KEY baru"
    ) else (
        call :error ".env.example tidak ditemukan!"
        exit /b 1
    )
)
goto :eof

REM ===========================================================
REM  DEBUG DIAGNOSTICS
REM ===========================================================
:print_dev_diagnostics
call :prepare_log_dir

call :info "Mode debug runner aktif"

for /f "delims=" %%i in ('where php 2^>nul') do set "_php_path=%%i"
for /f "delims=" %%i in ('where node 2^>nul') do set "_node_path=%%i"
for /f "delims=" %%i in ('where npm 2^>nul') do set "_npm_path=%%i"
for /f "delims=" %%i in ('where composer 2^>nul') do set "_composer_path=%%i"
if not defined _composer_path set "_composer_path=tidak ditemukan"

call :debug "Project dir : %~dp0"
call :debug "PHP source  : %PHP_SOURCE%"
call :debug "PHP (aktif) : !_php_path!"
call :debug "Node        : !_node_path!"
call :debug "NPM         : !_npm_path!"
call :debug "Composer    : !_composer_path!"
call :debug "APP_ENV     : !APP_ENV!"
call :debug "APP_DEBUG   : !APP_DEBUG!"
call :debug "Log runner  : %RUNNER_LOG%"
call :debug "Log PHP     : %PHP_DEV_LOG%"
call :debug "Log Vite    : %VITE_DEV_LOG%"
call :debug "Log Laravel : %~dp0storage\logs\laravel.log"

call :log_runner "Starting dev runner"
goto :eof

REM ===========================================================
REM  INSTALL
REM ===========================================================
:cmd_install
call :info "Installing dependencies..."
echo.

call :check_composer
if errorlevel 1 exit /b 1
call :check_node
if errorlevel 1 exit /b 1

call :info "Installing PHP dependencies (Composer)..."
call composer install --no-interaction --prefer-dist
if errorlevel 1 (
    call :error "Composer install gagal!"
    exit /b 1
)
call :success "Composer dependencies installed"
echo.

call :info "Installing Node.js dependencies (NPM)..."
call npm install
if errorlevel 1 (
    call :error "NPM install gagal!"
    exit /b 1
)
call :success "NPM dependencies installed"
echo.

call :check_env
if errorlevel 1 exit /b 1

call :success "Semua dependency berhasil diinstall!"
goto :eof

REM ===========================================================
REM  SETUP
REM ===========================================================
:cmd_setup
call :banner
call :info "Setup awal CateringKu..."
echo.

call :cmd_install
if errorlevel 1 exit /b 1
echo.

call :info "Menjalankan migrasi database..."
php artisan migrate --force
if errorlevel 1 (
    call :error "Migrasi gagal!"
    exit /b 1
)
call :success "Migrasi selesai"
echo.

call :info "Seeding database dengan data test..."
php artisan db:seed --class=FullTestSeeder --force
if errorlevel 1 (
    call :error "Seeding gagal!"
    exit /b 1
)
call :success "Seeding selesai"
echo.

echo %GREEN%%BOLD%
echo   +===================================================+
echo   ^|  Setup Selesai!                                  ^|
echo   ^|                                                  ^|
echo   ^|  Akun Test:                                      ^|
echo   ^|  Admin  : admin@cateringku.com / admin123        ^|
echo   ^|  Vendor : vendor@cateringku.com / vendor123      ^|
echo   ^|  User   : user@cateringku.com / user123          ^|
echo   ^|                                                  ^|
echo   ^|  Jalankan: run.bat dev                           ^|
echo   +===================================================+
echo %NC%
goto :eof

REM ===========================================================
REM  DEV
REM ===========================================================
:cmd_dev
call :banner
call :check_php
if errorlevel 1 exit /b 1
call :check_node
if errorlevel 1 exit /b 1
call :check_env
if errorlevel 1 exit /b 1
call :print_dev_diagnostics

call :info "Memutus port 8000 dan 5173..."
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000') do taskkill /f /pid %%a >nul 2>&1
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :5173') do taskkill /f /pid %%a >nul 2>&1
call :success "Port berhasil diputuskan"
echo.

call :info "Menjalankan CateringKu development server..."
echo.
echo   %CYAN%Laravel%NC%  -^> %BOLD%http://0.0.0.0:8000%NC%
echo.
call :info "Tekan %BOLD%Ctrl+C%NC% untuk menghentikan kedua server"
call :info "Log debug tersimpan di %BOLD%%LOG_DIR%%NC%"
echo.

REM Clear cache sebelum dev
call :log_runner "Clearing Laravel caches"
php artisan config:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1

REM PHP command dengan display_errors (sama seperti Linux)
set "PHP_CMD=php -d display_errors=1 -d error_reporting=32767 artisan serve --host=0.0.0.0 --port=8000"
REM Vite command dengan --host dan --debug (sama seperti Linux)
set "VITE_CMD=npm run dev -- --host 0.0.0.0"

call :debug "Command PHP  : %PHP_CMD%"
call :debug "Command Vite : %VITE_CMD%"
call :log_runner "PHP command: %PHP_CMD%"
call :log_runner "Vite command: %VITE_CMD%"

REM Coba gunakan concurrently
where npx >nul 2>&1
if errorlevel 1 goto dev_fallback

call npx --yes concurrently --version >nul 2>&1
if errorlevel 1 goto dev_fallback

REM Jalankan PHP + Vite via concurrently (output tampil di layar, log disimpan terpisah)
REM Catatan: tee tidak tersedia di Windows native, log ditulis via redirection terpisah
call npx concurrently ^
    --names "PHP,Vite" ^
    --prefix-colors "magenta,cyan" ^
    --prefix "[{name}] {time}" ^
    --timestamp-format "HH:mm:ss" ^
    --kill-others ^
    "%PHP_CMD%" ^
    "%VITE_CMD%"
goto :eof

:dev_fallback
call :info "concurrently tidak tersedia, menggunakan mode fallback..."
call :info "Menjalankan PHP server di background (log: %PHP_DEV_LOG%)..."
start "CateringKu-PHP" /min cmd /c "%PHP_CMD% >> "%PHP_DEV_LOG%" 2>&1"

call :info "Menjalankan Vite dev server..."
call :info "Tutup window ini atau tekan Ctrl+C untuk menghentikan Vite."
call :warn "Jalankan [run.bat stop] untuk menghentikan PHP server di background."
echo.
call npm run dev -- --host 0.0.0.0
goto :eof

REM ===========================================================
REM  MIGRATE
REM ===========================================================
:cmd_migrate
call :check_php
if errorlevel 1 exit /b 1
call :info "Menjalankan migrasi..."
php artisan migrate
call :success "Migrasi selesai"
goto :eof

REM ===========================================================
REM  SEED
REM ===========================================================
:cmd_seed
call :check_php
if errorlevel 1 exit /b 1
call :info "Seeding database..."
php artisan db:seed --class=FullTestSeeder
call :success "Seeding selesai"
echo.
echo   %CYAN%Akun Test:%NC%
echo   Admin  : admin@cateringku.com / admin123
echo   Vendor : vendor@cateringku.com / vendor123
echo   User   : user@cateringku.com / user123
goto :eof

REM ===========================================================
REM  FRESH
REM ===========================================================
:cmd_fresh
call :check_php
if errorlevel 1 exit /b 1
call :warn "Ini akan MENGHAPUS SEMUA DATA dan membuat ulang database!"
set /p "CONFIRM=Lanjutkan? (y/N): "
if /i not "%CONFIRM%"=="y" (
    call :info "Dibatalkan."
    goto :eof
)
call :info "Fresh migrate + seed..."
php artisan migrate:fresh --seed --seeder=FullTestSeeder --force
call :success "Database berhasil di-reset dan di-seed"
goto :eof

REM ===========================================================
REM  BUILD
REM ===========================================================
:cmd_build
call :check_node
if errorlevel 1 exit /b 1
call :info "Building production assets..."
call npm run build
call :success "Build selesai! Assets tersedia di public\build\"
goto :eof

REM ===========================================================
REM  CLEAR
REM ===========================================================
:cmd_clear
call :check_php
if errorlevel 1 exit /b 1
call :info "Membersihkan semua cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan event:clear
call :success "Semua cache berhasil dihapus"
goto :eof

REM ===========================================================
REM  TEST
REM ===========================================================
:cmd_test
call :check_php
if errorlevel 1 exit /b 1
call :info "Menjalankan test suite..."
php artisan test
goto :eof

REM ===========================================================
REM  STOP
REM ===========================================================
:cmd_stop
call :info "Menghentikan proses..."

taskkill /f /fi "WINDOWTITLE eq CateringKu-PHP*" >nul 2>&1

tasklist /fi "IMAGENAME eq php.exe" 2>nul | find "php.exe" >nul 2>&1
if not errorlevel 1 (
    taskkill /f /im php.exe >nul 2>&1
    call :success "PHP server dihentikan"
) else (
    call :warn "PHP server tidak berjalan"
)

tasklist /fi "IMAGENAME eq node.exe" 2>nul | find "node.exe" >nul 2>&1
if not errorlevel 1 (
    taskkill /f /im node.exe >nul 2>&1
    call :success "Vite server dihentikan"
) else (
    call :warn "Vite server tidak berjalan"
)

call :info "Memutus port 8000 dan 5173..."
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000') do taskkill /f /pid %%a >nul 2>&1
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :5173') do taskkill /f /pid %%a >nul 2>&1
call :success "Port berhasil diputuskan"
goto :eof

REM ===========================================================
REM  HELP
REM ===========================================================
:cmd_help
call :banner
echo %BOLD%Penggunaan:%NC% run.bat [command]
echo.
echo %BOLD%Commands:%NC%
echo   %GREEN%dev%NC%       Jalankan dev server (PHP + Vite)
echo   %GREEN%install%NC%   Install semua dependency (Composer + NPM)
echo   %GREEN%setup%NC%     Setup awal lengkap (install + migrate + seed)
echo   %GREEN%migrate%NC%   Jalankan migrasi database
echo   %GREEN%seed%NC%      Seed database dengan akun test
echo   %GREEN%fresh%NC%     Fresh migrate + seed %RED%(HAPUS SEMUA DATA)%NC%
echo   %GREEN%build%NC%     Build production assets
echo   %GREEN%clear%NC%     Clear semua cache Laravel
echo   %GREEN%test%NC%      Jalankan test suite
echo   %GREEN%stop%NC%      Hentikan semua server
echo   %GREEN%help%NC%      Tampilkan bantuan ini
echo.
echo %BOLD%Contoh:%NC%
echo   run.bat setup    ^(Setup pertama kali^)
echo   run.bat dev      ^(Jalankan development server^)
echo.
goto :eof
