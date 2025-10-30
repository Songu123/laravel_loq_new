# 🔧 Python Version Issue - MediaPipe Compatibility

## ❌ Problem
MediaPipe chưa hỗ trợ Python 3.13 (bạn đang dùng 3.13.5)
MediaPipe chỉ hỗ trợ: Python 3.8 - 3.11

## ✅ Solutions

### Option 1: Install Python 3.11 (RECOMMENDED)

1. Download Python 3.11.9 từ: https://www.python.org/downloads/release/python-3119/
   - Chọn: "Windows installer (64-bit)"
   
2. Install Python 3.11:
   - ✅ Add Python 3.11 to PATH
   - ✅ Install for all users (optional)
   
3. Create new venv với Python 3.11:
```powershell
# Xóa venv cũ
Remove-Item -Recurse -Force venv

# Tạo venv mới với Python 3.11
py -3.11 -m venv venv

# Activate
venv\Scripts\activate

# Verify Python version
python --version  # Should show Python 3.11.x

# Install dependencies
pip install -r requirements-minimal.txt
```

### Option 2: Use conda environment

```powershell
# Create conda env with Python 3.11
conda create -n face_recognition python=3.11 -y

# Activate
conda activate face_recognition

# Install dependencies
pip install -r requirements-minimal.txt
```

### Option 3: Skip MediaPipe, use dlib instead (NOT RECOMMENDED)

MediaPipe không available → dùng dlib thay thế:
- ❌ Không có 3D landmarks
- ❌ Liveness detection kém hơn
- ❌ Slower performance
- ✅ Nhưng vẫn hoạt động được

Sửa `face_detector.py` để dùng dlib thay vì MediaPipe.

## 📝 Recommended Action

**Use Python 3.11!** MediaPipe là core component của hệ thống 3D Face Recognition.

### Quick Steps:
1. Install Python 3.11.9
2. Delete old venv: `Remove-Item -Recurse -Force venv`
3. Create new venv: `py -3.11 -m venv venv`
4. Activate: `venv\Scripts\activate`
5. Install: `pip install -r requirements-minimal.txt`
6. Run: `python app.py`

## 🔍 Check Available Python Versions

```powershell
# List all Python versions
py --list

# Should show something like:
# -V:3.13 *    # Current
# -V:3.11      # Target
```

---

**Status:** Waiting for Python 3.11 installation
**Alternative:** Use conda with Python 3.11
