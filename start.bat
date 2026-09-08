@echo off
echo ====================================================
echo Starting Lib System Servers
echo ====================================================

echo [1/3] Starting Vite Dev Server (npm run dev)...
start "Vite Dev Server" cmd /k "npm run dev"

echo [2/3] Starting Laravel Server (php artisan serve)...
start "Laravel Server" cmd /k "php artisan serve"

echo [3/3] Starting schedule Worker (php artisan schedule:work)...
start "Scheduler" cmd /k "php artisan schedule:work"

echo.
echo All servers have been started in separate windows!
echo You can minimize those windows while working.
echo ====================================================
pause
