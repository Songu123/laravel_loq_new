# 🔧 Fix: Lỗi mất focus khi nộp bài

## ❌ Vấn đề
Khi học sinh click nút "Nộp bài" hoặc mở modal xác nhận, hệ thống anti-cheating ghi nhận vi phạm "Cửa sổ mất focus", gây ra cảnh báo không mong muốn.

## ✅ Giải pháp

### 1. Thêm flag `isSubmitting`
Tạo biến global để tắt detection khi đang submit:

```javascript
let isSubmitting = false; // Flag to disable violation detection when submitting
```

### 2. Cập nhật sự kiện `blur`
Thêm kiểm tra và delay để tránh false positive:

```javascript
window.addEventListener('blur', function() {
    // Only log if not submitting and not clicking on a button/modal
    if (!isSubmitting) {
        setTimeout(function() {
            if (!document.hasFocus() && !isSubmitting) {
                logViolation('tab_switch', 'Cửa sổ mất focus', 2, {
                    timestamp: new Date().toISOString(),
                    currentQuestion: currentQuestion + 1
                });
            }
        }, 100); // Small delay to avoid false positives
    }
});
```

### 3. Cập nhật `visibilitychange`
Kiểm tra flag trước khi ghi vi phạm:

```javascript
document.addEventListener('visibilitychange', function() {
    if (document.hidden && !isSubmitting) {
        logViolation('tab_switch', 'Người dùng chuyển tab/cửa sổ', 2, {
            timestamp: new Date().toISOString(),
            currentQuestion: currentQuestion + 1
        });
    }
});
```

### 4. Cập nhật hàm `logViolation()`
Thêm check ngay đầu hàm:

```javascript
function logViolation(type, description, severity, metadata = {}) {
    // Don't log violations if submitting
    if (isSubmitting) {
        return;
    }
    
    violationCount++;
    // ... rest of code
}
```

### 5. Cập nhật hàm `submitExam()`
Set flag trước khi submit:

```javascript
function submitExam() {
    // Disable violation detection during submission
    isSubmitting = true;
    
    clearInterval(timerInterval);
    
    // Calculate time spent
    let timeSpent = Math.floor((Date.now() - startTime) / 1000);
    document.getElementById('timeSpent').value = timeSpent;
    
    // Remove beforeunload warning
    window.removeEventListener('beforeunload', function() {});
    
    document.getElementById('examForm').submit();
}
```

### 6. Cập nhật hàm `confirmExit()`
Set flag khi thoát, reset nếu cancel:

```javascript
function confirmExit() {
    isSubmitting = true; // Prevent violation logging when exiting
    if (confirm('Bạn có chắc muốn thoát? Tiến độ làm bài sẽ bị mất!')) {
        window.location.href = '{{ route('student.exams.show', $exam) }}';
    } else {
        isSubmitting = false; // Re-enable if user cancels
    }
}
```

### 7. Cập nhật `beforeunload` listener
Chỉ cảnh báo nếu chưa submit:

```javascript
window.addEventListener('beforeunload', function(e) {
    if (!isSubmitting) {
        e.preventDefault();
        e.returnValue = 'Bạn có chắc muốn rời khỏi? Tiến độ làm bài sẽ bị mất!';
    }
});
```

## 📝 Chi tiết thay đổi

### File: `resources/views/student/exams/take.blade.php`

**Line 395:** Thêm biến `isSubmitting`
```javascript
let isSubmitting = false; // Flag to disable violation detection when submitting
```

**Line 403-408:** Cập nhật beforeunload
```javascript
window.addEventListener('beforeunload', function(e) {
    if (!isSubmitting) {
        e.preventDefault();
        e.returnValue = 'Bạn có chắc muốn rời khỏi? Tiến độ làm bài sẽ bị mất!';
    }
});
```

**Line 420-429:** Cập nhật visibilitychange
```javascript
document.addEventListener('visibilitychange', function() {
    if (document.hidden && !isSubmitting) {
        logViolation('tab_switch', 'Người dùng chuyển tab/cửa sổ', 2, {
            timestamp: new Date().toISOString(),
            currentQuestion: currentQuestion + 1
        });
    }
});
```

**Line 431-441:** Cập nhật blur với delay
```javascript
window.addEventListener('blur', function() {
    if (!isSubmitting) {
        setTimeout(function() {
            if (!document.hasFocus() && !isSubmitting) {
                logViolation('tab_switch', 'Cửa sổ mất focus', 2, {
                    timestamp: new Date().toISOString(),
                    currentQuestion: currentQuestion + 1
                });
            }
        }, 100);
    }
});
```

**Line 598-602:** Thêm check trong logViolation
```javascript
function logViolation(type, description, severity, metadata = {}) {
    if (isSubmitting) {
        return;
    }
    violationCount++;
    // ...
}
```

**Line 762-764:** Cập nhật submitExam
```javascript
function submitExam() {
    isSubmitting = true;
    // ...
}
```

**Line 776-782:** Cập nhật confirmExit
```javascript
function confirmExit() {
    isSubmitting = true;
    if (confirm('...')) {
        window.location.href = '...';
    } else {
        isSubmitting = false;
    }
}
```

## 🎯 Kết quả

✅ **Không còn vi phạm khi nộp bài**
- Click "Nộp bài" → Không ghi vi phạm
- Mở modal xác nhận → Không ghi vi phạm
- Click "Thoát" → Không ghi vi phạm nếu xác nhận
- Cancel thoát → Re-enable detection

✅ **Giữ nguyên tính năng anti-cheating**
- Vẫn phát hiện tab switching bình thường
- Vẫn phát hiện window blur bình thường
- Chỉ tắt khi đang submit hoặc thoát

✅ **Tránh false positive**
- Delay 100ms cho blur event
- Double-check document.hasFocus()
- Check flag ở mọi điểm detection

## 🧪 Test

### Scenario 1: Nộp bài bình thường
1. Làm bài thi
2. Click "Nộp bài"
3. Xác nhận trong modal
4. **Kết quả:** Không có vi phạm mới

### Scenario 2: Cancel nộp bài
1. Làm bài thi
2. Click "Nộp bài"
3. Click "Hủy" trong modal
4. Tiếp tục làm bài
5. Switch tab
6. **Kết quả:** Vi phạm vẫn được ghi nhận

### Scenario 3: Thoát bài thi
1. Làm bài thi
2. Click "Thoát"
3. Xác nhận
4. **Kết quả:** Không có vi phạm mới

### Scenario 4: Cancel thoát
1. Làm bài thi
2. Click "Thoát"
3. Click "Hủy"
4. Switch tab
5. **Kết quả:** Vi phạm vẫn được ghi nhận

## 📊 So sánh trước/sau

### ❌ Trước khi fix:
```
1. Click "Nộp bài"
2. Modal hiển thị
3. Window mất focus
4. ⚠️ VI PHẠM: "Cửa sổ mất focus"
5. Toast notification hiện
6. Violation count tăng
```

### ✅ Sau khi fix:
```
1. Click "Nộp bài"
2. isSubmitting = true
3. Modal hiển thị
4. Window mất focus
5. ✅ Không ghi vi phạm (isSubmitting = true)
6. Nộp bài thành công
```

## 🔍 Technical Details

### Tại sao cần delay 100ms?
```javascript
setTimeout(function() {
    if (!document.hasFocus() && !isSubmitting) {
        logViolation(...);
    }
}, 100);
```

**Lý do:**
- Click button/modal trigger blur event ngay lập tức
- Cần thời gian để focus chuyển sang modal
- 100ms đủ để kiểm tra lại focus state
- Tránh ghi vi phạm khi click UI elements

### Tại sao check hasFocus() 2 lần?
```javascript
if (!isSubmitting) {
    setTimeout(function() {
        if (!document.hasFocus() && !isSubmitting) {
            // ^^^ Check lại sau delay
```

**Lý do:**
1. **Check đầu:** Tránh tạo timeout không cần thiết nếu đang submit
2. **Check sau:** Verify lại state sau 100ms, có thể flag đã thay đổi

### Tại sao reset flag trong confirmExit()?
```javascript
} else {
    isSubmitting = false; // Re-enable if user cancels
}
```

**Lý do:**
- User có thể cancel dialog thoát
- Cần re-enable detection để tiếp tục giám sát
- Nếu không reset, detection sẽ bị tắt vĩnh viễn

## 🚀 Deployment

Không cần thay đổi database hoặc config, chỉ cần:
1. Deploy file `take.blade.php` đã cập nhật
2. Clear cache (nếu có): `php artisan view:clear`
3. Test trên browser

## 📝 Notes

- ✅ Backward compatible - Không ảnh hưởng code cũ
- ✅ Performance - Minimal overhead (chỉ thêm 1 boolean check)
- ✅ Maintainable - Code dễ hiểu, có comment rõ ràng
- ✅ Tested - Đã test các scenario chính

---

**Fixed by:** Laravel LOQ Quiz Team  
**Date:** October 22, 2025  
**Version:** 1.0.1
