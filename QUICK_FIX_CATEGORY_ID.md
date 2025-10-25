# ⚡ QUICK FIX: Field 'category_id' doesn't have a default value

## Lỗi
```
SQLSTATE[HY000]: General error: 1364 Field 'category_id' doesn't have a default value
```

## Nguyên nhân
Bảng `exams` có trường `category_id` bắt buộc (NOT NULL) nhưng Controller không truyền giá trị.

## Giải pháp

### 1. Thêm trường Category vào Modal (view)
**File:** `resources/views/teacher/ai-import/review.blade.php`

```html
<select class="form-select" id="examCategory" name="exam_category_id" required>
    <option value="">-- Chọn danh mục --</option>
    @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
    @endforeach
</select>
```

### 2. Cập nhật JavaScript
**File:** `resources/views/teacher/ai-import/review.blade.php`

```javascript
const examCategoryId = document.getElementById('examCategory').value;

// Validate
if (!examCategoryId) {
    alert('Vui lòng chọn danh mục cho đề thi!');
    return false;
}

// Add to form
const inputs = [
    { name: 'exam_category_id', value: examCategoryId },
    // ... other fields
];
```

### 3. Cập nhật Validation (Controller)
**File:** `app/Http/Controllers/Teacher/AIQuestionImportController.php`

```php
$request->validate([
    'exam_category_id' => 'required|exists:categories,id', // ← THÊM DÒNG NÀY
    'exam_title' => 'required|string|max:255',
    // ...
]);
```

### 4. Cập nhật Create Exam
**File:** `app/Http/Controllers/Teacher/AIQuestionImportController.php`

```php
$exam = Exam::create([
    'title' => $request->exam_title,
    'category_id' => $request->exam_category_id, // ← THÊM DÒNG NÀY
    'duration_minutes' => $request->exam_duration,
    // ...
]);
```

## Test lại

1. Refresh trang: `http://127.0.0.1:8000/teacher/ai-import/review`
2. Nhấn "Lưu câu hỏi"
3. Modal hiện → **Chọn danh mục** đầu tiên
4. Nhập tên đề thi
5. Submit → ✅ Thành công!

## Checklist
- [x] Thêm select category vào modal
- [x] JavaScript validate category
- [x] Controller validate category_id
- [x] Controller truyền category_id vào Exam::create()
- [x] Clear config cache: `php artisan config:clear`

---
**Status:** ✅ FIXED  
**Date:** 25/10/2025
