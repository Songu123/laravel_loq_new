@extends('layouts.dashboard')

@section('title', 'Thêm danh mục mới')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Thêm danh mục mới</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                    <li class="breadcrumb-item active">Thêm mới</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin danh mục</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tên danh mục sẽ tự động tạo slug URL thân thiện</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Mô tả ngắn về danh mục này...">{{ old('description') }}</textarea>
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
                                               value="{{ old('color', '#3498db') }}" 
                                               required>
                                        <input type="text" 
                                               class="form-control" 
                                               id="colorText" 
                                               value="{{ old('color', '#3498db') }}" 
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
                                             value="{{ old('icon', 'bi-folder') }}" 
                                             label="Icon" />
                                    </div>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Thứ tự sắp xếp</label>
                            <input type="number" 
                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   value="{{ old('sort_order', 0) }}" 
                                   min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Số thứ tự để sắp xếp danh mục (0 = đầu tiên)</div>
                        </div>

                        <!-- Active Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt danh mục
                                </label>
                            </div>
                            <div class="form-text">Chỉ những danh mục được kích hoạt mới hiển thị trong hệ thống</div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu danh mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Xem trước</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <div class="category-preview d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; border-radius: 50%; background-color: #3498db;">
                                <i id="previewIcon" class="fas fa-folder text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h5 id="previewName" class="mb-2">Tên danh mục</h5>
                        <p id="previewDescription" class="text-muted small">Mô tả danh mục sẽ hiển thị ở đây...</p>
                        <div class="badge bg-success" id="previewStatus">Hoạt động</div>
                    </div>
                </div>
            </div>

            <!-- Popular Icons -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Icon phổ biến</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-book">
                                <i class="fas fa-book"></i><br><small>Book</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-graduation-cap">
                                <i class="fas fa-graduation-cap"></i><br><small>Education</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-calculator">
                                <i class="fas fa-calculator"></i><br><small>Math</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-globe">
                                <i class="fas fa-globe"></i><br><small>Geography</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-atom">
                                <i class="fas fa-atom"></i><br><small>Science</small>
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-language">
                                <i class="fas fa-language"></i><br><small>Language</small>
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
    const isActiveInput = document.getElementById('is_active');
    
    // Preview elements
    const previewIcon = document.getElementById('previewIcon');
    const previewName = document.getElementById('previewName');
    const previewDescription = document.getElementById('previewDescription');
    const previewStatus = document.getElementById('previewStatus');
    const categoryPreview = document.querySelector('.category-preview');

    // Update preview functions
    function updatePreview() {
        // Name
        previewName.textContent = nameInput.value || 'Tên danh mục';
        
        // Description
        previewDescription.textContent = descriptionInput.value || 'Mô tả danh mục sẽ hiển thị ở đây...';
        
        // Color
        const color = colorInput.value;
        categoryPreview.style.backgroundColor = color;
        
        // Icon
        const iconClass = iconInput.value || 'fas fa-folder';
        previewIcon.className = iconClass + ' text-white';
        document.getElementById('iconPreview').className = iconClass;
        
        // Status
        if (isActiveInput.checked) {
            previewStatus.className = 'badge bg-success';
            previewStatus.textContent = 'Hoạt động';
        } else {
            previewStatus.className = 'badge bg-secondary';
            previewStatus.textContent = 'Tạm dừng';
        }
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
    isActiveInput.addEventListener('change', updatePreview);

    // Initial preview
    updatePreview();
});
</script>
@endpush