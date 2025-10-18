@extends('layouts.dashboard')

@section('title', 'Chi tiết danh mục')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Chi tiết danh mục</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Info Card -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin chi tiết</h6>
                        <div class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }} fs-6">
                            {{ $category->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-auto">
                            <div class="category-icon d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; border-radius: 50%; background-color: {{ $category->color }};">
                                @if($category->icon)
                                    <i class="{{ $category->icon }} text-white" style="font-size: 2rem;"></i>
                                @else
                                    <i class="fas fa-folder text-white" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-2">{{ $category->name }}</h2>
                            <p class="text-muted mb-0">
                                {{ $category->description ?: 'Chưa có mô tả cho danh mục này.' }}
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold" style="width: 120px;">ID:</td>
                                    <td>{{ $category->id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Slug:</td>
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
                                <tr>
                                    <td class="fw-semibold">Cập nhật:</td>
                                    <td>{{ $category->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border-end">
                                <h3 class="text-primary mb-1">0</h3>
                                <p class="text-muted mb-0 small">Bài kiểm tra</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h3 class="text-success mb-1">0</h3>
                                <p class="text-muted mb-0 small">Câu hỏi</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h3 class="text-info mb-1">0</h3>
                                <p class="text-muted mb-0 small">Học sinh tham gia</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-warning mb-1">0</h3>
                            <p class="text-muted mb-0 small">Lần làm bài</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Chỉnh sửa danh mục
                        </a>
                        
                        <button class="btn btn-{{ $category->is_active ? 'secondary' : 'success' }} toggle-status"
                                data-category-id="{{ $category->id }}">
                            <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }}"></i>
                            {{ $category->is_active ? 'Tạm dừng' : 'Kích hoạt' }}
                        </button>
                        
                        <hr>
                        
                        <button class="btn btn-primary" disabled>
                            <i class="fas fa-plus"></i> Tạo bài kiểm tra
                        </button>
                        
                        <button class="btn btn-info" disabled>
                            <i class="fas fa-list"></i> Xem bài kiểm tra
                        </button>
                        
                        <hr>
                        
                        <button class="btn btn-outline-danger delete-btn"
                                data-category-id="{{ $category->id }}"
                                data-category-name="{{ $category->name }}">
                            <i class="fas fa-trash"></i> Xóa danh mục
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Categories -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh mục khác</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Danh sách các danh mục tương tự sẽ hiển thị ở đây.</p>
                    <div class="text-center">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-primary">
                            Xem tất cả danh mục
                        </a>
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
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Hành động này không thể hoàn tác!</small></p>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status toggle
    document.querySelector('.toggle-status')?.addEventListener('click', function() {
        const categoryId = this.dataset.categoryId;
        const btn = this;
        
        fetch(`/admin/categories/${categoryId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to update all status indicators
                location.reload();
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra khi cập nhật trạng thái!');
        });
    });

    // Handle delete confirmation
    document.querySelector('.delete-btn')?.addEventListener('click', function() {
        const categoryId = this.dataset.categoryId;
        const categoryName = this.dataset.categoryName;
        
        document.getElementById('categoryName').textContent = categoryName;
        document.getElementById('deleteForm').action = `/admin/categories/${categoryId}`;
        
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>
@endpush