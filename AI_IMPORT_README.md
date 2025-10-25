# 🤖 Hệ thống tạo đề thi tự động từ PDF bằng AI

## 📋 Tổng quan

Hệ thống cho phép giáo viên upload file PDF trắc nghiệm và tự động trích xuất câu hỏi bằng AI. Hệ thống sử dụng Python FastAPI + OCR + NLP để xử lý PDF và Laravel để quản lý.

## ⚙️ Yêu cầu hệ thống

### 1. Python (3.8+)
- Download từ: https://www.python.org/downloads/
- Đảm bảo chọn "Add Python to PATH" khi cài đặt

### 2. Tesseract OCR (cho PDF scan)
- **Windows**: 
  - Download: https://github.com/UB-Mannheim/tesseract/wiki
  - Cài đặt vào: `C:\Program Files\Tesseract-OCR\`
  - Thêm vào PATH hoặc cấu hình trong `ocr_handler.py`

- **Linux/Mac**:
  ```bash
  # Ubuntu/Debian
  sudo apt-get install tesseract-ocr tesseract-ocr-vie
  
  # Mac
  brew install tesseract tesseract-lang
  ```

### 3. Poppler (cho pdf2image)
- **Windows**: 
  - Download: https://github.com/oschwartz10612/poppler-windows/releases
  - Giải nén và thêm `bin` folder vào PATH

- **Linux**: 
  ```bash
  sudo apt-get install poppler-utils
  ```

- **Mac**: 
  ```bash
  brew install poppler
  ```

## 🚀 Cài đặt

### Bước 1: Cài đặt Python dependencies

```bash
cd python_api_service
pip install -r requirements.txt
```

**Lưu ý**: Nếu bạn KHÔNG cần PhoBERT (AI NLP phức tạp), có thể bỏ qua lỗi cài đặt `transformers` và `torch`. Hệ thống vẫn hoạt động với OCR cơ bản.

### Bước 2: Cấu hình Laravel

File `.env` đã được cấu hình sẵn:
```env
PYTHON_API_URL=http://127.0.0.1:8000
```

Nếu muốn thay đổi port, sửa cả 2 nơi:
- `.env` → PYTHON_API_URL
- `python_api_service/app.py` → `uvicorn.run(app, host="0.0.0.0", port=8000)`

## 📖 Hướng dẫn sử dụng

### 1. Khởi động Python API Service

**Cách 1: Dùng script (Khuyến nghị)**
```bash
# Chạy file start-python-api.bat
start-python-api.bat
```

**Cách 2: Thủ công**
```bash
cd python_api_service
python app.py
```

Sau khi khởi động, bạn sẽ thấy:
```
INFO:     Uvicorn running on http://127.0.0.1:8000
```

**Kiểm tra API hoạt động:**
- Mở browser: http://127.0.0.1:8000
- Hoặc: http://127.0.0.1:8000/docs (Swagger UI)

### 2. Khởi động Laravel

Terminal khác:
```bash
php artisan serve
```

### 3. Sử dụng chức năng

1. **Đăng nhập Teacher**
2. **Vào menu**: "Tạo đề từ PDF (AI)" (có icon robot 🤖)
3. **Upload PDF**:
   - Chọn file PDF trắc nghiệm
   - Chọn danh mục môn học
   - Chọn ngôn ngữ (Tiếng Việt/English)
   - Chọn độ khó mặc định
   - Click "Trích xuất câu hỏi"

4. **Xem lại và chỉnh sửa**:
   - Hệ thống sẽ hiển thị tất cả câu hỏi đã trích xuất
   - Bạn có thể:
     - ✅ Chọn/bỏ chọn câu hỏi
     - ✏️ Chỉnh sửa nội dung câu hỏi
     - 🔄 Thay đổi danh mục/độ khó (từng câu hoặc hàng loạt)
     - ⭐ Đánh dấu đáp án đúng
     - 🗑️ Xóa câu hỏi không cần

5. **Lưu câu hỏi**:
   - Click "Lưu câu hỏi"
   - Các câu hỏi được chọn sẽ lưu vào database

## 📄 Định dạng PDF hợp lệ

### Ví dụ tốt:
```
Câu 1: Python là ngôn ngữ lập trình gì?
A. Ngôn ngữ biên dịch
B. Ngôn ngữ thông dịch
C. Ngôn ngữ máy
D. Ngôn ngữ Assembly
Đáp án: B

Câu 2: Laravel là framework của ngôn ngữ nào?
A. Python
B. JavaScript  
C. PHP
D. Ruby
Đáp án: C
```

### Yêu cầu:
- ✅ Đánh số câu rõ ràng: "Câu 1:", "Câu 2:", hoặc "Question 1:"
- ✅ Đáp án có ký hiệu A, B, C, D (hoặc A., B., C., D.)
- ✅ Có đáp án đúng (Đáp án:, Answer:, ĐA:, DA:)
- ✅ Mỗi câu hỏi có ít nhất 2 đáp án
- ✅ Font chữ rõ ràng (không quá mờ nếu là scan)

### Không hỗ trợ:
- ❌ Câu hỏi tự luận
- ❌ Câu hỏi điền khuyết không có đáp án
- ❌ Hình ảnh/biểu đồ trong câu hỏi
- ❌ Bảng biểu phức tạp

## 🔧 Xử lý sự cố

### 1. "Python API không kết nối được"

**Kiểm tra:**
```bash
# Kiểm tra Python API đang chạy
curl http://127.0.0.1:8000

# Hoặc mở browser
http://127.0.0.1:8000
```

**Giải pháp:**
- Chạy lại `start-python-api.bat`
- Kiểm tra port 8000 chưa bị chiếm bởi app khác
- Kiểm tra firewall

### 2. "OCR không hoạt động"

**Nguyên nhân**: Thiếu Tesseract hoặc Poppler

**Giải pháp:**
```bash
# Kiểm tra Tesseract
tesseract --version

# Kiểm tra Poppler (Windows)
pdftoppm -v
```

Nếu lỗi → Cài đặt lại và thêm vào PATH

### 3. "Không trích xuất được câu hỏi"

**Nguyên nhân**: Định dạng PDF không chuẩn

**Giải pháp:**
- Kiểm tra định dạng PDF theo mẫu trên
- Thử PDF khác để test
- Kiểm tra log trong terminal Python API

### 4. "Đáp án không đúng"

**Nguyên nhân**: AI nhận diện sai hoặc PDF không rõ

**Giải pháp:**
- Trong trang "Xem lại", đánh dấu lại đáp án đúng
- Hệ thống cho phép chỉnh sửa 100% trước khi lưu

## 🛠️ Tùy chỉnh

### Thay đổi port Python API

1. Sửa `python_api_service/app.py`:
```python
uvicorn.run(app, host="0.0.0.0", port=9000)  # Đổi port
```

2. Sửa `.env`:
```env
PYTHON_API_URL=http://127.0.0.1:9000
```

### Tắt PhoBERT (tiết kiệm RAM)

Trong `python_api_service/models/nlp_processor.py`:
```python
def __init__(self):
    # Comment out model loading
    # self.tokenizer = AutoTokenizer.from_pretrained("vinai/phobert-base")
    pass
```

### Tăng kích thước file upload

Trong `app/Http/Controllers/Teacher/AIQuestionImportController.php`:
```php
'pdf_file' => 'required|file|mimes:pdf|max:20480', // 20MB
```

## 📊 Hiệu suất

| Loại PDF | Thời gian xử lý | Độ chính xác |
|----------|-----------------|--------------|
| Digital PDF (text) | 5-10s | 90-95% |
| Scanned PDF (OCR) | 30-60s | 70-85% |
| PDF 10+ trang | 1-2 phút | Tùy chất lượng |

**Lưu ý**: 
- PDF digital (có text) xử lý nhanh và chính xác hơn
- PDF scan cần OCR nên lâu hơn và có thể sai
- Luôn kiểm tra lại trước khi lưu!

## 🔐 Bảo mật

- ✅ Chỉ teacher có quyền truy cập
- ✅ File PDF được xóa sau khi xử lý
- ✅ Timeout 2 phút để tránh overload
- ✅ Validate file type và size
- ✅ Session-based review (không lưu vào DB ngay)

## 🎯 Roadmap

- [ ] Hỗ trợ hình ảnh trong câu hỏi
- [ ] Export kết quả về Word/PDF
- [ ] Batch upload nhiều PDF
- [ ] History import với rollback
- [ ] Auto-categorize bằng AI
- [ ] Support English better

## 📞 Hỗ trợ

Nếu gặp vấn đề:
1. Kiểm tra log trong terminal Python API
2. Kiểm tra log Laravel: `storage/logs/laravel.log`
3. Đảm bảo Python dependencies đã cài đủ
4. Kiểm tra định dạng PDF theo mẫu

## 📝 License

MIT License - Free to use and modify
