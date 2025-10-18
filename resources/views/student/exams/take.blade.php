@extends('layouts.student-dashboard')

@section('title', 'Làm bài thi - ' . $exam->title)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.exams.index') }}">Đề thi</a></li>
        <li class="breadcrumb-item active">Làm bài thi</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
.exam-taking-container {
    background-color: #f8f9fc;
    min-height: calc(100vh - 100px);
}

.timer-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    position: sticky;
    top: 1rem;
}

.timer-display {
    font-size: 2.5rem;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

.timer-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.question-nav {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(45px, 1fr));
    gap: 0.5rem;
}

.question-nav-btn {
    aspect-ratio: 1;
    border: 2px solid #e3e6f0;
    background: white;
    border-radius: 0.35rem;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.question-nav-btn:hover {
    border-color: #4e73df;
    transform: scale(1.05);
}

.question-nav-btn.active {
    background: #4e73df;
    color: white;
    border-color: #4e73df;
}

.question-nav-btn.answered {
    background: #1cc88a;
    color: white;
    border-color: #1cc88a;
}

.question-nav-btn.marked {
    background: #f6c23e;
    color: white;
    border-color: #f6c23e;
}

.question-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
    min-height: 400px;
}

.answer-option {
    border: 2px solid #e3e6f0;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s;
    background: white;
}

.answer-option:hover {
    border-color: #4e73df;
    background: #f8f9fc;
}

.answer-option input[type="radio"]:checked ~ label,
.answer-option input[type="checkbox"]:checked ~ label {
    font-weight: 600;
}

.answer-option input[type="radio"]:checked,
.answer-option input[type="checkbox"]:checked {
    border-color: #4e73df !important;
}

.answer-option.selected {
    border-color: #4e73df;
    background: #e7f0ff;
}

.progress-bar-exam {
    height: 8px;
    background: #e3e6f0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-exam .progress {
    height: 100%;
    background: linear-gradient(90deg, #1cc88a 0%, #36b9cc 100%);
    transition: width 0.3s;
}
</style>
@endpush

@section('student-dashboard-content')
<div class="exam-taking-container">
    <form id="examForm" action="{{ route('student.exams.submit', $exam) }}" method="POST">
        @csrf
        <input type="hidden" name="time_spent" id="timeSpent" value="0">

        <div class="row">
            <!-- Questions Area -->
            <div class="col-lg-9">
                <!-- Exam Header -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $exam->title }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-question-circle"></i> {{ $questions->count() }} câu hỏi |
                                    <i class="bi bi-star"></i> {{ $exam->total_marks }} điểm
                                </p>
                            </div>
                            <button type="button" class="btn btn-outline-warning" onclick="toggleMarkQuestion()">
                                <i class="bi bi-bookmark"></i> Đánh dấu
                            </button>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Tiến độ</small>
                                <small class="text-muted"><span id="answeredCount">0</span>/{{ $questions->count() }}</small>
                            </div>
                            <div class="progress-bar-exam">
                                <div class="progress" id="examProgress" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions -->
                <div id="questionsContainer">
                    @foreach($questions as $index => $question)
                        <div class="question-card {{ $index === 0 ? '' : 'd-none' }}" 
                             id="question-{{ $index }}" 
                             data-question-index="{{ $index }}">
                            
                            <!-- Question Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="mb-0">
                                    <span class="badge bg-primary me-2">Câu {{ $index + 1 }}</span>
                                    @if($question->is_required)
                                        <span class="badge bg-danger">Bắt buộc</span>
                                    @endif
                                    <span class="badge bg-info">{{ $question->marks }} điểm</span>
                                </h5>
                                <span class="badge bg-secondary">{{ $question->question_type_text }}</span>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-4">
                                <p class="fs-5">{!! nl2br(e($question->question_text)) !!}</p>
                            </div>

                            <!-- Answers -->
                            <div class="answers-container">
                                @if($question->question_type === 'multiple_choice')
                                    @foreach($question->answers as $aIndex => $answer)
                                        <div class="answer-option" onclick="selectAnswer({{ $index }}, {{ $aIndex }})">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       id="q{{ $index }}_a{{ $aIndex }}"
                                                       value="{{ $answer->id }}"
                                                       onchange="markAsAnswered({{ $index }})">
                                                <label class="form-check-label w-100 cursor-pointer" for="q{{ $index }}_a{{ $aIndex }}">
                                                    {{ chr(65 + $aIndex) }}. {{ $answer->answer_text }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach

                                @elseif($question->question_type === 'true_false')
                                    @foreach($question->answers as $aIndex => $answer)
                                        <div class="answer-option" onclick="selectAnswer({{ $index }}, {{ $aIndex }})">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       id="q{{ $index }}_a{{ $aIndex }}"
                                                       value="{{ $answer->id }}"
                                                       onchange="markAsAnswered({{ $index }})">
                                                <label class="form-check-label w-100 cursor-pointer" for="q{{ $index }}_a{{ $aIndex }}">
                                                    {{ $answer->answer_text }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach

                                @elseif($question->question_type === 'short_answer')
                                    <div class="mb-3">
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               name="answers[{{ $question->id }}]"
                                               id="q{{ $index }}_text"
                                               placeholder="Nhập câu trả lời của bạn..."
                                               oninput="markAsAnswered({{ $index }})">
                                    </div>

                                @elseif($question->question_type === 'essay')
                                    <div class="mb-3">
                                        <textarea class="form-control" 
                                                  name="answers[{{ $question->id }}]"
                                                  id="q{{ $index }}_essay"
                                                  rows="8"
                                                  placeholder="Viết câu trả lời của bạn..."
                                                  oninput="markAsAnswered({{ $index }})"></textarea>
                                    </div>
                                @endif
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" 
                                        class="btn btn-outline-secondary {{ $index === 0 ? 'disabled' : '' }}" 
                                        onclick="previousQuestion()"
                                        {{ $index === 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-arrow-left"></i> Câu trước
                                </button>
                                
                                @if($index < $questions->count() - 1)
                                    <button type="button" 
                                            class="btn btn-primary" 
                                            onclick="nextQuestion()">
                                        Câu tiếp <i class="bi bi-arrow-right"></i>
                                    </button>
                                @else
                                    <button type="button" 
                                            class="btn btn-success" 
                                            onclick="confirmSubmit()">
                                        <i class="bi bi-check-circle"></i> Nộp bài
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Timer -->
                <div class="timer-box mb-4" id="timerBox">
                    <div class="text-center">
                        <div class="mb-2"><i class="bi bi-clock-history" style="font-size: 2rem;"></i></div>
                        <div class="timer-display" id="timer">{{ $exam->duration_minutes }}:00</div>
                        <small class="opacity-75">Thời gian còn lại</small>
                    </div>
                </div>

                <!-- Question Navigation -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-list-ol"></i> Danh sách câu hỏi</h6>
                    </div>
                    <div class="card-body">
                        <div class="question-nav">
                            @foreach($questions as $index => $question)
                                <button type="button" 
                                        class="question-nav-btn {{ $index === 0 ? 'active' : '' }}" 
                                        id="nav-btn-{{ $index }}"
                                        onclick="goToQuestion({{ $index }})"
                                        title="Câu {{ $index + 1 }}">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>

                        <hr>

                        <!-- Legend -->
                        <div class="small">
                            <div class="d-flex align-items-center mb-2">
                                <div style="width: 20px; height: 20px; background: #4e73df; border-radius: 4px;" class="me-2"></div>
                                <span>Đang xem</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div style="width: 20px; height: 20px; background: #1cc88a; border-radius: 4px;" class="me-2"></div>
                                <span>Đã trả lời</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div style="width: 20px; height: 20px; background: #f6c23e; border-radius: 4px;" class="me-2"></div>
                                <span>Đánh dấu</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="button" class="btn btn-success w-100 mb-2" onclick="confirmSubmit()">
                            <i class="bi bi-check-circle"></i> Nộp bài
                        </button>
                        <button type="button" class="btn btn-outline-danger w-100" onclick="confirmExit()">
                            <i class="bi bi-x-circle"></i> Thoát
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Confirm Submit Modal -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle"></i> Xác nhận nộp bài</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Bạn đã trả lời <strong><span id="modalAnsweredCount">0</span>/{{ $questions->count() }}</strong> câu hỏi.
                </div>
                <p>Bạn có chắc chắn muốn nộp bài không?</p>
                <p class="text-danger mb-0"><small>⚠️ Bạn không thể sửa đáp án sau khi nộp bài!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success" onclick="submitExam()">
                    <i class="bi bi-send"></i> Nộp bài
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentQuestion = 0;
let totalQuestions = {{ $questions->count() }};
let duration = {{ $exam->duration_minutes }};
let timeRemaining = duration * 60; // seconds
let timerInterval;
let startTime = Date.now();
let markedQuestions = new Set();

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    startTimer();
    updateProgress();
    
    // Warn before leaving
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = 'Bạn có chắc muốn rời khỏi? Tiến độ làm bài sẽ bị mất!';
    });
});

// Timer
function startTimer() {
    timerInterval = setInterval(function() {
        timeRemaining--;
        
        let minutes = Math.floor(timeRemaining / 60);
        let seconds = timeRemaining % 60;
        
        document.getElementById('timer').textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        
        // Warning at 5 minutes
        if (timeRemaining === 300) {
            document.getElementById('timerBox').classList.add('timer-warning');
            alert('⚠️ Còn 5 phút!');
        }
        
        // Time's up
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            alert('⏰ Hết giờ! Bài thi sẽ được nộp tự động.');
            submitExam();
        }
    }, 1000);
}

// Navigation
function goToQuestion(index) {
    document.querySelectorAll('.question-card').forEach(card => card.classList.add('d-none'));
    document.getElementById('question-' + index).classList.remove('d-none');
    
    document.querySelectorAll('.question-nav-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('nav-btn-' + index).classList.add('active');
    
    currentQuestion = index;
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        goToQuestion(currentQuestion + 1);
    }
}

function previousQuestion() {
    if (currentQuestion > 0) {
        goToQuestion(currentQuestion - 1);
    }
}

// Answer selection
function selectAnswer(questionIndex, answerIndex) {
    let radio = document.getElementById('q' + questionIndex + '_a' + answerIndex);
    radio.checked = true;
    markAsAnswered(questionIndex);
    
    // Visual feedback
    let parent = radio.closest('.answers-container');
    parent.querySelectorAll('.answer-option').forEach(opt => opt.classList.remove('selected'));
    radio.closest('.answer-option').classList.add('selected');
}

function markAsAnswered(questionIndex) {
    let navBtn = document.getElementById('nav-btn-' + questionIndex);
    if (!navBtn.classList.contains('marked')) {
        navBtn.classList.add('answered');
    }
    updateProgress();
}

function toggleMarkQuestion() {
    let navBtn = document.getElementById('nav-btn-' + currentQuestion);
    
    if (markedQuestions.has(currentQuestion)) {
        markedQuestions.delete(currentQuestion);
        navBtn.classList.remove('marked');
    } else {
        markedQuestions.add(currentQuestion);
        navBtn.classList.add('marked');
    }
}

function updateProgress() {
    let answered = document.querySelectorAll('.question-nav-btn.answered').length;
    document.getElementById('answeredCount').textContent = answered;
    document.getElementById('modalAnsweredCount').textContent = answered;
    
    let percentage = (answered / totalQuestions) * 100;
    document.getElementById('examProgress').style.width = percentage + '%';
}

// Submit
function confirmSubmit() {
    let modal = new bootstrap.Modal(document.getElementById('submitModal'));
    modal.show();
}

function submitExam() {
    clearInterval(timerInterval);
    
    // Calculate time spent
    let timeSpent = Math.floor((Date.now() - startTime) / 1000);
    document.getElementById('timeSpent').value = timeSpent;
    
    // Remove beforeunload warning
    window.removeEventListener('beforeunload', function() {});
    
    document.getElementById('examForm').submit();
}

function confirmExit() {
    if (confirm('Bạn có chắc muốn thoát? Tiến độ làm bài sẽ bị mất!')) {
        window.location.href = '{{ route('student.exams.show', $exam) }}';
    }
}
</script>
@endpush
