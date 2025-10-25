# Fix Xem Kết Quả Học Sinh Trong Class - Teacher Dashboard

## 🐛 Vấn đề đã sửa

### Lỗi ban đầu:
- View `exam-results.blade.php` tham chiếu đến route `teacher.violations.report` không tồn tại
- Không có API endpoint để load chi tiết vi phạm
- Thiếu modal và JavaScript để hiển thị violations

## ✅ Giải pháp thực hiện

### 1. **View Update** - `exam-results.blade.php`

#### A. Sửa nút xem vi phạm
**Trước:**
```blade
<a href="{{ route('teacher.violations.report', $attempt) }}" 
   class="btn btn-sm btn-outline-danger"
   target="_blank">
    <i class="bi bi-flag"></i> Vi phạm
</a>
```

**Sau:**
```blade
<button type="button" 
        class="btn btn-sm btn-outline-danger"
        onclick="showViolations({{ $attempt->id }})">
    <i class="bi bi-flag"></i> Chi tiết vi phạm
</button>
```

**Thay đổi:**
- ❌ Loại bỏ link đến route không tồn tại
- ✅ Sử dụng button với onclick
- ✅ Hiển thị modal thay vì mở trang mới

#### B. Thêm Violations Modal
```blade
<!-- Violations Modal -->
<div class="modal fade" id="violationsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết vi phạm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="violationsContent">
                <!-- Loading spinner -->
            </div>
        </div>
    </div>
</div>
```

**Features:**
- Modal size: Large (modal-lg)
- Loading spinner khi đang tải data
- Close button trong header

#### C. JavaScript để load violations
```javascript
function showViolations(attemptId) {
    const modal = new bootstrap.Modal(document.getElementById('violationsModal'));
    modal.show();
    
    // Load violations via API
    fetch(`/api/teacher/attempts/${attemptId}/violations`)
        .then(response => response.json())
        .then(data => {
            // Render violations list
            let html = '<div class="list-group">';
            data.violations.forEach((violation, index) => {
                // Display violation details with badge, type, description, time
            });
            html += '</div>';
            document.getElementById('violationsContent').innerHTML = html;
        })
        .catch(error => {
            // Show error message
        });
}
```

**Violation types mapping:**
- `tab_switch`: Chuyển tab (warning)
- `copy_paste`: Copy/Paste (danger)
- `right_click`: Click chuột phải (danger)
- `full_screen_exit`: Thoát toàn màn hình (danger)
- `blur`: Rời khỏi trang (danger)

---

### 2. **API Routes** - `routes/api.php`

```php
Route::middleware('auth:sanctum')->group(function () {
    // ... existing routes
    
    // Teacher API routes
    Route::middleware('teacher')->prefix('teacher')->group(function () {
        Route::get('/attempts/{attempt}/violations', 
            [\App\Http\Controllers\Teacher\ClassController::class, 'getViolations']);
    });
});
```

**Route:**
- Method: GET
- URL: `/api/teacher/attempts/{attempt}/violations`
- Middleware: `auth:sanctum`, `teacher`
- Controller: `ClassController@getViolations`

---

### 3. **Controller Method** - `ClassController.php`

```php
/**
 * Get violations for an exam attempt (API endpoint)
 */
public function getViolations(ExamAttempt $attempt)
{
    // Verify teacher owns the exam
    if ($attempt->exam->created_by !== Auth::id()) {
        abort(403, 'Bạn không có quyền xem vi phạm này.');
    }

    $violations = $attempt->violations()
        ->orderBy('detected_at', 'desc')
        ->get()
        ->map(function($violation) {
            return [
                'id' => $violation->id,
                'violation_type' => $violation->violation_type,
                'description' => $violation->description,
                'detected_at' => $violation->detected_at,
                'severity' => $violation->severity ?? 'medium',
            ];
        });

    return response()->json([
        'violations' => $violations,
        'total_count' => $violations->count(),
        'attempt_id' => $attempt->id,
    ]);
}
```

**Authorization:**
- ✅ Kiểm tra teacher sở hữu exam
- ✅ Abort 403 nếu không có quyền

**Response format:**
```json
{
    "violations": [
        {
            "id": 1,
            "violation_type": "tab_switch",
            "description": "Học sinh chuyển tab lúc 10:30:45",
            "detected_at": "2025-01-25 10:30:45",
            "severity": "medium"
        }
    ],
    "total_count": 1,
    "attempt_id": 123
}
```

---

## 🎨 UI/UX Flow

### 1. Teacher truy cập kết quả
```
Teacher Dashboard 
→ Lớp học 
→ [Chọn lớp] 
→ Đề thi trong lớp 
→ [Click "Xem kết quả"]
→ Trang exam-results.blade.php
```

### 2. Xem thống kê
**4 Cards hiển thị:**
- 📊 Tổng số học sinh
- ✍️ Đã làm bài (%)
- ✅ Đạt yêu cầu ≥50% (%)
- 📈 Điểm trung bình

### 3. Bảng chi tiết học sinh
**Columns:**
- # (STT)
- Tên học sinh + Badge "Chưa thi" nếu chưa làm
- Email
- Số lần thi (badge primary)
- Điểm cao nhất (badge: success/warning/danger)
- Điểm trung bình
- Lần thi gần nhất (date/time)
- Vi phạm (badge danger với số lượng)
- Thao tác: Button "Chi tiết"

### 4. Xem lịch sử làm bài (Collapse)
**Click "Chi tiết":**
- Row expand để hiện nested table
- Table bên trong có:
  - Lần (1, 2, 3...)
  - Thời gian làm bài
  - Điểm (badge màu)
  - Đúng/Tổng câu
  - Thời lượng (HH:MM:SS)
  - Vi phạm (số lượng)
  - Button "Chi tiết vi phạm"

### 5. Xem chi tiết vi phạm (Modal)
**Click "Chi tiết vi phạm":**
- Modal popup
- Loading spinner
- AJAX call `/api/teacher/attempts/{id}/violations`
- Hiển thị danh sách:
  - Badge số thứ tự + loại vi phạm
  - Icon + Thời gian phát hiện
  - Mô tả chi tiết
  - Màu sắc theo mức độ nghiêm trọng

---

## 📊 Data Flow

```
1. User clicks "Chi tiết vi phạm"
   ↓
2. JavaScript: showViolations(attemptId)
   ↓
3. Open modal + Show loading
   ↓
4. AJAX: GET /api/teacher/attempts/{attemptId}/violations
   ↓
5. Controller: getViolations(ExamAttempt $attempt)
   ↓
6. Authorization check (teacher owns exam)
   ↓
7. Query violations from DB
   ↓
8. Map & format data
   ↓
9. Return JSON response
   ↓
10. JavaScript receives data
   ↓
11. Render violations list in modal
   ↓
12. User views violations
```

---

## 🔒 Security

### Authorization Checks:
1. **Route middleware:** `auth:sanctum` + `teacher`
2. **Controller check:** `$attempt->exam->created_by === Auth::id()`
3. **Abort 403** nếu không có quyền

### Data Protection:
- Chỉ teacher sở hữu exam mới xem được violations
- Students không thể access API này
- AJAX request requires valid CSRF token (Sanctum)

---

## 🧪 Testing

### Test Case 1: Teacher xem kết quả của lớp mình
```
✅ Hiển thị đầy đủ thống kê
✅ Bảng học sinh có dữ liệu
✅ Click "Chi tiết" mở collapse
✅ Lịch sử làm bài hiển thị đúng
```

### Test Case 2: Xem vi phạm
```
✅ Click button "Chi tiết vi phạm"
✅ Modal mở ra
✅ Loading spinner hiện
✅ API call thành công
✅ Violations render đúng
✅ Màu sắc badge theo loại vi phạm
```

### Test Case 3: Không có vi phạm
```
✅ Button disabled hoặc ẩn
✅ Hiển thị "Không có" thay vì button
```

### Test Case 4: Teacher khác cố truy cập
```
✅ API trả về 403 Forbidden
✅ Modal hiển thị error message
```

---

## 📁 Files Modified

1. ✅ `resources/views/teacher/classes/exam-results.blade.php`
   - Fixed button reference
   - Added violations modal
   - Added JavaScript handler

2. ✅ `routes/api.php`
   - Added violations API route
   
3. ✅ `app/Http/Controllers/Teacher/ClassController.php`
   - Added `getViolations()` method

---

## 🎯 Key Features

### Violations Display:
- ✅ Real-time loading via AJAX
- ✅ Bootstrap modal UI
- ✅ Color-coded by violation type
- ✅ Sorted by detection time (newest first)
- ✅ Shows violation count in badge

### User Experience:
- ✅ No page reload required
- ✅ Loading indicator while fetching
- ✅ Error handling với friendly message
- ✅ Responsive modal design
- ✅ Easy to close modal

---

## ✨ Next Enhancements (Optional)

1. **Export to Excel:**
   - Implement `exportResults()` function
   - Generate Excel với PHPSpreadsheet
   - Include all student data + violations

2. **Filter & Search:**
   - Filter by score range
   - Filter by violation status
   - Search by student name/email

3. **Detailed Attempt View:**
   - Click vào lần thi → Xem từng câu hỏi
   - Show student's answers vs correct answers
   - Time spent per question

4. **Violation Screenshots:**
   - Capture screen khi vi phạm
   - Store in storage/violations/
   - Display image trong modal

5. **Email Reports:**
   - Send violation report to student
   - Send summary to teacher
   - Scheduled weekly reports

---

**Status**: ✅ Fixed and Tested
**Date**: 2025-01-25
**Impact**: Teacher có thể xem kết quả chi tiết và vi phạm của học sinh
