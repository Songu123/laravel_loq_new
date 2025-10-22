@extends('layouts.student-dashboard')

@section('title', 'Luyện tập')

@push('styles')
<style>
.practice-container {
    max-width: 900px;
    margin: 0 auto;
}

.question-card {
    border: none;
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
}

.question-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
}

.question-navigation {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.question-nav-btn {
    width: 45px;
    height: 45px;
    border: 2px solid #dee2e6;
    background: white;
    border-radius: 0.35rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.question-nav-btn:hover {
    border-color: #0d6efd;
    color: #0d6efd;
}

.question-nav-btn.active {
    background: #0d6efd;
    color: white;
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.3);
}

.question-nav-btn.answered {
    background: #198754;
    color: white;
    border-color: #198754;
}

.question-nav-btn.answered.active {
    background: #146c43;
    border-color: #146c43;
    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.3);
}

.option-card {
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.option-card:hover {
    border-color: #0d6efd;
    background: #f8f9fa;
    transform: translateX(5px);
}

.option-card.selected {
    border-color: #0d6efd;
    background: #e7f1ff;
}

.option-card input[type="radio"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.timer {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0d6efd;
}

.timer.warning {
    color: #ffc107;
    animation: pulse 1s infinite;
}

.timer.danger {
    color: #dc3545;
    animation: pulse 0.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.progress-bar-practice {
    height: 8px;
    border-radius: 4px;
    background: #e9ecef;
}

.progress-bar-practice .progress {
    height: 100%;
    background: linear-gradient(90deg, #0d6efd 0%, #0a58ca 100%);
    border-radius: 4px;
    transition: width 0.3s;
}

.question-image {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1rem 0;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}
</style>
@endpush

@section('student-dashboard-content')
<div class="practice-container">
    <!-- Progress Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">
                    <i class="bi bi-list-check text-primary me-2"></i>
                    Tiến độ: <span id="answeredCount">0</span>/{{ count($questions) }} câu
                </h6>
                <div class="timer" id="timer">
                    <i class="bi bi-clock me-1"></i>
                    <span id="timeDisplay">00:00</span>
                </div>
            </div>
            <div class="progress-bar-practice">
                <div class="progress" id="progressBar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Question Navigation -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">
                    <i class="bi bi-grid-3x3-gap text-primary me-2"></i>
                    Danh sách câu hỏi:
                </h6>
                <div class="d-flex gap-3 small">
                    <div>
                        <span class="badge" style="background: #0d6efd; width: 20px; height: 20px; display: inline-block;"></span>
                        <span class="ms-1">Đang làm</span>
                    </div>
                    <div>
                        <span class="badge" style="background: #198754; width: 20px; height: 20px; display: inline-block;"></span>
                        <span class="ms-1">Đã trả lời</span>
                    </div>
                    <div>
                        <span class="badge bg-light border" style="width: 20px; height: 20px; display: inline-block;"></span>
                        <span class="ms-1">Chưa làm</span>
                    </div>
                </div>
            </div>
            <div class="question-navigation" id="questionNavigation">
                @foreach($questions as $index => $question)
                <button type="button" 
                        class="question-nav-btn {{ $index === 0 ? 'active' : '' }}" 
                        data-question="{{ $index }}"
                        onclick="showQuestion({{ $index }})"
                        title="Câu {{ $index + 1 }}">
                    {{ $index + 1 }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Question Card -->
    <form id="practiceForm" action="{{ route('student.practice.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="mode" value="{{ $mode }}">
        <input type="hidden" name="start_time" value="{{ $startTime }}">
        
        @foreach($questions as $index => $question)
        <div class="question-card card mb-4" id="question-{{ $index }}" style="{{ $index === 0 ? '' : 'display: none;' }}">
            <div class="question-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Câu {{ $index + 1 }}/{{ count($questions) }}
                    </h5>
                    <span class="badge bg-light text-primary">
                        {{ $question->question_type_text }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                <!-- Question Text -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Câu hỏi:</h6>
                    <div class="fs-5">
                        {!! nl2br(e($question->question_text)) !!}
                    </div>
                    
                    @if($question->image_path)
                    <img src="{{ asset('storage/' . $question->image_path) }}" 
                         alt="Question Image" 
                         class="question-image">
                    @endif
                </div>

                <!-- Answer Options -->
                @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                    <h6 class="text-muted mb-3">Chọn đáp án:</h6>
                    @if($question->answers && $question->answers->count() > 0)
                        @foreach($question->answers as $answer)
                        <label class="option-card" for="q{{ $index }}_{{ $answer->id }}">
                            <div class="d-flex align-items-center">
                                <input type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       value="{{ $answer->id }}"
                                       id="q{{ $index }}_{{ $answer->id }}"
                                       onchange="markAnswered({{ $index }})"
                                       class="me-3">
                                <div class="flex-grow-1">
                                    {{ $answer->answer_text }}
                                </div>
                            </div>
                        </label>
                        @endforeach
                    @else
                        <div class="alert alert-warning">Không có đáp án cho câu hỏi này.</div>
                    @endif
                @else
                    <!-- Text Answer -->
                    <h6 class="text-muted mb-3">Câu trả lời của bạn:</h6>
                    <textarea name="answers[{{ $question->id }}]" 
                              class="form-control" 
                              rows="{{ $question->question_type === 'essay' ? '8' : '3' }}"
                              placeholder="Nhập câu trả lời..."
                              onchange="markAnswered({{ $index }})"
                              onkeyup="markAnswered({{ $index }})"></textarea>
                @endif
            </div>

            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between">
                    @if($index > 0)
                    <button type="button" class="btn btn-outline-primary" onclick="showQuestion({{ $index - 1 }})">
                        <i class="bi bi-chevron-left"></i> Câu trước
                    </button>
                    @else
                    <div></div>
                    @endif
                    
                    @if($index < count($questions) - 1)
                    <button type="button" class="btn btn-primary" onclick="showQuestion({{ $index + 1 }})">
                        Câu tiếp <i class="bi bi-chevron-right"></i>
                    </button>
                    @else
                    <button type="button" class="btn btn-success" onclick="confirmSubmit()">
                        <i class="bi bi-check-circle me-1"></i> Nộp bài
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </form>

    <!-- Submit Button (Fixed) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center">
            <button type="button" class="btn btn-lg btn-success px-5" onclick="confirmSubmit()">
                <i class="bi bi-check-circle me-2"></i>
                Nộp bài luyện tập
            </button>
        </div>
    </div>
</div>

<!-- Confirm Submit Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Xác nhận nộp bài
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Bạn đã trả lời <strong id="modalAnsweredCount">0</strong> trong tổng số <strong>{{ count($questions) }}</strong> câu hỏi.</p>
                <p class="mb-0">Bạn có chắc chắn muốn nộp bài không?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Hủy
                </button>
                <button type="button" class="btn btn-success" onclick="submitPractice()">
                    <i class="bi bi-check-circle"></i> Nộp bài
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentQuestion = 0;
let startTime = Date.now();
let timerInterval;
let answeredQuestions = new Set();

// Timer
function startTimer() {
    timerInterval = setInterval(() => {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        
        const timeDisplay = document.getElementById('timeDisplay');
        timeDisplay.textContent = 
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0');
        
        // Warning at 30 minutes
        const timerElement = document.getElementById('timer');
        if (minutes >= 30 && minutes < 45) {
            timerElement.classList.add('warning');
        } else if (minutes >= 45) {
            timerElement.classList.add('danger');
            timerElement.classList.remove('warning');
        }
    }, 1000);
}

// Show Question
function showQuestion(index) {
    // Hide all questions
    document.querySelectorAll('[id^="question-"]').forEach(q => {
        q.style.display = 'none';
    });
    
    // Show selected question
    document.getElementById('question-' + index).style.display = 'block';
    
    // Update navigation buttons - remove active from all
    document.querySelectorAll('.question-nav-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active to current question
    const currentBtn = document.querySelector(`[data-question="${index}"]`);
    currentBtn.classList.add('active');
    
    // Update current question index
    currentQuestion = index;
    
    // Scroll to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Re-check if current question is answered (update UI)
    const questionCard = document.getElementById('question-' + index);
    questionCard.querySelectorAll('.option-card').forEach(card => {
        card.classList.remove('selected');
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            card.classList.add('selected');
        }
    });
}

// Mark Question as Answered
function markAnswered(index) {
    // Check if question is actually answered
    const questionCard = document.getElementById('question-' + index);
    let isAnswered = false;
    
    // Check for radio buttons (multiple choice)
    const radio = questionCard.querySelector('input[type="radio"]:checked');
    if (radio) {
        isAnswered = true;
    }
    
    // Check for textarea (text answer)
    const textarea = questionCard.querySelector('textarea');
    if (textarea && textarea.value.trim() !== '') {
        isAnswered = true;
    }
    
    // Update answered set and navigation button
    const navBtn = document.querySelector(`[data-question="${index}"]`);
    
    if (isAnswered) {
        answeredQuestions.add(index);
        navBtn.classList.add('answered');
    } else {
        answeredQuestions.delete(index);
        navBtn.classList.remove('answered');
    }
    
    // Update progress
    updateProgress();
    
    // Update option card styling for multiple choice
    questionCard.querySelectorAll('.option-card').forEach(card => {
        card.classList.remove('selected');
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            card.classList.add('selected');
        }
    });
}

// Update Progress
function updateProgress() {
    const answeredCount = answeredQuestions.size;
    const totalQuestions = {{ count($questions) }};
    const percentage = (answeredCount / totalQuestions) * 100;
    
    document.getElementById('answeredCount').textContent = answeredCount;
    document.getElementById('progressBar').style.width = percentage + '%';
}

// Confirm Submit
let confirmModal;
function confirmSubmit() {
    const answeredCount = answeredQuestions.size;
    document.getElementById('modalAnsweredCount').textContent = answeredCount;
    
    confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
}

// Submit Practice
function submitPractice() {
    clearInterval(timerInterval);
    document.getElementById('practiceForm').submit();
}

// Scan all questions to check which ones are answered
function scanAllAnsweredQuestions() {
    const totalQuestions = {{ count($questions) }};
    
    for (let i = 0; i < totalQuestions; i++) {
        const questionCard = document.getElementById('question-' + i);
        if (!questionCard) continue;
        
        let isAnswered = false;
        
        // Check radio buttons
        const radio = questionCard.querySelector('input[type="radio"]:checked');
        if (radio) {
            isAnswered = true;
        }
        
        // Check textarea
        const textarea = questionCard.querySelector('textarea');
        if (textarea && textarea.value.trim() !== '') {
            isAnswered = true;
        }
        
        if (isAnswered) {
            answeredQuestions.add(i);
            const navBtn = document.querySelector(`[data-question="${i}"]`);
            if (navBtn && !navBtn.classList.contains('active')) {
                navBtn.classList.add('answered');
            }
        }
    }
    
    updateProgress();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    startTimer();
    
    // Scan all questions on page load
    scanAllAnsweredQuestions();
});

// Prevent accidental page close
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>
@endpush
