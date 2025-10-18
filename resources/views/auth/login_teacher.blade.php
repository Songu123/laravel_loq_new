@extends('layouts.auth')

@section('title', 'Đăng nhập Giáo viên - LOQ')
@section('auth-title', 'Đăng nhập Giáo viên')
@section('auth-subtitle', 'Truy cập hệ thống quản lý đề thi trực tuyến')

@section('auth-content')
<form method="POST" action="{{ route('login.teacher.post') }}" class="auth-form">
    @csrf
    
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
               autofocus
               placeholder="teacher@example.com">
        @error('email')
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
                   placeholder="Nhập mật khẩu">
            <button type="button" class="password-toggle" data-target="password">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">
            Ghi nhớ đăng nhập
        </label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-success btn-auth">
        <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
    </button>
</form>

<!-- Additional Links -->
<div class="auth-links">
    <p class="mb-3">Chưa có tài khoản? <a href="{{ route('register.teacher') }}" class="text-success">Đăng ký ngay</a></p>
    
    <div class="role-links">
        <p class="small text-muted mb-2">Đăng nhập với vai trò khác:</p>
        <div class="d-flex gap-2 flex-wrap justify-content-center">
            <a href="{{ route('login.admin') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-shield-check me-1"></i>Admin
            </a>
            <a href="{{ route('login.student') }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-person me-1"></i>Học sinh
            </a>
        </div>
    </div>
</div>

<!-- Google OAuth -->
<div class="oauth-section">
    <div class="divider">
        <span>hoặc</span>
    </div>
    <a href="{{ route('google.redirect') }}" class="btn btn-google">
        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" width="18" height="18">
        Đăng nhập với Google
    </a>
</div>

<!-- Feature Highlights for Teachers -->
<div class="features-section mt-4">
    <h6 class="text-center mb-3">Dành cho Giáo viên</h6>
    <div class="row g-3">
        <div class="col-12">
            <div class="feature-item">
                <i class="bi bi-file-earmark-plus text-success"></i>
                <div>
                    <h6>Tạo đề thi</h6>
                    <p class="small text-muted">Tạo và quản lý đề thi trực tuyến dễ dàng</p>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="feature-item">
                <i class="bi bi-graph-up text-success"></i>
                <div>
                    <h6>Thống kê kết quả</h6>
                    <p class="small text-muted">Xem báo cáo chi tiết về kết quả học tập</p>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="feature-item">
                <i class="bi bi-people text-success"></i>
                <div>
                    <h6>Quản lý học sinh</h6>
                    <p class="small text-muted">Theo dõi tiến độ học tập của học sinh</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection