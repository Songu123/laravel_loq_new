@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item active">
            <i class="bi bi-house-door me-1"></i>
            Dashboard
        </li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="welcome-title">Xin chào, {{ Auth::user()->name }}!</h1>
                        <p class="welcome-text">Chào mừng bạn quay trở lại với hệ thống quản lý LOQ. Hôm nay là {{ now()->format('l, d/m/Y') }}</p>
                        <div class="quick-stats">
                            <span class="stat-badge bg-primary">
                                <i class="bi bi-people me-1"></i>
                                {{ rand(150, 300) }} học viên online
                            </span>
                            <span class="stat-badge bg-success">
                                <i class="bi bi-file-earmark-text me-1"></i>
                                {{ rand(15, 45) }} bài thi hoạt động
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle me-2"></i>
                            Tạo đề thi mới
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card border-start-primary">
                <div class="stats-content">
                    <div class="stats-number text-primary">{{ rand(150, 200) }}</div>
                    <div class="stats-label">Tổng đề thi</div>
                    <div class="stats-change text-success">
                        <i class="bi bi-arrow-up"></i> +{{ rand(5, 15) }}%
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card border-start-success">
                <div class="stats-content">
                    <div class="stats-number text-success">{{ number_format(rand(2500, 3500)) }}</div>
                    <div class="stats-label">Học viên</div>
                    <div class="stats-change text-success">
                        <i class="bi bi-arrow-up"></i> +{{ rand(8, 20) }}%
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card border-start-info">
                <div class="stats-content">
                    <div class="stats-number text-info">{{ number_format(rand(15000, 25000)) }}</div>
                    <div class="stats-label">Lượt thi</div>
                    <div class="stats-change text-success">
                        <i class="bi bi-arrow-up"></i> +{{ rand(12, 28) }}%
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="bi bi-clipboard-data"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card border-start-warning">
                <div class="stats-content">
                    <div class="stats-number text-warning">{{ rand(85, 95) }}%</div>
                    <div class="stats-label">Tỷ lệ hoàn thành</div>
                    <div class="stats-change text-success">
                        <i class="bi bi-arrow-up"></i> +{{ rand(2, 8) }}%
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="bi bi-trophy"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Recent Activity -->
        <div class="col-lg-8">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-activity text-primary me-2"></i>
                        Hoạt động gần đây
                    </h5>
                    <div class="card-actions">
                        <select class="form-select form-select-sm">
                            <option>Hôm nay</option>
                            <option>Tuần này</option>
                            <option>Tháng này</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        @php
                            $activities = [
                                ['name' => 'Nguyễn Văn An', 'action' => 'hoàn thành bài thi', 'subject' => 'Toán học cơ bản', 'score' => '9.2', 'time' => '5 phút trước', 'status' => 'success'],
                                ['name' => 'Trần Thị Bích', 'action' => 'bắt đầu làm bài', 'subject' => 'Lịch sử Việt Nam', 'score' => null, 'time' => '12 phút trước', 'status' => 'progress'],
                                ['name' => 'Lê Văn Cường', 'action' => 'đăng ký tham gia', 'subject' => 'Tiếng Anh A2', 'score' => null, 'time' => '18 phút trước', 'status' => 'info'],
                                ['name' => 'Phạm Thị Dung', 'action' => 'hoàn thành bài thi', 'subject' => 'Vật lý 12', 'score' => '8.5', 'time' => '25 phút trước', 'status' => 'success'],
                                ['name' => 'Hoàng Minh Đức', 'action' => 'tạo đề thi mới', 'subject' => 'Hóa học hữu cơ', 'score' => null, 'time' => '32 phút trước', 'status' => 'warning'],
                            ];
                        @endphp

                        @foreach($activities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $activity['status'] }}">
                                @if($activity['status'] === 'success')
                                    <i class="bi bi-check"></i>
                                @elseif($activity['status'] === 'progress')
                                    <i class="bi bi-clock"></i>
                                @elseif($activity['status'] === 'warning')
                                    <i class="bi bi-plus"></i>
                                @else
                                    <i class="bi bi-person"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <strong>{{ $activity['name'] }}</strong> {{ $activity['action'] }}
                                    <span class="timeline-time">{{ $activity['time'] }}</span>
                                </div>
                                <div class="timeline-body">
                                    <span class="subject-name">{{ $activity['subject'] }}</span>
                                    @if($activity['score'])
                                        <span class="score-badge">{{ $activity['score'] }}/10</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Thao tác nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="#" class="quick-action-item">
                            <div class="action-icon bg-primary">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Tạo đề thi</div>
                                <div class="action-desc">Tạo đề thi mới</div>
                            </div>
                        </a>
                        
                        <a href="#" class="quick-action-item">
                            <div class="action-icon bg-success">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Thêm học viên</div>
                                <div class="action-desc">Quản lý người dùng</div>
                            </div>
                        </a>
                        
                        <a href="#" class="quick-action-item">
                            <div class="action-icon bg-info">
                                <i class="bi bi-bar-chart"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Báo cáo</div>
                                <div class="action-desc">Xem thống kê</div>
                            </div>
                        </a>
                        
                        <a href="#" class="quick-action-item">
                            <div class="action-icon bg-warning">
                                <i class="bi bi-gear"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Cài đặt</div>
                                <div class="action-desc">Cấu hình hệ thống</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-bell text-info me-2"></i>
                        Thông báo
                    </h5>
                    <div class="card-actions">
                        <span class="badge bg-danger">{{ rand(3, 8) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="notification-list">
                        <div class="notification-item">
                            <div class="notification-dot bg-success"></div>
                            <div class="notification-content">
                                <div class="notification-title">{{ rand(10, 25) }} bài thi mới hoàn thành</div>
                                <div class="notification-time">{{ rand(2, 10) }} phút trước</div>
                            </div>
                        </div>

                        <div class="notification-item">
                            <div class="notification-dot bg-info"></div>
                            <div class="notification-content">
                                <div class="notification-title">{{ rand(3, 8) }} học viên mới đăng ký</div>
                                <div class="notification-time">{{ rand(15, 30) }} phút trước</div>
                            </div>
                        </div>

                        <div class="notification-item">
                            <div class="notification-dot bg-warning"></div>
                            <div class="notification-content">
                                <div class="notification-title">Cần cập nhật câu hỏi cho {{ rand(2, 5) }} đề thi</div>
                                <div class="notification-time">{{ rand(1, 3) }} giờ trước</div>
                            </div>
                        </div>

                        <div class="notification-item">
                            <div class="notification-dot bg-danger"></div>
                            <div class="notification-content">
                                <div class="notification-title">Server sắp đạt giới hạn dung lượng</div>
                                <div class="notification-time">{{ rand(2, 6) }} giờ trước</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-outline-primary btn-sm">Xem tất cả</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Welcome Card */
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.welcome-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.welcome-text {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 1.5rem;
}

.stat-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 500;
    margin-right: 1rem;
}

/* Stats Cards */
.stats-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-left: 4px solid;
    transition: transform 0.2s ease;
    position: relative;
    overflow: hidden;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.border-start-primary { border-left-color: #6366f1 !important; }
.border-start-success { border-left-color: #10b981 !important; }
.border-start-info { border-left-color: #3b82f6 !important; }
.border-start-warning { border-left-color: #f59e0b !important; }

.stats-card .stats-content {
    position: relative;
    z-index: 2;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    line-height: 1;
}

.stats-label {
    color: #6b7280;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.stats-change {
    font-size: 0.875rem;
    font-weight: 600;
}

.stats-icon {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #9ca3af;
}

/* Content Cards */
.content-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.content-card .card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #374151;
}

.card-body {
    padding: 1.5rem;
}

/* Activity Timeline */
.activity-timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 18px;
    top: 40px;
    bottom: -24px;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-marker {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.timeline-content {
    flex: 1;
    min-width: 0;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
}

.timeline-time {
    color: #9ca3af;
    font-size: 0.875rem;
    white-space: nowrap;
}

.timeline-body {
    color: #6b7280;
    font-size: 0.875rem;
}

.subject-name {
    font-weight: 500;
    color: #4f46e5;
}

.score-badge {
    background: #10b981;
    color: white;
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quick-action-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.quick-action-item:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    transform: translateX(4px);
    color: inherit;
    text-decoration: none;
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 1.125rem;
}

.action-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.125rem;
}

.action-desc {
    font-size: 0.875rem;
    color: #6b7280;
}

/* Notifications */
.notification-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.notification-item {
    display: flex;
    align-items: flex-start;
}

.notification-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-top: 6px;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-size: 0.875rem;
    color: #374151;
    margin-bottom: 0.25rem;
}

.notification-time {
    font-size: 0.75rem;
    color: #9ca3af;
}

/* Responsive */
@media (max-width: 768px) {
    .welcome-title {
        font-size: 1.5rem;
    }
    
    .stats-number {
        font-size: 2rem;
    }
    
    .timeline-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .timeline-time {
        margin-top: 0.25rem;
    }
}
</style>
@endpush