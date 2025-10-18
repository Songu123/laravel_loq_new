@extends('layouts.teacher-dashboard')

@section('title', 'Chỉnh sửa danh mục')

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
            <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa danh mục</h1>
            <p class="text-muted">Chỉnh sửa danh mục: {{ $category->name }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('teacher.categories.show', $category) }}" class="btn btn-info">
                <i class="bi bi-eye"></i> Xem chi tiết
            </a>
            <a href="{{ route('teacher.categories.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">Thông tin danh mục</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $category->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Slug hiện tại: <code>{{ $category->slug }}</code>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Mô tả ngắn về danh mục này...">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Color -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Màu sắc <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="color" 
                                               class="form-control form-control-color @error('color') is-invalid @enderror" 
                                               id="color" 
                                               name="color" 
                                               value="{{ old('color', $category->color) }}" 
                                               required>
                                        <input type="text" 
                                               class="form-control" 
                                               id="colorText" 
                                               value="{{ old('color', $category->color) }}" 
                                               readonly>
                                    </div>
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Icon -->
                            <div class="col-md-6">
                                <x-icon-picker name="icon" 
                                             value="{{ old('icon', $category->icon ?: 'bi-folder') }}" 
                                             label="Icon" />
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Thứ tự sắp xếp</label>
                            <input type="number" 
                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   value="{{ old('sort_order', $category->sort_order) }}" 
                                   min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Số thứ tự để sắp xếp danh mục (số nhỏ hiển thị trước)</div>
                        </div>

                        <!-- Status Notice -->
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Thông tin:</strong> Danh mục sẽ được cập nhật và tiếp tục hoạt động ngay lập tức.
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('teacher.categories.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Cập nhật danh mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <!-- Preview Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">Xem trước</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <div class="category-preview d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; border-radius: 50%; background-color: {{ $category->color }};">
                                <i id="previewIcon" class="{{ $category->icon ?: 'bi bi-folder' }} text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h5 id="previewName" class="mb-2">{{ $category->name }}</h5>
                        <p id="previewDescription" class="text-muted small">{{ $category->description ?: 'Chưa có mô tả...' }}</p>
                        <div class="badge {{ $category->is_active ? 'bg-success' : 'bg-warning' }}" id="previewStatus">
                            @if($category->is_active)
                                <i class="bi bi-check-circle"></i> Đã duyệt
                            @else
                                <i class="bi bi-clock"></i> Chờ duyệt
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Info -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">Thông tin danh mục</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td>{{ $category->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Slug:</strong></td>
                            <td><code>{{ $category->slug }}</code></td>
                        </tr>
                        <tr>
                            <td><strong>Người tạo:</strong></td>
                            <td>{{ $category->creator->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ngày tạo:</strong></td>
                            <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Cập nhật:</strong></td>
                            <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Popular Icons -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">Icon phổ biến</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi bi-book">
                                <i class="bi bi-book"></i><br><small>Sách</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi bi-calculator">
                                <i class="bi bi-calculator"></i><br><small>Toán</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi bi-globe">
                                <i class="bi bi-globe"></i><br><small>Địa lý</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi bi-cpu">
                                <i class="bi bi-cpu"></i><br><small>Tin học</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi bi-translate">
                                <i class="bi bi-translate"></i><br><small>Ngoại ngữ</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi bi-palette">
                                <i class="bi bi-palette"></i><br><small>Mỹ thuật</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const colorInput = document.getElementById('color');
    const colorTextInput = document.getElementById('colorText');
    const iconInput = document.getElementById('icon');
    
    // Preview elements
    const previewIcon = document.getElementById('previewIcon');
    const previewName = document.getElementById('previewName');
    const previewDescription = document.getElementById('previewDescription');
    const categoryPreview = document.querySelector('.category-preview');

    // Update preview functions
    function updatePreview() {
        // Name
        previewName.textContent = nameInput.value || 'Tên danh mục';
        
        // Description
        previewDescription.textContent = descriptionInput.value || 'Chưa có mô tả...';
        
        // Color
        const color = colorInput.value;
        categoryPreview.style.backgroundColor = color;
        
        // Icon
        const iconClass = iconInput.value || 'bi bi-folder';
        previewIcon.className = iconClass + ' text-white';
        document.getElementById('iconPreview').className = iconClass;
    }

    // Color picker sync
    colorInput.addEventListener('input', function() {
        colorTextInput.value = this.value;
        updatePreview();
    });

    // Icon buttons
    document.querySelectorAll('.icon-btn').forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.dataset.icon;
            iconInput.value = icon;
            updatePreview();
        });
    });

    // Real-time preview updates
    nameInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    iconInput.addEventListener('input', updatePreview);
});
</script>
@endpush