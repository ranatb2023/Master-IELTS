@echo off
REM Backup and Update PHP.ini for Large File Uploads

set PHP_INI=C:\xampp\php\php.ini
set BACKUP_DIR=C:\xampp\php\backups

echo ========================================
echo PHP Configuration Update Script
echo ========================================
echo.

REM Check if php.ini exists
if not exist "%PHP_INI%" (
    echo ERROR: php.ini not found at %PHP_INI%
    echo Please check your XAMPP installation path.
    pause
    exit /b 1
)

REM Create backups directory
if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
    echo Created backups directory: %BACKUP_DIR%
)

REM Create backup with timestamp
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c%%a%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a%%b)
set BACKUP_FILE=%BACKUP_DIR%\php.ini.backup.%mydate%_%mytime%

echo Creating backup...
copy "%PHP_INI%" "%BACKUP_FILE%"
if %errorlevel% neq 0 (
    echo ERROR: Failed to create backup
    pause
    exit /b 1
)
echo Backup created: %BACKUP_FILE%
echo.

echo Updating PHP configuration values...
echo.

REM Use PowerShell to update the values
powershell -Command "(Get-Content '%PHP_INI%') -replace '^upload_max_filesize\s*=.*', 'upload_max_filesize = 1024M' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace '^post_max_size\s*=.*', 'post_max_size = 1030M' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace '^max_execution_time\s*=.*', 'max_execution_time = 600' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace '^max_input_time\s*=.*', 'max_input_time = 600' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace '^memory_limit\s*=.*', 'memory_limit = 1024M' | Set-Content '%PHP_INI%'"

echo.
echo ========================================
echo Configuration Updated Successfully!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Open XAMPP Control Panel
echo 2. Stop Apache
echo 3. Start Apache
echo 4. Run: php update_php_limits.php (to verify changes)
echo 5. Try uploading your video again
echo.
echo If something goes wrong, restore from backup:
echo %BACKUP_FILE%
echo.
pause
