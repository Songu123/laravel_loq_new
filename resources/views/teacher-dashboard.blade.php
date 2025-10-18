@extends('layouts.teacher-dashboard')

@section('title', 'Teacher Dashboard - LOQ')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</nav>
@endsection

@section('teacher-dashboard-content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-section">
                <h1 class="h3 mb-2">Xin chào, {{ Auth::user()->name }}!</h1>
                <p class="text-muted">Chào mừng bạn đến với hệ thống quản lý đề thi LOQ dành cho giáo viên</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đề thi đã tạo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Học sinh của tôi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">48</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Bài thi đang diễn ra</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Bài thi hoàn thành</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">156</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activities -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Thao tác nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <button class="btn btn-success btn-block">
                                <i class="bi bi-plus-circle me-2"></i>Tạo đề thi mới
                            </button>
                        </div>
                        <div class="col-6 mb-3">
                            <button class="btn btn-info btn-block">
                                <i class="bi bi-people me-2"></i>Quản lý học sinh
                            </button>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('teacher.categories.index') }}" class="btn btn-primary btn-block">
                                <i class="bi bi-tags me-2"></i>Xem danh mục
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <button class="btn btn-warning btn-block">
                                <i class="bi bi-graph-up me-2"></i>Xem thống kê
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Hoạt động gần đây</h6>
                </div>
                <div class="card-body">
                    <div class="activity-item d-flex align-items-center mb-3">
                        <div class="activity-icon bg-success me-3">
                            <i class="bi bi-file-earmark-plus text-white"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Tạo đề thi "Toán học lớp 10"</div>
                            <div class="activity-time text-muted">2 giờ trước</div>
                        </div>
                    </div>
                    
                    <div class="activity-item d-flex align-items-center mb-3">
                        <div class="activity-icon bg-info me-3">
                            <i class="bi bi-people text-white"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Thêm 5 học sinh mới</div>
                            <div class="activity-time text-muted">1 ngày trước</div>
                        </div>
                    </div>
                    
                    <div class="activity-item d-flex align-items-center mb-3">
                        <div class="activity-icon bg-warning me-3">
                            <i class="bi bi-clipboard-data text-white"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Xem kết quả bài thi "Vật lý 11"</div>
                            <div class="activity-time text-muted">2 ngày trước</div>
                        </div>
                    </div>
                    
                    <div class="activity-item d-flex align-items-center">
                        <div class="activity-icon bg-primary me-3">
                            <i class="bi bi-download text-white"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Xuất báo cáo tháng 9</div>
                            <div class="activity-time text-muted">3 ngày trước</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Exams -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Đề thi gần đây</h6>
                    <a href="#" class="btn btn-sm btn-outline-success">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tên đề thi</th>
                                    <th>Môn học</th>
                                    <th>Số câu hỏi</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Kiểm tra Toán học - Chương 1</td>
                                    <td>Toán học</td>
                                    <td>20</td>
                                    <td><span class="badge bg-success">Đã xuất bản</span></td>
                                    <td>14/10/2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-1">Xem</button>
                                        <button class="btn btn-sm btn-warning me-1">Sửa</button>
                                        <button class="btn btn-sm btn-info">Sao chép</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bài thi Vật lý - Động học</td>
                                    <td>Vật lý</td>
                                    <td>15</td>
                                    <td><span class="badge bg-warning">Bản nháp</span></td>
                                    <td>13/10/2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-1">Xem</button>
                                        <button class="btn btn-sm btn-warning me-1">Sửa</button>
                                        <button class="btn btn-sm btn-info">Sao chép</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kiểm tra Hóa học - Bảng tuần hoàn</td>
                                    <td>Hóa học</td>
                                    <td>25</td>
                                    <td><span class="badge bg-success">Đã xuất bản</span></td>
                                    <td>12/10/2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-1">Xem</button>
                                        <button class="btn btn-sm btn-warning me-1">Sửa</button>
                                        <button class="btn btn-sm btn-info">Sao chép</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-section {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.border-left-success {
    border-left: 4px solid #10b981 !important;
}

.border-left-info {
    border-left: 4px solid #3b82f6 !important;
}

.border-left-warning {
    border-left: 4px solid #f59e0b !important;
}

.border-left-primary {
    border-left: 4px solid #6366f1 !important;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.activity-title {
    font-weight: 500;
    color: #374151;
}

.activity-time {
    font-size: 0.875rem;
}

.btn-block {
    display: block;
    width: 100%;
}
</style>
@endsection