@echo off
cls
echo ============================================
echo Laravel Exam System - Database Fix
echo ============================================
echo.

echo [1/3] Resetting database...
php artisan migrate:reset --force
echo.

echo [2/3] Running migrations...
php artisan migrate --force
echo.

echo [3/3] Seeding data...
php artisan db:seed --force
echo.

echo ============================================
echo Database setup completed!
echo ============================================
echo.
echo Login accounts:
echo Admin: admin@example.com / password
echo Teacher: teacher1@example.com / password
echo Student: student1@example.com / password
echo.
pause