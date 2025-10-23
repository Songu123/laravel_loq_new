@extends('layouts.student-dashboard')

@section('title','Lớp của tôi')

@section('student-dashboard-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Lớp của tôi</h4>
        <a href="{{ route('student.classes.join') }}" class="btn btn-primary">Tham gia lớp</a>
    </div>

    <div class="row g-3">
        @forelse($classes as $class)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('student.classes.show', $class) }}" class="text-decoration-none">{{ $class->name }}</a>
                        </h5>
                        <p class="text-muted small mb-2">Giáo viên: {{ optional($class->teacher)->name }}</p>
                        <p class="mb-0">{{ Str::limit($class->description, 120) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-muted">Bạn chưa tham gia lớp nào.</div>
        @endforelse
    </div>

    <div class="mt-3">{{ $classes->links() }}</div>
</div>
@endsection
