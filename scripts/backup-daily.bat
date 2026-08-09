@echo off
REM =============================================================================
REM  MCYF — Daily Database Backup Script (Windows / XAMPP)
REM
REM  Kya karta hai:
REM   1. MySQL database ka poora dump (.sql file) banata hai
REM   2. Us file ko Google Drive ke sync folder mein save karta hai
REM      (agar Google Drive Desktop app install hai, wo folder khud-ba-khud
REM       cloud par upload kar dega — kisi API/token ki zaroorat nahi)
REM   3. public/assets/uploads folder ka bhi ek zip backup banata hai
REM      (member photos, gallery images, event photos waghera)
REM   4. 30 din se purani backups khud delete kar deta hai (jagah bachane ke liye)
REM
REM  Setup (pehli baar):
REM   1. Neeche di gayi 5 settings apne hisab se edit karein
REM   2. Is file ko double-click karke ek baar test karein
REM   3. Phir Windows Task Scheduler mein daily chalne ke liye set karein
REM      (neeche instructions dekhein)
REM =============================================================================

REM ── EDIT THESE 5 SETTINGS ───────────────────────────────────────────────────

REM 1. XAMPP ka mysql\bin folder (jahan mysqldump.exe hai)
SET MYSQL_BIN=B:\xampp-8.2\mysql\bin

REM 2. Database ka naam, username, password (config/database.php se match karein)
SET DB_NAME=mcyf_db
SET DB_USER=root
SET DB_PASS=

REM 3. Aapke Google Drive folder ka poora path (Google Drive Desktop app se
REM    sync hone wala koi bhi folder — e.g. "G:\My Drive\MCYF-Backups"
REM    ya "C:\Users\YourName\Google Drive\MCYF-Backups")
SET BACKUP_DIR=G:\My Drive\MCYF-Backups

REM 4. App ka uploads folder (member photos, gallery, etc.)
SET UPLOADS_DIR=B:\xampp-8.2\htdocs\Projects\Masood Youth Forum Bolt\MCYF-PHP-Application\public\assets\uploads

REM 5. Kitne din tak purani backups rakhni hain (uske baad khud delete ho jaengi)
SET KEEP_DAYS=30

REM ── DO NOT EDIT BELOW THIS LINE ─────────────────────────────────────────────

REM Backup folder na ho to bana dein
IF NOT EXIST "%BACKUP_DIR%" MKDIR "%BACKUP_DIR%"

REM Aaj ki tareekh/waqt ko filename ke liye format karein (e.g. 2026-08-09_1430)
SET TIMESTAMP=%date:~-4%-%date:~3,2%-%date:~0,2%_%time:~0,2%%time:~3,2%
SET TIMESTAMP=%TIMESTAMP: =0%

REM ── 1. Database dump ─────────────────────────────────────────────────────────
echo Database backup ban raha hai...
"%MYSQL_BIN%\mysqldump.exe" -u%DB_USER% %DB_PASS% --single-transaction --routines --triggers %DB_NAME% > "%BACKUP_DIR%\mcyf_db_%TIMESTAMP%.sql"

IF %ERRORLEVEL% NEQ 0 (
    echo KHARABI: Database backup fail ho gaya! Settings check karein.
    exit /b 1
)
echo Database backup mukammal: mcyf_db_%TIMESTAMP%.sql

REM ── 2. Uploads folder ka zip backup ────────────────────────────────────────────
echo Uploads folder ka backup ban raha hai...
powershell -Command "Compress-Archive -Path '%UPLOADS_DIR%' -DestinationPath '%BACKUP_DIR%\uploads_%TIMESTAMP%.zip' -Force"
echo Uploads backup mukammal: uploads_%TIMESTAMP%.zip

REM ── 3. 30 din se purani backups delete karein ──────────────────────────────────
echo Purani backups saaf ki ja rahi hain...
forfiles /p "%BACKUP_DIR%" /m mcyf_db_*.sql /d -%KEEP_DAYS% /c "cmd /c del @path" 2>nul
forfiles /p "%BACKUP_DIR%" /m uploads_*.zip /d -%KEEP_DAYS% /c "cmd /c del @path" 2>nul

echo.
echo ✅ Backup mukammal ho gaya: %BACKUP_DIR%
echo Google Drive Desktop app khud-ba-khud is folder ko cloud par sync kar dega.
