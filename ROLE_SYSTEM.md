# Hệ thống Role trong Laravel Exam System

## 📋 Tổng quan

Hệ thống sử dụng 3 role chính cho users:
- **Admin**: Quản trị viên hệ thống
- **Teacher**: Giáo viên 
- **Student**: Học sinh

## 🗄️ Database Structure

### Users Table
```sql
users (
    id,
    name,
    email,
    email_verified_at,
    role ENUM('admin', 'teacher', 'student') DEFAULT 'student',
    phone,
    bio,
    avatar,
    is_active BOOLEAN DEFAULT true,
    password,
    remember_token,
    created_at,
    updated_at
)
```

## 🔐 Permissions & Access Control

### Admin Role
- **Full system access**
- Manage all categories, exams, users
- System configuration
- View all statistics
- Access: `/admin/*`

### Teacher Role  
- **Content creation & management**
- Create/edit own exams and questions
- Create/request categories
- View own statistics
- Access: `/teacher/*`

### Student Role
- **Learning & testing**
- Browse available exams
- Take exams
- View own results
- Access: `/student/*`

## 🛡️ Middleware

### Available Middleware
```php
'admin' => AdminMiddleware::class,      // Admin only
'teacher' => TeacherMiddleware::class,  // Teacher only  
'student' => StudentMiddleware::class,  // Student only
'role' => CheckRole::class,             // Multiple roles
```

### Usage Examples
```php
// Single role
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin only routes
});

// Multiple roles using CheckRole middleware
Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    // Admin and Teacher routes
});
```

## 🎯 User Model Methods

### Role Checking
```php
$user->isAdmin()     // bool
$user->isTeacher()   // bool  
$user->isStudent()   // bool
```

### Display Methods
```php
$user->getRoleDisplayName()    // "Quản trị viên", "Giáo viên", "Học sinh"
$user->getRoleBadgeColor()     // "danger", "warning", "primary"
$user->getFullNameWithRole()   // "John Doe (Giáo viên)"
$user->getAvatarUrl()          // Avatar URL with role-based defaults
```

### Scopes
```php
User::role('teacher')->get()    // Get all teachers
User::active()->get()           // Get active users
User::verified()->get()         // Get verified users
```

## 🔀 Route Structure

```
/admin/*        - Admin dashboard & management
/teacher/*      - Teacher tools & content creation  
/student/*      - Student learning & testing
```

## 🌱 Sample Data

### Default Accounts
| Role | Email | Password | Name |
|------|-------|----------|------|
| Admin | admin@example.com | password | Admin System |
| Teacher | teacher1@example.com | password | Nguyễn Văn Giáo |
| Student | student1@example.com | password | Phạm Văn An |

## 🚀 Setup Instructions

1. **Run Migration:**
   ```bash
   php artisan migrate
   ```

2. **Seed Sample Data:**
   ```bash
   php artisan db:seed
   ```

3. **Or Use Batch Script:**
   ```bash
   .\setup-exam-system.bat
   ```

## 🔧 Configuration

### Middleware Registration
Middleware được đăng ký trong `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'teacher' => \App\Http\Middleware\TeacherMiddleware::class, 
    'student' => \App\Http\Middleware\StudentMiddleware::class,
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

### Role-based Redirects
Sau khi login, user sẽ được redirect theo role:
- Admin → `/admin/dashboard`
- Teacher → `/teacher/dashboard`  
- Student → `/student/dashboard`

## 📱 Frontend Integration

### Blade Templates
```blade
@if(auth()->user()->isAdmin())
    <!-- Admin content -->
@elseif(auth()->user()->isTeacher())
    <!-- Teacher content -->
@elseif(auth()->user()->isStudent())
    <!-- Student content -->
@endif
```

### Role Badge Display
```blade
<span class="badge bg-{{ auth()->user()->getRoleBadgeColor() }}">
    {{ auth()->user()->getRoleDisplayName() }}
</span>
```

---
*Hệ thống role được thiết kế để dễ dàng mở rộng và bảo trì.*