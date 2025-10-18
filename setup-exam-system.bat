@echo off
echo "=== Laravel Exam System Setup ==="
echo.

echo "Step 1: Cleaning up existing tables..."
php artisan migrate --path=database/migrations/2025_10_14_004000_drop_existing_exam_tables.php --force
echo.

echo "Step 2: Running fresh migrations..."
php artisan migrate --force
echo.

echo "Step 3: Seeding database with sample data..."
php artisan db:seed --force
echo.

echo "=== Setup Complete! ==="
echo "You can now access the exam system with these accounts:"
echo.
echo "Admin:"
echo "  Email: admin@example.com"
echo "  Password: password"
echo "  Role: Administrator"
echo.
echo "Teacher:"
echo "  Email: teacher1@example.com"
echo "  Password: password" 
echo "  Role: Teacher"
echo.
echo "Student:"
echo "  Email: student1@example.com"
echo "  Password: password"
echo "  Role: Student"
echo.
pause