@extends('layouts.teacher-dashboard')

@section('title', $class->name)

@section('teacher-dashboard-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ $class->name }}</h4>
        <form action="{{ route('teacher.classes.regenerate-code', $class) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Tạo mã mới</button>
        </form>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Mã tham gia:</strong> <span class="badge bg-primary">{{ $class->join_code }}</span></p>
            <p>
                <strong>Chế độ tham gia:</strong>
                @if($class->require_approval)
                    <span class="badge bg-warning text-dark">Cần duyệt</span>
                @else
                    <span class="badge bg-success">Tự động</span>
                @endif
            </p>
            <form action="{{ route('teacher.classes.toggle-approval', $class) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">Chuyển chế độ</button>
            </form>
            <p class="mb-0 mt-2">{{ $class->description }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Đề thi trong lớp</span>
            <form class="d-flex" method="POST" action="{{ route('teacher.classes.attach-exam', $class) }}">
                @csrf
                <select name="exam_id" class="form-select form-select-sm me-2" style="min-width: 260px;">
                    @foreach($teacherExams as $ex)
                        <option value="{{ $ex->id }}">{{ $ex->title }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Thêm đề</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên đề</th>
                        <th>Danh mục</th>
                        <th>Câu hỏi</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($class->exams as $i => $ex)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $ex->title }}</td>
                        <td>{{ optional($ex->category)->name }}</td>
                        <td>{{ $ex->total_questions }}</td>
                        <td class="text-end">
                            <form action="{{ route('teacher.classes.detach-exam', $class) }}" method="POST" class="d-inline" onsubmit="return confirm('Gỡ đề này khỏi lớp?');">
                                @csrf
                                <input type="hidden" name="exam_id" value="{{ $ex->id }}">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Gỡ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Chưa có đề nào trong lớp.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Danh sách học sinh</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Tham gia lúc</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($class->students as $i => $s)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->email }}</td>
                        <td>{{ optional($s->pivot->created_at ?? null)->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <form action="{{ route('teacher.classes.remove-student', $class) }}" method="POST" onsubmit="return confirm('Xóa học sinh khỏi lớp?');">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $s->id }}">
                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Chưa có học sinh tham gia.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Yêu cầu tham gia ({{ $class->joinRequests->where('status','pending')->count() }})</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Học sinh</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($class->joinRequests as $i => $r)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ optional($r->student)->name }}<div class="text-muted small">{{ optional($r->student)->email }}</div></td>
                        <td>
                            @if($r->status === 'pending')
                                <span class="badge bg-warning">Chờ duyệt</span>
                            @elseif($r->status === 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @else
                                <span class="badge bg-danger">Từ chối</span>
                            @endif
                        </td>
                        <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            @if($r->status === 'pending')
                                <form action="{{ route('teacher.classes.requests.approve', [$class, $r]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success" type="submit">Duyệt</button>
                                </form>
                                <form action="{{ route('teacher.classes.requests.reject', [$class, $r]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="note" value="">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Từ chối</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Không có yêu cầu.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
