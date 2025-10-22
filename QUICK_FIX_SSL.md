# ⚡ Quick Fix - SSL Certificate Error

## ❌ Lỗi bạn gặp:
```
cURL error 60: SSL certificate problem: unable to get local issuer certificate
```

## ✅ Fix nhanh (1 phút):

### TEMPORARY FIX (Development only):

**File `.env` đã được update:**
```env
CURL_SSL_VERIFYPEER=false
```

**Controller đã được update** để handle SSL issue.

### Test ngay:

1. **Restart server:**
   ```bash
   # Stop (Ctrl+C) and restart
   php artisan serve
   ```

2. **Test login:**
   ```
   http://127.0.0.1:8000/login
   Click "Đăng nhập với Google"
   ```

3. **Should work now!** ✅

## ⚠️ QUAN TRỌNG:

**ĐÂY CHỈ LÀ TEMPORARY FIX CHO DEVELOPMENT!**

Trước khi deploy production, phải:

### ✅ Fix đúng cách (Recommended):

1. **Download cacert.pem:**
   - Link: https://curl.se/ca/cacert.pem
   - Save to: `C:\cacert\cacert.pem`

2. **Find php.ini:**
   ```bash
   php --ini
   ```

3. **Edit php.ini:**
   ```ini
   # Tìm và sửa 2 dòng này:
   curl.cainfo = "C:\cacert\cacert.pem"
   openssl.cafile = "C:\cacert\cacert.pem"
   ```

4. **Remove from `.env`:**
   ```env
   # Delete this line:
   CURL_SSL_VERIFYPEER=false
   ```

5. **Restart server**

## 📚 Chi tiết:

Xem file: `FIX_SSL_CERTIFICATE_ERROR.md`

---

**Current Status:** ✅ Fixed với temporary solution  
**Production Ready:** ❌ Cần fix đúng cách trước khi deploy  
**Next Step:** Download cacert.pem và config php.ini
