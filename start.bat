@echo off
set PHP=C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64\php.exe
echo ====================================================
echo Starting Lib System Servers (PHP 8.4)
echo ====================================================

echo [1/4] Starting Vite Dev Server (npm run dev)...
start "Vite Dev Server" cmd /k "npm run dev"

echo [2/4] Starting Laravel Server (php artisan serve)...
start "Laravel Server" cmd /k "%PHP% artisan serve"

echo [3/4] Starting Queue Worker (php artisan queue:work)...
start "Queue Worker" cmd /k "%PHP% artisan queue:work --tries=3 --timeout=90"

echo [4/4] Starting Schedule Worker (php artisan schedule:work)...
start "Scheduler" cmd /k "%PHP% artisan schedule:work"

echo.
echo All servers and queue workers have been started in separate windows!
echo Queue worker akan mengirim email notifikasi secara otomatis.
echo ====================================================
pause
