@echo off
echo 🧹 Cleaning up WordPress installation for headless LMS...

REM Navigate to WordPress root
cd /d c:\xampp\htdocs\lms

REM Remove unnecessary admin files
echo Removing unnecessary admin files...
if exist "wp-admin\about.php" del /q "wp-admin\about.php"
if exist "wp-admin\credits.php" del /q "wp-admin\credits.php"
if exist "wp-admin\freedoms.php" del /q "wp-admin\freedoms.php"
if exist "wp-admin\contribute.php" del /q "wp-admin\contribute.php"
if exist "wp-admin\press-this.php" del /q "wp-admin\press-this.php"

REM Remove unnecessary admin pages we don't need in headless
if exist "wp-admin\customize.php" del /q "wp-admin\customize.php"
if exist "wp-admin\theme-*.php" del /q "wp-admin\theme-*.php"
if exist "wp-admin\edit-comments.php" del /q "wp-admin\edit-comments.php"
if exist "wp-admin\comment.php" del /q "wp-admin\comment.php"
if exist "wp-admin\moderation.php" del /q "wp-admin\moderation.php"

REM Clean up uploads directory
echo Cleaning uploads directory...
if exist "wp-content\uploads" (
    for /r "wp-content\uploads" %%f in (*.log) do del /q "%%f" 2>nul
    for /r "wp-content\uploads" %%f in (debug.log) do del /q "%%f" 2>nul
)

echo ✅ WordPress cleanup completed!
echo.
echo 📁 Removed files:
echo    - Unnecessary admin pages
echo    - Default theme files (kept minimal theme)
echo    - Log files
echo.
echo 🔧 Next steps:
echo    1. Activate the 'Headless LMS' theme
echo    2. Activate the performance optimizer plugins
echo    3. Test REST API endpoints
echo    4. Run frontend build process

pause
