@extends('layouts.auth')

@section('title', 'Xác thực Email - LOQ')
@section('auth-title', 'Xác thực Email')
@section('auth-subtitle', 'Chúng tôi đã gửi link xác thực đến email của bạn')

@section('auth-content')
<div class="text-center mb-4">
    <div class="verify-icon mb-3">
        <i class="bi bi-envelope-check" style="font-size: 4rem; color: #10b981;"></i>
    </div>
    
    @if (session('message'))
        <div class="alert alert-success" role="alert">
            {{ session('message') }}
        </div>
    @endif
    
    <p class="mb-4">
        Trước khi tiếp tục, vui lòng kiểm tra email của bạn để tìm link xác thực.
        Nếu bạn không nhận được email, chúng tôi có thể gửi lại cho bạn.
    </p>
</div>

<form method="POST" action="{{ route('verification.send') }}" class="auth-form">
    @csrf
    <button type="submit" class="btn btn-primary btn-auth">
        <i class="bi bi-arrow-clockwise me-2"></i>Gửi lại email xác thực
    </button>
</form>

<div class="auth-links mt-4">
    <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-link text-muted">
            <i class="bi bi-box-arrow-right me-1"></i>Đăng xuất
        </button>
    </form>
</div>

<style>
.verify-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.alert {
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.alert-success {
    background-color: #d1fae5;
    border-color: #a7f3d0;
    color: #065f46;
}
</style>
@endsection