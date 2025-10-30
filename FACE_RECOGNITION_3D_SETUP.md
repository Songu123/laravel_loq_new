# 🔐 3D Face Recognition System cho Admin Login

## 📋 Tổng quan

Hệ thống nhận diện khuôn mặt 3D để đăng nhập Admin với độ chính xác cao và khả năng chống giả mạo (anti-spoofing).

## 🏗️ Kiến trúc hệ thống

```
┌──────────────────┐         ┌──────────────────┐         ┌─────────────────────┐
│  Admin Browser   │ ──────► │   Laravel API    │ ◄─────► │  Face Recognition   │
│  (Webcam)        │  HTTPS  │   (Backend)      │  HTTP   │  Python Service     │
└──────────────────┘         └──────────────────┘         └─────────────────────┘
        │                             │                            │
        │                             │                            │
        ▼                             ▼                            ▼
  Capture Video              Store Face Data              3D Analysis
  Send to Server             Verify User                  Deep Learning
```

## 🛠️ Stack công nghệ

### Python Service:
- **FastAPI**: Web framework (async, nhanh)
- **OpenCV**: Xử lý video/ảnh
- **MediaPipe**: Face mesh 3D (468 landmarks)
- **FaceNet**: Face embedding (512-dim vector)
- **TensorFlow/PyTorch**: Deep learning
- **Dlib** (optional): Face detection fallback
- **NumPy**: Matrix calculations

### Laravel Integration:
- **Guzzle HTTP**: Call Python API
- **Redis**: Cache face embeddings
- **MySQL**: Store user face data

## 📦 Cài đặt

### 1. Python Dependencies

```bash
cd face_recognition_service
pip install -r requirements.txt
```

**requirements.txt:**
```
fastapi==0.104.1
uvicorn[standard]==0.24.0
opencv-python==4.8.1.78
mediapipe==0.10.8
tensorflow==2.15.0
# hoặc pytorch:
# torch==2.1.0
# torchvision==0.16.0
facenet-pytorch==2.5.3
numpy==1.26.2
pillow==10.1.0
scikit-learn==1.3.2
python-multipart==0.0.6
python-dotenv==1.0.0
redis==5.0.1
```

### 2. Laravel Migration

```bash
php artisan make:migration add_face_data_to_users_table
```

### 3. Cấu hình

**.env Laravel:**
```env
FACE_RECOGNITION_API_URL=http://127.0.0.1:8001
FACE_RECOGNITION_ENABLED=true
FACE_RECOGNITION_THRESHOLD=0.6
FACE_RECOGNITION_CACHE_TTL=3600
```

**.env Python:**
```env
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
MODEL_PATH=./models/facenet.pth
CONFIDENCE_THRESHOLD=0.6
ENABLE_ANTI_SPOOFING=true
```

## 🎯 Tính năng

### 1. Enrollment (Đăng ký khuôn mặt)
- Chụp 5-10 ảnh từ nhiều góc độ
- Trích xuất 3D landmarks (468 điểm)
- Tạo face embedding 512-dim
- Lưu vào database

### 2. Authentication (Xác thực)
- Capture realtime từ webcam
- 3D liveness detection
- So sánh embedding với database
- Trả về user nếu match > threshold

### 3. Anti-Spoofing (Chống giả mạo)
- Phát hiện ảnh in, video replay
- Kiểm tra chuyển động 3D
- Phân tích texture
- Eye blink detection

### 4. Security Features
- Rate limiting (5 attempts/minute)
- Logging mọi lần thử
- Alert nếu có nhiều lần fail
- 2FA backup (OTP qua email)

## 📝 API Endpoints

### Python Service (Port 8001)

#### 1. Health Check
```http
GET /health
Response: {"status": "healthy", "model_loaded": true}
```

#### 2. Enroll Face
```http
POST /api/face/enroll
Body: {
    "user_id": 1,
    "images": ["base64_img1", "base64_img2", ...]
}
Response: {
    "success": true,
    "face_id": "uuid",
    "embeddings_count": 5
}
```

#### 3. Verify Face
```http
POST /api/face/verify
Body: {
    "image": "base64_image",
    "user_id": 1
}
Response: {
    "success": true,
    "matched": true,
    "confidence": 0.89,
    "is_live": true,
    "user_id": 1
}
```

#### 4. Authenticate (Unknown user)
```http
POST /api/face/authenticate
Body: {
    "image": "base64_image"
}
Response: {
    "success": true,
    "user_id": 1,
    "confidence": 0.91,
    "name": "Admin User"
}
```

### Laravel Routes

```php
// Admin face recognition routes
Route::prefix('admin')->middleware('web')->group(function () {
    Route::get('/face-login', [FaceLoginController::class, 'showForm'])
        ->name('admin.face.login');
    
    Route::post('/face-verify', [FaceLoginController::class, 'verify'])
        ->name('admin.face.verify');
    
    Route::get('/face-enroll', [FaceLoginController::class, 'showEnroll'])
        ->middleware('auth', 'role:admin')
        ->name('admin.face.enroll');
    
    Route::post('/face-enroll', [FaceLoginController::class, 'enroll'])
        ->middleware('auth', 'role:admin')
        ->name('admin.face.enroll.store');
});
```

## 🔄 Workflow

### Admin Enrollment (Lần đầu setup)

```
1. Admin đăng nhập bằng email/password
   ↓
2. Vào trang "Face Recognition Setup"
   ↓
3. Cho phép truy cập webcam
   ↓
4. Hệ thống hướng dẫn:
   - Nhìn thẳng
   - Quay trái 30°
   - Quay phải 30°
   - Ngẩng lên
   - Cúi xuống
   ↓
5. Chụp 5-10 ảnh tự động
   ↓
6. Gửi đến Python API
   ↓
7. Trích xuất embeddings
   ↓
8. Lưu vào database (encrypted)
   ↓
9. ✅ Enrollment hoàn tất
```

### Admin Login với Face

```
1. Admin vào trang login
   ↓
2. Chọn "Login with Face Recognition"
   ↓
3. Webcam mở
   ↓
4. Hệ thống phát hiện khuôn mặt
   ↓
5. Kiểm tra liveness (3D, blink)
   ↓
6. Chụp ảnh và gửi API
   ↓
7. Python service:
   - Extract embedding
   - So sánh với database
   - Tính confidence score
   ↓
8. Nếu match > 0.6:
   ✅ Login thành công
   ↓
9. Nếu không match:
   ❌ Hiện thông báo lỗi
   → Cho phép thử lại (max 3 lần)
   → Sau đó fallback sang email/password
```

## 💾 Database Schema

```sql
-- Migration: add_face_data_to_users_table
ALTER TABLE users ADD COLUMN face_embeddings JSON NULL;
ALTER TABLE users ADD COLUMN face_enrolled_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN face_images TEXT NULL; -- Lưu paths

-- Table: face_login_attempts
CREATE TABLE face_login_attempts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    success BOOLEAN,
    confidence DECIMAL(3,2),
    is_live BOOLEAN,
    image_path VARCHAR(255),
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_success (success)
);

-- Table: face_embeddings_cache (Redis alternative)
CREATE TABLE face_embeddings_cache (
    user_id BIGINT PRIMARY KEY,
    embeddings JSON,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 🔐 Security Best Practices

### 1. Encryption
```php
// Encrypt embeddings before storing
$encrypted = encrypt(json_encode($embeddings));
$user->face_embeddings = $encrypted;
```

### 2. Rate Limiting
```php
// Laravel middleware
RateLimiter::for('face-login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});
```

### 3. Logging
```php
// Log every attempt
FaceLoginAttempt::create([
    'user_id' => $userId,
    'ip_address' => $request->ip(),
    'success' => $result['matched'],
    'confidence' => $result['confidence'],
    'is_live' => $result['is_live']
]);
```

### 4. Fallback Options
- OTP via email nếu face recognition fail 3 lần
- Admin password reset
- 2FA backup codes

## 📊 Performance

- **Enrollment**: ~3-5 giây cho 5 ảnh
- **Verification**: ~500ms - 1s
- **Accuracy**: 95-98% (với good lighting)
- **False Accept Rate**: < 0.1%
- **False Reject Rate**: < 2%

## 🚀 Deployment

### Development
```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Face Recognition Service
cd face_recognition_service
python app.py

# Terminal 3: Redis (optional)
redis-server
```

### Production
```bash
# Systemd service cho Python
sudo nano /etc/systemd/system/face-recognition.service

# Nginx reverse proxy
upstream face_recognition {
    server 127.0.0.1:8001;
}

# SSL/TLS bắt buộc cho webcam access
```

## 📖 Tài liệu tham khảo

- MediaPipe Face Mesh: https://google.github.io/mediapipe/solutions/face_mesh
- FaceNet Paper: https://arxiv.org/abs/1503.03832
- Anti-Spoofing: https://github.com/minivision-ai/Silent-Face-Anti-Spoofing

## 🎨 UI/UX

### Login Page có thêm:
```html
<div class="face-login-option">
    <button id="faceLoginBtn" class="btn btn-primary btn-lg">
        <i class="bi bi-person-badge"></i>
        Login with Face Recognition
    </button>
</div>

<div id="faceLoginModal" class="modal">
    <video id="webcam" autoplay></video>
    <canvas id="faceCanvas"></canvas>
    <div id="instructions">
        Please look at the camera and stay still...
    </div>
    <div id="status"></div>
</div>
```

---

**Next Steps:**
1. Tạo Python service structure
2. Implement face detection + embedding
3. Laravel integration
4. Frontend webcam capture
5. Testing & optimization

**Status:** Ready to implement 🚀
**Priority:** High security feature
**Estimated time:** 2-3 ngày
