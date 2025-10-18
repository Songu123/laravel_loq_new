<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name' => 'Admin System',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0123456789',
            'bio' => 'Quản trị viên hệ thống',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Teacher users
        $teachers = [
            [
                'name' => 'Nguyễn Văn Giáo',
                'email' => 'teacher1@example.com',
                'phone' => '0987654321',
                'bio' => 'Giáo viên môn Toán học với 10 năm kinh nghiệm',
            ],
            [
                'name' => 'Trần Thị Lan',
                'email' => 'teacher2@example.com', 
                'phone' => '0987654322',
                'bio' => 'Giáo viên môn Tiếng Anh, chuyên gia IELTS',
            ],
            [
                'name' => 'Lê Minh Khoa',
                'email' => 'teacher3@example.com',
                'phone' => '0987654323', 
                'bio' => 'Giáo viên môn Vật lý, thạc sĩ khoa học',
            ],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                ...$teacher,
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create Student users
        $students = [
            [
                'name' => 'Phạm Văn An',
                'email' => 'student1@example.com',
                'phone' => '0912345678',
                'bio' => 'Học sinh lớp 12A1, yêu thích môn Toán',
                'student_id' => 'SV001',
            ],
            [
                'name' => 'Hoàng Thị Bình',
                'email' => 'student2@example.com',
                'phone' => '0912345679',
                'bio' => 'Học sinh lớp 11B2, đam mê Tiếng Anh',
                'student_id' => 'SV002',
            ],
            [
                'name' => 'Vũ Minh Cường',
                'email' => 'student3@example.com',
                'phone' => '0912345680',
                'bio' => 'Học sinh lớp 10A3, thích khám phá khoa học',
                'student_id' => 'SV003',
            ],
            [
                'name' => 'Đặng Thị Dung',
                'email' => 'student4@example.com',
                'phone' => '0912345681',
                'bio' => 'Học sinh lớp 12C1, ước mơ trở thành bác sĩ',
                'student_id' => 'SV004',
            ],
            [
                'name' => 'Ngô Văn Dũng',
                'email' => 'student5@example.com',
                'phone' => '0912345682',
                'bio' => 'Học sinh lớp 11A2, yêu thích lập trình',
                'student_id' => 'SV005',
            ],
        ];

        foreach ($students as $student) {
            User::create([
                ...$student,
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Sample users created successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Teacher: teacher1@example.com / password');
        $this->command->info('Student: student1@example.com / password');
    }
}