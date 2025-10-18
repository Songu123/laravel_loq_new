@echo off
echo "=== Laravel Exam System - Complete Fix ==="
echo.

echo "🧹 Step 1: Cleaning up duplicate/problematic migrations..."
if exist "database\migrations\2025_10_14_003000_create_exams_table.php" (
    del /f "database\migrations\2025_10_14_003000_create_exams_table.php"
    echo "   ✅ Removed old exams migration"
)
if exist "database\migrations\2025_10_14_003100_create_questions_table.php" (
    del /f "database\migrations\2025_10_14_003100_create_questions_table.php"
    echo "   ✅ Removed old questions migration"
)
if exist "database\migrations\2025_10_14_003200_create_answers_table.php" (
    del /f "database\migrations\2025_10_14_003200_create_answers_table.php"
    echo "   ✅ Removed old answers migration"
)
if exist "database\migrations\2025_10_14_004000_drop_existing_exam_tables.php" (
    del /f "database\migrations\2025_10_14_004000_drop_existing_exam_tables.php"
    echo "   ✅ Removed drop tables migration"
)
if exist "database\migrations\2025_10_14_165823_drop_exam_tables.php" (
    del /f "database\migrations\2025_10_14_165823_drop_exam_tables.php" 
    echo "   ✅ Removed second drop tables migration"
)
if exist "database\migrations\2025_10_14_144551_add_role_to_users_table.php" (
    del /f "database\migrations\2025_10_14_144551_add_role_to_users_table.php"
    echo "   ✅ Removed duplicate role migration 1"
)
if exist "database\migrations\2025_10_14_170247_add_role_to_users_table.php" (
    del /f "database\migrations\2025_10_14_170247_add_role_to_users_table.php"
    echo "   ✅ Removed duplicate role migration 2"
)
if exist "database\migrations\2025_10_14_170306_add_role_to_users_table.php" (
    del /f "database\migrations\2025_10_14_170306_add_role_to_users_table.php"
    echo "   ✅ Removed duplicate role migration 3"
)
echo.

echo "📊 Step 2: Checking current database state..."
php artisan tinker --execute="try { echo 'Tables: ' . count(DB::select('SHOW TABLES')) . PHP_EOL; } catch(Exception $e) { echo 'Database connection issue: ' . $e->getMessage() . PHP_EOL; }" 2>nul
echo.

echo "🔄 Step 3: Running fresh migration..."
php artisan migrate:fresh --force
if %errorlevel% neq 0 (
    echo "❌ Migration failed! Trying to fix..."
    php artisan migrate:reset --force
    php artisan migrate --force
)
echo.

echo "🌱 Step 4: Seeding database with sample data..."
php artisan db:seed --force
echo.

echo "✅ Step 5: Verifying database structure..."
php artisan tinker --execute="
try {
    echo 'Users table: ' . DB::table('users')->count() . ' records' . PHP_EOL;
    echo 'Categories table: ' . DB::table('categories')->count() . ' records' . PHP_EOL; 
    echo 'Exams table: ' . DB::table('exams')->count() . ' records' . PHP_EOL;
    echo 'Questions table: ' . DB::table('questions')->count() . ' records' . PHP_EOL;
    echo 'Answers table: ' . DB::table('answers')->count() . ' records' . PHP_EOL;
} catch(Exception $e) {
    echo 'Verification failed: ' . $e->getMessage() . PHP_EOL;
}" 2>nul
echo.

echo "🎉 === SETUP COMPLETE! ==="
echo.
echo "📝 Login credentials:"
echo "   Admin: admin@example.com / password"
echo "   Teacher: teacher1@example.com / password"
echo "   Student: student1@example.com / password"
echo.
echo "🔗 Access URLs:"
echo "   Admin Dashboard: http://localhost:8000/admin/dashboard"
echo "   Teacher Dashboard: http://localhost:8000/teacher/dashboard"
echo "   Student Dashboard: http://localhost:8000/student/dashboard"
echo.
pause