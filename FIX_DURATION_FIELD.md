# 🔧 Fix: "The duration minutes field is required" Error

## ❌ Vấn đề:
Khi **chỉnh sửa đề thi** (edit exam), bạn gặp lỗi:
```
The duration minutes field is required.
```

## 🔍 Nguyên nhân:

### Không nhất quán về tên field:

| File | Field Name | Status |
|------|-----------|---------|
| **Database** | `duration_minutes` | ✅ Correct |
| **Model Exam** | `duration_minutes` | ✅ Correct |
| **create.blade.php** | `duration_minutes` | ✅ Correct |
| **edit.blade.php** | ❌ `duration` | ⚠️ SAI! |
| **UpdateExamRequest** | `duration_minutes` | ✅ Correct |
| **StoreExamRequest** | `duration_minutes` | ✅ Correct |

### Chi tiết lỗi:

**edit.blade.php** (dòng ~102):
```html
<!-- SAI -->
<input type="number" 
       name="duration"           <!-- ❌ Tên field sai -->
       value="{{ old('duration', $exam->duration) }}">
```

**UpdateExamRequest.php**:
```php
public function rules(): array
{
    return [
        'duration_minutes' => 'nullable|integer|min:1|max:600',  // ✅ Validate đúng
        // ...
    ];
}
```

➡️ Form gửi `duration`, nhưng validation đợi `duration_minutes` → **Lỗi!**

---

## ✅ Giải pháp:

### Sửa `edit.blade.php` - Đổi tên field:

```html
<!-- ĐÚNG -->
<input type="number" 
       class="form-control @error('duration_minutes') is-invalid @enderror" 
       id="duration_minutes" 
       name="duration_minutes"     <!-- ✅ Tên field đúng -->
       value="{{ old('duration_minutes', $exam->duration_minutes) }}" 
       min="1">
@error('duration_minutes')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
```

---

## 📝 Các thay đổi:

### 1. **edit.blade.php** - Line ~102
```diff
- <label for="duration" class="form-label">Thời gian làm bài (phút)</label>
+ <label for="duration_minutes" class="form-label">Thời gian làm bài (phút)</label>
  <input type="number" 
-        class="form-control @error('duration') is-invalid @enderror" 
-        id="duration" 
-        name="duration" 
-        value="{{ old('duration', $exam->duration) }}" 
+        class="form-control @error('duration_minutes') is-invalid @enderror" 
+        id="duration_minutes" 
+        name="duration_minutes" 
+        value="{{ old('duration_minutes', $exam->duration_minutes) }}" 
         min="1">
- @error('duration')
+ @error('duration_minutes')
      <div class="invalid-feedback">{{ $message }}</div>
  @enderror
```

---

## ✅ Kết quả:

### Trước khi fix:
```
Form data:
  duration: "60"           ❌ Field sai tên
  
Validation:
  duration_minutes: required  ✅ Validation đúng
  
➡️ FAIL: "The duration minutes field is required"
```

### Sau khi fix:
```
Form data:
  duration_minutes: "60"   ✅ Field đúng tên
  
Validation:
  duration_minutes: required  ✅ Validation đúng
  
➡️ SUCCESS: ✅ Validation pass!
```

---

## 🧪 Test:

1. **Vào trang edit exam:**
   ```
   http://localhost:8000/teacher/exams/{id}/edit
   ```

2. **Chỉnh sửa đề thi:**
   - Thêm/xóa câu hỏi
   - Sửa thông tin cơ bản
   - **Không cần điền duration** (field nullable)
   
3. **Submit form** → ✅ Không còn lỗi!

---

## 📋 Checklist toàn bộ hệ thống:

### Database Schema:
- ✅ `duration_minutes` INTEGER DEFAULT 60

### Model:
```php
// app/Models/Exam.php
protected $fillable = [
    'duration_minutes',  // ✅
    // ...
];
```

### Validation:
```php
// StoreExamRequest.php & UpdateExamRequest.php
'duration_minutes' => 'nullable|integer|min:1|max:600',  // ✅
```

### Views:
```html
<!-- create.blade.php -->
<input name="duration_minutes" ...>  ✅

<!-- edit.blade.php -->
<input name="duration_minutes" ...>  ✅ (vừa sửa)
```

### Controller:
```php
// ExamService.php
public function createExam(array $data)
{
    $exam = Exam::create([
        'duration_minutes' => $data['duration_minutes'] ?? 60,  // ✅
        // ...
    ]);
}
```

---

## 🎯 Bài học:

### Quy tắc đặt tên field:
1. **Database column name** = Form field name = Validation rule name
2. Luôn kiểm tra tất cả các file liên quan:
   - Migration
   - Model
   - Controller/Service
   - Request Validation
   - View (create/edit)

### Convention:
- ✅ `duration_minutes` (snake_case - Laravel standard)
- ❌ `duration` (quá ngắn, dễ nhầm)
- ❌ `durationMinutes` (camelCase - không dùng cho database/form)

---

**Status: ✅ FIXED**

*Ngày fix: 15/10/2025*
