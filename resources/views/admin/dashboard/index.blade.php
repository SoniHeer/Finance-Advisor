@extends('layouts.app')

@section('content')

@php
    /* ================= SAFE DATA ================= */

    $totalUsers      = (int) ($totalUsers ?? 0);
    $totalIncome     = (float) ($totalIncome ?? 0);
    $totalExpenses   = (float) ($totalExpenses ?? 0);

    $months          = $months ?? [];
    $monthlyIncome   = $monthlyIncome ?? [];
    $monthlyExpenses = $monthlyExpenses ?? [];
    $activities      = $activities ?? collect();

    $netRevenue = $totalIncome - $totalExpenses;

    $healthIndex = $totalIncome > 0
        ? round((($netRevenue / $totalIncome) * 100),1)
        : 0;

    $healthIndex = min(max($healthIndex, -100), 100);
@endphp

<style>
.card{
    background:#ffffff;
    border:1px solid #e5e7eb;
    border-radius:28px;
    box-shadow:0 30px 80px rgba(15,23,42,.08);
    transition:.3s ease;
}
.card:hover{ transform:translateY(-4px); }

.kpi{
    font-size:44px;
    font-weight:900;
    letter-spacing:-1px;
}

.live-dot{
    animation:pulse 1.5s infinite ease-in-out;
}
@keyframes pulse{
    0%,100%{opacity:.35}
    50%{opacity:1}
}
</style>

<div class="max-w-7xl mx-auto px-6 py-14 space-y-16 bg-slate-50">

{{-- ================= HEADER ================= --}}
<div class="flex justify-between items-end">
    <div>
        <h1 class="text-5xl font-black text-slate-800">
            Admin Intelligence Hub
        </h1>
        <p class="text-slate-500 mt-3 flex items-center gap-2 text-sm">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full live-dot"></span>
            Real-time Financial Telemetry
        </p>
    </div>

    <span class="px-4 py-1.5 rounded-full text-xs font-bold
                 bg-emerald-100 text-emerald-700 border">
        LIVE
    </span>
</div>

{{-- ================= KPI GRID ================= --}}
<div class="grid md:grid-cols-4 gap-8">

    <div class="card p-8">
        <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">
            Total Users
        </p>
        <h3 class="kpi text-slate-800 mt-4 counter"
            data-target="{{ $totalUsers }}">
            0
        </h3>
    </div>

    <div class="card p-8">
        <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">
            Platform Income
        </p>
        <h3 class="kpi text-emerald-600 mt-4 counter-currency"
            data-target="{{ $totalIncome }}">
            ₹0
        </h3>
    </div>

    <div class="card p-8">
        <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">
            Platform Expenses
        </p>
        <h3 class="kpi text-rose-600 mt-4 counter-currency"
            data-target="{{ $totalExpenses }}">
            ₹0
        </h3>
    </div>

    <div class="card p-8">
        <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">
            Net Revenue
        </p>
        <h3 class="kpi mt-4 {{ $netRevenue >= 0 ? 'text-indigo-600' : 'text-rose-600' }} counter-currency"
            data-target="{{ $netRevenue }}">
            ₹0
        </h3>

        <p class="text-xs mt-3 {{ $healthIndex >= 0 ? 'text-emerald-600':'text-rose-600' }}">
            Health Index: {{ $healthIndex }}%
        </p>
    </div>

</div>

{{-- ================= CHART ================= --}}
<div class="card p-10">

    <div class="flex justify-between mb-6">
        <h2 class="text-xl font-bold text-slate-800">
            Monthly Income vs Expense
        </h2>
    </div>

    @if(count($months))
        <div class="h-[380px]">
            <canvas id="financeChart"></canvas>
        </div>
    @else
        <div class="text-center py-16 text-slate-500">
            No financial data available yet.
        </div>
    @endif

</div>

{{-- ================= ACTIVITY ================= --}}
<div class="card p-10">
    <h2 class="text-xl font-bold text-slate-800 mb-8">
        Recent Activity
    </h2>

    @if($activities->count())
        <ul class="space-y-4 text-sm">
            @foreach($activities as $activity)
                <li class="flex justify-between border-b pb-3">
                    <span>{{ $activity->description ?? 'System event' }}</span>
                    <span class="text-xs text-slate-500">
                        {{ optional($activity->created_at)->diffForHumans() }}
                    </span>
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center text-slate-500 py-12">
            No recent activity
        </div>
    @endif
</div>

</div>

{{-- ================= SCRIPTS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ================= SAFE COUNTERS ================= */

    function animateNumber(el, isCurrency = false) {
        let target = parseFloat(el.dataset.target) || 0;
        let duration = 1000;
        let start = null;

        function step(timestamp) {
            if (!start) start = timestamp;
            let progress = Math.min((timestamp - start) / duration, 1);
            let value = target * progress;

            if(isCurrency){
                el.innerText = '₹' + value.toLocaleString(undefined,{minimumFractionDigits:2});
            } else {
                el.innerText = Math.floor(value).toLocaleString();
            }

            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        }

        window.requestAnimationFrame(step);
    }

    document.querySelectorAll('.counter').forEach(el => {
        animateNumber(el, false);
    });

    document.querySelectorAll('.counter-currency').forEach(el => {
        animateNumber(el, true);
    });

    /* ================= CHART ================= */

    const canvas = document.getElementById('financeChart');

    if (canvas && typeof Chart !== 'undefined') {

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [
                    {
                        label: 'Income',
                        data: @json($monthlyIncome),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,.12)',
                        fill: true,
                        tension: .4
                    },
                    {
                        label: 'Expenses',
                        data: @json($monthlyExpenses),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,.12)',
                        fill: true,
                        tension: .4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value){
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });
    }

});
</script>

@endsection
