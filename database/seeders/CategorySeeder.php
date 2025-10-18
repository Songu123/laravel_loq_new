<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user, or create one if none exists
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            // Create a default admin if none exists
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        $categories = [
            [
                'name' => 'Toán học',
                'description' => 'Các bài kiểm tra về toán học từ cơ bản đến nâng cao',
                'color' => '#3b82f6',
                'icon' => 'fas fa-calculator',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Tiếng Anh',
                'description' => 'Kiểm tra kỹ năng tiếng Anh: ngữ pháp, từ vựng, đọc hiểu',
                'color' => '#10b981',
                'icon' => 'fas fa-language',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Vật lý',
                'description' => 'Bài tập và kiểm tra vật lý phổ thông',
                'color' => '#f59e0b',
                'icon' => 'fas fa-atom',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Hóa học',
                'description' => 'Kiểm tra kiến thức hóa học và phản ứng hóa học',
                'color' => '#ef4444',
                'icon' => 'fas fa-flask',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Sinh học',
                'description' => 'Các chủ đề sinh học từ cơ bản đến nâng cao',
                'color' => '#22c55e',
                'icon' => 'fas fa-leaf',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Địa lý',
                'description' => 'Kiến thức địa lý Việt Nam và thế giới',
                'color' => '#8b5cf6',
                'icon' => 'fas fa-globe',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Lịch sử',
                'description' => 'Lịch sử Việt Nam và lịch sử thế giới',
                'color' => '#f97316',
                'icon' => 'fas fa-landmark',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Văn học',
                'description' => 'Văn học Việt Nam và văn học thế giới',
                'color' => '#ec4899',
                'icon' => 'fas fa-book-open',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Tin học',
                'description' => 'Lập trình, tin học văn phòng và CNTT',
                'color' => '#06b6d4',
                'icon' => 'fas fa-laptop-code',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Giáo dục công dân',
                'description' => 'Kiến thức về công dân và xã hội',
                'color' => '#84cc16',
                'icon' => 'fas fa-balance-scale',
                'sort_order' => 10,
                'is_active' => false, // This one is inactive for testing
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                ...$categoryData,
                'created_by' => $admin->id,
            ]);
        }
    }
}
