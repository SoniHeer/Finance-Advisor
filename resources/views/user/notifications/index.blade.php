@extends('layouts.app')

@section('content')

@php
    $notifications = $notifications ?? collect();
    $totalIncome   = (float) ($totalIncome ?? 0);
    $totalExpense  = (float) ($totalExpense ?? 0);

    $canMarkRead = Route::has('user.notifications.read');

    $unreadCount = $notifications->filter(function ($note) {
        return is_array($note)
            ? !($note['is_read'] ?? false)
            : !($note->is_read ?? false);
    })->count();
@endphp

<div class="max-w-7xl mx-auto px-6 py-14 space-y-12">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">

        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">
                Notification Center
            </h1>

            <p class="text-sm text-slate-500 mt-2">
                System alerts & financial intelligence updates
            </p>

            @if($unreadCount)
                <p class="text-xs mt-2 text-blue-600 font-semibold">
                    {{ $unreadCount }} unread notification(s)
                </p>
            @endif
        </div>

        {{-- FINANCIAL SNAPSHOT --}}
        <div class="flex flex-wrap gap-4 text-sm">

            <div class="stat-card bg-emerald-50">
                <div class="text-emerald-600 font-bold text-lg">
                    ₹{{ number_format($totalIncome,2) }}
                </div>
                <div class="text-xs text-slate-500">Income</div>
            </div>

            <div class="stat-card bg-rose-50">
                <div class="text-rose-600 font-bold text-lg">
                    ₹{{ number_format($totalExpense,2) }}
                </div>
                <div class="text-xs text-slate-500">Expense</div>
            </div>

        </div>
    </div>

    {{-- NOTIFICATIONS LIST --}}
    <div class="space-y-4">

        @forelse($notifications as $note)

            @php
                $type = is_array($note)
                        ? ($note['type'] ?? 'info')
                        : ($note->type ?? 'info');

                $message = is_array($note)
                        ? ($note['message'] ?? 'System notification')
                        : ($note->message ?? 'System notification');

                $isRead = is_array($note)
                        ? ($note['is_read'] ?? false)
                        : ($note->is_read ?? false);

                $createdAt = is_array($note)
                        ? ($note['created_at'] ?? null)
                        : ($note->created_at ?? null);

                $id = is_array($note)
                        ? ($note['id'] ?? null)
                        : ($note->id ?? null);

                $iconMap = [
                    'danger'  => ['⚠','bg-rose-100','text-rose-600'],
                    'warning' => ['🔔','bg-amber-100','text-amber-600'],
                    'success' => ['✔','bg-emerald-100','text-emerald-600'],
                    'info'    => ['ℹ','bg-sky-100','text-sky-600'],
                ];

                [$icon,$bg,$color] = $iconMap[$type] ?? $iconMap['info'];

                $timeText = $createdAt instanceof \Carbon\Carbon
                    ? $createdAt->diffForHumans()
                    : ($createdAt ? \Carbon\Carbon::parse($createdAt)->diffForHumans() : 'Just now');
            @endphp

            <div class="notification-card {{ !$isRead ? 'unread' : '' }}">

                {{-- ICON --}}
                <div class="icon {{ $bg }} {{ $color }}">
                    {{ $icon }}
                </div>

                {{-- CONTENT --}}
                <div class="flex-1">

                    <div class="flex justify-between items-start gap-4">

                        <p class="text-sm font-semibold text-slate-800 leading-relaxed">
                            {{ $message }}
                        </p>

                        @unless($isRead)
                            <span class="badge-new">
                                NEW
                            </span>
                        @endunless

                    </div>

                    <p class="text-xs text-slate-500 mt-3">
                        {{ $timeText }}
                    </p>

                </div>

                {{-- ACTION --}}
                @if($id && $canMarkRead && !$isRead)
                    <form method="POST"
                          action="{{ route('user.notifications.read',$id) }}">
                        @csrf
                        <button class="mark-btn">
                            Mark Read
                        </button>
                    </form>
                @endif

            </div>

        @empty

            <div class="empty-card">
                <div class="text-5xl mb-6">🌙</div>
                <p class="font-semibold text-slate-800 text-lg">
                    You're all caught up
                </p>
                <p class="text-sm text-slate-500 mt-3">
                    No new notifications available.
                </p>
            </div>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if(method_exists($notifications,'links'))
        <div class="pt-8">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

{{-- STYLES --}}
<style>

.stat-card{
    padding:14px 20px;
    border-radius:14px;
    border:1px solid #e2e8f0;
}

.notification-card{
    display:flex;
    gap:18px;
    align-items:flex-start;
    padding:20px;
    background:white;
    border-radius:18px;
    border:1px solid #e2e8f0;
    transition:.3s ease;
    box-shadow:0 8px 20px rgba(15,23,42,.05);
}

.notification-card:hover{
    transform:translateY(-4px);
    box-shadow:0 18px 35px rgba(15,23,42,.08);
}

.notification-card.unread{
    border-left:4px solid #3b82f6;
    background:#f8fbff;
}

.icon{
    height:42px;
    width:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}

.badge-new{
    font-size:10px;
    padding:4px 8px;
    border-radius:999px;
    background:#dbeafe;
    color:#1d4ed8;
    font-weight:bold;
}

.mark-btn{
    font-size:12px;
    color:#2563eb;
    font-weight:600;
}

.mark-btn:hover{
    text-decoration:underline;
}

.empty-card{
    background:white;
    border:1px solid #e2e8f0;
    border-radius:24px;
    padding:70px;
    text-align:center;
    box-shadow:0 25px 60px rgba(15,23,42,.05);
}

</style>

@endsection
