@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-clipboard-check text-success"></i>
                        Xem lại câu hỏi đã trích xuất
                    </h2>
                    <p class="text-muted mb-0">
                        Tổng cộng: <strong>{{ count($questions) }}</strong> câu hỏi từ 
                        <span class="badge bg-info">{{ $metadata['filename'] ?? 'PDF' }}</span>
                    </p>
                </div>
                <div>
                    <form action="{{ route('teacher.ai-import.cancel') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-1"></i>
                            Hủy bỏ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Save Form -->
    <form action="{{ route('teacher.ai-import.save') }}" method="POST" id="saveForm">
        @csrf

        <!-- Bulk Actions -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label fw-bold" for="selectAll">
                                Chọn tất cả (<span id="selectedCount">0</span>/{{ count($questions) }})
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="bulkCategory">
                            <option value="">-- Đổi danh mục hàng loạt --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="bulkDifficulty">
                            <option value="">-- Đổi độ khó hàng loạt --</option>
                            <option value="easy">Dễ</option>
                            <option value="medium">Trung bình</option>
                            <option value="hard">Khó</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions List -->
        <div class="row">
            @foreach($questions as $index => $question)
            <div class="col-12 mb-3">
                <div class="card question-card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input 
                                    class="form-check-input question-checkbox" 
                                    type="checkbox" 
                                    name="questions[{{ $index }}][selected]" 
                                    id="q{{ $index }}"
                                    value="1"
                                    checked
                                >
                                <label class="form-check-label fw-bold" for="q{{ $index }}">
                                    Câu {{ $index + 1 }}
                                    @if(isset($question['original_number']))
                                        <span class="badge bg-secondary">Câu gốc: {{ $question['original_number'] }}</span>
                                    @endif
                                </label>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestion({{ $index }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Question Content -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                            <textarea 
                                class="form-control" 
                                name="questions[{{ $index }}][content]" 
                                rows="2" 
                                required
                            >{{ $question['content'] }}</textarea>
                        </div>

                        <!-- Category & Difficulty -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select" name="questions[{{ $index }}][category_id]" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $question['category_id'] == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Độ khó <span class="text-danger">*</span></label>
                                <select class="form-select" name="questions[{{ $index }}][difficulty]" required>
                                    <option value="easy" {{ $question['difficulty'] == 'easy' ? 'selected' : '' }}>Dễ</option>
                                    <option value="medium" {{ $question['difficulty'] == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                    <option value="hard" {{ $question['difficulty'] == 'hard' ? 'selected' : '' }}>Khó</option>
                                </select>
                            </div>
                        </div>

                        <!-- Answers -->
                        <div class="mb-2">
                            <label class="form-label fw-bold">Đáp án</label>
                        </div>
                        
                        @foreach($question['answers'] as $aIndex => $answer)
                        <div class="input-group mb-2">
                            <span class="input-group-text">
                                {{ $answer['letter'] ?? chr(65 + $aIndex) }}
                            </span>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="questions[{{ $index }}][answers][{{ $aIndex }}][content]" 
                                value="{{ $answer['content'] }}"
                                required
                            >
                            <div class="input-group-text">
                                <input 
                                    class="form-check-input mt-0" 
                                    type="radio" 
                                    name="questions[{{ $index }}][correct_answer]" 
                                    value="{{ $aIndex }}"
                                    {{ ($question['correct_answer'] ?? '') == ($answer['letter'] ?? chr(65 + $aIndex)) ? 'checked' : '' }}
                                    onchange="updateCorrectAnswer({{ $index }}, {{ $aIndex }})"
                                >
                            </div>
                            <span class="input-group-text">
                                <i class="bi bi-check-circle text-success" title="Đáp án đúng"></i>
                            </span>
                        </div>
                        <input 
                            type="hidden" 
                            name="questions[{{ $index }}][answers][{{ $aIndex }}][is_correct]" 
                            value="{{ ($question['correct_answer'] ?? '') == ($answer['letter'] ?? chr(65 + $aIndex)) ? '1' : '0' }}"
                            id="is_correct_{{ $index }}_{{ $aIndex }}"
                        >
                        @endforeach

                        <!-- Add Answer Button -->
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAnswer({{ $index }})">
                            <i class="bi bi-plus-circle me-1"></i>
                            Thêm đáp án
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Submit Buttons -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            Sẽ lưu <strong id="finalCount">{{ count($questions) }}</strong> câu hỏi đã chọn
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success btn-lg" id="saveBtn" data-bs-toggle="modal" data-bs-target="#createExamModal">
                            <i class="bi bi-save me-2"></i>
                            Lưu câu hỏi
                        </button>
                        <a href="{{ route('teacher.ai-import.form') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left me-1"></i>
                            Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Create Exam Modal -->
<div class="modal fade" id="createExamModal" tabindex="-1" aria-labelledby="createExamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createExamModalLabel">
                    <i class="bi bi-file-earmark-plus me-2"></i>
                    Tạo đề thi mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createExamForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Nhập thông tin đề thi để lưu <strong id="modalSelectedCount">{{ count($questions) }}</strong> câu hỏi đã chọn
                    </div>

                    <!-- Exam Category -->
                    <div class="mb-3">
                        <label for="examCategory" class="form-label fw-bold">
                            Danh mục đề thi <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="examCategory" name="exam_category_id" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn danh mục cho đề thi</div>
                    </div>

                    <!-- Exam Title -->
                    <div class="mb-3">
                        <label for="examTitle" class="form-label fw-bold">
                            Tên đề thi <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="examTitle" 
                            name="exam_title"
                            placeholder="VD: Kiểm tra giữa kỳ môn Toán lớp 10"
                            required
                        >
                        <div class="invalid-feedback">Vui lòng nhập tên đề thi</div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="examDescription" class="form-label fw-bold">
                            Mô tả
                        </label>
                        <textarea 
                            class="form-control" 
                            id="examDescription" 
                            name="exam_description"
                            rows="3"
                            placeholder="Mô tả về đề thi này..."
                        ></textarea>
                    </div>

                    <!-- Duration & Marks -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="examDuration" class="form-label fw-bold">
                                Thời gian làm bài (phút) <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="examDuration" 
                                name="exam_duration"
                                min="5"
                                max="300"
                                value="60"
                                required
                            >
                        </div>
                        <div class="col-md-6">
                            <label for="examMarks" class="form-label fw-bold">
                                Tổng điểm <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="examMarks" 
                                name="exam_total_marks"
                                min="1"
                                max="1000"
                                value="10"
                                step="0.5"
                                required
                            >
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái đề thi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exam_status" id="statusDraft" value="draft" checked>
                            <label class="form-check-label" for="statusDraft">
                                <i class="bi bi-file-earmark text-secondary"></i> Nháp (chưa công bố)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exam_status" id="statusPublished" value="published">
                            <label class="form-check-label" for="statusPublished">
                                <i class="bi bi-file-earmark-check text-success"></i> Công bố (học sinh có thể thi)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-success" id="confirmSaveBtn">
                        <i class="bi bi-check-circle me-1"></i>
                        Tạo đề thi và lưu câu hỏi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.question-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

// Update selected count
function updateSelectedCount() {
    const checked = document.querySelectorAll('.question-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('finalCount').textContent = checked;
}

document.querySelectorAll('.question-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

// Bulk change category
document.getElementById('bulkCategory').addEventListener('change', function() {
    if (this.value) {
        const selects = document.querySelectorAll('select[name*="[category_id]"]');
        const checked = document.querySelectorAll('.question-checkbox:checked');
        
        checked.forEach(cb => {
            const index = cb.name.match(/\d+/)[0];
            document.querySelector(`select[name="questions[${index}][category_id]"]`).value = this.value;
        });
        
        this.value = '';
    }
});

// Bulk change difficulty
document.getElementById('bulkDifficulty').addEventListener('change', function() {
    if (this.value) {
        const checked = document.querySelectorAll('.question-checkbox:checked');
        
        checked.forEach(cb => {
            const index = cb.name.match(/\d+/)[0];
            document.querySelector(`select[name="questions[${index}][difficulty]"]`).value = this.value;
        });
        
        this.value = '';
    }
});

// Delete question
function deleteQuestion(index) {
    if (confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
        const card = event.target.closest('.question-card').closest('.col-12');
        card.remove();
        updateSelectedCount();
    }
}

// Update correct answer
function updateCorrectAnswer(qIndex, aIndex) {
    const form = document.getElementById('saveForm');
    const answerInputs = form.querySelectorAll(`input[name^="questions[${qIndex}][answers]"][name$="[is_correct]"]`);
    
    answerInputs.forEach((input, idx) => {
        input.value = idx === aIndex ? '1' : '0';
    });
}

// Add answer
function addAnswer(qIndex) {
    // Implementation for adding new answer dynamically
    alert('Chức năng đang phát triển. Vui lòng chỉnh sửa trong form.');
}

// Initialize count
updateSelectedCount();

// Update modal count when modal opens
document.getElementById('createExamModal').addEventListener('show.bs.modal', function() {
    const checked = document.querySelectorAll('.question-checkbox:checked').length;
    document.getElementById('modalSelectedCount').textContent = checked;
    
    if (checked === 0) {
        alert('Vui lòng chọn ít nhất 1 câu hỏi để lưu!');
        return false;
    }
});

// Create exam form submission
document.getElementById('createExamForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const checked = document.querySelectorAll('.question-checkbox:checked').length;
    if (checked === 0) {
        alert('Vui lòng chọn ít nhất 1 câu hỏi để lưu!');
        return false;
    }
    
    // Get exam data from modal
    const examCategoryId = document.getElementById('examCategory').value;
    const examTitle = document.getElementById('examTitle').value;
    const examDescription = document.getElementById('examDescription').value;
    const examDuration = document.getElementById('examDuration').value;
    const examMarks = document.getElementById('examMarks').value;
    const examStatus = document.querySelector('input[name="exam_status"]:checked').value;
    
    // Validate category
    if (!examCategoryId) {
        alert('Vui lòng chọn danh mục cho đề thi!');
        return false;
    }
    
    // Add exam data to main form
    const mainForm = document.getElementById('saveForm');
    
    // Create hidden inputs for exam data
    const inputs = [
        { name: 'exam_category_id', value: examCategoryId },
        { name: 'exam_title', value: examTitle },
        { name: 'exam_description', value: examDescription },
        { name: 'exam_duration', value: examDuration },
        { name: 'exam_total_marks', value: examMarks },
        { name: 'exam_status', value: examStatus }
    ];
    
    inputs.forEach(({ name, value }) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        mainForm.appendChild(input);
    });
    
    // Disable button and submit main form
    document.getElementById('confirmSaveBtn').disabled = true;
    document.getElementById('confirmSaveBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
    
    mainForm.submit();
});

// Form submission validation (removed auto-submit)
document.getElementById('saveForm').addEventListener('submit', function(e) {
    // This will only trigger when submitted from modal
    const checked = document.querySelectorAll('.question-checkbox:checked').length;
    
    if (checked === 0) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất 1 câu hỏi để lưu!');
        return false;
    }
});
</script>
@endpush
@endsection
