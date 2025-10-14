@extends('layouts.app')

@section('content')
<div class="container mt-5 text-center">
    @auth
        <h1 class="mb-4">Chào mừng, {{ Auth::user()->name }}!</h1>
        <p class="mb-4">Bạn đã đăng nhập thành công 🎉</p>
        
        <!-- Nút vào Dashboard -->
        <a href="{{ route('dashboard') }}" class="btn btn-primary me-3">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        
        <!-- Nút Logout -->
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
            </button>
        </form>
    @else
        <h1 class="mb-4">Chào mừng đến với LOQ</h1>
        <p class="mb-4">Hệ thống quản lý trắc nghiệm trực tuyến</p>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Bắt đầu sử dụng hệ thống</h5>
                        <p class="card-text mb-4">Chọn loại tài khoản phù hợp để đăng nhập vào hệ thống</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-body text-center">
                                        <i class="bi bi-person-gear fs-1 text-primary mb-3"></i>
                                        <h6 class="card-title">Quản trị viên / Giảng viên</h6>
                                        <p class="card-text small">Quản lý đề thi, câu hỏi và kết quả</p>
                                        <a href="{{ route('login.admin') }}" class="btn btn-primary">
                                            <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-success">
                                    <div class="card-body text-center">
                                        <i class="bi bi-person-check fs-1 text-success mb-3"></i>
                                        <h6 class="card-title">Học sinh / Sinh viên</h6>
                                        <p class="card-text small">Tham gia làm bài thi trắc nghiệm</p>
                                        <a href="{{ route('login.student') }}" class="btn btn-success">
                                            <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                Chưa có tài khoản? Liên hệ quản trị viên để được cấp quyền truy cập.
            </small>
        </div>
    @endauth
</div>
@endsection
