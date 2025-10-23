@extends('layouts.student-dashboard')

@section('title', 'Thông báo')

@section('student-dashboard-content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thông báo</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span>Thông báo của bạn</span>
                <span class="badge bg-primary">Chưa đọc: {{ $unreadCount }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('student.notifications', ['unread' => 1]) }}" class="btn btn-outline-primary btn-sm {{ $onlyUnread ? 'active' : '' }}">
                    <i class="bi bi-envelope-open"></i> Chỉ hiện chưa đọc
                </a>
                <form action="{{ url('/user/notifications/read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check2-all"></i> Đánh dấu tất cả đã đọc
                    </button>
                </form>
            </div>
        </div>
        <div class="list-group list-group-flush">
            @forelse($notifications as $n)
                @php
                    $data = $n->data ?? [];
                    $type = $data['type'] ?? '';
                    $message = $data['message'] ?? 'Thông báo';
                    $icon = 'bi-bell text-secondary';
                    $url = '#';
                    if ($type === 'exam_completed' && !empty($data['attempt_id'])) {
                        $icon = 'bi-check-circle text-success';
                        $url = url('/student/results/' . $data['attempt_id']);
                    } elseif ($type === 'exam_published' && !empty($data['exam_id'])) {
                        $icon = 'bi-file-earmark-plus text-primary';
                        $url = url('/student/exams/' . $data['exam_id']);
                    } elseif ($type === 'exam_reminder' && !empty($data['exam_id'])) {
                        $icon = 'bi-alarm text-warning';
                        $url = url('/student/exams/' . $data['exam_id']);
                    }
                @endphp
                <div class="list-group-item d-flex align-items-start {{ $n->read_at ? '' : 'bg-light' }}">
                    <div class="me-3 flex-shrink-0">
                        <i class="bi {{ $icon }}" style="font-size: 1.25rem"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <a href="{{ $url }}" class="stretched-link text-decoration-none">
                                <div class="fw-semibold {{ $n->read_at ? '' : 'text-dark' }}">{!! $message !!}</div>
                            </a>
                            <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="mt-1">
                            <span class="badge rounded-pill {{ $n->read_at ? 'bg-secondary' : 'bg-success' }}">
                                {{ $n->read_at ? 'Đã đọc' : 'Chưa đọc' }}
                            </span>
                        </div>
                    </div>
                    <div class="ms-3">
                        @if(!$n->read_at)
                        <form action="{{ url('/user/notifications/'.$n->id.'/read') }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-success" type="submit">
                                <i class="bi bi-check2"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-4">
                    Không có thông báo.
                </div>
            @endforelse
        </div>
        @if($notifications->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Hiển thị {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} / {{ $notifications->total() }}
            </div>
            <div>
                {{ $notifications->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
