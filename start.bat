@echo off
echo ====================================================
echo Starting Lib System Servers
echo ====================================================

if exist "C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64" (
    set "PATH=C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64;%PATH%"
)

echo [1/5] Starting Vite Dev Server (npm run dev)...
start "Vite Dev Server" cmd /k "npm run dev"

echo [2/5] Starting Laravel Server (php artisan serve)...
start "Laravel Server" cmd /k "php artisan serve"

echo [3/5] Starting Queue Worker (php artisan queue:work)...
start "Queue Worker" cmd /k "php artisan queue:work --tries=3 --timeout=90 --sleep=3"

echo [4/5] Starting Schedule Worker (php artisan schedule:work)...
start "Scheduler" cmd /k "php artisan schedule:work"

echo [5/5] Starting ngrok tunnel (greeter-undaunted-bath.ngrok-free.dev)...
start "ngrok Tunnel" cmd /k "C:\laragon\bin\ngrok\ngrok.exe http --domain=greeter-undaunted-bath.ngrok-free.dev 8000"

echo.
echo All servers started!
echo.
echo Local:   http://127.0.0.1:8000
echo Hosting: https://greeter-undaunted-bath.ngrok-free.dev
echo.
echo Queue worker akan mengirim email notifikasi secara otomatis.
echo ====================================================

pause

