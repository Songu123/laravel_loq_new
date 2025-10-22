@extends('layouts.teacher-dashboard')

@section('title', 'Vi phạm & Gian lận')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Vi phạm & Gian lận</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
.violation-badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

.risk-level {
    width: 100px;
    text-align: center;
}

.risk-critical {
    background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
}

.risk-high {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.risk-medium {
    background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
}

.risk-low {
    background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
}

.attempt-card {
    transition: all 0.3s;
    border-left: 4px solid transparent;
}

.attempt-card.flagged {
    border-left-color: #dc3545;
}

.attempt-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.stats-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    height: 100%;
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
</style>
@endpush

@section('teacher-dashboard-content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-shield-exclamation text-danger me-2"></i>Quản lý Vi phạm & Gian lận</h2>
            <p class="text-muted mb-0">Theo dõi và xử lý các hành vi gian lận trong kỳ thi</p>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                <i class="bi bi-download me-1"></i>Xuất báo cáo
            </button>
            <button class="btn btn-primary" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i>Làm mới
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block mb-1">Tổng vi phạm</small>
                        <h3 class="mb-0">{{ $attempts->sum(function($a) { return $a->violations->count(); }) }}</h3>
                    </div>
                    <div class="stats-icon" style="background: #e3f2fd; color: #2196f3;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block mb-1">Bài thi bị gắn cờ</small>
                        <h3 class="mb-0">{{ $attempts->count() }}</h3>
                    </div>
                    <div class="stats-icon" style="background: #ffebee; color: #f44336;">
                        <i class="bi bi-flag-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block mb-1">Mức độ cao</small>
                        <h3 class="mb-0">{{ $attempts->where('high_severity_count', '>=', 3)->count() }}</h3>
                    </div>
                    <div class="stats-icon" style="background: #fce4ec; color: #e91e63;">
                        <i class="bi bi-shield-x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block mb-1">Cần xem xét</small>
                        <h3 class="mb-0">{{ $attempts->where('high_severity_count', '>=', 2)->count() }}</h3>
                    </div>
                    <div class="stats-icon" style="background: #fff3e0; color: #ff9800;">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.violations.flagged') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kỳ thi</label>
                    <select name="exam_id" class="form-select">
                        <option value="">Tất cả kỳ thi</option>
                        <!-- Add exam options here -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Loại vi phạm</label>
                    <select name="violation_type" class="form-select">
                        <option value="">Tất cả loại</option>
                        <option value="tab_switch">Chuyển tab</option>
                        <option value="copy_paste">Sao chép/Dán</option>
                        <option value="fullscreen_exit">Thoát fullscreen</option>
                        <option value="keyboard_shortcut">Phím tắt</option>
                        <option value="multiple_devices">Nhiều thiết bị</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mức độ nghiêm trọng</label>
                    <select name="severity" class="form-select">
                        <option value="">Tất cả mức độ</option>
                        <option value="4">Nghiêm trọng</option>
                        <option value="3">Cao</option>
                        <option value="2">Trung bình</option>
                        <option value="1">Thấp</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="bi bi-funnel me-1"></i>Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flagged Attempts List -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Danh sách bài thi bị gắn cờ</h5>
        </div>
        <div class="card-body p-0">
            @if($attempts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Học sinh</th>
                                <th>Kỳ thi</th>
                                <th>Thời gian</th>
                                <th>Điểm số</th>
                                <th>Vi phạm</th>
                                <th>Mức độ cao</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                <tr class="attempt-card {{ $attempt->is_flagged ? 'flagged' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2" style="width: 40px; height: 40px; background: #0d6efd; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                {{ strtoupper(substr($attempt->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $attempt->user->name }}</div>
                                                <small class="text-muted">{{ $attempt->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $attempt->exam->title }}</div>
                                        <small class="text-muted">{{ $attempt->exam->category->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $attempt->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $attempt->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $attempt->score >= 70 ? 'success' : ($attempt->score >= 50 ? 'warning' : 'danger') }} px-3 py-2">
                                            {{ $attempt->score }}/{{ $attempt->exam->total_marks }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger violation-badge">
                                            {{ $attempt->violations->count() }} vi phạm
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning violation-badge">
                                            {{ $attempt->high_severity_count }} nghiêm trọng
                                        </span>
                                    </td>
                                    <td>
                                        @if($attempt->is_flagged)
                                            <span class="badge bg-danger">
                                                <i class="bi bi-flag-fill me-1"></i>Đã gắn cờ
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('teacher.violations.report', $attempt->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-file-text"></i>
                                            </a>
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="approveAttempt({{ $attempt->id }})"
                                                    title="Chấp nhận kết quả">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="rejectAttempt({{ $attempt->id }})"
                                                    title="Hủy kết quả">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white">
                    {{ $attempts->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Không có vi phạm nào</h4>
                    <p class="text-muted">Tất cả học sinh đã làm bài trung thực!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Chấp nhận kết quả</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn chấp nhận kết quả này? Vi phạm sẽ được đánh dấu là đã xử lý.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success" id="confirmApprove">Chấp nhận</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Hủy kết quả thi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn hủy kết quả này? Điểm số sẽ bị đặt về 0.</p>
                <div class="mb-3">
                    <label class="form-label">Lý do hủy</label>
                    <textarea class="form-control" id="rejectReason" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmReject">Hủy kết quả</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedAttemptId = null;

function approveAttempt(attemptId) {
    selectedAttemptId = attemptId;
    let modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

function rejectAttempt(attemptId) {
    selectedAttemptId = attemptId;
    let modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

document.getElementById('confirmApprove')?.addEventListener('click', function() {
    // Implementation for approve
    alert('Chức năng này sẽ được triển khai trong phiên bản tiếp theo');
    bootstrap.Modal.getInstance(document.getElementById('approveModal')).hide();
});

document.getElementById('confirmReject')?.addEventListener('click', function() {
    let reason = document.getElementById('rejectReason').value;
    if (!reason) {
        alert('Vui lòng nhập lý do hủy');
        return;
    }
    // Implementation for reject
    alert('Chức năng này sẽ được triển khai trong phiên bản tiếp theo');
    bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
});

function exportReport() {
    alert('Chức năng xuất báo cáo sẽ được triển khai trong phiên bản tiếp theo');
}
</script>
@endpush
