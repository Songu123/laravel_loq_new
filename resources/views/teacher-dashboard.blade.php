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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalExams }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ongoingExams }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedAttempts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-bookmark-fill me-2"></i>Danh mục của tôi
                    </h6>
                    <a href="{{ route('teacher.categories.index') }}" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-right-circle me-1"></i>Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    @if($categories->count() > 0)
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-xl-3 col-md-6 mb-3">
                                    <div class="card category-card h-100" style="border-left: 4px solid {{ $category->color ?? '#6366f1' }};">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="category-icon me-3" style="background-color: {{ $category->color ?? '#6366f1' }}20;">
                                                    <i class="bi {{ $category->icon ?? 'bi-folder' }} fa-2x" style="color: {{ $category->color ?? '#6366f1' }};"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold">{{ $category->name }}</h6>
                                                    @if($category->description)
                                                        <small class="text-muted">{{ Str::limit($category->description, 30) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="category-stats">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small">
                                                        <i class="bi bi-file-earmark-text me-1"></i>Đề thi:
                                                    </span>
                                                    <strong class="text-primary">{{ $category->teacher_exams_count }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small">
                                                        <i class="bi bi-question-circle me-1"></i>Câu hỏi:
                                                    </span>
                                                    <strong class="text-success">{{ $category->questions_count }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted small">
                                                        <i class="bi bi-people me-1"></i>Học sinh:
                                                    </span>
                                                    <strong class="text-info">{{ $category->students_count }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-folder-x fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Bạn chưa có danh mục nào. <a href="{{ route('teacher.categories.create') }}">Tạo danh mục mới</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activities -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-lightning-fill me-2"></i>Thao tác nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary btn-block">
                                <i class="bi bi-plus-circle me-2"></i>Tạo đề thi mới
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('teacher.classes.index') }}" class="btn btn-info btn-block">
                                <i class="bi bi-people me-2"></i>Quản lý lớp học
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('teacher.categories.index') }}" class="btn btn-success btn-block">
                                <i class="bi bi-tags me-2"></i>Quản lý danh mục
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('teacher.exams.index') }}" class="btn btn-warning btn-block">
                                <i class="bi bi-graph-up me-2"></i>Xem đề thi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-activity me-2"></i>Hoạt động gần đây
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentAttempts->count() > 0)
                        @foreach($recentAttempts->take(4) as $attempt)
                            <div class="activity-item d-flex align-items-center mb-3">
                                <div class="activity-icon bg-{{ $attempt->percentage >= 80 ? 'success' : ($attempt->percentage >= 50 ? 'warning' : 'danger') }} me-3">
                                    <i class="bi bi-person-check text-white"></i>
                                </div>
                                <div class="activity-content flex-grow-1">
                                    <div class="activity-title">
                                        <strong>{{ $attempt->user->name }}</strong> đã hoàn thành 
                                        <span class="text-primary">{{ Str::limit($attempt->exam->title, 30) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ $attempt->created_at->diffForHumans() }}</small>
                                        <span class="badge bg-{{ $attempt->percentage >= 80 ? 'success' : ($attempt->percentage >= 50 ? 'warning' : 'danger') }}">
                                            {{ round($attempt->percentage, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có hoạt động nào gần đây</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Exams -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-clock-history me-2"></i>Đề thi gần đây
                    </h6>
                    <a href="{{ route('teacher.exams.index') }}" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-right-circle me-1"></i>Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    @if($recentExams->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tên đề thi</th>
                                        <th>Danh mục</th>
                                        <th>Câu hỏi</th>
                                        <th>Lượt thi</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentExams as $exam)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                                    <strong>{{ $exam->title }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                @if($exam->category)
                                                    <span class="badge" style="background-color: {{ $exam->category->color ?? '#6366f1' }};">
                                                        <i class="bi {{ $exam->category->icon ?? 'bi-folder' }} me-1"></i>
                                                        {{ $exam->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $exam->questions_count }} câu</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $exam->attempts_count }} lượt</span>
                                            </td>
                                            <td>
                                                @if($exam->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Hoạt động
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-pause-circle me-1"></i>Tạm dừng
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $exam->created_at->format('d/m/Y') }}</small>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-sm btn-outline-primary" title="Xem">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('teacher.exams.edit', $exam) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-x fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Bạn chưa có đề thi nào. <a href="{{ route('teacher.exams.create') }}">Tạo đề thi mới</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-section {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
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
    flex-shrink: 0;
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

/* Category Card Styles */
.category-card {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

.category-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-stats {
    border-top: 1px solid #e5e7eb;
    padding-top: 0.75rem;
}

/* Table hover effects */
.table-hover tbody tr {
    transition: background-color 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

/* Badge custom colors */
.badge {
    font-weight: 500;
    padding: 0.375rem 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .category-icon {
        width: 50px;
        height: 50px;
    }
    
    .welcome-section h1 {
        font-size: 1.5rem;
    }
}
</style>
@endsection