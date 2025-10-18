@extends('layouts.teacher-dashboard')

@section('title', 'Chi tiết danh mục - ' . $category->name)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('teacher.categories.index') }}">Danh mục</a></li>
        <li class="breadcrumb-item active">{{ $category->name }}</li>
    </ol>
</nav>
@endsection

@section('teacher-dashboard-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Chi tiết danh mục</h1>
            <p class="text-muted">Thông tin về danh mục {{ $category->name }}</p>
        </div>
        <div class="btn-group">
            @if($category->created_by === Auth::id())
                <a href="{{ route('teacher.categories.edit', $category) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Chỉnh sửa
                </a>
                <button class="btn btn-outline-danger delete-btn"
                        data-category-id="{{ $category->id }}"
                        data-category-name="{{ $category->name }}">
                    <i class="bi bi-trash"></i> Xóa
                </button>
            @else
                <button class="btn btn-success" onclick="createExamWithCategory({{ $category->id }})">
                    <i class="bi bi-plus"></i> Tạo đề thi mới
                </button>
            @endif
            <a href="{{ route('teacher.categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Category Info -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            <div class="category-icon-large d-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px; border-radius: 50%; background-color: {{ $category->color }}20;">
                                @if($category->icon)
                                    <i class="{{ $category->icon }}" style="color: {{ $category->color }}; font-size: 3rem;"></i>
                                @else
                                    <i class="bi bi-folder" style="color: {{ $category->color }}; font-size: 3rem;"></i>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-2">{{ $category->name }}</h2>
                            <p class="text-muted mb-0 lead">
                                {{ $category->description ?: 'Chưa có mô tả cho danh mục này.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Category Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold" style="width: 120px;">Mã danh mục:</td>
                                    <td><code>{{ $category->slug }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Màu sắc:</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="color-preview me-2" 
                                                 style="width: 20px; height: 20px; background-color: {{ $category->color }}; border-radius: 50%; border: 1px solid #ddd;"></div>
                                            <code>{{ $category->color }}</code>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Icon:</td>
                                    <td>
                                        @if($category->icon)
                                            <i class="{{ $category->icon }} me-2" style="color: {{ $category->color }};"></i>
                                            <code>{{ $category->icon }}</code>
                                        @else
                                            <span class="text-muted">Chưa có icon</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold" style="width: 120px;">Thứ tự:</td>
                                    <td><span class="badge bg-light text-dark">{{ $category->sort_order }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Người tạo:</td>
                                    <td>{{ $category->creator->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Ngày tạo:</td>
                                    <td>{{ $category->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">Thống kê danh mục</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-card p-3">
                                <h3 class="text-primary mb-1">0</h3>
                                <p class="text-muted mb-0">Đề thi của tôi</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card p-3">
                                <h3 class="text-success mb-1">0</h3>
                                <p class="text-muted mb-0">Câu hỏi đã tạo</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card p-3">
                                <h3 class="text-info mb-1">0</h3>
                                <p class="text-muted mb-0">Học sinh tham gia</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card p-3">
                                <h3 class="text-warning mb-1">0</h3>
                                <p class="text-muted mb-0">Lần làm bài</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Content -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">Thao tác nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($category->created_by === Auth::id())
                            <a href="{{ route('teacher.categories.edit', $category) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Chỉnh sửa danh mục
                            </a>
                            
                            <button class="btn btn-outline-danger delete-btn"
                                    data-category-id="{{ $category->id }}"
                                    data-category-name="{{ $category->name }}">
                                <i class="bi bi-trash"></i> Xóa danh mục
                            </button>
                            
                            <hr>
                        @endif
                        
                        <button class="btn btn-success" onclick="createExamWithCategory({{ $category->id }})">
                            <i class="bi bi-plus-circle"></i> Tạo đề thi mới
                        </button>
                        
                        <button class="btn btn-info" onclick="createQuestionWithCategory({{ $category->id }})">
                            <i class="bi bi-question-circle"></i> Thêm câu hỏi
                        </button>
                        
                        <button class="btn btn-warning" onclick="viewCategoryStats({{ $category->id }})">
                            <i class="bi bi-graph-up"></i> Xem thống kê
                        </button>
                        
                        <hr>
                        
                        <button class="btn btn-outline-primary" onclick="exportCategoryData({{ $category->id }})">
                            <i class="bi bi-download"></i> Xuất dữ liệu
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Exams in this Category -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">Đề thi gần đây</h6>
                </div>
                <div class="card-body">
                    <div class="recent-exams">
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-text text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0">Chưa có đề thi nào trong danh mục này</p>
                            <button class="btn btn-sm btn-success mt-2" onclick="createExamWithCategory({{ $category->id }})">
                                Tạo đề thi đầu tiên
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                <p>Bạn có chắc chắn muốn xóa danh mục <strong id="categoryName"></strong>?</p>
                <p class="text-danger"><small><i class="bi bi-exclamation-triangle"></i> Hành động này không thể hoàn tác!</small></p>
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
.category-icon-large {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-card {
    border-radius: 10px;
    background: #f8f9fa;
    margin: 0.5rem 0;
}

.stat-card h3 {
    font-weight: 700;
    font-size: 2rem;
}

.recent-exams .exam-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.recent-exams .exam-item:last-child {
    border-bottom: none;
}

.recent-exams .exam-title {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.25rem;
}

.recent-exams .exam-meta {
    font-size: 0.875rem;
    color: #6b7280;
}

.color-preview {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation
    document.querySelector('.delete-btn')?.addEventListener('click', function() {
        const categoryId = this.dataset.categoryId;
        const categoryName = this.dataset.categoryName;
        
        document.getElementById('categoryName').textContent = categoryName;
        document.getElementById('deleteForm').action = `{{ route('teacher.categories.index') }}/${categoryId}`;
        
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});

function createExamWithCategory(categoryId) {
    // TODO: Redirect to exam creation with pre-selected category
    alert('Chức năng tạo đề thi sẽ được triển khai sau.\nDanh mục ID: ' + categoryId + '\nTên danh mục: {{ $category->name }}');
}

function createQuestionWithCategory(categoryId) {
    // TODO: Redirect to question creation with pre-selected category
    alert('Chức năng tạo câu hỏi sẽ được triển khai sau.\nDanh mục ID: ' + categoryId);
}

function viewCategoryStats(categoryId) {
    // TODO: Show detailed statistics for this category
    alert('Chức năng xem thống kê sẽ được triển khai sau.\nDanh mục ID: ' + categoryId);
}

function exportCategoryData(categoryId) {
    // TODO: Export category data
    alert('Chức năng xuất dữ liệu sẽ được triển khai sau.\nDanh mục ID: ' + categoryId);
}
</script>
@endpush