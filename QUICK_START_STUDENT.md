# 🚀 Quick Start - Student System

## ⚡ Chạy Migration

```bash
php artisan migrate
```

## 🧪 Test Routes

```bash
php artisan route:list --name=student
```

Kết quả mong đợi:
```
GET    student/dashboard              -> dashboard
GET    student/exams                  -> exams.index
GET    student/exams/{exam}           -> exams.show
GET    student/exams/{exam}/take      -> exams.take
POST   student/exams/{exam}/submit    -> exams.submit
GET    student/history                -> history
GET    student/results/{attempt}      -> results.show
```

## 👨‍🎓 Login Student

**URL**: `http://localhost:8000/login/student`

**Test Account** (từ UserSeeder):
```
Email: student1@example.com
Password: password
```

## 🔗 URLs Chính

| Page | URL | Description |
|------|-----|-------------|
| Dashboard | `/student/dashboard` | Trang chủ sinh viên |
| Browse Exams | `/student/exams` | Danh sách đề thi |
| Exam Detail | `/student/exams/{id}` | Chi tiết đề thi |
| Take Exam | `/student/exams/{id}/take` | Làm bài thi |
| History | `/student/history` | Lịch sử thi |
| Result | `/student/results/{attempt_id}` | Kết quả chi tiết |

## 📝 Test Flow

### 1. Tạo đề thi (Teacher)
```
1. Login as teacher
2. Go to /teacher/exams/create
3. Create exam with questions
4. Activate exam (is_active = true, is_public = true)
```

### 2. Làm bài thi (Student)
```
1. Login as student
2. Go to /student/exams
3. Click "Bắt đầu" on an exam
4. Fill answers
5. Click "Nộp bài"
6. View result automatically
```

### 3. Xem lịch sử
```
1. Go to /student/history
2. See all attempts
3. Click "Xem" to see detail
```

## 🗄️ Database Check

```bash
php artisan tinker
```

```php
// Check tables
Schema::hasTable('exam_attempts')
Schema::hasTable('exam_attempt_answers')

// Count records
\App\Models\ExamAttempt::count()
\App\Models\ExamAttemptAnswer::count()

// Get latest attempt
\App\Models\ExamAttempt::with('exam', 'user', 'answers')->latest()->first()
```

## 🐛 Troubleshooting

### Lỗi: Class not found
```bash
composer dump-autoload
```

### Lỗi: Route not found
```bash
php artisan route:clear
php artisan route:list --name=student
```

### Lỗi: View not found
```bash
php artisan view:clear
```

### Lỗi: Migration already exists
```bash
# Xóa duplicate migrations
# Giữ lại file mới nhất
```

### Lỗi: Foreign key constraint
```bash
# Chạy migrations theo thứ tự:
php artisan migrate --path=database/migrations/2025_10_15_002529_create_exam_attempts_table.php
php artisan migrate --path=database/migrations/2025_10_15_002609_create_exam_attempt_answers_table.php
```

## 📊 Sample Data

### Create test attempt manually:
```php
$attempt = \App\Models\ExamAttempt::create([
    'exam_id' => 1,
    'user_id' => 6, // student1
    'total_questions' => 5,
    'started_at' => now()->subMinutes(10),
    'completed_at' => now(),
    'time_spent' => 600,
]);

// Add answers
foreach($exam->questions as $question) {
    \App\Models\ExamAttemptAnswer::create([
        'attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'answer_id' => $question->answers->first()->id, // Random answer
    ])->checkCorrectness();
}

// Calculate score
$attempt->calculateScore();
```

## ✅ Verification Checklist

- [ ] Migrations run successfully
- [ ] Tables created (exam_attempts, exam_attempt_answers)
- [ ] Routes registered
- [ ] Middleware works (student only)
- [ ] Can view dashboard
- [ ] Can browse exams
- [ ] Can view exam detail
- [ ] Can take exam
- [ ] Timer works
- [ ] Can submit exam
- [ ] Score calculated correctly
- [ ] Can view result
- [ ] Can view history
- [ ] Filters work (category, difficulty)
- [ ] Pagination works
- [ ] Print result works

## 🎯 Performance Tips

### Eager Loading:
```php
// Good ✓
$exams = Exam::with('category', 'questions')->get();

// Bad ✗
$exams = Exam::all();
foreach($exams as $exam) {
    $exam->category; // N+1 query problem
}
```

### Query Optimization:
```php
// Use select to get only needed columns
$attempts = ExamAttempt::select('id', 'exam_id', 'score', 'percentage')
    ->forUser($userId)
    ->get();

// Use pagination
$exams = Exam::paginate(12);
```

## 📚 Documentation

- [STUDENT_INTERFACE_COMPLETE.md](STUDENT_INTERFACE_COMPLETE.md) - Frontend views
- [STUDENT_BACKEND_COMPLETE.md](STUDENT_BACKEND_COMPLETE.md) - Backend implementation

---

**Happy Coding! 🚀**
