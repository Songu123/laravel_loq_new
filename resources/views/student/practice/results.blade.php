@extends('layouts.student-dashboard')

@section('title', 'Kết quả luyện tập')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.practice.index') }}">Luyện tập</a></li>
        <li class="breadcrumb-item active">Kết quả</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
.results-container {
    max-width: 1000px;
    margin: 0 auto;
}

.score-card {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    color: white;
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 1rem 3rem rgba(13, 110, 253, 0.3);
}

.score-number {
    font-size: 5rem;
    font-weight: 700;
    line-height: 1;
}

.score-label {
    font-size: 1.25rem;
    opacity: 0.9;
}

.stat-box {
    text-align: center;
    padding: 1.5rem;
    border-radius: 0.5rem;
    background: white;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
}

.stat-box .number {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
}

.stat-box .label {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.result-card {
    border-left: 4px solid;
    transition: all 0.3s;
}

.result-card.correct {
    border-left-color: #198754;
    background: #f0fdf4;
}

.result-card.wrong {
    border-left-color: #dc3545;
    background: #fff5f5;
}

.result-card:hover {
    transform: translateX(5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.answer-comparison {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.answer-box {
    padding: 1rem;
    border-radius: 0.5rem;
    border: 2px solid;
}

.answer-box.user {
    border-color: #dc3545;
    background: #fff5f5;
}

.answer-box.correct {
    border-color: #198754;
    background: #f0fdf4;
}

@media (max-width: 768px) {
    .answer-comparison {
        grid-template-columns: 1fr;
    }
    
    .score-number {
        font-size: 3rem;
    }
}

.improvement-tip {
    background: #fff8e1;
    border-left: 4px solid #ffc107;
    padding: 1rem;
    border-radius: 0.5rem;
}
</style>
@endpush

@section('student-dashboard-content')
<div class="results-container">
    <!-- Score Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="score-card">
                <div class="mb-3">
                    @if($results['accuracy'] >= 80)
                        <i class="bi bi-trophy-fill" style="font-size: 3rem;"></i>
                    @elseif($results['accuracy'] >= 60)
                        <i class="bi bi-hand-thumbs-up-fill" style="font-size: 3rem;"></i>
                    @else
                        <i class="bi bi-arrow-repeat" style="font-size: 3rem;"></i>
                    @endif
                </div>
                <div class="score-number">{{ $results['accuracy'] }}%</div>
                <div class="score-label mb-3">Độ chính xác</div>
                <h4>
                    @if($results['accuracy'] >= 80)
                        Xuất sắc! 🎉
                    @elseif($results['accuracy'] >= 60)
                        Làm tốt lắm! 👍
                    @else
                        Cần cố gắng thêm! 💪
                    @endif
                </h4>
                <p class="mb-0" style="opacity: 0.9;">
                    Bạn đã trả lời đúng {{ $results['correct'] }}/{{ $results['total'] }} câu hỏi
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-box">
                <div class="number text-primary">{{ $results['total'] }}</div>
                <div class="label">Tổng số câu</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-box">
                <div class="number text-success">{{ $results['correct'] }}</div>
                <div class="label">Câu đúng</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-box">
                <div class="number text-danger">{{ $results['wrong'] }}</div>
                <div class="label">Câu sai</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-box">
                <div class="number text-info">
                    <i class="bi bi-clock"></i>
                    {{ gmdate('i:s', $results['time_spent'] ?? 0) }}
                </div>
                <div class="label">Thời gian</div>
            </div>
        </div>
    </div>

    <!-- Improvement Tip -->
    @if($results['accuracy'] < 70)
    <div class="row mb-4">
        <div class="col-12">
            <div class="improvement-tip">
                <h6 class="mb-2">
                    <i class="bi bi-lightbulb-fill me-2"></i>
                    Gợi ý cải thiện:
                </h6>
                <p class="mb-0">
                    @if($results['accuracy'] < 50)
                        Bạn cần ôn tập kỹ hơn các kiến thức cơ bản. Hãy đọc lại tài liệu và làm thêm bài tập nhé!
                    @else
                        Bạn đang tiến bộ! Hãy tập trung vào các câu sai để cải thiện điểm số.
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <a href="{{ route('student.practice.index') }}" class="btn btn-primary btn-lg me-2 mb-2">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại luyện tập
                    </a>
                    @if($results['wrong'] > 0)
                    <form action="{{ route('student.practice.start') }}" method="GET" class="d-inline">
                        <input type="hidden" name="mode" value="retry">
                        <input type="hidden" name="questions" value="{{ json_encode($wrongQuestionIds ?? []) }}">
                        <button type="submit" class="btn btn-warning btn-lg mb-2">
                            <i class="bi bi-arrow-repeat me-1"></i> Luyện lại câu sai
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary btn-lg mb-2">
                        <i class="bi bi-house me-1"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Results -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2 text-primary"></i>
                        Chi tiết từng câu hỏi
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($results['details'] as $index => $detail)
                    <div class="result-card card mb-3 {{ $detail['is_correct'] ? 'correct' : 'wrong' }}">
                        <div class="card-body">
                            <!-- Question Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="bi bi-{{ $detail['is_correct'] ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }} me-2"></i>
                                    Câu {{ $index + 1 }}
                                </h6>
                                <span class="badge bg-{{ $detail['is_correct'] ? 'success' : 'danger' }}">
                                    {{ $detail['is_correct'] ? 'Đúng' : 'Sai' }}
                                </span>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-3">
                                <strong>Câu hỏi:</strong>
                                <p class="mb-0 mt-2">{!! nl2br(e($detail['question'])) !!}</p>
                            </div>

                            <!-- Answers Comparison -->
                            @if(!$detail['is_correct'])
                            <div class="answer-comparison mb-3">
                                <div class="answer-box user">
                                    <div class="mb-2">
                                        <i class="bi bi-x-circle text-danger me-1"></i>
                                        <strong>Câu trả lời của bạn:</strong>
                                    </div>
                                    <div>{{ $detail['user_answer'] ?? 'Không trả lời' }}</div>
                                </div>
                                <div class="answer-box correct">
                                    <div class="mb-2">
                                        <i class="bi bi-check-circle text-success me-1"></i>
                                        <strong>Đáp án đúng:</strong>
                                    </div>
                                    <div>{{ $detail['correct_answer'] }}</div>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-success mb-3">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Đáp án của bạn:</strong> {{ $detail['user_answer'] }}
                            </div>
                            @endif

                            <!-- Explanation -->
                            @if(isset($detail['explanation']) && $detail['explanation'])
                            <div class="alert alert-info mb-0">
                                <strong><i class="bi bi-lightbulb me-2"></i>Giải thích:</strong>
                                <p class="mb-0 mt-2">{{ $detail['explanation'] }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #f8f9fc;">
                <div class="card-body text-center p-4">
                    <h5 class="mb-3">
                        <i class="bi bi-star-fill text-warning me-2"></i>
                        Cảm ơn bạn đã hoàn thành bài luyện tập!
                    </h5>
                    <p class="text-muted mb-3">
                        Hãy tiếp tục luyện tập để cải thiện kết quả của bạn.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('student.practice.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-repeat me-1"></i> Luyện tập tiếp
                        </a>
                        <a href="{{ route('student.exams.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-text me-1"></i> Xem đề thi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to first wrong answer
    const firstWrong = document.querySelector('.result-card.wrong');
    if (firstWrong) {
        setTimeout(() => {
            firstWrong.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 500);
    }
    
    // Confetti for high score
    @if($results['accuracy'] >= 80)
    // Simple confetti effect using CSS
    const score = document.querySelector('.score-card');
    score.style.animation = 'pulse 0.5s ease-in-out';
    @endif
});
</script>
@endpush
