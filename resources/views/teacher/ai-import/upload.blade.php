@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-robot text-primary"></i>
                        Tạo câu hỏi từ PDF bằng AI
                    </h2>
                    <p class="text-muted mb-0">Upload file PDF trắc nghiệm để tự động trích xuất câu hỏi</p>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-info mb-4">
                <div class="card-body">
                    <h6 class="card-title text-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Hướng dẫn sử dụng
                    </h6>
                    <ul class="mb-0 small">
                        <li><strong>File PDF phải có định dạng trắc nghiệm chuẩn</strong> (Câu 1, Câu 2...)</li>
                        <li><strong>Mỗi câu hỏi cần có ít nhất 1 đáp án</strong> (A, B, C, D)</li>
                        <li><strong>AI sẽ tự động nhận diện đáp án đúng</strong> từ:
                            <ul class="mt-1">
                                <li>✅ Text được <mark>bôi màu/highlight</mark></li>
                                <li>✅ Đánh dấu rõ ràng: "Đáp án: A" hoặc "ĐA: B"</li>
                                <li>✅ Format đặc biệt: <strong>bold</strong>, <u>underline</u></li>
                            </ul>
                        </li>
                        <li>File PDF tối đa <strong>10MB</strong></li>
                        <li>Bạn có thể xem lại và chỉnh sửa trước khi lưu</li>
                    </ul>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-upload me-2"></i>
                        Upload PDF
                    </h5>
                </div>
                <div class="card-body">
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

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('teacher.ai-import.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <!-- PDF File -->
                        <div class="mb-4">
                            <label for="pdf_file" class="form-label fw-bold">
                                File PDF <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="file" 
                                class="form-control @error('pdf_file') is-invalid @enderror" 
                                id="pdf_file" 
                                name="pdf_file" 
                                accept=".pdf"
                                required
                            >
                            @error('pdf_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Chỉ chấp nhận file PDF, tối đa 10MB</div>
                            
                            <!-- File preview -->
                            <div id="filePreview" class="mt-2 d-none">
                                <div class="alert alert-light border">
                                    <i class="bi bi-file-pdf text-danger fs-3"></i>
                                    <span id="fileName" class="ms-2"></span>
                                    <span id="fileSize" class="ms-2 text-muted"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-bold">
                                Danh mục môn học <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" 
                                name="category_id" 
                                required
                            >
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        <i class="{{ $category->icon }}"></i> {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tất cả câu hỏi sẽ được lưu vào danh mục này (có thể thay đổi sau)</div>
                        </div>

                        <!-- Language -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Ngôn ngữ <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="language" id="lang_vie" value="vie" {{ old('language', 'vie') == 'vie' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-primary" for="lang_vie">
                                    <i class="bi bi-flag-fill me-1"></i>
                                    Tiếng Việt
                                </label>

                                <input type="radio" class="btn-check" name="language" id="lang_eng" value="eng" {{ old('language') == 'eng' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="lang_eng">
                                    <i class="bi bi-flag me-1"></i>
                                    English
                                </label>
                            </div>
                            @error('language')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Default Difficulty -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Độ khó mặc định <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="default_difficulty" id="diff_easy" value="easy" {{ old('default_difficulty') == 'easy' ? 'checked' : '' }}>
                                <label class="btn btn-outline-success" for="diff_easy">
                                    <i class="bi bi-emoji-smile me-1"></i>
                                    Dễ
                                </label>

                                <input type="radio" class="btn-check" name="default_difficulty" id="diff_medium" value="medium" {{ old('default_difficulty', 'medium') == 'medium' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-warning" for="diff_medium">
                                    <i class="bi bi-emoji-neutral me-1"></i>
                                    Trung bình
                                </label>

                                <input type="radio" class="btn-check" name="default_difficulty" id="diff_hard" value="hard" {{ old('default_difficulty') == 'hard' ? 'checked' : '' }}>
                                <label class="btn btn-outline-danger" for="diff_hard">
                                    <i class="bi bi-emoji-frown me-1"></i>
                                    Khó
                                </label>
                            </div>
                            @error('default_difficulty')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">AI sẽ tự động đánh giá, hoặc dùng độ khó này nếu không xác định được</div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1" id="submitBtn">
                                <i class="bi bi-magic me-2"></i>
                                <span id="submitText">Trích xuất câu hỏi</span>
                            </button>
                            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Example Format -->
            <div class="card border-secondary mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-book me-2"></i>
                        Ví dụ định dạng PDF hợp lệ
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Cách 1: Có đánh dấu đáp án rõ ràng</strong></p>
                    <pre class="mb-3"><code>Câu 1: Python là ngôn ngữ lập trình gì?
A. Ngôn ngữ biên dịch
B. Ngôn ngữ thông dịch
C. Ngôn ngữ máy
D. Ngôn ngữ Assembly
Đáp án: B</code></pre>

                    <p class="mb-2"><strong>Cách 2: Đáp án đúng được bôi màu/highlight</strong></p>
                    <pre class="mb-3"><code>Câu 2: Laravel là framework của ngôn ngữ nào?
A. Python
B. JavaScript  
<mark>C. PHP</mark>  ← (text được bôi màu vàng)
D. Ruby</code></pre>

                    <p class="mb-2"><strong>Cách 3: Đáp án đúng được in đậm</strong></p>
                    <pre class="mb-0"><code>Câu 3: HTML là viết tắt của?
A. Hyper Text Markup Language
<strong>B. HyperText Markup Language</strong>  ← (text in đậm)
C. High Text Markup Language
D. Hyper Transfer Markup Language</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('pdf_file');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadForm = document.getElementById('uploadForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');

    // File preview
    fileInput.addEventListener('change', function(e) {
        if (this.files.length > 0) {
            const file = this.files[0];
            fileName.textContent = file.name;
            fileSize.textContent = `(${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            filePreview.classList.remove('d-none');
        } else {
            filePreview.classList.add('d-none');
        }
    });

    // Form submission loading
    uploadForm.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
        
        // Show processing message
        const processingAlert = document.createElement('div');
        processingAlert.className = 'alert alert-info mt-3';
        processingAlert.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-3" role="status"></div>
                <div>
                    <strong>Đang xử lý PDF...</strong><br>
                    <small>Quá trình này có thể mất 1-2 phút tùy vào kích thước file. Vui lòng đợi...</small>
                </div>
            </div>
        `;
        uploadForm.after(processingAlert);
    });
});
</script>
@endpush
@endsection
