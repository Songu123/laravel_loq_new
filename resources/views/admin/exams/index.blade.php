@extends('layouts.dashboard')

@section('title', 'Quản lý đề thi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Quản lý đề thi</h1>
            <p class="text-muted">Tạo và quản lý các đề thi trong hệ thống</p>
        </div>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo đề thi mới
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.exams.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Tên đề thi...">
                    </div>
                    <div class="col-md-2">
                        <label for="category" class="form-label">Danh mục</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Tất cả</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="difficulty" class="form-label">Độ khó</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="">Tất cả</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option>
                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tất cả</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Exams Grid -->
    @if($exams->count() > 0)
        <div class="row">
            @foreach($exams as $exam)
                <div class="col-xl-4 col-lg-6 col-md-12 mb-4">
                    <div class="card exam-card h-100 shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1 fw-bold">{{ $exam->title }}</h6>
                                    <div class="text-muted small">
                                        <i class="fas fa-folder me-1" style="color: {{ $exam->category->color }}"></i>
                                        {{ $exam->category->name }}
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.exams.show', $exam) }}">
                                            <i class="fas fa-eye me-2"></i>Xem chi tiết</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.exams.edit', $exam) }}">
                                            <i class="fas fa-edit me-2"></i>Chỉnh sửa</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.exams.toggle-status', $exam) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button class="dropdown-item" type="submit">
                                                    @if($exam->is_active)
                                                        <i class="fas fa-pause me-2"></i>Tạm dừng
                                                    @else
                                                        <i class="fas fa-play me-2"></i>Kích hoạt
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-danger delete-exam" 
                                                    data-exam-id="{{ $exam->id }}"
                                                    data-exam-title="{{ $exam->title }}">
                                                <i class="fas fa-trash me-2"></i>Xóa
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <p class="card-text text-muted small mb-3">
                                {{ Str::limit($exam->description, 120) }}
                            </p>
                            
                            <!-- Stats -->
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="stat-item">
                                        <div class="stat-number text-primary">{{ $exam->questions_count }}</div>
                                        <div class="stat-label">Câu hỏi</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <div class="stat-number text-success">{{ $exam->total_marks }}</div>
                                        <div class="stat-label">Điểm</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <div class="stat-number text-info">{{ $exam->duration_text }}</div>
                                        <div class="stat-label">Thời gian</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Badges -->
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-{{ $exam->difficulty_level === 'easy' ? 'success' : ($exam->difficulty_level === 'medium' ? 'warning' : 'danger') }}">
                                    {{ $exam->difficulty_level_text }}
                                </span>
                                @if($exam->is_active)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Tạm dừng</span>
                                @endif
                                @if($exam->is_public)
                                    <span class="badge bg-info">Công khai</span>
                                @endif
                            </div>
                            
                            <!-- Meta info -->
                            <div class="text-muted small">
                                <div class="mb-1">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $exam->creator->name }}
                                </div>
                                <div>
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $exam->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                                <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-sm btn-outline-warning flex-fill">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($exams->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $exams->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-5">
            <i class="fas fa-clipboard-list display-1 text-muted mb-3"></i>
            <h5 class="text-muted">Chưa có đề thi nào</h5>
            <p class="text-muted">Tạo đề thi đầu tiên để bắt đầu quản lý bài kiểm tra.</p>
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus"></i> Tạo đề thi đầu tiên
            </a>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa đề thi <strong id="examTitle"></strong>?</p>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Hành động này sẽ xóa tất cả câu hỏi và đáp án liên quan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.exam-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.exam-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.stat-item {
    padding: 0.5rem 0;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.card-header {
    border-bottom: 1px solid #e3e6f0;
}

.card-footer {
    border-top: 1px solid #e3e6f0;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation
    document.querySelectorAll('.delete-exam').forEach(button => {
        button.addEventListener('click', function() {
            const examId = this.dataset.examId;
            const examTitle = this.dataset.examTitle;
            
            document.getElementById('examTitle').textContent = examTitle;
            document.getElementById('deleteForm').action = `{{ route('admin.exams.index') }}/${examId}`;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});
</script>
@endpush