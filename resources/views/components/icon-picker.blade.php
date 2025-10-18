@props(['name' => 'icon', 'value' => '', 'label' => 'Icon', 'required' => false])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required) <span class="text-danger">*</span> @endif
    </label>
    
    <div class="input-group">
        <span class="input-group-text icon-preview" id="iconPreview">
            @if($value)
                <i class="{{ $value }}" aria-hidden="true"></i>
            @else
                <i class="bi bi-circle" aria-hidden="true"></i>
            @endif
        </span>
        <input type="text" 
               class="form-control" 
               id="{{ $name }}" 
               name="{{ $name }}" 
               value="{{ old($name, $value) }}" 
               placeholder="Chọn icon hoặc nhập class name..."
               aria-describedby="iconPreview {{ $name }}_help">
        <button class="btn btn-outline-secondary" type="button" id="iconPickerBtn" data-bs-toggle="modal" data-bs-target="#iconPickerModal" aria-label="Chọn icon">
            <i class="bi bi-grid-3x3-gap"></i> Chọn
        </button>
    </div>
    
    <div id="{{ $name }}_help" class="form-text">Chọn icon từ thư viện hoặc nhập class name trực tiếp</div>
    </div>
    
    @error($name)
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<!-- Icon Picker Modal -->
<div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="iconPickerModalLabel">Chọn Icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Box -->
                <div class="mb-3">
                    <input type="text" class="form-control" id="iconSearch" placeholder="Tìm kiếm icon...">
                </div>
                
                <!-- Icon Categories -->
                <ul class="nav nav-tabs" id="iconTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="bootstrap-tab" data-bs-toggle="tab" data-bs-target="#bootstrap-icons" type="button" role="tab">
                            Bootstrap Icons
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fontawesome-tab" data-bs-toggle="tab" data-bs-target="#fontawesome-icons" type="button" role="tab">
                            FontAwesome
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content mt-3" id="iconTabContent">
                    <!-- Bootstrap Icons -->
                    <div class="tab-pane fade show active" id="bootstrap-icons" role="tabpanel">
                        <div class="row g-2" id="bootstrapIconGrid">
                            @php
                            $bootstrapIcons = [
                                'bi-book', 'bi-calculator', 'bi-atom', 'bi-globe', 'bi-trophy', 
                                'bi-star', 'bi-heart', 'bi-lightbulb', 'bi-laptop', 'bi-phone',
                                'bi-camera', 'bi-music-note', 'bi-brush', 'bi-palette', 'bi-pencil',
                                'bi-folder', 'bi-file-text', 'bi-download', 'bi-upload', 'bi-cloud',
                                'bi-shield', 'bi-lock', 'bi-unlock', 'bi-key', 'bi-gear',
                                'bi-wrench', 'bi-tools', 'bi-hammer', 'bi-screwdriver', 'bi-house',
                                'bi-building', 'bi-shop', 'bi-cart', 'bi-bag', 'bi-gift',
                                'bi-envelope', 'bi-telephone', 'bi-chat', 'bi-send', 'bi-bell',
                                'bi-calendar', 'bi-clock', 'bi-stopwatch', 'bi-hourglass', 'bi-alarm',
                                'bi-sun', 'bi-moon', 'bi-cloud-rain', 'bi-lightning', 'bi-snow',
                                'bi-tree', 'bi-flower1', 'bi-bug', 'bi-butterfly', 'bi-fish',
                                'bi-person', 'bi-people', 'bi-person-check', 'bi-person-plus', 'bi-emoji-smile',
                                'bi-hand-thumbs-up', 'bi-hand-thumbs-down', 'bi-hand-index', 'bi-eye', 'bi-eyeglasses',
                                'bi-headphones', 'bi-mic', 'bi-speaker', 'bi-volume-up', 'bi-play',
                                'bi-pause', 'bi-stop', 'bi-skip-forward', 'bi-skip-backward', 'bi-shuffle',
                                'bi-repeat', 'bi-record', 'bi-film', 'bi-tv', 'bi-display'
                            ];
                            @endphp
                            
                            @foreach($bootstrapIcons as $icon)
                                <div class="col-2 col-md-1">
                                    <div class="icon-option p-2 text-center border rounded" 
                                         data-icon="{{ $icon }}" 
                                         title="{{ $icon }}"
                                         role="button"
                                         tabindex="0"
                                         aria-label="Chọn icon {{ $icon }}">
                                        <i class="{{ $icon }}" style="font-size: 1.5rem;" aria-hidden="true"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- FontAwesome Icons -->
                    <div class="tab-pane fade" id="fontawesome-icons" role="tabpanel">
                        <div class="row g-2" id="fontawesomeIconGrid">
                            @php
                            $fontawesomeIcons = [
                                'fas fa-book', 'fas fa-calculator', 'fas fa-atom', 'fas fa-globe', 'fas fa-trophy',
                                'fas fa-star', 'fas fa-heart', 'fas fa-lightbulb', 'fas fa-laptop', 'fas fa-mobile',
                                'fas fa-camera', 'fas fa-music', 'fas fa-paint-brush', 'fas fa-palette', 'fas fa-pencil',
                                'fas fa-folder', 'fas fa-file-text', 'fas fa-download', 'fas fa-upload', 'fas fa-cloud',
                                'fas fa-shield', 'fas fa-lock', 'fas fa-unlock', 'fas fa-key', 'fas fa-cog',
                                'fas fa-wrench', 'fas fa-tools', 'fas fa-hammer', 'fas fa-screwdriver', 'fas fa-home',
                                'fas fa-building', 'fas fa-store', 'fas fa-shopping-cart', 'fas fa-shopping-bag', 'fas fa-gift',
                                'fas fa-envelope', 'fas fa-phone', 'fas fa-comment', 'fas fa-paper-plane', 'fas fa-bell',
                                'fas fa-calendar', 'fas fa-clock', 'fas fa-stopwatch', 'fas fa-hourglass', 'fas fa-bell',
                                'fas fa-sun', 'fas fa-moon', 'fas fa-cloud-rain', 'fas fa-bolt', 'fas fa-snowflake',
                                'fas fa-tree', 'fas fa-seedling', 'fas fa-bug', 'fas fa-dove', 'fas fa-fish',
                                'fas fa-user', 'fas fa-users', 'fas fa-user-check', 'fas fa-user-plus', 'fas fa-smile',
                                'fas fa-thumbs-up', 'fas fa-thumbs-down', 'fas fa-hand-point-up', 'fas fa-eye', 'fas fa-glasses',
                                'fas fa-headphones', 'fas fa-microphone', 'fas fa-volume-up', 'fas fa-play', 'fas fa-pause',
                                'fas fa-stop', 'fas fa-step-forward', 'fas fa-step-backward', 'fas fa-random', 'fas fa-redo',
                                'fas fa-circle', 'fas fa-video', 'fas fa-tv', 'fas fa-desktop'
                            ];
                            @endphp
                            
                            @foreach($fontawesomeIcons as $icon)
                                <div class="col-2 col-md-1">
                                    <div class="icon-option p-2 text-center border rounded" 
                                         data-icon="{{ $icon }}" 
                                         title="{{ $icon }}"
                                         role="button"
                                         tabindex="0"
                                         aria-label="Chọn icon {{ $icon }}">
                                        <i class="{{ $icon }}" style="font-size: 1.5rem;" aria-hidden="true"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="selectIconBtn" disabled>Chọn Icon</button>
            </div>
        </div>
    </div>
</div>

<style>
.icon-option {
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.icon-option:hover,
.icon-option:focus {
    background-color: #e9ecef;
    border-color: #0d6efd !important;
    transform: scale(1.1);
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

.icon-option.selected {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd !important;
}

.icon-preview {
    min-width: 45px;
}

#iconSearch {
    border-radius: 8px;
}

.modal-lg {
    max-width: 800px;
}

.nav-tabs .nav-link {
    border-radius: 8px 8px 0 0;
}

.tab-content {
    max-height: 400px;
    overflow-y: auto;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedIcon = '{{ $value }}';
    const iconInput = document.getElementById('{{ $name }}');
    const iconPreview = document.getElementById('iconPreview');
    const selectIconBtn = document.getElementById('selectIconBtn');
    const iconSearch = document.getElementById('iconSearch');
    
    // Handle icon selection
    document.querySelectorAll('.icon-option').forEach(option => {
        option.addEventListener('click', function() {
            selectIcon(this);
        });
        
        // Handle keyboard navigation
        option.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectIcon(this);
            }
        });
    });
    
    function selectIcon(element) {
        // Remove previous selection
        document.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
        
        // Add selection to clicked option
        element.classList.add('selected');
        
        // Store selected icon
        selectedIcon = element.dataset.icon;
        
        // Enable select button
        selectIconBtn.disabled = false;
    }
    
    // Handle icon confirmation
    selectIconBtn.addEventListener('click', function() {
        if (selectedIcon) {
            // Update input value
            iconInput.value = selectedIcon;
            
            // Update preview
            iconPreview.innerHTML = `<i class="${selectedIcon}" aria-hidden="true"></i>`;
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('iconPickerModal')).hide();
        }
    });
    
    // Handle search
    iconSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const activeTab = document.querySelector('.tab-pane.active');
        const iconOptions = activeTab.querySelectorAll('.icon-option');
        
        iconOptions.forEach(option => {
            const iconName = option.dataset.icon.toLowerCase();
            const parentCol = option.closest('.col-2, .col-md-1');
            
            if (iconName.includes(searchTerm)) {
                parentCol.style.display = '';
            } else {
                parentCol.style.display = 'none';
            }
        });
    });
    
    // Reset search when switching tabs
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function() {
            iconSearch.value = '';
            document.querySelectorAll('.col-2, .col-md-1').forEach(col => {
                col.style.display = '';
            });
        });
    });
    
    // Handle manual input
    iconInput.addEventListener('input', function() {
        const iconClass = this.value.trim();
        if (iconClass) {
            // Update preview
            iconPreview.innerHTML = `<i class="${iconClass}" aria-hidden="true"></i>`;
        } else {
            // Reset to default
            iconPreview.innerHTML = `<i class="bi bi-circle" aria-hidden="true"></i>`;
        }
    });
    
    // Highlight current icon if exists
    if (selectedIcon) {
        const currentIcon = document.querySelector(`[data-icon="${selectedIcon}"]`);
        if (currentIcon) {
            currentIcon.classList.add('selected');
            selectIconBtn.disabled = false;
        }
    }
});
</script>