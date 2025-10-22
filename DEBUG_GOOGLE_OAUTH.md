# 🔍 Quick Debug Script

## Test Google OAuth từ Terminal

### 1. Check Environment Variables
```bash
php -r "
echo 'GOOGLE_CLIENT_ID: ' . env('GOOGLE_CLIENT_ID') . PHP_EOL;
echo 'GOOGLE_CLIENT_SECRET: ' . (env('GOOGLE_CLIENT_SECRET') ? 'Set (hidden)' : 'NOT SET') . PHP_EOL;
echo 'GOOGLE_REDIRECT: ' . env('GOOGLE_REDIRECT') . PHP_EOL;
"
```

### 2. Check Routes
```bash
php artisan route:list --name=auth.google
```

Expected output:
```
GET auth/google .............. auth.google
GET auth/google/callback ..... auth.google.callback
```

### 3. Check Database
```sql
-- Check if google_id column exists
SHOW COLUMNS FROM users LIKE 'google_id';

-- Check users table structure
DESCRIBE users;
```

### 4. Test Redirect (Browser)
```
Open: http://127.0.0.1:8000/auth/google
Should redirect to: accounts.google.com
```

### 5. Check Socialite Config
```bash
php artisan tinker
```

Then run:
```php
config('services.google');
// Should output array with client_id, client_secret, redirect
```

### 6. Test User Creation
```bash
php artisan tinker
```

Test code:
```php
$user = \App\Models\User::create([
    'name' => 'Test Google User',
    'email' => 'testgoogle@example.com',
    'google_id' => '123456789',
    'avatar' => 'https://via.placeholder.com/150',
    'password' => \Hash::make('password'),
    'role' => 'student',
    'email_verified_at' => now(),
]);

echo "User created: " . $user->id;
```

## Common Issues & Fixes

### Issue 1: "Class Socialite not found"
```bash
composer require laravel/socialite
php artisan config:clear
```

### Issue 2: "Driver [google] not supported"
Check `config/services.php`:
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT'),
],
```

### Issue 3: "Column google_id doesn't exist"
```bash
php artisan migrate
# Or
php artisan migrate:fresh  # WARNING: Deletes all data!
```

### Issue 4: Config cached
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Real-time Debug

Add to `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Then check logs after error:
```bash
# Windows
Get-Content storage\logs\laravel.log -Tail 100

# Linux/Mac
tail -f storage/logs/laravel.log
```

## Google Console Checklist

1. ✅ OAuth 2.0 Client ID created
2. ✅ Authorized redirect URIs added:
   - `http://127.0.0.1:8000/auth/google/callback`
   - `http://localhost:8000/auth/google/callback`
3. ✅ OAuth consent screen configured
4. ✅ Google+ API enabled
5. ✅ Test users added (if in Testing mode)

## URL to Test

### Development:
```
Login: http://127.0.0.1:8000/login
Google Auth: http://127.0.0.1:8000/auth/google
Callback: http://127.0.0.1:8000/auth/google/callback
```

### Production:
```
Login: https://yourdomain.com/login
Google Auth: https://yourdomain.com/auth/google
Callback: https://yourdomain.com/auth/google/callback
```

## Expected Flow

```
1. User clicks "Đăng nhập với Google"
   → Redirect to: http://127.0.0.1:8000/auth/google
   
2. Laravel redirects to Google
   → Redirect to: accounts.google.com/o/oauth2/auth?...
   
3. User selects Google account
   → Google redirects to: http://127.0.0.1:8000/auth/google/callback?code=xxx
   
4. Laravel processes callback
   → Create/Update user
   → Login user
   → Redirect to dashboard based on role
   
5. Success!
   → Student: /student/dashboard
   → Teacher: /teacher/dashboard
   → Admin: /admin/dashboard
```

## If Still Failing

1. **Enable detailed errors in SocialAuthController:**
   Already done - check `storage/logs/laravel.log`

2. **Test with curl:**
   ```bash
   curl -i http://127.0.0.1:8000/auth/google
   ```
   Should return 302 redirect to Google

3. **Check Google Console Activity:**
   Google Console → APIs & Services → Dashboard
   See if requests are hitting Google API

4. **Try different browser:**
   - Chrome Incognito
   - Firefox Private
   - Clear cookies

5. **Check firewall/antivirus:**
   Make sure port 8000 is not blocked

---

Good luck! 🚀
