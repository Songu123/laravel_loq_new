@extends('layouts.teacher-dashboard')

@section('title', 'Danh mục môn học')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Danh mục</li>
    </ol>
</nav>
@endsection

@section('teacher-dashboard-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Danh mục môn học của tôi</h1>
            <p class="text-muted">Quản lý các danh mục cho đề thi và câu hỏi của bạn</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('teacher.categories.create') }}" class="btn btn-success">
                <i class="bi bi-plus"></i> Tạo danh mục mới
            </a>
            <button class="btn btn-outline-secondary" onclick="toggleView('grid')">
                <i class="bi bi-grid-3x3-gap"></i> Lưới
            </button>
            <button class="btn btn-outline-secondary" onclick="toggleView('list')">
                <i class="bi bi-list-ul"></i> Danh sách
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Categories Grid View -->
    <div id="gridView" class="categories-grid">
        @if($categories->count() > 0)
            <div class="row">
                @foreach($categories as $category)
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="category-card card h-100 shadow-sm border-0">
                            <div class="card-body text-center p-4">
                                <div class="category-icon mb-3" style="background-color: {{ $category->color }}20;">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }}" style="color: {{ $category->color }}; font-size: 2.5rem;"></i>
                                    @else
                                        <i class="bi bi-folder" style="color: {{ $category->color }}; font-size: 2.5rem;"></i>
                                    @endif
                                </div>
                                <h5 class="card-title mb-2">{{ $category->name }}</h5>
                                <p class="card-text text-muted small mb-3">{{ Str::limit($category->description, 80) }}</p>
                                
                                <!-- Quick Stats -->
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <div class="stat-number text-primary">0</div>
                                            <div class="stat-label">Đề thi</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <div class="stat-number text-success">0</div>
                                            <div class="stat-label">Câu hỏi</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <div class="stat-number text-info">0</div>
                                            <div class="stat-label">Học sinh</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('teacher.categories.show', $category) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('teacher.categories.edit', $category) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i> Sửa
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-btn" 
                                            data-category-id="{{ $category->id }}"
                                            data-category-name="{{ $category->name }}">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-folder-x display-1 text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có danh mục nào</h5>
                <p class="text-muted">Tạo danh mục đầu tiên để bắt đầu tổ chức đề thi và câu hỏi của bạn.</p>
                <a href="{{ route('teacher.categories.create') }}" class="btn btn-success mt-2">
                    <i class="bi bi-plus"></i> Tạo danh mục đầu tiên
                </a>
            </div>
        @endif
    </div>

    <!-- Categories List View -->
    <div id="listView" class="categories-list d-none">
        @if($categories->count() > 0)
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Danh mục</th>
                                    <th>Mô tả</th>
                                    <th style="width: 120px;">Số đề thi</th>
                                    <th style="width: 120px;">Số câu hỏi</th>
                                    <th style="width: 150px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($category->icon)
                                                    <i class="{{ $category->icon }} me-2" style="color: {{ $category->color }}; font-size: 1.2em;"></i>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $category->name }}</div>
                                                    <div class="text-muted small">Thứ tự: {{ $category->sort_order }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ Str::limit($category->description, 60) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">0</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">0</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('teacher.categories.show', $category) }}" 
                                                   class="btn btn-outline-info" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('teacher.categories.edit', $category) }}" 
                                                   class="btn btn-outline-warning" title="Chỉnh sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-outline-danger delete-btn"
                                                        data-category-id="{{ $category->id }}"
                                                        data-category-name="{{ $category->name }}"
                                                        title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-folder-x display-1 text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có danh mục nào</h5>
                <p class="text-muted">Tạo danh mục đầu tiên để bắt đầu tổ chức đề thi và câu hỏi của bạn.</p>
                <a href="{{ route('teacher.categories.create') }}" class="btn btn-success mt-2">
                    <i class="bi bi-plus"></i> Tạo danh mục đầu tiên
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $categories->links() }}
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
.categories-grid .category-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.categories-grid .category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.category-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
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

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

.categories-list .table th {
    border-top: none;
    font-weight: 600;
    color: #374151;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize with grid view
    toggleView('grid');
    
    // Handle delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.dataset.categoryId;
            const categoryName = this.dataset.categoryName;
            
            document.getElementById('categoryName').textContent = categoryName;
            document.getElementById('deleteForm').action = `{{ route('teacher.categories.index') }}/${categoryId}`;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});

function toggleView(viewType) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.querySelector('button[onclick="toggleView(\'grid\')"]');
    const listBtn = document.querySelector('button[onclick="toggleView(\'list\')"]');
    
    if (viewType === 'grid') {
        gridView.classList.remove('d-none');
        listView.classList.add('d-none');
        gridBtn.classList.add('btn-primary');
        gridBtn.classList.remove('btn-outline-secondary');
        listBtn.classList.add('btn-outline-secondary');
        listBtn.classList.remove('btn-primary');
    } else {
        gridView.classList.add('d-none');
        listView.classList.remove('d-none');
        listBtn.classList.add('btn-primary');
        listBtn.classList.remove('btn-outline-secondary');
        gridBtn.classList.add('btn-outline-secondary');
        gridBtn.classList.remove('btn-primary');
    }
    
    // Store preference in localStorage
    localStorage.setItem('teacherCategoryView', viewType);
}

function createExamWithCategory(categoryId) {
    // TODO: Redirect to exam creation with pre-selected category
    alert('Chức năng tạo đề thi sẽ được triển khai sau. Category ID: ' + categoryId);
}

// Load saved view preference
window.addEventListener('load', function() {
    const savedView = localStorage.getItem('teacherCategoryView') || 'grid';
    toggleView(savedView);
});
</script>
@endpush