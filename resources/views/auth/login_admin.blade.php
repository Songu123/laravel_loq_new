@extends('layouts.auth')

@section('auth-title', 'Đăng nhập Quản trị viên/Giảng viên')
@section('auth-subtitle', 'Chỉ dành cho quản trị viên và giảng viên. Vui lòng sử dụng tài khoản được cấp quyền.')
@section('form-title', 'Đăng nhập Quản trị viên/Giảng viên')
@section('form-subtitle', 'Nhập email và mật khẩu để truy cập hệ thống quản lý.')
@section('auth-content')
<form method="POST" action="{{ route('login.admin.post') }}" class="auth-form">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="Email quản trị/giảng viên">
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Mật khẩu</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Mật khẩu">
        @error('password')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right me-2"></i> Đăng nhập
        </button>
    </div>
    <div class="d-grid mb-3">
        <a href="{{ route('google.redirect') }}" class="btn btn-outline-danger mb-2">
            <i class="bi bi-google me-2"></i> Đăng nhập bằng Google
        </a>
    </div>
</form>
@endsection
@section('auth-footer')
<p class="mb-0">Bạn là học sinh/sinh viên? <a href="{{ route('login.student') }}" class="fw-semibold">Đăng nhập tại đây</a></p>
@endsection