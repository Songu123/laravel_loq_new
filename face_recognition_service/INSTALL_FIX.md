# 🔧 Quick Fix - Install Instructions

## Vấn đề hiện tại:
- ❌ Python 3.13.5 không tương thích với MediaPipe
- ❌ MediaPipe là core component cho 3D face recognition
- ❌ Conda environment chưa được activate đúng cách

## ✅ Giải pháp NHANH (Chọn 1 trong 3):

---

### Option 1: Dùng Conda Python 3.11 (RECOMMENDED - ĐÃ TẠO)

**Bước 1: ĐÓNG và MỞ LẠI PowerShell**
- Cần để conda init có hiệu lực

**Bước 2: Activate environment**
```powershell
# Trong PowerShell MỚI:
cd D:\PHP_Laravel\laravel_loq_quizz\face_recognition_service
conda activate face_recognition
```

**Bước 3: Verify Python version**
```powershell
python --version
# PHẢI hiện: Python 3.11.14
```

**Bước 4: Install dependencies**
```powershell
pip install -r requirements-minimal.txt
```

**Bước 5: Run**
```powershell
python app.py
```

---

### Option 2: Dùng Anaconda Navigator (GUI - DỄ NHẤT)

**Bước 1: Mở Anaconda Navigator**

**Bước 2: Chọn Environment "face_recognition"** (đã được tạo)

**Bước 3: Click "Open Terminal"** trong environment đó

**Bước 4: Trong terminal:**
```bash
cd D:\PHP_Laravel\laravel_loq_quizz\face_recognition_service
pip install -r requirements-minimal.txt
python app.py
```

---

### Option 3: Tạm thời SKIP 3D Face Recognition

**Nếu muốn test NHANH mà không cần Python 3.11:**

**Bước 1: Sửa code để KHÔNG dùng MediaPipe**

File: `models/face_detector.py`
```python
# Comment out MediaPipe import
# import mediapipe as mp

# Dùng OpenCV Haar Cascade thay thế (simple 2D detection)
import cv2

class FaceDetector:
    def __init__(self):
        # Load Haar Cascade
        self.face_cascade = cv2.CascadeClassifier(
            cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'
        )
        logger.info("✅ OpenCV Haar Cascade loaded (2D detection only)")
    
    def detect(self, image):
        gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
        faces = self.face_cascade.detectMultiScale(gray, 1.1, 4)
        
        result = []
        for (x, y, w, h) in faces:
            result.append({
                'bbox': {'x1': x, 'y1': y, 'x2': x+w, 'y2': y+h},
                'landmarks': [],  # No landmarks in Haar Cascade
                'confidence': 0.8
            })
        return result
```

**Bước 2: Install simple requirements**
```powershell
pip install fastapi uvicorn opencv-python numpy pillow python-dotenv loguru pymysql requests
```

**Bước 3: Run**
```powershell
python app.py
```

**⚠️ Nhược điểm:**
- Không có 3D landmarks
- Liveness detection kém hơn
- Accuracy thấp hơn
- Chỉ nên dùng để TEST

---

## 🎯 KHUYẾN NGHỊ

**Làm theo Option 1 hoặc Option 2** để có đầy đủ tính năng 3D Face Recognition!

### Checklist cho Option 1 (Conda):
- [ ] Đóng PowerShell hiện tại
- [ ] Mở PowerShell MỚI
- [ ] `cd D:\PHP_Laravel\laravel_loq_quizz\face_recognition_service`
- [ ] `conda activate face_recognition`
- [ ] `python --version` → Phải là 3.11.x
- [ ] `pip install -r requirements-minimal.txt`
- [ ] `copy .env.example .env`
- [ ] Edit `.env` với database credentials
- [ ] `python app.py`
- [ ] Test: `curl http://127.0.0.1:8001/health`

---

## 📞 Nếu vẫn lỗi:

**Show error message và tôi sẽ giúp!**

Các lệnh debug:
```powershell
# Check conda
conda --version

# List environments
conda env list

# Check which Python
where python

# Check pip
pip --version
```

---

**Current Status:** ⏳ Chờ activate conda environment Python 3.11
**Next Step:** Đóng PowerShell, mở lại, chạy `conda activate face_recognition`
