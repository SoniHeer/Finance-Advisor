@extends('layouts.app')

@section('content')

@php
$analysis = $analysis ?? [];

$income      = (float)($analysis['totalIncome'] ?? 0);
$expense     = (float)($analysis['totalExpense'] ?? 0);
$savings     = (float)($analysis['savings'] ?? 0);
$rate        = (float)($analysis['savingRate'] ?? 0);
$score       = (int)($analysis['score'] ?? 0);
$risk        = $analysis['riskLevel'] ?? 'Stable';
$runway      = (int)($analysis['runway'] ?? 0);

$labels      = $analysis['labels'] ?? [];
$incomeData  = $analysis['incomeSeries'] ?? [];
$expenseData = $analysis['expenseSeries'] ?? [];
$netWorthData= $analysis['netWorthSeries'] ?? [];

$categoryLabels = $analysis['categoryLabels'] ?? [];
$categorySeries = $analysis['categorySeries'] ?? [];
@endphp

<div class="max-w-[1600px] mx-auto px-6 py-12 space-y-12">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight">
                FinanceAI Enterprise Dashboard
            </h1>
            <p class="text-slate-500 mt-2">
                Advanced Financial Intelligence • Real-Time Metrics
            </p>
        </div>

        <div class="text-sm text-slate-500">
            Updated: {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

    {{-- KPI GRID --}}
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="bg-white rounded-2xl shadow-md p-6 border hover:shadow-xl transition">
            <p class="text-sm text-slate-500">Total Income</p>
            <h2 class="text-3xl font-bold text-emerald-600 mt-2">
                ₹{{ number_format($income,2) }}
            </h2>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6 border hover:shadow-xl transition">
            <p class="text-sm text-slate-500">Total Expense</p>
            <h2 class="text-3xl font-bold text-rose-600 mt-2">
                ₹{{ number_format($expense,2) }}
            </h2>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6 border hover:shadow-xl transition">
            <p class="text-sm text-slate-500">Net Savings</p>
            <h2 class="text-3xl font-bold mt-2">
                ₹{{ number_format($savings,2) }}
            </h2>
            <p class="text-xs mt-1 text-slate-500">
                Saving Rate: {{ number_format($rate,1) }}%
            </p>
        </div>

        <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-2xl p-6 shadow-xl">
            <p class="text-sm opacity-80">AI Stability Score</p>
            <h2 class="text-4xl font-extrabold mt-2">
                {{ $score }}
            </h2>
            <p class="text-sm mt-2 opacity-80">
                Risk: {{ $risk }}
            </p>
            <p class="text-xs mt-2 opacity-70">
                Runway: {{ $runway }} months
            </p>
        </div>

    </div>

    {{-- CHART GRID --}}
    <div class="grid lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl shadow-md p-8 border">
            <h3 class="font-semibold text-lg mb-6">Cashflow Intelligence</h3>
            <div class="h-[320px]">
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-8 border">
            <h3 class="font-semibold text-lg mb-6">Net Worth Growth</h3>
            <div class="h-[320px]">
                <canvas id="netWorthChart"></canvas>
            </div>
        </div>

    </div>

    {{-- CATEGORY BREAKDOWN --}}
    <div class="bg-white rounded-2xl shadow-md p-8 border">
        <h3 class="font-semibold text-lg mb-6">Expense Category Breakdown</h3>
        <div class="h-[320px]">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const labels = @json($labels);
    const incomeData = @json($incomeData);
    const expenseData = @json($expenseData);
    const netWorthData = @json($netWorthData);
    const categoryLabels = @json($categoryLabels);
    const categorySeries = @json($categorySeries);

    const currency = (val) => '₹' + new Intl.NumberFormat().format(val);

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': ' + currency(ctx.raw)
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: val => currency(val) }
            }
        }
    };

    if (labels.length > 0) {

        new Chart(document.getElementById('financeChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Income',
                        data: incomeData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.15)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Expense',
                        data: expenseData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,0.15)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: baseOptions
        });

        new Chart(document.getElementById('netWorthChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Net Worth',
                    data: netWorthData,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.15)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: baseOptions
        });
    }

    if (categoryLabels.length > 0) {
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categorySeries,
                    backgroundColor: [
                        '#6366f1',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#0ea5e9'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    }

});
</script>
@endpush
