@extends('layouts.student-dashboard')

@section('title', 'Luyện tập')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Luyện tập</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
.practice-card {
    transition: all 0.3s;
    cursor: pointer;
    border: 2px solid transparent;
}

.practice-card:hover {
    transform: translateY(-5px);
    border-color: #0d6efd;
    box-shadow: 0 0.5rem 1rem rgba(13, 110, 253, 0.2);
}

.chart-container {
    position: relative;
    height: 300px;
}

.improvement-badge {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    border-left: 4px solid;
}

.improvement-badge.high {
    background-color: #fff5f5;
    border-left-color: #dc3545;
}

.improvement-badge.medium {
    background-color: #fff8e1;
    border-left-color: #ffc107;
}

.improvement-badge.low {
    background-color: #e7f5ff;
    border-left-color: #0dcaf0;
}

.progress-day {
    text-align: center;
    padding: 0.5rem;
    border-radius: 0.35rem;
    background: #f8f9fc;
}

.progress-day.active {
    background: #e7f1ff;
    border: 2px solid #0d6efd;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.5rem;
}
</style>
@endpush

@section('student-dashboard-content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #0d6efd;">
                <div class="card-body p-4 text-white">
                    <h2 class="mb-2"><i class="bi bi-trophy-fill me-2"></i>Luyện tập & Cải thiện</h2>
                    <p class="mb-0" style="opacity: 0.9;">
                        Phân tích kết quả làm bài và luyện tập để cải thiện điểm số
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-number text-primary">{{ $stats['total_attempts'] }}</div>
                    <div class="stat-label">Tổng số bài thi</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-number text-success">{{ $stats['correct_answers'] }}</div>
                    <div class="stat-label">Câu trả lời đúng</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-number text-danger">{{ $stats['wrong_answers'] }}</div>
                    <div class="stat-label">Câu trả lời sai</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-number text-warning">{{ $stats['accuracy_rate'] }}%</div>
                    <div class="stat-label">Độ chính xác</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Practice Modes -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3"><i class="bi bi-play-circle me-2"></i>Chế độ luyện tập</h4>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card practice-card border-0 shadow-sm h-100" 
                 onclick="showPracticeModal('wrong')">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-2">Luyện câu sai</h5>
                    <p class="text-muted small mb-3">
                        Ôn lại các câu hỏi bạn đã trả lời sai
                    </p>
                    <span class="badge bg-danger">{{ $stats['wrong_answers'] }} câu</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card practice-card border-0 shadow-sm h-100" 
                 onclick="showPracticeModal('category')">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-folder text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-2">Luyện theo chủ đề</h5>
                    <p class="text-muted small mb-3">
                        Chọn chủ đề bạn muốn luyện tập
                    </p>
                    <span class="badge bg-primary">{{ $categoryPerformance->count() }} chủ đề</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card practice-card border-0 shadow-sm h-100" 
                 onclick="showPracticeModal('random')">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-shuffle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-2">Luyện ngẫu nhiên</h5>
                    <p class="text-muted small mb-3">
                        Câu hỏi ngẫu nhiên từ tất cả đề thi
                    </p>
                    <span class="badge bg-success">Tất cả</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Category Performance -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Hiệu suất theo chủ đề</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Type Performance -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Hiệu suất theo loại câu hỏi</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="questionTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Difficulty Analysis & Weekly Progress -->
    <div class="row mb-4">
        <!-- Difficulty Analysis -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-speedometer2 me-2 text-warning"></i>Phân tích theo độ khó</h5>
                </div>
                <div class="card-body">
                    @foreach($difficultyAnalysis as $difficulty)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge bg-{{ $difficulty['level'] == 'easy' ? 'success' : ($difficulty['level'] == 'medium' ? 'warning' : 'danger') }}">
                                    {{ $difficulty['name'] }}
                                </span>
                                <span class="text-muted small ms-2">{{ $difficulty['total'] }} câu</span>
                            </div>
                            <strong class="text-primary">{{ $difficulty['accuracy'] }}%</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $difficulty['accuracy'] >= 70 ? 'success' : ($difficulty['accuracy'] >= 50 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $difficulty['accuracy'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Weekly Progress -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-calendar-week me-2 text-info"></i>Tiến độ 7 ngày</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($weeklyProgress as $day)
                        <div class="col">
                            <div class="progress-day {{ $day['attempts'] > 0 ? 'active' : '' }}">
                                <div class="small fw-bold mb-1">{{ $day['date'] }}</div>
                                <div class="h5 mb-1 text-primary">{{ $day['attempts'] }}</div>
                                <div class="small text-muted">bài thi</div>
                                @if($day['attempts'] > 0)
                                <div class="small text-success mt-1">
                                    <i class="bi bi-check-circle"></i> {{ $day['accuracy'] }}%
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Improvement Areas -->
    @if(count($improvementAreas) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-lightbulb me-2 text-warning"></i>Các mảng cần cải thiện</h5>
                </div>
                <div class="card-body">
                    @foreach($improvementAreas as $area)
                    <div class="improvement-badge {{ $area['priority'] }} mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">
                                    <i class="bi bi-{{ $area['type'] == 'category' ? 'folder' : 'question-circle' }} me-2"></i>
                                    {{ $area['name'] }}
                                </h6>
                                <p class="mb-0 small">{{ $area['suggestion'] }}</p>
                            </div>
                            <div class="text-end">
                                <div class="h4 mb-0">{{ $area['accuracy'] }}%</div>
                                <span class="badge bg-{{ $area['priority'] == 'high' ? 'danger' : 'warning' }}">
                                    {{ $area['priority'] == 'high' ? 'Ưu tiên cao' : 'Ưu tiên trung bình' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Practice Sessions -->
    @if($recentPractice->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-secondary"></i>Lịch sử luyện tập gần đây</h5>
                        <a href="{{ route('student.practice.wrong-answers') }}" class="btn btn-sm btn-outline-primary">
                            Xem tất cả câu sai
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Đề thi</th>
                                    <th>Thời gian</th>
                                    <th>Điểm số</th>
                                    <th>Độ chính xác</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPractice as $practice)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $practice->exam->title }}</div>
                                        <small class="text-muted">{{ $practice->exam->category->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $practice->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $practice->score >= 70 ? 'success' : ($practice->score >= 50 ? 'warning' : 'danger') }}">
                                            {{ $practice->score }}/{{ $practice->exam->total_marks }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $total = $practice->answers->count();
                                            $correct = $practice->answers->where('is_correct', true)->count();
                                            $accuracy = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
                                        @endphp
                                        <div class="progress" style="width: 100px; height: 8px;">
                                            <div class="progress-bar bg-{{ $accuracy >= 70 ? 'success' : ($accuracy >= 50 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $accuracy }}%"></div>
                                        </div>
                                        <small>{{ $accuracy }}%</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('student.results.show', $practice->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Practice Modal -->
<div class="modal fade" id="practiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-play-circle me-2"></i>Bắt đầu luyện tập</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('student.practice.start') }}" method="GET">
                <div class="modal-body">
                    <input type="hidden" name="mode" id="practiceMode">
                    
                    <div id="categorySelect" class="mb-3" style="display: none;">
                        <label class="form-label">Chọn chủ đề</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($categoryPerformance as $category)
                            <option value="{{ $category['name'] }}">
                                {{ $category['name'] }} ({{ $category['accuracy'] }}% - {{ $category['total'] }} câu)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Số lượng câu hỏi</label>
                        <select name="limit" class="form-select">
                            <option value="5">5 câu</option>
                            <option value="10" selected>10 câu</option>
                            <option value="15">15 câu</option>
                            <option value="20">20 câu</option>
                            <option value="30">30 câu</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <small id="practiceInfo">Chọn chế độ luyện tập phù hợp với bạn</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-play-fill me-1"></i>Bắt đầu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Category Performance Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($categoryPerformance->pluck('name')) !!},
        datasets: [{
            label: 'Độ chính xác (%)',
            data: {!! json_encode($categoryPerformance->pluck('accuracy')) !!},
            backgroundColor: 'rgba(13, 110, 253, 0.8)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 2,
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

// Question Type Performance Chart
const typeCtx = document.getElementById('questionTypeChart').getContext('2d');
const typeChart = new Chart(typeCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($questionTypePerformance->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($questionTypePerformance->pluck('total')) !!},
            backgroundColor: [
                'rgba(13, 110, 253, 0.8)',
                'rgba(25, 135, 84, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(220, 53, 69, 0.8)',
            ],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Practice Modal
let practiceModal;
document.addEventListener('DOMContentLoaded', function() {
    practiceModal = new bootstrap.Modal(document.getElementById('practiceModal'));
});

function showPracticeModal(mode) {
    document.getElementById('practiceMode').value = mode;
    
    const categorySelect = document.getElementById('categorySelect');
    const practiceInfo = document.getElementById('practiceInfo');
    
    if (mode === 'category') {
        categorySelect.style.display = 'block';
        practiceInfo.textContent = 'Chọn chủ đề bạn muốn luyện tập để cải thiện';
    } else {
        categorySelect.style.display = 'none';
        
        if (mode === 'wrong') {
            practiceInfo.textContent = 'Luyện tập lại các câu hỏi bạn đã trả lời sai để cải thiện';
        } else {
            practiceInfo.textContent = 'Luyện tập với câu hỏi ngẫu nhiên từ tất cả các đề thi';
        }
    }
    
    practiceModal.show();
}
</script>
@endpush
