@extends('layouts.app')

@section('content')

@php
    /* ================= SAFE DATA NORMALIZATION ================= */

    $analysis    = $analysis ?? [];
    $activities  = $activities ?? collect();
    $severities  = $severities ?? [];

    $totalLogs     = (int) ($analysis['totalLogs'] ?? 0);
    $deleteCount   = (int) ($analysis['deleteCount'] ?? 0);
    $updateCount   = (int) ($analysis['updateCount'] ?? 0);
    $criticalCount = (int) ($analysis['criticalCount'] ?? 0);

    $threatScore = min(max((int) ($analysis['score'] ?? 0), 0), 100);

    $defaultLevel = [
        'label' => 'SECURE',
        'color' => '#16a34a',
        'text'  => 'text-emerald-600'
    ];

    $levelData = array_merge($defaultLevel, $analysis['level'] ?? []);

    $anomalyRatio = $totalLogs > 0
        ? round(($deleteCount / $totalLogs) * 100)
        : 0;
@endphp

<style>
.surface{
    background:#ffffff;
    border:1px solid #e5e7eb;
    border-radius:30px;
    box-shadow:0 25px 60px rgba(15,23,42,.08);
    transition:.3s ease;
}
.surface:hover{
    box-shadow:0 30px 70px rgba(15,23,42,.12);
}
.metric{
    font-size:38px;
    font-weight:900;
}
.status-pill{
    padding:6px 14px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
}
.threat-ring{
    height:150px;
    width:150px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
}
.threat-inner{
    height:105px;
    width:105px;
    border-radius:50%;
    background:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    font-size:28px;
}
</style>

<div class="max-w-7xl mx-auto px-6 py-14 space-y-16 bg-slate-50">

{{-- ================= HEADER ================= --}}
<div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">

    <div>
        <h1 class="text-4xl font-extrabold text-slate-900">
            Security Intelligence Console
        </h1>
        <p class="text-sm text-slate-500 mt-2">
            Advanced real-time audit logging & anomaly detection
        </p>
    </div>

    <span class="px-4 py-1.5 rounded-full text-xs font-bold
                 bg-indigo-100 text-indigo-700 border">
        LIVE MONITORING
    </span>

</div>


{{-- ================= METRIC CARDS ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">

    <div class="surface p-6">
        <p class="text-xs uppercase text-slate-500 font-bold">Total Logs</p>
        <div class="metric mt-2">
            {{ number_format($totalLogs) }}
        </div>
    </div>

    <div class="surface p-6">
        <p class="text-xs uppercase text-rose-600 font-bold">Delete Events</p>
        <div class="metric text-rose-600 mt-2">
            {{ number_format($deleteCount) }}
        </div>
    </div>

    <div class="surface p-6">
        <p class="text-xs uppercase text-amber-600 font-bold">Update Events</p>
        <div class="metric text-amber-600 mt-2">
            {{ number_format($updateCount) }}
        </div>
    </div>

    <div class="surface p-6">
        <p class="text-xs uppercase text-purple-600 font-bold">Critical Alerts</p>
        <div class="metric text-purple-600 mt-2">
            {{ number_format($criticalCount) }}
        </div>
    </div>

    {{-- Threat Index --}}
    <div class="surface p-6 flex flex-col items-center justify-center">

        <div class="threat-ring"
            style="background:conic-gradient(
                {{ e($levelData['color']) }} {{ $threatScore * 3.6 }}deg,
                #e5e7eb {{ $threatScore * 3.6 }}deg
            );">

            <div class="threat-inner">
                {{ $threatScore }}
            </div>
        </div>

        <p class="mt-4 text-sm font-bold {{ e($levelData['text']) }}">
            {{ e($levelData['label']) }} LEVEL
        </p>

    </div>

</div>


{{-- ================= RISK BAR ================= --}}
<div class="surface p-8">

    <div class="flex justify-between text-xs mb-2">
        <span class="font-semibold">Deletion Ratio</span>
        <span class="font-bold">{{ $anomalyRatio }}%</span>
    </div>

    <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
        <div class="h-2 bg-rose-500 transition-all duration-500"
             style="width: {{ min(max($anomalyRatio,0),100) }}%">
        </div>
    </div>

</div>


{{-- ================= SECURITY LOG TABLE ================= --}}
<div class="surface overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full text-sm min-w-[800px]">

            <thead class="bg-slate-100 uppercase text-xs tracking-wide text-slate-600">
            <tr>
                <th class="px-6 py-4 text-left">User</th>
                <th class="px-6 py-4 text-left">Action</th>
                <th class="px-6 py-4 text-center">Severity</th>
                <th class="px-6 py-4 text-right">Timestamp</th>
            </tr>
            </thead>

            <tbody class="divide-y">

            @forelse($activities as $activity)

                @php
                    $severity = $severities[$activity->id] ?? [
                        'label' => 'Info',
                        'class' => 'bg-slate-200 text-slate-700'
                    ];
                @endphp

                <tr class="hover:bg-slate-50 transition">

                    <td class="px-6 py-4 font-semibold text-slate-900">
                        {{ e(optional($activity->user)->name ?? 'System') }}
                    </td>

                    <td class="px-6 py-4 text-slate-600">
                        {{ e($activity->description ?? 'N/A') }}
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="status-pill {{ $severity['class'] }}">
                            {{ e($severity['label']) }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-right text-slate-600 whitespace-nowrap">
                        {{ optional($activity->created_at)->format('d M Y • h:i A') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="4"
                        class="px-6 py-14 text-center text-slate-500">
                        No security logs recorded.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- ================= PAGINATION ================= --}}
@if($activities instanceof \Illuminate\Contracts\Pagination\Paginator ||
    $activities instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)

    <div>
        {{ $activities->withQueryString()->links() }}
    </div>

@endif

</div>

@endsection
