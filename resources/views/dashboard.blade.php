@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <i class="bi bi-house-door me-1"></i>
            Trang chủ
        </li>
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="welcome-content">
                            <h1 class="welcome-title">
                                <i class="bi bi-sun text-warning me-2"></i>
                                Chào mừng trở lại, {{ Auth::user()->name ?? 'Admin' }}!
                            </h1>
                            <p class="welcome-subtitle">
                                Hôm nay là {{ now()->format('l, d/m/Y') }} - Đây là tổng quan về hoạt động hệ thống LOQ
                            </p>
                            <div class="welcome-stats">
                                <span class="badge bg-primary me-2">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ now()->format('d/m/Y') }}
                                </span>
                                <span class="badge bg-success me-2">
                                    <i class="bi bi-people me-1"></i>
                                    {{ rand(150, 300) }} học viên online
                                </span>
                                <span class="badge bg-info">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ rand(15, 45) }} bài thi đang diễn ra
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="welcome-actions">
                            <button class="btn btn-primary btn-lg shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#quickCreateModal">
                                <i class="bi bi-plus-circle me-2"></i>
                                Tạo đề thi mới
                            </button>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Xuất báo cáo</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-upload me-2"></i>Nhập dữ liệu</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Cài đặt</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Grid -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon bg-gradient-primary">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="stat-menu dropdown">
                    <button class="btn btn-sm" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Xem chi tiết</a></li>
                        <li><a class="dropdown-item" href="#">Tạo mới</a></li>
                    </ul>
                </div>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ rand(150, 200) }}</div>
                <div class="stat-label">Tổng đề thi</div>
                <div class="stat-change positive">
                    <i class="bi bi-arrow-up"></i>
                    +{{ rand(5, 15) }}% từ tháng trước
                </div>
            </div>
            <div class="stat-chart">
                <div class="mini-chart" data-chart="exams"></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon bg-gradient-success">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-menu dropdown">
                    <button class="btn btn-sm" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Quản lý học viên</a></li>
                        <li><a class="dropdown-item" href="#">Thêm học viên</a></li>
                    </ul>
                </div>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ number_format(rand(2500, 3500)) }}</div>
                <div class="stat-label">Học viên hoạt động</div>
                <div class="stat-change positive">
                    <i class="bi bi-arrow-up"></i>
                    +{{ rand(8, 20) }}% từ tháng trước
                </div>
            </div>
            <div class="stat-chart">
                <div class="mini-chart" data-chart="students"></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon bg-gradient-warning">
                    <i class="bi bi-clipboard-data"></i>
                </div>
                <div class="stat-menu dropdown">
                    <button class="btn btn-sm" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Kết quả chi tiết</a></li>
                        <li><a class="dropdown-item" href="#">Phân tích</a></li>
                    </ul>
                </div>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ number_format(rand(15000, 25000)) }}</div>
                <div class="stat-label">Lượt thi hoàn thành</div>
                <div class="stat-change positive">
                    <i class="bi bi-arrow-up"></i>
                    +{{ rand(12, 28) }}% từ tháng trước
                </div>
            </div>
            <div class="stat-chart">
                <div class="mini-chart" data-chart="completions"></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon bg-gradient-info">
                    <i class="bi bi-trophy"></i>
                </div>
                <div class="stat-menu dropdown">
                    <button class="btn btn-sm" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Bảng xếp hạng</a></li>
                        <li><a class="dropdown-item" href="#">Thống kê điểm</a></li>
                    </ul>
                </div>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ rand(75, 95) }}.{{ rand(0, 9) }}%</div>
                <div class="stat-label">Điểm trung bình</div>
                <div class="stat-change {{ rand(0, 1) ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ rand(0, 1) ? 'up' : 'down' }}"></i>
                    {{ rand(0, 1) ? '+' : '-' }}{{ rand(1, 5) }}.{{ rand(0, 9) }}% từ tháng trước
                </div>
            </div>
            <div class="stat-chart">
                <div class="mini-chart" data-chart="average"></div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="row g-4">
        <!-- Recent Activity Enhanced -->
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="header-left">
                        <h5 class="card-title-modern">
                            <i class="bi bi-activity text-primary me-2"></i>
                            Hoạt động gần đây
                        </h5>
                        <p class="card-subtitle">Theo dõi hoạt động mới nhất của học viên</p>
                    </div>
                    <div class="header-right">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="activityFilter" id="today" checked>
                            <label class="btn btn-outline-primary btn-sm" for="today">Hôm nay</label>
                            
                            <input type="radio" class="btn-check" name="activityFilter" id="week">
                            <label class="btn btn-outline-primary btn-sm" for="week">Tuần này</label>
                            
                            <input type="radio" class="btn-check" name="activityFilter" id="month">
                            <label class="btn btn-outline-primary btn-sm" for="month">Tháng này</label>
                        </div>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div class="activity-list">
                        @php
                            $activities = [
                                ['name' => 'Nguyễn Văn An', 'exam' => 'Toán học cơ bản', 'score' => '9.2', 'time' => '5 phút', 'status' => 'completed', 'avatar' => 'NVA'],
                                ['name' => 'Trần Thị Bích', 'exam' => 'Lịch sử Việt Nam', 'score' => '8.5', 'time' => '12 phút', 'status' => 'completed', 'avatar' => 'TTB'],
                                ['name' => 'Lê Văn Cường', 'exam' => 'Tiếng Anh A2', 'score' => null, 'time' => '18 phút', 'status' => 'in-progress', 'avatar' => 'LVC'],
                                ['name' => 'Phạm Thị Dung', 'exam' => 'Vật lý 12', 'score' => '7.8', 'time' => '25 phút', 'status' => 'completed', 'avatar' => 'PTD'],
                                ['name' => 'Hoàng Minh Đức', 'exam' => 'Hóa học hữu cơ', 'score' => null, 'time' => '32 phút', 'status' => 'in-progress', 'avatar' => 'HMD'],
                            ];
                        @endphp
                        @foreach($activities as $activity)
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <div class="avatar-circle {{ $activity['status'] === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $activity['avatar'] }}
                                </div>
                                <div class="activity-status {{ $activity['status'] === 'completed' ? 'status-completed' : 'status-progress' }}"></div>
                            </div>
                            <div class="activity-content">
                                <div class="activity-header">
                                    <h6 class="activity-name">{{ $activity['name'] }}</h6>
                                    <span class="activity-time">{{ $activity['time'] }} trước</span>
                                </div>
                                <div class="activity-details">
                                    <span class="activity-exam">{{ $activity['exam'] }}</span>
                                    @if($activity['status'] === 'completed')
                                        <span class="activity-score badge bg-success">{{ $activity['score'] }}/10</span>
                                    @else
                                        <span class="activity-score badge bg-warning">Đang làm bài</span>
                                    @endif
                                </div>
                            </div>
                            <div class="activity-actions">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Tải thêm hoạt động
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions Enhanced -->
            <div class="dashboard-card mb-4">
                <div class="card-header-modern">
                    <h5 class="card-title-modern">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Thao tác nhanh
                    </h5>
                </div>
                <div class="card-body-modern">
                    <div class="quick-actions-grid">
                        <button class="quick-action-btn btn-primary">
                            <i class="bi bi-plus-circle"></i>
                            <span>Tạo đề thi</span>
                        </button>
                        <button class="quick-action-btn btn-success">
                            <i class="bi bi-person-plus"></i>
                            <span>Thêm học viên</span>
                        </button>
                        <button class="quick-action-btn btn-info">
                            <i class="bi bi-bar-chart"></i>
                            <span>Xem báo cáo</span>
                        </button>
                        <button class="quick-action-btn btn-warning">
                            <i class="bi bi-gear"></i>
                            <span>Cài đặt</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Performance -->
            <div class="dashboard-card mb-4">
                <div class="card-header-modern">
                    <h5 class="card-title-modern">
                        <i class="bi bi-speedometer2 text-info me-2"></i>
                        Hiệu suất hệ thống
                    </h5>
                </div>
                <div class="card-body-modern">
                    <div class="performance-metrics">
                        <div class="metric-item">
                            <div class="metric-header">
                                <span class="metric-label">CPU Usage</span>
                                <span class="metric-value">{{ rand(35, 65) }}%</span>
                            </div>
                            <div class="progress metric-progress">
                                <div class="progress-bar bg-success" style="width: {{ rand(35, 65) }}%"></div>
                            </div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-header">
                                <span class="metric-label">Memory</span>
                                <span class="metric-value">{{ rand(45, 75) }}%</span>
                            </div>
                            <div class="progress metric-progress">
                                <div class="progress-bar bg-warning" style="width: {{ rand(45, 75) }}%"></div>
                            </div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-header">
                                <span class="metric-label">Storage</span>
                                <span class="metric-value">{{ rand(60, 85) }}%</span>
                            </div>
                            <div class="progress metric-progress">
                                <div class="progress-bar bg-danger" style="width: {{ rand(60, 85) }}%"></div>
                            </div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-header">
                                <span class="metric-label">Network</span>
                                <span class="metric-value">{{ rand(20, 50) }}%</span>
                            </div>
                            <div class="progress metric-progress">
                                <div class="progress-bar bg-info" style="width: {{ rand(20, 50) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <h5 class="card-title-modern">
                        <i class="bi bi-bell text-warning me-2"></i>
                        Thông báo mới
                    </h5>
                </div>
                <div class="card-body-modern">
                    <div class="notification-list">
                        <div class="notification-item">
                            <div class="notification-icon bg-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">15 bài thi mới đã được hoàn thành</p>
                                <span class="notification-time">2 phút trước</span>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon bg-info">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">3 học viên mới đã đăng ký</p>
                                <span class="notification-time">15 phút trước</span>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon bg-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">Cần cập nhật câu hỏi cho đề thi Toán 12</p>
                                <span class="notification-time">1 giờ trước</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            Xem tất cả thông báo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Create Modal -->
<div class="modal fade" id="quickCreateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle text-primary me-2"></i>
                    Tạo đề thi nhanh
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên đề thi</label>
                                <input type="text" class="form-control" placeholder="Nhập tên đề thi">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Môn học</label>
                                <select class="form-select">
                                    <option>Chọn môn học</option>
                                    <option>Toán học</option>
                                    <option>Vật lý</option>
                                    <option>Hóa học</option>
                                    <option>Tiếng Anh</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Số câu hỏi</label>
                                <input type="number" class="form-control" value="20" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Thời gian (phút)</label>
                                <input type="number" class="form-control" value="60" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Điểm tối đa</label>
                                <input type="number" class="form-control" value="10" min="1">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tạo đề thi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Welcome Header */
.welcome-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.welcome-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.welcome-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.welcome-stats .badge {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

/* Enhanced Stat Cards */
.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.stat-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.bg-gradient-primary { background: linear-gradient(135deg, #667eea, #764ba2); }
.bg-gradient-success { background: linear-gradient(135deg, #11998e, #38ef7d); }
.bg-gradient-warning { background: linear-gradient(135deg, #f093fb, #f5576c); }
.bg-gradient-info { background: linear-gradient(135deg, #4facfe, #00f2fe); }

.stat-menu button {
    border: none;
    background: none;
    color: #6c757d;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #718096;
    font-weight: 500;
    margin-bottom: 0.75rem;
}

.stat-change {
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.stat-change.positive { color: #48bb78; }
.stat-change.negative { color: #f56565; }

.stat-chart {
    position: absolute;
    bottom: -10px;
    right: -10px;
    width: 100px;
    height: 40px;
    opacity: 0.1;
}

/* Modern Card Headers */
.card-header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.card-title-modern {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
}

.card-subtitle {
    color: #718096;
    font-size: 0.875rem;
    margin: 0;
}

.card-body-modern {
    padding: 1.5rem;
}

/* Activity List */
.activity-list {
    max-height: 500px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 0.75rem;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}

.activity-item:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
}

.activity-avatar {
    position: relative;
    margin-right: 1rem;
}

.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}

.activity-status {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid white;
}

.status-completed { background: #48bb78; }
.status-progress { background: #ed8936; }

.activity-content {
    flex: 1;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
}

.activity-name {
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    font-size: 0.95rem;
}

.activity-time {
    color: #a0aec0;
    font-size: 0.8rem;
}

.activity-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.activity-exam {
    color: #4a5568;
    font-size: 0.875rem;
}

/* Quick Actions Grid */
.quick-actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem 1rem;
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: white;
}

.quick-action-btn i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.quick-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    color: white;
}

/* Performance Metrics */
.metric-item {
    margin-bottom: 1.5rem;
}

.metric-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-weight: 500;
    color: #4a5568;
}

.metric-value {
    font-weight: 600;
    color: #2d3748;
}

.metric-progress {
    height: 8px;
    border-radius: 4px;
}

/* Notifications */
.notification-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 0.75rem;
    background: #f7fafc;
    border: 1px solid #e2e8f0;
}

.notification-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    color: white;
    font-size: 0.875rem;
}

.notification-content {
    flex: 1;
}

.notification-text {
    margin: 0 0 0.25rem;
    font-size: 0.875rem;
    color: #4a5568;
}

.notification-time {
    font-size: 0.75rem;
    color: #a0aec0;
}

/* Responsive */
@media (max-width: 768px) {
    .welcome-title { font-size: 1.5rem; }
    .stat-value { font-size: 2rem; }
    .quick-actions-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats on load
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(stat => {
        const finalValue = stat.textContent.replace(/[^\d.-]/g, '');
        if (!isNaN(finalValue)) {
            let currentValue = 0;
            const increment = finalValue / 30;
            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                if (stat.textContent.includes('%')) {
                    stat.textContent = Math.floor(currentValue) + '%';
                } else if (stat.textContent.includes(',')) {
                    stat.textContent = Math.floor(currentValue).toLocaleString();
                } else {
                    stat.textContent = Math.floor(currentValue);
                }
            }, 50);
        }
    });

    // Auto-refresh activity every 30 seconds
    setInterval(() => {
        // In a real app, you would fetch new data here
        console.log('Refreshing activity data...');
    }, 30000);

    // Add click handlers for quick actions
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.querySelector('span').textContent;
            console.log('Quick action clicked:', action);
            // Handle the action here
        });
    });
});
</script>
@endpush