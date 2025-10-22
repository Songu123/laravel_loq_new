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
    <!-- Welcome Section with Quick Actions -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm" style="background: #0d6efd;">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2 class="mb-2">Xin chào, {{ auth()->user()->name }}! 👋</h2>
                            <p class="mb-3" style="opacity: 0.9;">
                                @php
                                    $hour = date('H');
                                    if ($hour < 12) echo "Chào buổi sáng! ";
                                    elseif ($hour < 18) echo "Chào buổi chiều! ";
                                    else echo "Chào buổi tối! ";
                                @endphp
                                Sẵn sàng chinh phục tri thức hôm nay?
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('student.exams.index') }}" class="btn btn-light btn-sm">
                                    <i class="bi bi-file-earmark-text me-1"></i> Xem đề thi
                                </a>
                                <a href="{{ route('student.history') }}" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-clock-history me-1"></i> Lịch sử
                                </a>
                                <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#practiceModal">
                                    <i class="bi bi-trophy me-1"></i> Luyện tập
                                </button>
                            </div>
                        </div>
                        <div class="text-end d-none d-md-block">
                            <div class="display-4" style="opacity: 0.2;">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Learning Streak & Today's Goal -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 mb-2">🔥</div>
                        <h5 class="mb-1">Chuỗi học tập</h5>
                        <h2 class="text-primary mb-0">{{ $learningStreak ?? 0 }} ngày</h2>
                        <small class="text-muted">Tiếp tục phát huy!</small>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ min(100, ($todayExamsCompleted ?? 0) * 33.33) }}%">
                        </div>
                    </div>
                    <small class="text-muted">
                        Hôm nay: {{ $todayExamsCompleted ?? 0 }}/3 bài thi
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Đề thi khả dụng</div>
                            <div class="h3 mb-0 font-weight-bold text-primary">{{ $availableExams ?? 0 }}</div>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> +{{ $newExamsThisWeek ?? 0 }} tuần này
                            </small>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-file-earmark-text text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Đã hoàn thành</div>
                            <div class="h3 mb-0 font-weight-bold text-success">{{ $completedExams ?? 0 }}</div>
                            <small class="text-muted">
                                Tổng {{ $totalExams ?? 0 }} đề
                            </small>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Điểm trung bình</div>
                            <div class="h3 mb-0 font-weight-bold text-warning">{{ number_format($averageScore ?? 0, 1) }}</div>
                            <small class="text-{{ ($averageScore ?? 0) >= 80 ? 'success' : 'muted' }}">
                                <i class="bi bi-{{ ($averageScore ?? 0) >= ($lastMonthAverage ?? 0) ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ abs(($averageScore ?? 0) - ($lastMonthAverage ?? 0)) > 0 ? number_format(abs(($averageScore ?? 0) - ($lastMonthAverage ?? 0)), 1) : '0' }} so với tháng trước
                            </small>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(255, 193, 7, 0.1);">
                            <i class="bi bi-bar-chart text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Xếp hạng</div>
                            <div class="h3 mb-0 font-weight-bold text-info">#{{ $ranking ?? '-' }}</div>
                            <small class="text-muted">
                                Top {{ $rankPercentage ?? '0' }}%
                            </small>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(13, 202, 240, 0.1);">
                            <i class="bi bi-trophy text-info" style="font-size: 1.5rem;"></i>
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

    <!-- Categories & Achievements Section -->
    <div class="row mt-4">
        <!-- Categories -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-folder me-2"></i>Danh mục đề thi</h6>
                </div>
                <div class="card-body">
                    @if(isset($categories) && $categories->count() > 0)
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <a href="{{ route('student.exams.index', ['category' => $category->id]) }}" 
                                       class="text-decoration-none">
                                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                                            <div class="card-body text-center p-3">
                                                <div class="mb-2" style="font-size: 2rem; color: {{ $category->color ?? '#0d6efd' }};">
                                                    <i class="bi bi-folder-fill"></i>
                                                </div>
                                                <h6 class="mb-1 small">{{ $category->name }}</h6>
                                                <small class="text-muted">{{ $category->exams_count ?? 0 }} đề</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0 mt-2">Chưa có danh mục nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Achievements & Badges -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Thành tích</h6>
                </div>
                <div class="card-body">
                    <div class="achievement-item mb-3 p-3 rounded" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);">
                        <div class="d-flex align-items-center">
                            <div class="achievement-icon me-3" style="font-size: 2.5rem;">🏆</div>
                            <div>
                                <h6 class="mb-0">Người mới</h6>
                                <small class="text-dark opacity-75">Hoàn thành 1 bài thi</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="achievement-item mb-3 p-3 rounded bg-light">
                        <div class="d-flex align-items-center">
                            <div class="achievement-icon me-3" style="font-size: 2.5rem; filter: grayscale(1); opacity: 0.5;">🎯</div>
                            <div>
                                <h6 class="mb-0 text-muted">Học giỏi</h6>
                                <small class="text-muted">Đạt điểm 90+ trong 5 bài</small>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar" style="width: {{ min(100, ($highScoreCount ?? 0) * 20) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="achievement-item mb-3 p-3 rounded bg-light">
                        <div class="d-flex align-items-center">
                            <div class="achievement-icon me-3" style="font-size: 2.5rem; filter: grayscale(1); opacity: 0.5;">🔥</div>
                            <div>
                                <h6 class="mb-0 text-muted">Siêng năng</h6>
                                <small class="text-muted">Học liên tục 7 ngày</small>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: {{ min(100, ($learningStreak ?? 0) * 14.28) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Study Analytics Chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Biểu đồ tiến độ học tập</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active">7 ngày</button>
                        <button type="button" class="btn btn-outline-primary">30 ngày</button>
                        <button type="button" class="btn btn-outline-primary">3 tháng</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="studyChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Practice Mode Modal -->
<div class="modal fade" id="practiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="bi bi-trophy text-warning me-2"></i>Chế độ luyện tập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Chọn chế độ luyện tập của bạn:</p>
                
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-lightning-charge text-warning me-2"></i>Luyện tập nhanh</h6>
                                <small class="text-muted">10 câu hỏi ngẫu nhiên - 10 phút</small>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                    
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-bookmark text-primary me-2"></i>Ôn tập theo chủ đề</h6>
                                <small class="text-muted">Chọn chủ đề để ôn luyện</small>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                    
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-x-circle text-danger me-2"></i>Ôn câu sai</h6>
                                <small class="text-muted">Luyện lại các câu đã sai</small>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
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

.list-group-item-action:hover {
    background-color: #f8f9fc;
}

.stat-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.1) !important;
}

.achievement-item {
    transition: all 0.3s ease;
}

.achievement-item:hover {
    transform: scale(1.02);
}
</style>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Study Progress Chart
    const ctx = document.getElementById('studyChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                datasets: [{
                    label: 'Số bài thi',
                    data: [2, 3, 1, 4, 2, 5, 3],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Điểm trung bình',
                    data: [75, 80, 70, 85, 78, 90, 82],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số bài thi'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Điểm TB'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
