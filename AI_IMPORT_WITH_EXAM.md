# Hướng dẫn Import câu hỏi từ PDF và tạo đề thi

## 🎯 Tổng quan

Tính năng này cho phép giáo viên:
1. Upload file PDF chứa câu hỏi trắc nghiệm
2. AI tự động trích xuất câu hỏi và đáp án
3. Xem lại và chỉnh sửa câu hỏi
4. **Tạo đề thi mới** với tên và thông tin cụ thể
5. Lưu câu hỏi vào đề thi đã tạo

## 📋 Yêu cầu file PDF

### Định dạng chuẩn:
- Câu hỏi phải được đánh số: **Câu 1**, **Câu 2**, **Question 1**, v.v.
- Mỗi câu hỏi có **ít nhất 1 đáp án** (A, B, C, D, ...)
- File PDF tối đa **10MB**

### Cách đánh dấu đáp án đúng (3 cách):

#### 1. Ghi rõ đáp án đúng:
```
Câu 1: Thủ đô của Việt Nam là gì?
A. Hà Nội
B. Sài Gòn
C. Đà Nẵng
D. Huế
Đáp án: A
```

#### 2. Bôi màu/highlight đáp án đúng:
```
Câu 2: 2 + 2 = ?
A. 3
B. 4  ← (bôi màu/highlight)
C. 5
D. 6
```

#### 3. In đậm hoặc gạch chân đáp án đúng:
```
Câu 3: Python là ngôn ngữ gì?
A. **Lập trình** ← (in đậm)
B. Tự nhiên
C. Máy móc
D. Hội họa
```

## 🚀 Quy trình sử dụng

### Bước 1: Upload PDF

1. Đăng nhập với tài khoản **Teacher**
2. Vào menu **"AI Import Questions"** → **"Upload PDF"**
3. Chọn file PDF (tối đa 10MB)
4. Chọn:
   - **Danh mục** mặc định cho câu hỏi
   - **Ngôn ngữ**: Tiếng Việt hoặc Tiếng Anh
   - **Độ khó** mặc định: Dễ, Trung bình, Khó
5. Nhấn **"Upload và trích xuất"**

### Bước 2: Xem lại câu hỏi

Hệ thống hiển thị tất cả câu hỏi đã trích xuất:

- ✅ Tích chọn câu hỏi muốn lưu (mặc định chọn tất cả)
- ✏️ Chỉnh sửa nội dung câu hỏi nếu cần
- 🔄 Thay đổi danh mục hoặc độ khó
- 🎯 Chọn đáp án đúng (radio button)
- ➕ Thêm đáp án mới nếu cần
- 🗑️ Xóa câu hỏi không cần thiết

**Chức năng hàng loạt:**
- Chọn tất cả câu hỏi cùng lúc
- Đổi danh mục cho nhiều câu hỏi
- Đổi độ khó cho nhiều câu hỏi

### Bước 3: Tạo đề thi

Khi nhấn **"Lưu câu hỏi"**, một modal sẽ hiện ra yêu cầu:

1. **Tên đề thi** (*bắt buộc*)
   - VD: "Kiểm tra giữa kỳ môn Toán lớp 10"

2. **Mô tả** (tùy chọn)
   - VD: "Đề thi kiểm tra kiến thức chương 1-3"

3. **Thời gian làm bài** (*bắt buộc*)
   - Đơn vị: Phút
   - Giá trị: 5-300 phút
   - Mặc định: 60 phút

4. **Tổng điểm** (*bắt buộc*)
   - Giá trị: 1-1000 điểm
   - Mặc định: 10 điểm
   - Điểm sẽ được chia đều cho các câu hỏi

5. **Trạng thái đề thi**:
   - 📄 **Nháp**: Chưa công bố, học sinh không thấy
   - ✅ **Công bố**: Học sinh có thể thi ngay

### Bước 4: Hoàn tất

Sau khi nhấn **"Tạo đề thi và lưu câu hỏi"**:

1. Hệ thống tạo đề thi mới
2. Lưu tất cả câu hỏi đã chọn vào đề thi
3. Tính toán điểm cho từng câu (chia đều)
4. Chuyển đến trang chi tiết đề thi

## 📊 Kết quả

Sau khi lưu thành công:

- Đề thi mới được tạo với slug tự động
- Câu hỏi được sắp xếp theo thứ tự
- Metadata lưu thông tin:
  - `category_id`: Danh mục gốc
  - `difficulty`: Độ khó
  - `imported_from`: "ai_pdf"
  - `imported_at`: Thời gian import

## 🔍 Kiểm tra và chỉnh sửa

Sau khi tạo đề thi:

1. Vào **"Quản lý đề thi"** → Tìm đề vừa tạo
2. Nhấn **"Xem chi tiết"** để kiểm tra
3. **Chỉnh sửa** nếu cần:
   - Thêm/xóa câu hỏi
   - Sửa nội dung câu hỏi
   - Điều chỉnh điểm số
   - Thay đổi thời gian
4. **Gán cho lớp học** nếu muốn
5. **Công bố** khi đã sẵn sàng

## ⚙️ Cấu hình hệ thống

### Khởi động Python AI Service:

**Windows:**
```bash
# Cách 1: Dùng file bat
start-python-api.bat

# Cách 2: Chạy thủ công
cd python_api_service
python app.py
```

**API chạy tại:** `http://127.0.0.1:5001`

### Khởi động Laravel:

```bash
php artisan serve
# Server: http://127.0.0.1:8000
```

### Kiểm tra kết nối:

```bash
# Test Python API
curl http://127.0.0.1:5001/health

# Kết quả mong đợi:
# {"status":"healthy","service":"LOQ AI Question Extractor"}
```

## 🐛 Xử lý lỗi thường gặp

### 1. "Python API không kết nối được"

**Nguyên nhân:**
- Python API chưa chạy
- Port 5001 bị chiếm

**Giải pháp:**
```bash
# Kiểm tra Python API đã chạy chưa
curl http://127.0.0.1:5001/health

# Nếu chưa chạy, khởi động:
cd python_api_service
python app.py
```

### 2. "SQLSTATE[HY000]: Field 'exam_id' doesn't have a default value"

**Nguyên nhân:**
- Đã được sửa! Bây giờ hệ thống tạo Exam trước khi lưu Question

**Giải pháp:**
- Cập nhật code mới nhất
- Luôn nhập tên đề thi trước khi lưu

### 3. "Không trích xuất được câu hỏi"

**Nguyên nhân:**
- File PDF không đúng định dạng
- Câu hỏi không được đánh số

**Giải pháp:**
- Kiểm tra file PDF có định dạng:
  - `Câu 1:`, `Câu 2:`, hoặc
  - `Question 1:`, `Question 2:`, hoặc
  - `Câu hỏi 1:`, `CH 1:`
- Đảm bảo đáp án có ký tự A, B, C, D

### 4. "Không nhận diện đáp án đúng"

**Giải pháp:**
- Dùng 1 trong 3 cách đánh dấu:
  1. Ghi rõ "Đáp án: A"
  2. Bôi màu/highlight đáp án đúng
  3. In đậm `**A. Đáp án**`
- Nếu AI không nhận được, chọn thủ công ở bước Review

## 📝 Lưu ý quan trọng

1. **Luôn kiểm tra lại** câu hỏi sau khi AI trích xuất
2. **Không thể sửa đề thi** sau khi học sinh đã thi
3. **Backup file PDF gốc** để import lại nếu cần
4. **Giới hạn file**: 10MB, định dạng PDF
5. **Ít nhất 1 đáp án**: Mỗi câu hỏi phải có tối thiểu 1 đáp án
6. **Tên đề thi unique**: Slug tự động đảm bảo không trùng lặp

## 🎓 Ví dụ thực tế

### Tạo đề thi "Kiểm tra Toán 10"

1. Upload PDF có 20 câu hỏi
2. AI trích xuất → Chọn 15 câu tốt nhất
3. Tạo đề thi:
   - Tên: "Kiểm tra giữa kỳ Toán 10 - HK1"
   - Thời gian: 45 phút
   - Tổng điểm: 10 điểm
   - Trạng thái: Nháp
4. Lưu → Mỗi câu được 10/15 = 0.67 điểm
5. Kiểm tra và gán cho lớp 10A, 10B
6. Công bố khi sẵn sàng

## 🔗 Links liên quan

- **Upload PDF**: `/teacher/ai-import/upload`
- **Review Questions**: `/teacher/ai-import/review`
- **Quản lý đề thi**: `/teacher/exams`
- **API Health Check**: `http://127.0.0.1:5001/health`
- **API Documentation**: `http://127.0.0.1:5001/docs`

## 📞 Hỗ trợ

Nếu gặp vấn đề:
1. Kiểm tra log: `storage/logs/laravel.log`
2. Kiểm tra Python console
3. Xem chi tiết lỗi trong modal alert

---

**Phiên bản:** 2.0 (Có tạo đề thi)  
**Cập nhật:** 25/10/2025  
**AI Engine:** Python FastAPI + PyMuPDF + Tesseract OCR
