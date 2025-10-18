# 🎓 Laravel Exam System - Teacher Guide

## 📖 Tài liệu hướng dẫn

- **[EXAM_GUIDE.md](./EXAM_GUIDE.md)** - Hướng dẫn chi tiết tạo và quản lý đề thi
- **[ROLE_SYSTEM.md](./ROLE_SYSTEM.md)** - Hệ thống phân quyền và vai trò

## 🚀 Quick Start

### 1. Setup Database
```bash
.\setup-db.bat
```

Hoặc manual:
```bash
php artisan migrate:fresh --seed
```

### 2. Đăng nhập

| Role | URL | Email | Password |
|------|-----|-------|----------|
| Admin | /login/admin | admin@example.com | password |
| Teacher | /login/teacher | teacher1@example.com | password |
| Student | /login/student | student1@example.com | password |

### 3. Truy cập Dashboard

- Admin: `http://localhost:8000/admin/dashboard`
- Teacher: `http://localhost:8000/teacher/dashboard`
- Student: `http://localhost:8000/student/dashboard`

## ✨ Tính năng chính

### 👨‍🏫 Teacher Features

#### 📝 Quản lý Đề thi
- ✅ Tạo đề thi với 4 loại câu hỏi
- ✅ Chỉnh sửa và cập nhật đề thi
- ✅ Xóa đề thi
- ✅ Sao chép đề thi
- ✅ Kích hoạt/Tắt đề thi
- ✅ Xem thống kê chi tiết

#### 📁 Quản lý Categories
- ✅ Tạo category mới
- ✅ Chỉnh sửa category
- ✅ Request approval cho category
- ✅ API endpoint cho dropdown

#### 📊 Dashboard
- ✅ Tổng quan đề thi
- ✅ Thống kê câu hỏi
- ✅ Báo cáo hoạt động

### 🎯 Loại câu hỏi

1. **Multiple Choice** - Trắc nghiệm
   - Nhiều đáp án
   - Chọn 1 đáp án đúng
   - Có giải thích

2. **True/False** - Đúng/Sai
   - 2 đáp án
   - Đơn giản, nhanh

3. **Short Answer** - Trả lời ngắn
   - Tự nhập đáp án
   - Text input

4. **Essay** - Tự luận
   - Câu trả lời dài
   - Textarea

## 🗂️ Cấu trúc Database

```
users
├── id
├── name
├── email
├── role (admin, teacher, student)
├── phone
├── bio
├── avatar
└── is_active

categories
├── id
├── name
├── description
├── icon
├── color
└── created_by

exams
├── id
├── title
├── slug
├── description
├── category_id
├── created_by
├── duration_minutes
├── total_questions
├── total_marks
├── difficulty_level
├── is_active
├── is_public
├── start_time
├── end_time
└── settings (JSON)

questions
├── id
├── exam_id
├── question_text
├── question_type
├── marks
├── is_required
├── explanation
└── order

answers
├── id
├── question_id
├── answer_text
├── is_correct
└── order
```

## 🔧 Architecture

### Backend
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Teacher/
│   │   │   ├── ExamController.php
│   │   │   └── CategoryController.php
│   │   └── Api/
│   │       └── CategoryApiController.php
│   ├── Middleware/
│   │   ├── AdminMiddleware.php
│   │   ├── TeacherMiddleware.php
│   │   ├── StudentMiddleware.php
│   │   └── CheckRole.php
│   └── Requests/
│       ├── StoreExamRequest.php
│       └── UpdateExamRequest.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Exam.php
│   ├── Question.php
│   └── Answer.php
└── Services/
    └── ExamService.php
```

### Frontend
```
resources/views/
├── layouts/
│   ├── teacher-dashboard.blade.php
│   └── app.blade.php
└── teacher/
    ├── exams/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   └── show.blade.php
    └── categories/
        ├── index.blade.php
        └── create.blade.php
```

## 🎨 UI/UX Features

- ✅ Bootstrap 5.3.3
- ✅ Bootstrap Icons
- ✅ Responsive Design
- ✅ Grid/List View Toggle
- ✅ Real-time Validation
- ✅ Dynamic Question Builder
- ✅ AJAX Category Loading
- ✅ Dark Mode Ready

## 🔒 Security Features

- ✅ Role-based Access Control
- ✅ Ownership Verification
- ✅ CSRF Protection
- ✅ XSS Prevention
- ✅ SQL Injection Protection
- ✅ Email Verification
- ✅ Password Hashing

## 📝 API Endpoints

### Teacher Exams
```
GET    /teacher/exams              - Danh sách đề thi
GET    /teacher/exams/create       - Form tạo đề thi
POST   /teacher/exams              - Lưu đề thi mới
GET    /teacher/exams/{id}         - Chi tiết đề thi
GET    /teacher/exams/{id}/edit    - Form sửa đề thi
PUT    /teacher/exams/{id}         - Cập nhật đề thi
DELETE /teacher/exams/{id}         - Xóa đề thi
POST   /teacher/exams/{id}/toggle  - Toggle status
POST   /teacher/exams/{id}/duplicate - Sao chép đề thi
```

### Teacher Categories
```
GET    /teacher/categories         - Danh sách category
POST   /teacher/categories         - Tạo category
GET    /teacher/api/categories     - API lấy categories
```

## 🛠️ Development Commands

### Migration
```bash
# Reset và chạy lại migrations
php artisan migrate:fresh

# Chạy seeder
php artisan db:seed

# Reset + Seed
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

### Cache
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear compiled
php artisan clear-compiled
php artisan optimize:clear
```

### Testing
```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter ExamTest
```

## 🐛 Troubleshooting

### Database Issues
```bash
# Fix database tables
.\setup-db.bat

# Or manual
php artisan migrate:reset
php artisan migrate
php artisan db:seed
```

### Permission Issues
- Check user role in database
- Verify middleware in routes
- Clear auth cache

### Validation Errors
- Check required fields
- Verify data types
- Review validation rules

## 📊 Sample Data

### Users
- 1 Admin
- 3 Teachers
- 5 Students

### Categories
- 10 categories (subjects)
- Icons and colors

### Exams
- 2 sample exams
- Multiple question types
- With answers and explanations

## 🚀 Deployment

### Requirements
- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Node.js & NPM

### Steps
```bash
# 1. Clone repository
git clone [repo-url]

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# 5. Run migrations
php artisan migrate:fresh --seed

# 6. Build assets
npm run build

# 7. Serve
php artisan serve
```

## 📞 Support

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap Documentation](https://getbootstrap.com)
- [MySQL Documentation](https://dev.mysql.com/doc/)

### Issues
- Check logs: `storage/logs/laravel.log`
- Debug mode: Set `APP_DEBUG=true` in `.env`
- Contact: [your-email]

---

**Made with ❤️ using Laravel 10.x**

*Last Updated: October 15, 2025*