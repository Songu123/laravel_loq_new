# 🚀 Quick Start - AI Import từ PDF

## Bước 1: Cài đặt Python Dependencies (Chỉ lần đầu)

```bash
cd python_api_service
pip install -r requirements.txt
```

## Bước 2: Khởi động Python API

**Windows:**
```bash
start-python-api.bat
```

**Linux/Mac:**
```bash
cd python_api_service
python app.py
```

Đợi xuất hiện:
```
INFO: Uvicorn running on http://127.0.0.1:8000
```

## Bước 3: Khởi động Laravel (Terminal mới)

```bash
php artisan serve
```

## Bước 4: Sử dụng

1. Đăng nhập Teacher
2. Menu sidebar → **"Tạo đề từ PDF (AI)"** (icon robot 🤖)
3. Upload PDF trắc nghiệm
4. Xem lại và chỉnh sửa
5. Lưu câu hỏi

## ✅ Test nhanh

**Kiểm tra API hoạt động:**
```bash
curl http://127.0.0.1:8000
```

Hoặc mở browser: http://127.0.0.1:8000/docs

## ⚠️ Lưu ý quan trọng

1. **PHẢI chạy Python API trước** khi upload PDF
2. File PDF phải có định dạng:
   ```
   Câu 1: Nội dung câu hỏi?
   A. Đáp án A
   B. Đáp án B
   C. Đáp án C
   D. Đáp án D
   Đáp án: B
   ```
3. Kích thước tối đa: **10MB**
4. **Luôn kiểm tra lại** câu hỏi trước khi lưu

## 🔧 Troubleshooting

| Vấn đề | Giải pháp |
|--------|-----------|
| "Python API không kết nối" | Chạy lại `start-python-api.bat` |
| "Module not found" | `pip install -r requirements.txt` |
| "Không trích xuất được câu hỏi" | Kiểm tra định dạng PDF |
| Port 8000 bị chiếm | Đổi port trong `app.py` và `.env` |

## 📚 Đọc thêm

Chi tiết đầy đủ: [AI_IMPORT_README.md](AI_IMPORT_README.md)
