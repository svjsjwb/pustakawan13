@echo off
echo ====================================================
echo Starting ngrok tunnel for Perpustakaan TS
echo Domain: greeter-undaunted-bath.ngrok-free.dev
echo ====================================================
echo.
echo PASTIKAN Laravel server sudah berjalan di port 8000!
echo (jalankan start.bat terlebih dahulu jika belum)
echo.
C:\laragon\bin\ngrok\ngrok.exe http --domain=greeter-undaunted-bath.ngrok-free.dev 8000
pause
