# 🔧 FIX: Thêm tạo đề thi trước khi lưu câu hỏi

## ❌ Lỗi cũ

```
SQLSTATE[HY000]: General error: 1364 Field 'exam_id' doesn't have a default value
```

**Nguyên nhân:**
- Model `Question` có trường `exam_id` bắt buộc (NOT NULL)
- Controller cũ lưu Question trực tiếp mà không có `exam_id`

## ✅ Giải pháp

### 1. Thêm Modal tạo đề thi

**File:** `resources/views/teacher/ai-import/review.blade.php`

Thay nút "Lưu câu hỏi" bằng nút mở modal:

```html
<button type="button" class="btn btn-success" 
        data-bs-toggle="modal" 
        data-bs-target="#createExamModal">
    <i class="bi bi-save me-2"></i>
    Lưu câu hỏi
</button>
```

Modal yêu cầu teacher nhập:
- ✅ **Tên đề thi** (required)
- ✅ **Mô tả** (optional)
- ✅ **Thời gian** (required, 5-300 phút)
- ✅ **Tổng điểm** (required, 1-1000)
- ✅ **Trạng thái**: Nháp hoặc Công bố

### 2. JavaScript xử lý Modal

Khi submit modal:
1. Lấy dữ liệu từ form modal
2. Tạo hidden inputs trong form chính
3. Submit form chính với đầy đủ dữ liệu

```javascript
document.getElementById('createExamForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get exam data
    const examTitle = document.getElementById('examTitle').value;
    const examDescription = document.getElementById('examDescription').value;
    // ...
    
    // Add to main form as hidden inputs
    const mainForm = document.getElementById('saveForm');
    const inputs = [
        { name: 'exam_title', value: examTitle },
        { name: 'exam_description', value: examDescription },
        // ...
    ];
    
    inputs.forEach(({ name, value }) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        mainForm.appendChild(input);
    });
    
    mainForm.submit();
});
```

### 3. Controller xử lý

**File:** `app/Http/Controllers/Teacher/AIQuestionImportController.php`

#### Thêm use Exam model:
```php
use App\Models\Exam;
```

#### Validation bổ sung:
```php
$request->validate([
    // Exam data
    'exam_title' => 'required|string|max:255',
    'exam_description' => 'nullable|string',
    'exam_duration' => 'required|integer|min:5|max:300',
    'exam_total_marks' => 'required|numeric|min:1|max:1000',
    'exam_status' => 'required|in:draft,published',
    
    // Questions data (giữ nguyên)
    'questions' => 'required|array|min:1',
    // ...
]);
```

#### Logic mới:

```php
DB::beginTransaction();
try {
    // 1. TẠO EXAM TRƯỚC
    $exam = Exam::create([
        'title' => $request->exam_title,
        'description' => $request->exam_description,
        'duration_minutes' => $request->exam_duration,
        'total_marks' => $request->exam_total_marks,
        'is_active' => $request->exam_status === 'published',
        'is_public' => $request->exam_status === 'published',
        'created_by' => auth()->id(),
        'difficulty_level' => 'medium',
    ]);

    // 2. TẠO QUESTIONS VỚI exam_id
    foreach ($request->questions as $index => $questionData) {
        if (!isset($questionData['selected'])) continue;
        
        // Tính điểm cho mỗi câu
        $marksPerQuestion = round($request->exam_total_marks / $selectedCount, 2);
        
        $question = Question::create([
            'exam_id' => $exam->id,  // ← QUAN TRỌNG!
            'question_text' => $questionData['content'],
            'question_type' => 'multiple_choice',
            'marks' => $marksPerQuestion,
            'order' => $questionOrder++,
            'metadata' => json_encode([
                'category_id' => $questionData['category_id'],
                'difficulty' => $questionData['difficulty'],
                'imported_from' => 'ai_pdf',
            ])
        ]);
        
        // Tạo answers
        foreach ($questionData['answers'] as $answerData) {
            Answer::create([
                'question_id' => $question->id,
                'answer_text' => $answerData['content'],
                'is_correct' => $answerData['is_correct'],
                'order' => $answerOrder++,
            ]);
        }
    }
    
    // 3. CẬP NHẬT EXAM STATS
    $exam->update(['total_questions' => $savedCount]);
    
    DB::commit();
    
    // 4. REDIRECT ĐẾN TRANG CHI TIẾT EXAM
    return redirect()->route('teacher.exams.show', $exam->id)
        ->with('success', "Đã tạo đề thi '{$exam->title}' với {$savedCount} câu hỏi!");
        
} catch (\Exception $e) {
    DB::rollBack();
    // Error handling
}
```

### 4. Cập nhật cấu hình Port

**Sửa port từ 5000 → 5001** để tránh conflict:

#### File: `.env`
```properties
PYTHON_API_URL=http://127.0.0.1:5001
```

#### File: `config/services.php`
```php
'python_api' => [
    'url' => env('PYTHON_API_URL', 'http://127.0.0.1:5001'),
],
```

#### File: `python_api_service/app.py`
```python
if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=5001)
```

#### File: `start-python-api.bat`
```bat
echo   URL: http://127.0.0.1:5001
```

## 📋 Tóm tắt thay đổi

### Files đã sửa:

1. ✅ `resources/views/teacher/ai-import/review.blade.php`
   - Thêm modal tạo đề thi
   - JavaScript xử lý submit

2. ✅ `app/Http/Controllers/Teacher/AIQuestionImportController.php`
   - Thêm use Exam model
   - Validation đầy đủ
   - Tạo Exam → Questions → Answers

3. ✅ `.env`
   - Port: 5000 → 5001

4. ✅ `config/services.php`
   - Port default: 5000 → 5001

5. ✅ `python_api_service/app.py`
   - Port: 5000 → 5001

6. ✅ `start-python-api.bat`
   - Thông báo port: 5001

### Files mới:

7. ✅ `AI_IMPORT_WITH_EXAM.md`
   - Hướng dẫn chi tiết

8. ✅ `FIX_EXAM_ID_ERROR.md`
   - File này

## 🎯 Luồng mới

```
1. Upload PDF
   ↓
2. AI trích xuất câu hỏi
   ↓
3. Review & chọn câu hỏi
   ↓
4. Nhấn "Lưu câu hỏi"
   ↓
5. Modal hiện lên ← MỚI
   ↓
6. Nhập thông tin đề thi ← MỚI
   ↓
7. Tạo Exam ← MỚI
   ↓
8. Lưu Questions với exam_id ← FIXED
   ↓
9. Chuyển đến trang chi tiết Exam
```

## 🧪 Cách test

### 1. Khởi động services:

```bash
# Terminal 1: Python API
cd python_api_service
python app.py
# Kết quả: INFO: Uvicorn running on http://0.0.0.0:5001

# Terminal 2: Laravel
php artisan serve
# Kết quả: Server running on [http://127.0.0.1:8000]
```

### 2. Test upload:

1. Login teacher: `http://127.0.0.1:8000/login`
2. Vào: `http://127.0.0.1:8000/teacher/ai-import/upload`
3. Upload file PDF
4. Chọn câu hỏi ở trang Review
5. Nhấn "Lưu câu hỏi"
6. Modal hiện → Nhập tên đề thi
7. Submit

### 3. Kiểm tra database:

```sql
-- Kiểm tra Exam vừa tạo
SELECT * FROM exams ORDER BY id DESC LIMIT 1;

-- Kiểm tra Questions có exam_id
SELECT id, exam_id, question_text FROM questions 
WHERE exam_id = (SELECT MAX(id) FROM exams);

-- Kiểm tra Answers
SELECT a.* FROM answers a
JOIN questions q ON a.question_id = q.id
WHERE q.exam_id = (SELECT MAX(id) FROM exams);
```

## ✨ Kết quả mong đợi

1. ✅ Không còn lỗi "Field 'exam_id' doesn't have a default value"
2. ✅ Exam được tạo với slug tự động
3. ✅ Questions có exam_id hợp lệ
4. ✅ Điểm được chia đều cho các câu hỏi
5. ✅ Redirect đến trang chi tiết đề thi
6. ✅ Teacher có thể chỉnh sửa, gán lớp, công bố

## 📊 So sánh Before/After

### ❌ Before:

```php
// Lưu Question không có exam_id
Question::create([
    'content' => $questionData['content'],
    'category_id' => $questionData['category_id'],
    'difficulty' => $questionData['difficulty'],
    // ← THIẾU exam_id → LỖI!
]);
```

### ✅ After:

```php
// 1. Tạo Exam trước
$exam = Exam::create([...]);

// 2. Lưu Question có exam_id
Question::create([
    'exam_id' => $exam->id,  // ← CÓ exam_id
    'question_text' => $questionData['content'],
    'marks' => $marksPerQuestion,
    'metadata' => json_encode([
        'category_id' => $questionData['category_id'],
        'difficulty' => $questionData['difficulty'],
    ])
]);
```

## 🚀 Deploy checklist

- [ ] Cập nhật `.env` → `PYTHON_API_URL=http://127.0.0.1:5001`
- [ ] Restart Python API trên port 5001
- [ ] Restart Laravel server
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Test upload PDF → Review → Tạo đề thi
- [ ] Kiểm tra database có Exam + Questions + Answers
- [ ] Kiểm tra trang chi tiết đề thi hiển thị đúng

---

**Status:** ✅ FIXED  
**Date:** 25/10/2025  
**Fixed by:** AI Assistant  
**Related:** `AI_IMPORT_WITH_EXAM.md`
