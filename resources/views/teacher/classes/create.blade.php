@extends('layouts.teacher-dashboard')

@section('title','Tạo lớp học')

@section('teacher-dashboard-content')
<div class="container-fluid">
    <h4 class="mb-3">Tạo lớp học mới</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.classes.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tên lớp</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Chế độ tham gia lớp</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="require_approval" id="modeApproval" value="1" {{ old('require_approval', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="modeApproval">Phải duyệt mới vào lớp</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="require_approval" id="modeAuto" value="0" {{ old('require_approval') === '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="modeAuto">Tự động tham gia (không cần duyệt)</label>
                    </div>
                    <div class="form-text">Bạn có thể thay đổi chế độ sau khi tạo lớp.</div>
                </div>
                <button type="submit" class="btn btn-primary">Tạo lớp</button>
            </form>
        </div>
    </div>
</div>
@endsection
