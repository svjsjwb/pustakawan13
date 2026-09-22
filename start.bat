@echo off
echo ====================================================
echo Starting Lib System Servers
echo ====================================================

echo [1/4] Starting Vite Dev Server (npm run dev)...
start "Vite Dev Server" cmd /k "npm run dev"

echo [2/4] Starting Laravel Server (php artisan serve)...
start "Laravel Server" cmd /k "php artisan serve"

echo [3/4] Starting Queue Worker (php artisan queue:listen)...
start "Queue Worker" cmd /k "php artisan queue:listen"

echo [4/4] Starting Schedule Worker (php artisan schedule:work)...
start "Scheduler" cmd /k "php artisan schedule:work"

echo.
echo All servers and queue workers have been started in separate windows!
echo You can minimize those windows while working.
echo ====================================================
pause
