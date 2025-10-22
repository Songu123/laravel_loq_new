# 🔒 Fix SSL Certificate Error - cURL error 60

## Vấn đề
```
cURL error 60: SSL certificate problem: unable to get local issuer certificate
```

## Nguyên nhân
PHP/cURL trên Windows không có SSL certificate bundle để verify HTTPS requests.

## ✅ Giải pháp (3 cách)

### Cách 1: Download cacert.pem (RECOMMENDED) ✨

#### Bước 1: Download certificate
```
1. Vào: https://curl.se/ca/cacert.pem
2. Save As: cacert.pem
3. Lưu vào: C:\cacert\cacert.pem
   (Hoặc bất kỳ thư mục nào bạn muốn)
```

#### Bước 2: Config PHP
```
1. Tìm file php.ini:
   - XAMPP: C:\xampp\php\php.ini
   - Laravel Herd: C:\Users\YourName\.config\herd\bin\php.ini
   - Hoặc chạy: php --ini

2. Mở php.ini bằng Notepad

3. Tìm dòng:
   ;curl.cainfo =

4. Bỏ dấu ; và sửa thành:
   curl.cainfo = "C:\cacert\cacert.pem"

5. Tìm dòng:
   ;openssl.cafile=

6. Bỏ dấu ; và sửa thành:
   openssl.cafile = "C:\cacert\cacert.pem"

7. Save file

8. Restart server:
   - XAMPP: Stop/Start Apache
   - Laravel: php artisan serve (restart terminal)
```

#### Bước 3: Verify
```bash
php -i | findstr "curl.cainfo"
php -i | findstr "openssl.cafile"
```

Should output:
```
curl.cainfo => C:\cacert\cacert.pem
openssl.cafile => C:\cacert\cacert.pem
```

### Cách 2: Disable SSL Verification (DEVELOPMENT ONLY) ⚠️

**WARNING: CHỈ DÙNG CHO DEVELOPMENT, KHÔNG BAO GIỜ DÙNG PRODUCTION!**

Thêm vào file `.env`:
```env
CURL_SSL_VERIFYPEER=false
```

Tạo file `config/curl.php`:
```php
<?php

return [
    'verify' => env('CURL_SSL_VERIFYPEER', true),
];
```

Update `SocialAuthController.php`:
```php
public function redirectToGoogle()
{
    if (config('curl.verify') === false) {
        // Disable SSL verification for development
        $httpClient = new \GuzzleHttp\Client([
            'verify' => false
        ]);
        
        return Socialite::driver('google')
            ->setHttpClient($httpClient)
            ->redirect();
    }
    
    return Socialite::driver('google')->redirect();
}

public function handleGoogleCallback()
{
    try {
        if (config('curl.verify') === false) {
            $httpClient = new \GuzzleHttp\Client([
                'verify' => false
            ]);
            
            $googleUser = Socialite::driver('google')
                ->setHttpClient($httpClient)
                ->user();
        } else {
            $googleUser = Socialite::driver('google')->user();
        }
        
        // Rest of the code...
```

### Cách 3: Set Certificate trong Code (TEMPORARY)

Thêm vào `bootstrap/app.php`:
```php
// Set certificate bundle path
ini_set('curl.cainfo', 'C:/cacert/cacert.pem');
ini_set('openssl.cafile', 'C:/cacert/cacert.pem');
```

## 🎯 Recommended Solution

**Cách 1 (Download cacert.pem)** là best practice và an toàn nhất.

### Quick Steps:

1. **Download:**
   ```
   https://curl.se/ca/cacert.pem
   Save to: C:\cacert\cacert.pem
   ```

2. **Find php.ini:**
   ```bash
   php --ini
   ```
   Output shows: `Loaded Configuration File: C:\path\to\php.ini`

3. **Edit php.ini:**
   ```ini
   curl.cainfo = "C:\cacert\cacert.pem"
   openssl.cafile = "C:\cacert\cacert.pem"
   ```

4. **Restart server:**
   ```bash
   # Stop current server (Ctrl+C)
   php artisan serve
   ```

5. **Test:**
   ```
   Vào: http://127.0.0.1:8000/login
   Click "Đăng nhập với Google"
   Should work now! ✅
   ```

## 🧪 Test SSL Connection

```bash
php -r "var_dump(curl_version());"
```

Check output có `ssl_version` không.

```bash
php -r "\$ch = curl_init('https://www.googleapis.com'); curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, 1); \$result = curl_exec(\$ch); if(curl_errno(\$ch)) { echo 'Error: ' . curl_error(\$ch); } else { echo 'Success!'; } curl_close(\$ch);"
```

Should output: `Success!`

## 📝 Troubleshooting

### Error: "php.ini not found"
```bash
# Create php.ini copy
copy "C:\xampp\php\php.ini-development" "C:\xampp\php\php.ini"
```

### Error: "cacert.pem not found"
```bash
# Check file exists
dir C:\cacert\cacert.pem
```

### Error: "Permission denied"
Run as Administrator or save cacert.pem to user folder:
```
C:\Users\YourName\cacert\cacert.pem
```

Then update php.ini:
```ini
curl.cainfo = "C:\Users\YourName\cacert\cacert.pem"
```

## 🔐 Security Notes

- ✅ Cách 1: An toàn, nên dùng
- ⚠️ Cách 2: CHỈ development, XÓA trước khi deploy
- ⚡ Cách 3: Temporary workaround

## 🚀 After Fix

1. Restart PHP server
2. Clear Laravel cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
3. Test Google login again
4. Should work perfectly! 🎉

---

**Issue**: cURL SSL certificate error  
**Fix**: Download & configure cacert.pem  
**Time**: ~5 minutes  
**Status**: ✅ Solvable
