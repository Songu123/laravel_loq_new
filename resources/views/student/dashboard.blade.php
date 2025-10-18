@extends('layouts.student-dashboard')

@section('title', 'Dashboard')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</nav>
@endsection

@section('student-dashboard-content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <h2 class="mb-2">Xin chào, {{ auth()->user()->name }}! 👋</h2>
                    <p class="mb-0 opacity-75">Sẵn sàng cho bài thi mới hôm nay?</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">Đề thi khả dụng</div>
                            <div class="h3 mb-0 font-weight-bold text-primary">{{ $availableExams ?? 0 }}</div>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">Đã hoàn thành</div>
                            <div class="h3 mb-0 font-weight-bold text-success">{{ $completedExams ?? 0 }}</div>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">Điểm trung bình</div>
                            <div class="h3 mb-0 font-weight-bold text-warning">{{ $averageScore ?? 0 }}<small>/100</small></div>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-bar-chart" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">Xếp hạng</div>
                            <div class="h3 mb-0 font-weight-bold text-info">#{{ $ranking ?? '-' }}</div>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-trophy" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Available Exams -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Đề thi mới</h6>
                    <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-outline-primary">
                        Xem tất cả <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($recentExams) && $recentExams->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentExams as $exam)
                                <div class="list-group-item px-0 border-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $exam->title }}</h6>
                                            <p class="text-muted small mb-2">{{ Str::limit($exam->description, 100) }}</p>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-question-circle"></i> {{ $exam->questions_count }} câu
                                                </span>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-star"></i> {{ $exam->total_marks }} điểm
                                                </span>
                                                <span class="badge bg-info">
                                                    <i class="bi bi-clock"></i> {{ $exam->duration_minutes }} phút
                                                </span>
                                                <span class="badge bg-{{ $exam->difficulty_level === 'easy' ? 'success' : ($exam->difficulty_level === 'medium' ? 'warning' : 'danger') }}">
                                                    {{ $exam->difficulty_level_text }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <a href="{{ route('student.exams.show', $exam) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-play-circle"></i> Bắt đầu
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-3">Chưa có đề thi mới</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activity & Quick Stats -->
        <div class="col-lg-4">
            <!-- Recent Results -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Kết quả gần đây</h6>
                </div>
                <div class="card-body">
                    @if(isset($recentResults) && $recentResults->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentResults as $result)
                                <a href="{{ route('student.results.show', $result) }}" 
                                   class="list-group-item list-group-item-action px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold small">{{ Str::limit($result->exam->title, 25) }}</div>
                                            <small class="text-muted">{{ $result->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="badge bg-{{ $result->percentage >= 80 ? 'success' : ($result->percentage >= 50 ? 'warning' : 'danger') }}">
                                                {{ number_format($result->percentage, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted small mb-0 mt-2">Chưa có kết quả</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progress Chart -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Tiến độ học tập</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Hoàn thành</small>
                            <small class="text-muted">{{ $completedExams ?? 0 }}/{{ $totalExams ?? 0 }}</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $totalExams > 0 ? ($completedExams / $totalExams * 100) : 0 }}%">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Điểm đạt (≥50%)</small>
                            <small class="text-muted">{{ $passedExams ?? 0 }}/{{ $completedExams ?? 0 }}</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $completedExams > 0 ? ($passedExams / $completedExams * 100) : 0 }}%">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <div class="text-muted small mb-2">Điểm cao nhất</div>
                        <h3 class="text-primary mb-0">{{ $highestScore ?? 0 }}<small>/100</small></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-folder me-2"></i>Danh mục đề thi</h6>
                </div>
                <div class="card-body">
                    @if(isset($categories) && $categories->count() > 0)
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-4 col-lg-3 mb-3">
                                    <a href="{{ route('student.exams.index', ['category' => $category->id]) }}" 
                                       class="text-decoration-none">
                                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                                            <div class="card-body text-center">
                                                <div class="mb-3" style="font-size: 2rem; color: {{ $category->color ?? '#4e73df' }};">
                                                    <i class="bi bi-folder-fill"></i>
                                                </div>
                                                <h6 class="mb-1">{{ $category->name }}</h6>
                                                <small class="text-muted">{{ $category->exams_count ?? 0 }} đề thi</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">Chưa có danh mục nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}

.bg-gradient {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}

.list-group-item-action:hover {
    background-color: #f8f9fc;
}
</style>
@endsection
