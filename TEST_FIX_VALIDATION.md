# 🧪 Test Tạo Đề Thi - Fix Validation

## ✅ Các lỗi đã được sửa:

### 1. **is_required field must be true or false**
- ✅ Thêm hidden input với value="0"
- ✅ Checkbox có value="1"
- ✅ PrepareForValidation convert sang boolean

### 2. **answer_text field is required**
- ✅ Thêm attribute `required` cho input answer_text
- ✅ Validation sẽ kiểm tra khi submit

### 3. **is_correct field must be true or false**
- ✅ Thêm hidden input với value="0"
- ✅ Checkbox có value="1"
- ✅ PrepareForValidation convert sang boolean

## 🔧 Các thay đổi:

### 1. **Template HTML** (create.blade.php)
```html
<!-- Checkbox is_required -->
<input type="hidden" name="questions[][is_required]" value="0">
<input type="checkbox" name="questions[][is_required]" value="1" checked>

<!-- Answer template -->
<input type="hidden" name="questions[][answers][][is_correct]" value="0">
<input type="checkbox" name="questions[][answers][][is_correct]" value="1">
<input type="text" name="questions[][answers][][answer_text]" required>
```

### 2. **JavaScript** (create.blade.php)
- ✅ Fixed naming: `questions[0][answers][0][answer_text]`
- ✅ Added `reindexAnswers()` function
- ✅ Updated `addAnswer()` với proper indexing
- ✅ Updated `addTrueFalseAnswers()` với specific indices

### 3. **Request Validation** (StoreExamRequest.php, UpdateExamRequest.php)
```php
protected function prepareForValidation(): void
{
    // Convert string "1"/"0" to boolean true/false
    $questions = $this->input('questions', []);
    
    foreach ($questions as $qIndex => $question) {
        $questions[$qIndex]['is_required'] = 
            isset($question['is_required']) && $question['is_required'] == '1';
        
        if (isset($question['answers'])) {
            foreach ($question['answers'] as $aIndex => $answer) {
                $questions[$qIndex]['answers'][$aIndex]['is_correct'] = 
                    isset($answer['is_correct']) && $answer['is_correct'] == '1';
            }
        }
    }
    
    $this->merge(['questions' => $questions]);
}
```

## 🧪 Cách test:

### Bước 1: Clear cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Bước 2: Đăng nhập Teacher
```
URL: http://localhost:8000/login/teacher
Email: teacher1@example.com
Password: password
```

### Bước 3: Vào trang tạo đề thi
```
http://localhost:8000/teacher/exams/create
```

### Bước 4: Tạo đề thi test

#### Test Case 1: Multiple Choice
1. Nhập tên: "Test Multiple Choice"
2. Chọn category
3. Thêm câu hỏi:
   - Loại: Multiple Choice
   - Câu hỏi: "2 + 2 = ?"
   - Thêm 4 đáp án:
     - "3" (không check)
     - "4" (check ✓)
     - "5" (không check)
     - "6" (không check)
   - Điểm: 1
   - Check "Bắt buộc trả lời"
4. Submit

#### Test Case 2: True/False
1. Nhập tên: "Test True False"
2. Chọn category
3. Thêm câu hỏi:
   - Loại: True/False
   - Câu hỏi: "Trái đất hình tròn?"
   - Đáp án tự động: Đúng (check ✓), Sai
   - Điểm: 1
4. Submit

#### Test Case 3: Mixed Questions
1. Nhập tên: "Test Mixed"
2. Thêm Multiple Choice
3. Thêm True/False
4. Thêm Short Answer
5. Thêm Essay
6. Submit

## 🔍 Kiểm tra data được gửi:

### Mở Browser DevTools (F12)
1. Vào tab **Network**
2. Submit form
3. Click vào request **exams**
4. Xem tab **Payload**

### Format mong đợi:
```
questions[0][question_text]: "Câu hỏi 1"
questions[0][question_type]: "multiple_choice"
questions[0][marks]: "1"
questions[0][is_required]: "1"
questions[0][answers][0][answer_text]: "Đáp án 1"
questions[0][answers][0][is_correct]: "0"
questions[0][answers][1][answer_text]: "Đáp án 2"
questions[0][answers][1][is_correct]: "1"
...
```

## ✅ Kết quả mong đợi:

### Success:
- ✅ Form submit thành công
- ✅ Redirect đến exam show page
- ✅ Message: "Đề thi đã được tạo thành công!"
- ✅ Câu hỏi và đáp án hiển thị đúng

### Errors (nếu có):
- Check `storage/logs/laravel.log`
- Check DevTools Console tab
- Check Network tab response

## 🐛 Troubleshooting:

### Lỗi validation vẫn còn:
1. Check xem hidden input có trong HTML không
2. Check JavaScript console có lỗi không
3. Verify field names trong Network tab
4. Check prepareForValidation() có chạy không

### Checkbox không hoạt động:
1. Inspect element để xem value
2. Check JavaScript reindexAnswers()
3. Verify name attributes

### Dữ liệu không lưu:
1. Check database log
2. Verify ExamService::createExam()
3. Check foreign key constraints

## 📝 Notes:

- Hidden input phải đứng **TRƯỚC** checkbox
- Checkbox value="1", hidden value="0"
- JavaScript phải reindex khi add/remove
- prepareForValidation() convert string to boolean
- Validation rules đã update để accept boolean

---

**Chúc bạn test thành công! 🎉**

*Nếu vẫn gặp lỗi, kiểm tra:*
1. Browser Console (F12)
2. Network tab payload
3. storage/logs/laravel.log