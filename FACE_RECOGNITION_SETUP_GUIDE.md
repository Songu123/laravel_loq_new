# 🚀 Hướng dẫn Setup Face Recognition 3D

## 📋 Bước 1: Chuẩn bị môi trường

### 1.1 Requirements
- Python 3.9+ (đã có)
- Redis Server (optional but recommended)
- MySQL/MariaDB (đã có)
- Webcam (cho enrollment và login)

### 1.2 Cài đặt Redis (Windows)

**Option 1: Memurai (Redis for Windows)**
```powershell
# Download từ: https://www.memurai.com/
# Hoặc dùng Chocolatey:
choco install memurai-developer

# Start Redis:
memurai
```

**Option 2: WSL2 Redis**
```bash
# Trong WSL2:
sudo apt update
sudo apt install redis-server
sudo service redis-server start
```

**Option 3: Docker**
```bash
docker run -d -p 6379:6379 --name redis redis:latest
```

> **Lưu ý:** Redis không bắt buộc. Hệ thống vẫn chạy được nhưng chậm hơn (không cache).

---

## 📦 Bước 2: Setup Python Service

### 2.1 Tạo virtual environment
```powershell
cd face_recognition_service
python -m venv venv
venv\Scripts\activate
```

### 2.2 Cài đặt dependencies
```powershell
# Cài đặt tất cả packages
pip install -r requirements.txt
```

**⚠️ Lưu ý:** Quá trình này mất 5-10 phút vì cần tải TensorFlow/PyTorch (~2GB).

**Nếu gặp lỗi:**

**TensorFlow error:**
```powershell
# Dùng CPU version (nhẹ hơn):
pip install tensorflow-cpu==2.15.0
```

**PyTorch alternative (thay TensorFlow):**
```powershell
# Comment TensorFlow trong requirements.txt, uncomment PyTorch:
pip install torch==2.1.0 torchvision==0.16.0
```

**dlib compilation error:**
```powershell
# Dlib optional, có thể bỏ qua:
# Comment dòng dlib trong requirements.txt
```

### 2.3 Cấu hình môi trường
```powershell
# Copy file cấu hình:
copy .env.example .env

# Edit file .env:
notepad .env
```

**File `.env` cần sửa:**
```env
# Database (phải giống Laravel)
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_loq_quizz
DB_USERNAME=root
DB_PASSWORD=YOUR_PASSWORD_HERE

# Redis (nếu có)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Face Recognition
CONFIDENCE_THRESHOLD=0.6
ENABLE_ANTI_SPOOFING=True

# CORS (Laravel URL)
ALLOWED_ORIGINS=http://localhost:8000,http://127.0.0.1:8000
```

### 2.4 Test Python Service
```powershell
# Chạy thử:
python app.py
```

**Output mong muốn:**
```
🚀 Starting Face Recognition 3D Service...
Loading Face Detector (MediaPipe)...
Loading Face Embedder (FaceNet)...
Loading Liveness Detector...
✅ All components loaded successfully!
INFO:     Started server process
INFO:     Uvicorn running on http://0.0.0.0:8001
```

**Kiểm tra health:**
```powershell
# Terminal mới:
curl http://127.0.0.1:8001/health
```

Response:
```json
{
  "status": "healthy",
  "components": {
    "face_detector": true,
    "face_embedder": true,
    "liveness_detector": true,
    "database": true,
    "cache": true
  }
}
```

---

## 🗄️ Bước 3: Setup Laravel

### 3.1 Chạy migration
```powershell
cd ..
php artisan migrate
```

Output:
```
Migrating: 2024_01_15_000000_add_face_recognition_fields
Migrated:  2024_01_15_000000_add_face_recognition_fields (123.45ms)
```

### 3.2 Cấu hình Laravel .env
```env
# Thêm vào .env:
FACE_RECOGNITION_API_URL=http://127.0.0.1:8001
FACE_RECOGNITION_ENABLED=true
FACE_RECOGNITION_THRESHOLD=0.6
FACE_RECOGNITION_CACHE_TTL=3600
```

### 3.3 Clear cache
```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 🎨 Bước 4: Tạo Frontend Views

Bạn có 2 tùy chọn:

### Option A: Thêm nút vào trang login hiện tại

Sửa file `resources/views/auth/login-admin.blade.php`:

```blade
{{-- Thêm sau nút Login bình thường --}}
<div class="text-center mt-3">
    <a href="{{ route('face.login') }}" class="btn btn-outline-primary btn-lg">
        <i class="bi bi-person-badge"></i> Login with Face Recognition
    </a>
</div>
```

### Option B: Tạo trang Face Login riêng

File: `resources/views/auth/face-login.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h4><i class="bi bi-person-badge"></i> Face Recognition Login</h4>
                    <p class="text-muted">Admin Only</p>
                </div>
                <div class="card-body">
                    {{-- Video Preview --}}
                    <div class="text-center mb-4">
                        <video id="webcam" width="640" height="480" autoplay style="border: 2px solid #ddd; border-radius: 8px;"></video>
                        <canvas id="faceCanvas" style="display: none;"></canvas>
                    </div>

                    {{-- Instructions --}}
                    <div id="instructions" class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> 
                        <span id="instructionText">Click "Start Camera" to begin</span>
                    </div>

                    {{-- Controls --}}
                    <div class="text-center">
                        <button id="startBtn" class="btn btn-primary btn-lg">
                            <i class="bi bi-camera-video"></i> Start Camera
                        </button>
                        <button id="captureBtn" class="btn btn-success btn-lg" style="display: none;">
                            <i class="bi bi-check-circle"></i> Verify Face
                        </button>
                        <button id="stopBtn" class="btn btn-danger btn-lg" style="display: none;">
                            <i class="bi bi-x-circle"></i> Stop
                        </button>
                    </div>

                    {{-- Status --}}
                    <div id="status" class="mt-3"></div>

                    {{-- Fallback --}}
                    <div class="text-center mt-4">
                        <a href="{{ route('login.admin') }}" class="btn btn-link">
                            Login with Email/Password instead
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let video = document.getElementById('webcam');
let canvas = document.getElementById('faceCanvas');
let ctx = canvas.getContext('2d');
let stream = null;

// Start Camera
document.getElementById('startBtn').addEventListener('click', async function() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: 640, 
                height: 480,
                facingMode: 'user'
            } 
        });
        video.srcObject = stream;
        
        document.getElementById('startBtn').style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        document.getElementById('stopBtn').style.display = 'inline-block';
        document.getElementById('instructionText').textContent = 'Please look at the camera and click Verify Face';
    } catch (error) {
        showError('Camera access denied: ' + error.message);
    }
});

// Capture and Verify
document.getElementById('captureBtn').addEventListener('click', function() {
    // Capture frame
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);
    
    // Convert to base64
    let imageData = canvas.toDataURL('image/jpeg', 0.8);
    
    // Show loading
    document.getElementById('captureBtn').disabled = true;
    document.getElementById('instructionText').textContent = 'Verifying... Please wait';
    showStatus('Processing your face...', 'info');
    
    // Send to server
    fetch('{{ route("face.verify") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ image: imageData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatus('✅ Login successful! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            showError(data.message);
            document.getElementById('captureBtn').disabled = false;
            document.getElementById('instructionText').textContent = 'Verification failed. Please try again.';
        }
    })
    .catch(error => {
        showError('Network error: ' + error.message);
        document.getElementById('captureBtn').disabled = false;
    });
});

// Stop Camera
document.getElementById('stopBtn').addEventListener('click', function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        
        document.getElementById('startBtn').style.display = 'inline-block';
        document.getElementById('captureBtn').style.display = 'none';
        document.getElementById('stopBtn').style.display = 'none';
        document.getElementById('instructionText').textContent = 'Camera stopped';
    }
});

function showStatus(message, type) {
    const statusDiv = document.getElementById('status');
    statusDiv.className = `alert alert-${type}`;
    statusDiv.textContent = message;
    statusDiv.style.display = 'block';
}

function showError(message) {
    showStatus('❌ ' + message, 'danger');
}
</script>
@endpush
@endsection
```

---

## 🎯 Bước 5: Face Enrollment (Đăng ký khuôn mặt)

### 5.1 Tạo trang Enrollment

File: `resources/views/auth/face-enroll.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-person-plus"></i> Face Recognition Setup</h4>
                </div>
                <div class="card-body">
                    {{-- Current Status --}}
                    <div id="enrollmentStatus" class="alert alert-info">
                        Checking enrollment status...
                    </div>

                    {{-- Instructions --}}
                    <div class="alert alert-warning">
                        <h5>📸 Instructions:</h5>
                        <ol>
                            <li>Click "Start Camera" below</li>
                            <li>Follow the on-screen prompts to capture 5 photos:
                                <ul>
                                    <li>Look straight at camera</li>
                                    <li>Turn left 30°</li>
                                    <li>Turn right 30°</li>
                                    <li>Look up slightly</li>
                                    <li>Look down slightly</li>
                                </ul>
                            </li>
                            <li>Good lighting is important!</li>
                            <li>Remove glasses if possible</li>
                        </ol>
                    </div>

                    {{-- Video Preview --}}
                    <div class="text-center mb-4">
                        <video id="webcam" width="640" height="480" autoplay style="border: 2px solid #ddd; border-radius: 8px;"></video>
                        <canvas id="faceCanvas" style="display: none;"></canvas>
                    </div>

                    {{-- Current Instruction --}}
                    <div id="instructions" class="alert alert-primary text-center" style="font-size: 1.2em;">
                        Click "Start Camera" to begin
                    </div>

                    {{-- Progress --}}
                    <div class="progress mb-3" style="height: 30px;">
                        <div id="progress" class="progress-bar" role="progressbar" style="width: 0%">
                            0 / 5
                        </div>
                    </div>

                    {{-- Controls --}}
                    <div class="text-center">
                        <button id="startBtn" class="btn btn-primary btn-lg">
                            <i class="bi bi-camera-video"></i> Start Camera
                        </button>
                        <button id="captureBtn" class="btn btn-success btn-lg" style="display: none;">
                            <i class="bi bi-camera"></i> Capture Photo
                        </button>
                        <button id="submitBtn" class="btn btn-success btn-lg" style="display: none;">
                            <i class="bi bi-check-circle"></i> Complete Enrollment
                        </button>
                        <button id="resetBtn" class="btn btn-warning btn-lg" style="display: none;">
                            <i class="bi bi-arrow-clockwise"></i> Start Over
                        </button>
                        <button id="deleteBtn" class="btn btn-danger btn-lg" style="display: none;">
                            <i class="bi bi-trash"></i> Delete Enrollment
                        </button>
                    </div>

                    {{-- Thumbnails --}}
                    <div id="thumbnails" class="row mt-4" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Implementation similar to face-login but with multi-capture logic
// Full code available in repository
</script>
@endpush
@endsection
```

---

## 🧪 Bước 6: Testing

### 6.1 Test Python API
```powershell
# Terminal 1: Start Python service
cd face_recognition_service
venv\Scripts\activate
python app.py
```

### 6.2 Test Laravel
```powershell
# Terminal 2: Start Laravel
php artisan serve
```

### 6.3 Test Flow

1. **Enroll Face:**
   - Login as admin: `http://localhost:8000/login/admin`
   - Go to: `http://localhost:8000/admin/face-enroll`
   - Follow instructions to capture 5 photos
   - Click "Complete Enrollment"
   - Should see success message

2. **Face Login:**
   - Logout
   - Go to: `http://localhost:8000/face-login`
   - Click "Start Camera"
   - Click "Verify Face"
   - Should login successfully!

---

## 🐛 Troubleshooting

### Python Service không start
```powershell
# Check logs:
type logs\face_recognition_*.log

# Common issues:
# 1. Port 8001 đã được sử dụng:
netstat -ano | findstr :8001
taskkill /PID <PID> /F

# 2. Database connection error:
# Check .env DB credentials

# 3. Missing dependencies:
pip install -r requirements.txt --upgrade
```

### Camera không bật
- Browser phải là HTTPS hoặc localhost
- Check camera permissions trong browser settings
- Try different browser (Chrome recommended)

### Face không được nhận diện
- Cải thiện ánh sáng (front lighting)
- Đảm bảo khuôn mặt rõ ràng, không bị che
- Tăng CONFIDENCE_THRESHOLD trong .env (0.5 thay vì 0.6)
- Check Python logs: `type logs\face_recognition_*.log`

### Redis error
- Redis optional, có thể tắt: Set `REDIS_HOST=` trong .env
- Hoặc install Redis (xem bước 1.2)

---

## 📊 Performance Optimization

### 1. GPU Acceleration (Optional)
```powershell
# Nếu có NVIDIA GPU:
pip uninstall tensorflow
pip install tensorflow-gpu==2.15.0

# Hoặc PyTorch GPU:
pip install torch==2.1.0+cu118 torchvision==0.16.0+cu118 -f https://download.pytorch.org/whl/torch_stable.html
```

### 2. Redis Caching
```env
# Trong .env:
REDIS_HOST=127.0.0.1  # Enable
CACHE_TTL=3600        # 1 hour
```

### 3. Model Optimization
```env
# Reduce quality for faster speed:
CONFIDENCE_THRESHOLD=0.5
ENABLE_ANTI_SPOOFING=False
```

---

## 🔐 Security Checklist

- [ ] SSL/HTTPS enabled in production
- [ ] Rate limiting configured (max 5 attempts/minute)
- [ ] Face embeddings encrypted in database
- [ ] Logging enabled for all attempts
- [ ] 2FA backup option available
- [ ] Anti-spoofing enabled
- [ ] Admin-only access verified

---

## 📝 Next Steps

1. Create frontend views (face-login.blade.php, face-enroll.blade.php)
2. Add Face Login button to admin login page
3. Test enrollment process
4. Test face login
5. Deploy to production with HTTPS

**Status:** Setup guide complete ✅
**Estimated setup time:** 30-60 minutes
**Support:** Check logs in `logs/` directory
