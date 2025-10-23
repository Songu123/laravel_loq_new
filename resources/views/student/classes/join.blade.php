@extends('layouts.student-dashboard')

@section('title','Tham gia lớp')

@section('student-dashboard-content')
<div class="container-fluid">
    <h4 class="mb-3">Tham gia lớp bằng mã</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('student.classes.join.post') }}">
                @csrf
                <div class="mb-3" style="max-width: 420px;">
                    <label class="form-label">Mã lớp</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" placeholder="VD: ABC123" style="text-transform: uppercase;" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Tham gia</button>
            </form>
        </div>
    </div>
</div>
@endsection
