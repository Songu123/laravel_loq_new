@echo off
echo ========================================
echo   SETUP STUDENT SYSTEM - BACKEND
echo ========================================
echo.

echo [1/4] Running migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: Migration failed!
    pause
    exit /b 1
)
echo     ✓ Migrations completed successfully
echo.

echo [2/4] Clearing cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo     ✓ Cache cleared
echo.

echo [3/4] Listing student routes...
php artisan route:list --name=student
echo.

echo [4/4] Checking database tables...
php artisan tinker --execute="echo 'exam_attempts: ' . (Schema::hasTable('exam_attempts') ? 'EXISTS' : 'NOT FOUND') . PHP_EOL; echo 'exam_attempt_answers: ' . (Schema::hasTable('exam_attempt_answers') ? 'EXISTS' : 'NOT FOUND') . PHP_EOL;"
echo.

echo ========================================
echo   ✓ SETUP COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo Next Steps:
echo 1. Login as teacher and create exams
echo 2. Login as student: http://localhost:8000/login/student
echo 3. Test student dashboard: http://localhost:8000/student/dashboard
echo.
pause
