@echo off
title QR Cafe PHP Server
echo ====================================================================
echo             ☕ STARTING QR CAFE PHP WEBSITE ☕
echo ====================================================================
echo.
echo Server running at: http://localhost:8000
echo.
echo Pages:
echo   - 1. Order Menu:          http://localhost:8000/
echo   - 2. Orders & Bills:      http://localhost:8000/orders.php
echo   - 3. Manage Menu Items:   http://localhost:8000/menu.php
echo   - 4. Table QR Standees:   http://localhost:8000/qr_codes.php
echo.
echo Press Ctrl + C to stop the server.
echo ====================================================================
echo.

"C:\Users\Shyam\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" -S localhost:8000
pause
