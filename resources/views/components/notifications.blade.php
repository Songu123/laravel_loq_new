@php
    $notifications = auth()->user()->unreadNotifications()->limit(10)->get();
@endphp

<div class="dropdown">
  <button class="btn btn-light" data-bs-toggle="dropdown">
    Thông báo
    @if($count = auth()->user()->unreadNotifications()->count())
      <span class="badge bg-danger">{{ $count }}</span>
    @endif
  </button>
  <ul class="dropdown-menu dropdown-menu-end">
    @forelse($notifications as $n)
      <li class="px-3 py-2">
        <div class="small text-muted">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</div>
        <div>{{ $n->data['message'] ?? 'Thông báo' }}</div>
      </li>
    @empty
      <li class="px-3 py-2 text-muted">Không có thông báo mới</li>
    @endforelse
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item" href="{{ route('notifications.index') }}">Xem tất cả</a></li>
  </ul>
</div>
