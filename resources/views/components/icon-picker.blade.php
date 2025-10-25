@props(['name' => 'icon', 'value' => '', 'label' => 'Icon', 'required' => false])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required) <span class="text-danger">*</span> @endif
    </label>
    
    <div class="input-group">
        <span class="input-group-text">
            <i class="{{ old($name, $value) ?: 'bi-circle' }}" id="preview_{{ $name }}"></i>
        </span>
        <input 
            type="text" 
            class="form-control" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}" 
            placeholder="VD: bi-book, bi-calculator, fa-home..."
            {{ $required ? 'required' : '' }}
        >
    </div>
    
    <div class="form-text">Nhập class name của icon (Bootstrap Icons hoặc FontAwesome)</div>
    
    @error($name)
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    
    <div class="mt-2">
        <small class="text-muted">Gợi ý: </small>
        @foreach(['bi-book', 'bi-calculator', 'bi-atom', 'bi-globe', 'bi-trophy', 'bi-heart', 'bi-lightbulb', 'bi-folder'] as $iconSuggestion)
            <button 
                type="button" 
                class="btn btn-sm btn-outline-secondary me-1 mb-1 icon-suggestion" 
                data-icon="{{ $iconSuggestion }}"
                data-target="{{ $name }}"
            >
                <i class="{{ $iconSuggestion }}"></i> {{ $iconSuggestion }}
            </button>
        @endforeach
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-update preview when typing
    document.querySelectorAll('input[id^="icon"]').forEach(input => {
        const previewId = 'preview_' + input.id;
        const preview = document.getElementById(previewId);
        
        if (preview) {
            input.addEventListener('input', function() {
                preview.className = this.value || 'bi-circle';
            });
        }
    });
    
    // Click suggestion to fill input
    document.querySelectorAll('.icon-suggestion').forEach(btn => {
        btn.addEventListener('click', function() {
            const iconClass = this.dataset.icon;
            const targetName = this.dataset.target;
            const input = document.getElementById(targetName);
            const preview = document.getElementById('preview_' + targetName);
            
            if (input) {
                input.value = iconClass;
                if (preview) {
                    preview.className = iconClass;
                }
            }
        });
    });
});
</script>
@endpush
@endonce
