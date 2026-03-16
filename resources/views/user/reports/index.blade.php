@extends('layouts.app')

@section('content')

@php
    /* ================= SAFE DATA ================= */

    $income  = isset($totalIncome) ? (float)$totalIncome : 0;
    $expense = isset($totalExpense) ? (float)$totalExpense : 0;

    $net = $income - $expense;

    $savingRate = $income > 0
        ? round(($net / $income) * 100, 1)
        : 0;

    $expenseRatio = $income > 0
        ? round(($expense / $income) * 100, 1)
        : 0;

    /* ================= STABILITY SCORE ================= */

    $score = 0;

    if ($savingRate >= 40) $score += 50;
    elseif ($savingRate >= 25) $score += 40;
    elseif ($savingRate >= 10) $score += 25;
    elseif ($savingRate >= 0)  $score += 15;

    if ($expenseRatio <= 50) $score += 30;
    elseif ($expenseRatio <= 70) $score += 20;
    elseif ($expenseRatio <= 90) $score += 10;

    if ($net > 0) $score += 20;

    $score = min(max($score,0),100);

    /* ================= GRADE ================= */

    if ($score >= 85) {
        $grade = 'A+';
        $color = 'text-emerald-600';
        $status = 'Elite Financial Stability';
    } elseif ($score >= 70) {
        $grade = 'A';
        $color = 'text-emerald-500';
        $status = 'Strong & Sustainable';
    } elseif ($score >= 55) {
        $grade = 'B';
        $color = 'text-indigo-600';
        $status = 'Healthy Position';
    } elseif ($score >= 40) {
        $grade = 'C';
        $color = 'text-amber-600';
        $status = 'Moderate Risk';
    } else {
        $grade = 'D';
        $color = 'text-rose-600';
        $status = 'Financial Stress';
    }

    /* ================= FIXED BUFFER ================= */

    $monthlyExpense = $expense > 0 ? $expense : 0;
    $bufferMonths = $monthlyExpense > 0
        ? round($net / $monthlyExpense, 1)
        : 0;

    /* ================= PROJECTIONS ================= */

    $projection3  = $net * 3;
    $projection12 = $net * 12;

    $ringDegree = $score * 3.6;
@endphp

<div class="max-w-7xl mx-auto px-6 py-16 space-y-16 bg-slate-50">

{{-- HEADER --}}
<div>
    <h1 class="text-4xl font-black text-slate-900">
        Financial Intelligence Dashboard
    </h1>
    <p class="text-slate-500 mt-2">
        AI Stability Model — {{ now()->format('F Y') }}
    </p>
</div>

{{-- KPI GRID --}}
<div class="grid md:grid-cols-4 gap-8">

    <div class="bg-white rounded-2xl p-8 shadow">
        <p class="text-xs font-bold uppercase text-emerald-600">
            Total Income
        </p>
        <h2 class="text-2xl font-bold text-emerald-600 mt-2">
            ₹{{ number_format($income,2) }}
        </h2>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow">
        <p class="text-xs font-bold uppercase text-rose-600">
            Total Expense
        </p>
        <h2 class="text-2xl font-bold text-rose-600 mt-2">
            ₹{{ number_format($expense,2) }}
        </h2>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow">
        <p class="text-xs font-bold uppercase text-indigo-600">
            Net Position
        </p>
        <h2 class="text-2xl font-bold {{ $net >= 0 ? 'text-indigo-600':'text-rose-600' }} mt-2">
            ₹{{ number_format($net,2) }}
        </h2>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow">
        <p class="text-xs font-bold uppercase text-slate-500">
            Stability Score
        </p>
        <h2 class="text-2xl font-bold {{ $color }} mt-2">
            {{ $score }}/100
        </h2>
    </div>

</div>

{{-- SCORE + CHART --}}
<div class="grid lg:grid-cols-2 gap-10">

    <div class="bg-white p-10 rounded-2xl shadow text-center">

        <div class="mx-auto w-44 h-44 rounded-full flex items-center justify-center"
             style="background:conic-gradient(#6366f1 {{ $ringDegree }}deg,#e5e7eb {{ $ringDegree }}deg);">
            <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center text-3xl font-black">
                {{ $grade }}
            </div>
        </div>

        <p class="mt-6 font-semibold {{ $color }}">
            {{ $status }}
        </p>

        <p class="text-sm text-slate-500 mt-2">
            Expense Ratio: {{ $expenseRatio }}%
        </p>

    </div>

    <div class="bg-white p-10 rounded-2xl shadow">
        <canvas id="financeChart"></canvas>
    </div>

</div>

</div>

{{-- CHART --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded',function(){

    new Chart(document.getElementById('financeChart'),{
        type:'bar',
        data:{
            labels:['Income','Expense','Net'],
            datasets:[{
                data:[
                    {{ $income }},
                    {{ $expense }},
                    {{ $net }}
                ],
                backgroundColor:[
                    '#10b981',
                    '#ef4444',
                    '#6366f1'
                ]
            }]
        },
        options:{
            responsive:true,
            plugins:{legend:{display:false}}
        }
    });

});
</script>

@endsection
