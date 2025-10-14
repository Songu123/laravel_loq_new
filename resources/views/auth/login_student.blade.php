@extends('layouts.auth')

@section('auth-title', 'Đăng nhập Học sinh/Sinh viên')
@section('auth-subtitle', 'Dành cho học sinh, sinh viên tham gia thi trắc nghiệm. Vui lòng nhập mã hoặc email được cấp.')
@section('form-title', 'Đăng nhập Học sinh/Sinh viên')
@section('form-subtitle', 'Nhập thông tin để bắt đầu làm bài thi.')
@section('auth-content')
<form method="POST" action="{{ route('login.student.post') }}" class="auth-form">
    @csrf
    <div class="mb-3">
        <label for="student_id" class="form-label">Mã học sinh/sinh viên hoặc Email</label>
        <input id="student_id" type="text" class="form-control @error('student_id') is-invalid @enderror" name="student_id" value="{{ old('student_id') }}" required autofocus placeholder="Mã hoặc email">
        @error('student_id')
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
<p class="mb-0">Bạn là giảng viên/quản trị viên? <a href="{{ route('login.admin') }}" class="fw-semibold">Đăng nhập tại đây</a></p>
@endsection