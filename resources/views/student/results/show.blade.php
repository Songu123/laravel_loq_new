@extends('layouts.student-dashboard')

@section('title', 'Kết quả thi - ' . $attempt->exam->title)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.history') }}">Lịch sử thi</a></li>
        <li class="breadcrumb-item active">Kết quả</li>
    </ol>
</nav>
@endsection

@section('student-dashboard-content')
<div class="container-fluid">
    <!-- Success Animation on Load -->
    <div id="successAnimation" class="text-center mb-4" style="display: none;">
        <div class="success-checkmark">
            <div class="check-icon">
                <span class="icon-line line-tip"></span>
                <span class="icon-line line-long"></span>
                <div class="icon-circle"></div>
                <div class="icon-fix"></div>
            </div>
        </div>
    </div>

    <!-- Result Header -->
    <div class="card mb-4 border-0 shadow-lg result-header-card">
        <div class="card-body p-4 text-center" style="background: linear-gradient(135deg, {{ $attempt->percentage >= 80 ? '#1cc88a' : ($attempt->percentage >= 50 ? '#f6c23e' : '#e74a3b') }} 0%, {{ $attempt->percentage >= 80 ? '#36b9cc' : ($attempt->percentage >= 50 ? '#f093fb' : '#f5576c') }} 100%); color: white;">
            <div class="mb-3 trophy-animation">
                @if($attempt->percentage >= 80)
                    <i class="bi bi-trophy-fill" style="font-size: 4rem;"></i>
                @elseif($attempt->percentage >= 50)
                    <i class="bi bi-star-fill" style="font-size: 4rem;"></i>
                @else
                    <i class="bi bi-exclamation-circle-fill" style="font-size: 4rem;"></i>
                @endif
            </div>
            
            <h2 class="mb-2">{{ $attempt->exam->title }}</h2>
            
            <div class="display-4 fw-bold mb-2 score-animation">{{ number_format($attempt->percentage, 1) }}%</div>
            
            <h4 class="mb-3">
                @if($attempt->percentage >= 80)
                    🎉 Xuất sắc!
                @elseif($attempt->percentage >= 50)
                    👍 Đạt yêu cầu
                @else
                    💪 Cố gắng hơn nhé!
                @endif
            </h4>
            
            <p class="mb-0 opacity-75">
                Hoàn thành lúc: {{ $attempt->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 mb-4">
            <!-- Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Tổng quan</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="summary-stat">
                                <div class="stat-icon text-success mb-2">
                                    <i class="bi bi-check-circle" style="font-size: 2.5rem;"></i>
                                </div>
                                <h3 class="mb-1">{{ $attempt->correct_answers }}</h3>
                                <small class="text-muted">Câu đúng</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="summary-stat">
                                <div class="stat-icon text-danger mb-2">
                                    <i class="bi bi-x-circle" style="font-size: 2.5rem;"></i>
                                </div>
                                <h3 class="mb-1">{{ $attempt->total_questions - $attempt->correct_answers }}</h3>
                                <small class="text-muted">Câu sai</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="summary-stat">
                                <div class="stat-icon text-primary mb-2">
                                    <i class="bi bi-star" style="font-size: 2.5rem;"></i>
                                </div>
                                <h3 class="mb-1">{{ $attempt->score }}</h3>
                                <small class="text-muted">Điểm đạt được</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="summary-stat">
                                <div class="stat-icon text-info mb-2">
                                    <i class="bi bi-clock" style="font-size: 2.5rem;"></i>
                                </div>
                                <h3 class="mb-1">{{ floor($attempt->time_spent / 60) }}</h3>
                                <small class="text-muted">Phút</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Answers -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ol"></i> Chi tiết câu trả lời</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="toggleAllExplanations(true)">
                            <i class="bi bi-eye"></i> Hiện tất cả giải thích
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleAllExplanations(false)">
                            <i class="bi bi-eye-slash"></i> Ẩn tất cả
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($attempt->answers as $index => $answer)
                        @php
                            $question = $answer->question;
                            $isCorrect = $answer->is_correct;
                        @endphp
                        
                        <div class="question-result mb-4 p-3 border rounded {{ $isCorrect ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10' }}">
                            <!-- Question Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="mb-0">
                                    <span class="badge bg-primary me-2">Câu {{ $index + 1 }}</span>
                                    <span class="badge bg-{{ $isCorrect ? 'success' : 'danger' }}">
                                        <i class="bi bi-{{ $isCorrect ? 'check-circle' : 'x-circle' }}"></i>
                                        {{ $isCorrect ? 'Đúng' : 'Sai' }}
                                    </span>
                                    <span class="badge bg-info">{{ $question->marks }} điểm</span>
                                </h6>
                                <small class="text-muted">{{ $question->question_type_text }}</small>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-3">
                                <strong>{!! nl2br(e($question->question_text)) !!}</strong>
                            </div>

                            @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                                <!-- Answers for Multiple Choice / True False -->
                                <div class="answers-review">
                                    @foreach($question->answers as $qAnswer)
                                        @php
                                            $isSelected = $answer->answer_id == $qAnswer->id;
                                            $isCorrectAnswer = $qAnswer->is_correct;
                                        @endphp
                                        
                                        <div class="answer-option-review p-2 mb-2 rounded border
                                            {{ $isSelected && $isCorrectAnswer ? 'border-success bg-success bg-opacity-25' : '' }}
                                            {{ $isSelected && !$isCorrectAnswer ? 'border-danger bg-danger bg-opacity-25' : '' }}
                                            {{ !$isSelected && $isCorrectAnswer ? 'border-success border-2' : '' }}">
                                            <div class="d-flex align-items-center">
                                                @if($isSelected)
                                                    <i class="bi bi-{{ $isCorrectAnswer ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }} me-2"></i>
                                                @elseif($isCorrectAnswer)
                                                    <i class="bi bi-check-circle text-success me-2"></i>
                                                @else
                                                    <i class="bi bi-circle me-2 text-muted"></i>
                                                @endif
                                                
                                                <span class="{{ $isCorrectAnswer ? 'fw-bold' : '' }}">
                                                    {{ $qAnswer->answer_text }}
                                                </span>
                                                
                                                @if($isSelected)
                                                    <span class="ms-2 badge bg-secondary">Bạn chọn</span>
                                                @endif
                                                @if($isCorrectAnswer)
                                                    <span class="ms-2 badge bg-success">Đáp án đúng</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Text Answer (Short Answer / Essay) -->
                                <div class="mb-3">
                                    <div class="alert alert-secondary mb-2">
                                        <strong>Câu trả lời của bạn:</strong>
                                        <p class="mb-0 mt-2">{{ $answer->answer_text ?: '(Không trả lời)' }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Explanation -->
                            @if($question->explanation)
                                <div class="explanation-section mt-3">
                                    <button class="btn btn-sm btn-outline-info w-100" type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#explanation-{{ $index }}"
                                            onclick="toggleExplanationIcon(this)">
                                        <i class="bi bi-lightbulb"></i> 
                                        <span class="toggle-text">Xem giải thích</span>
                                        <i class="bi bi-chevron-down ms-2 toggle-icon"></i>
                                    </button>
                                    <div class="collapse explanation-content mt-2" id="explanation-{{ $index }}">
                                        <div class="alert alert-info mb-0">
                                            <h6 class="alert-heading"><i class="bi bi-lightbulb-fill"></i> Giải thích chi tiết</h6>
                                            <p class="mb-0">{!! nl2br(e($question->explanation)) !!}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Score Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-trophy"></i> Điểm số</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="progress" style="height: 150px; border-radius: 75px;">
                            <div class="progress-bar bg-{{ $attempt->percentage >= 80 ? 'success' : ($attempt->percentage >= 50 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $attempt->percentage }}%; writing-mode: vertical-lr; text-orientation: upright;">
                                <div class="p-3">
                                    <h2 class="mb-0">{{ number_format($attempt->percentage, 1) }}%</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mb-2">{{ $attempt->score }}/{{ $attempt->exam->total_marks }}</h4>
                    <p class="text-muted mb-0">Tổng điểm</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Thống kê</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Độ chính xác</small>
                            <small class="fw-bold">{{ number_format($attempt->percentage, 1) }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $attempt->percentage >= 80 ? 'success' : ($attempt->percentage >= 50 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $attempt->percentage }}%"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tổng câu hỏi</span>
                        <strong>{{ $attempt->total_questions }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-success">Trả lời đúng</span>
                        <strong>{{ $attempt->correct_answers }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-danger">Trả lời sai</span>
                        <strong>{{ $attempt->total_questions - $attempt->correct_answers }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Thời gian làm bài</span>
                        <strong>{{ floor($attempt->time_spent / 60) }}:{{ str_pad($attempt->time_spent % 60, 2, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.exams.show', $attempt->exam) }}" 
                           class="btn btn-primary">
                            <i class="bi bi-arrow-repeat"></i> Thi lại
                        </a>
                        <a href="{{ route('student.history') }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Về lịch sử
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-primary">
                            <i class="bi bi-printer"></i> In kết quả
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Success Animation */
@keyframes checkmark {
    0% { stroke-dashoffset: 50px; }
    100% { stroke-dashoffset: 0; }
}

@keyframes checkmark-circle {
    0% { stroke-dashoffset: 166px; }
    100% { stroke-dashoffset: 0; }
}

.success-checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.success-checkmark .check-icon {
    width: 80px;
    height: 80px;
    position: relative;
    border-radius: 50%;
    box-sizing: content-box;
    border: 4px solid #1cc88a;
}

.success-checkmark .check-icon::before {
    top: 3px;
    left: -2px;
    width: 30px;
    transform-origin: 100% 50%;
    border-radius: 100px 0 0 100px;
}

.success-checkmark .check-icon::after {
    top: 0;
    left: 30px;
    width: 60px;
    transform-origin: 0 50%;
    border-radius: 0 100px 100px 0;
    animation: checkmark-circle 0.6s ease;
}

.success-checkmark .check-icon .icon-line {
    height: 5px;
    background-color: #1cc88a;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.success-checkmark .check-icon .icon-line.line-tip {
    top: 46px;
    left: 14px;
    width: 25px;
    transform: rotate(45deg);
    animation: checkmark 0.3s ease 0.6s backwards;
}

.success-checkmark .check-icon .icon-line.line-long {
    top: 38px;
    right: 8px;
    width: 47px;
    transform: rotate(-45deg);
    animation: checkmark 0.6s ease 0.75s backwards;
}

.success-checkmark .check-icon .icon-circle {
    top: -4px;
    left: -4px;
    z-index: 10;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    position: absolute;
    box-sizing: content-box;
    border: 4px solid rgba(28, 200, 138, .5);
}

/* Header Card Animation */
.result-header-card {
    animation: slideInDown 0.5s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.trophy-animation {
    animation: bounce 1s ease-in-out infinite alternate;
}

@keyframes bounce {
    from { transform: translateY(0); }
    to { transform: translateY(-10px); }
}

.score-animation {
    animation: scaleIn 0.5s ease-out 0.3s backwards;
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.5);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Answer Review Animations */
.question-result {
    animation: fadeInUp 0.5s ease-out;
    animation-fill-mode: both;
}

.question-result:nth-child(1) { animation-delay: 0.1s; }
.question-result:nth-child(2) { animation-delay: 0.2s; }
.question-result:nth-child(3) { animation-delay: 0.3s; }
.question-result:nth-child(4) { animation-delay: 0.4s; }
.question-result:nth-child(5) { animation-delay: 0.5s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Toggle Icon Animation */
.toggle-icon {
    transition: transform 0.3s ease;
}

.collapsed .toggle-icon {
    transform: rotate(0deg);
}

button:not(.collapsed) .toggle-icon {
    transform: rotate(180deg);
}

/* Explanation Content */
.explanation-content {
    transition: all 0.3s ease;
}

@media print {
    .sidebar, .topbar, .btn, nav, .explanation-section button {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
    }
    
    .explanation-content {
        display: block !important;
    }
}

.answer-option-review {
    transition: all 0.3s;
}
</style>

<script>
// Show success animation on page load
document.addEventListener('DOMContentLoaded', function() {
    @if($attempt->percentage >= 50)
    const successAnim = document.getElementById('successAnimation');
    successAnim.style.display = 'block';
    
    setTimeout(() => {
        successAnim.style.transition = 'opacity 0.5s';
        successAnim.style.opacity = '0';
        setTimeout(() => {
            successAnim.style.display = 'none';
        }, 500);
    }, 2000);
    @endif
});

// Toggle explanation icon
function toggleExplanationIcon(button) {
    const icon = button.querySelector('.toggle-icon');
    const text = button.querySelector('.toggle-text');
    
    if (button.classList.contains('collapsed')) {
        text.textContent = 'Xem giải thích';
    } else {
        text.textContent = 'Ẩn giải thích';
    }
}

// Toggle all explanations
function toggleAllExplanations(show) {
    const collapseElements = document.querySelectorAll('.explanation-content');
    const buttons = document.querySelectorAll('.explanation-section button[data-bs-toggle="collapse"]');
    
    collapseElements.forEach((element, index) => {
        const bsCollapse = new bootstrap.Collapse(element, { toggle: false });
        
        if (show) {
            bsCollapse.show();
            buttons[index].classList.remove('collapsed');
            buttons[index].querySelector('.toggle-text').textContent = 'Ẩn giải thích';
        } else {
            bsCollapse.hide();
            buttons[index].classList.add('collapsed');
            buttons[index].querySelector('.toggle-text').textContent = 'Xem giải thích';
        }
    });
}
</script>
@endsection
