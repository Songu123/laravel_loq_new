@extends('layouts.dashboard')

@section('title', 'Quản lý danh mục')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Quản lý danh mục</h1>
            <p class="text-muted">Tạo và quản lý các danh mục cho bài kiểm tra</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus"></i> Thêm danh mục mới
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Categories Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách danh mục</h6>
        </div>
        <div class="card-body">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tên danh mục</th>
                                <th>Mô tả</th>
                                <th style="width: 80px;">Màu sắc</th>
                                <th style="width: 80px;">Icon</th>
                                <th style="width: 100px;">Trạng thái</th>
                                <th style="width: 100px;">Thứ tự</th>
                                <th>Người tạo</th>
                                <th>Ngày tạo</th>
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
                                                <i class="{{ $category->icon }} me-2" style="color: {{ $category->color }};"></i>
                                            @endif
                                            <span class="fw-semibold">{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ Str::limit($category->description, 50) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="color-preview me-2" 
                                                 style="width: 20px; height: 20px; background-color: {{ $category->color }}; border-radius: 50%; border: 1px solid #ddd;"></div>
                                            <small class="text-muted">{{ $category->color }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($category->icon)
                                            <i class="{{ $category->icon }}" style="color: {{ $category->color }}; font-size: 1.2em;"></i>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm toggle-status {{ $category->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                data-category-id="{{ $category->id }}"
                                                title="Click để {{ $category->is_active ? 'vô hiệu hóa' : 'kích hoạt' }}">
                                            <i class="fas {{ $category->is_active ? 'fa-check' : 'fa-times' }}"></i>
                                            {{ $category->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $category->sort_order }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $category->creator->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $category->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.categories.show', $category) }}" 
                                               class="btn btn-sm btn-outline-info"
                                               title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="btn btn-sm btn-outline-warning"
                                               title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-category-id="{{ $category->id }}"
                                                    data-category-name="{{ $category->name }}"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có danh mục nào</h5>
                    <p class="text-muted">Hãy tạo danh mục đầu tiên để bắt đầu tổ chức bài kiểm tra.</p>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tạo danh mục đầu tiên
                    </a>
                </div>
            @endif
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
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
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
                    // Update button appearance
                    if (data.is_active) {
                        btn.className = 'btn btn-sm toggle-status btn-success';
                        btn.innerHTML = '<i class="fas fa-check"></i> Hoạt động';
                        btn.title = 'Click để vô hiệu hóa';
                    } else {
                        btn.className = 'btn btn-sm toggle-status btn-secondary';
                        btn.innerHTML = '<i class="fas fa-times"></i> Tạm dừng';
                        btn.title = 'Click để kích hoạt';
                    }
                    
                    // Show success message
                    showAlert('success', data.message);
                }
            })
            .catch(error => {
                showAlert('danger', 'Có lỗi xảy ra khi cập nhật trạng thái!');
            });
        });
    });

    // Handle delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.dataset.categoryId;
            const categoryName = this.dataset.categoryName;
            
            document.getElementById('categoryName').textContent = categoryName;
            document.getElementById('deleteForm').action = `/admin/categories/${categoryId}`;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            new bootstrap.Alert(alert).close();
        }
    }, 3000);
}
</script>
@endpush