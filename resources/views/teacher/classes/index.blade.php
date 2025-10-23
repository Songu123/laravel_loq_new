@extends('layouts.teacher-dashboard')

@section('title','Lớp học của tôi')

@section('teacher-dashboard-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Lớp học của tôi</h4>
        <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">Tạo lớp</a>
    </div>

    <div class="row g-3">
        @forelse($classes as $class)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $class->name }}</h5>
                        <p class="text-muted small mb-1">Mã tham gia: <span class="fw-bold">{{ $class->join_code }}</span></p>
                        <p class="text-muted small mb-2">
                            Chế độ: 
                            @if($class->require_approval)
                                <span class="badge bg-warning text-dark">Cần duyệt</span>
                            @else
                                <span class="badge bg-success">Tự động</span>
                            @endif
                        </p>
                        <p class="mb-0">{{ Str::limit($class->description, 120) }}</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <a href="{{ route('teacher.classes.show', $class) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                        <form action="{{ route('teacher.classes.regenerate-code', $class) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Tạo mã mới</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-muted">Chưa có lớp nào.</div>
        @endforelse
    </div>

    <div class="mt-3">{{ $classes->links() }}</div>
</div>
@endsection
