@extends('layouts.auth')

@section('title', 'Đăng ký Giáo viên - LOQ')
@section('auth-title', 'Đăng ký Giáo viên')
@section('auth-subtitle', 'Tạo tài khoản giáo viên cho hệ thống LOQ')

@section('auth-content')
<form method="POST" action="{{ route('register.teacher.post') }}" class="auth-form">
    @csrf
    
    <!-- Name Field -->
    <div class="form-group">
        <label for="name" class="form-label">
            <i class="bi bi-person me-2"></i>Họ và tên
        </label>
        <input type="text" 
               class="form-control @error('name') is-invalid @enderror" 
               id="name" 
               name="name" 
               value="{{ old('name') }}" 
               required 
               autofocus
               placeholder="Nhập họ và tên đầy đủ">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Email Field -->
    <div class="form-group">
        <label for="email" class="form-label">
            <i class="bi bi-envelope me-2"></i>Email
        </label>
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               id="email" 
               name="email" 
               value="{{ old('email') }}" 
               required
               placeholder="teacher@example.com">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Phone Field -->
    <div class="form-group">
        <label for="phone" class="form-label">
            <i class="bi bi-telephone me-2"></i>Số điện thoại
        </label>
        <input type="tel" 
               class="form-control @error('phone') is-invalid @enderror" 
               id="phone" 
               name="phone" 
               value="{{ old('phone') }}" 
               placeholder="0123456789">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Address Field -->
    <div class="form-group">
        <label for="address" class="form-label">
            <i class="bi bi-geo-alt me-2"></i>Địa chỉ
        </label>
        <textarea class="form-control @error('address') is-invalid @enderror" 
                  id="address" 
                  name="address" 
                  rows="3"
                  placeholder="Nhập địa chỉ">{{ old('address') }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password Field -->
    <div class="form-group">
        <label for="password" class="form-label">
            <i class="bi bi-lock me-2"></i>Mật khẩu
        </label>
        <div class="password-input">
            <input type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   name="password" 
                   required
                   placeholder="Tối thiểu 6 ký tự">
            <button type="button" class="password-toggle" data-target="password">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Confirm Password Field -->
    <div class="form-group">
        <label for="password_confirmation" class="form-label">
            <i class="bi bi-lock-fill me-2"></i>Xác nhận mật khẩu
        </label>
        <div class="password-input">
            <input type="password" 
                   class="form-control" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   required
                   placeholder="Nhập lại mật khẩu">
            <button type="button" class="password-toggle" data-target="password_confirmation">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-success btn-auth">
        <i class="bi bi-mortarboard me-2"></i>Đăng ký Giáo viên
    </button>
</form>

<!-- Additional Links -->
<div class="auth-links">
    <p class="mb-3">Đã có tài khoản?</p>
    <div class="d-flex gap-2 flex-wrap justify-content-center">
        <a href="{{ route('login.admin') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-shield-check me-1"></i>Đăng nhập Admin
        </a>
        <a href="{{ route('login.teacher') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập Teacher
        </a>
        <a href="{{ route('login.student') }}" class="btn btn-outline-info btn-sm">
            <i class="bi bi-person me-1"></i>Đăng nhập Student
        </a>
    </div>
</div>

<!-- Google OAuth -->
<div class="oauth-section">
    <div class="divider">
        <span>hoặc</span>
    </div>
    <a href="{{ route('google.redirect') }}" class="btn btn-google">
        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" width="18" height="18">
        Đăng ký với Google
    </a>
</div>
@endsection