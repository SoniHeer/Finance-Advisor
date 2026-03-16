@extends('layouts.app')

@section('content')

<style>
body{
    background:linear-gradient(to bottom,#f8fafc,#eef2ff);
}
.header-surface{
    background:white;
    border-bottom:1px solid #e5e7eb;
}
.premium-card{
    background:white;
    border-radius:24px;
    padding:2rem;
    box-shadow:0 25px 60px rgba(15,23,42,.08);
    transition:.35s ease;
}
.premium-card:hover{
    transform:translateY(-8px);
    box-shadow:0 40px 90px rgba(15,23,42,.15);
}
.kpi-gradient{
    background:linear-gradient(135deg,#3b82f6,#6366f1);
    color:white;
}
.live-dot{
    animation:pulse 2s infinite;
}
@keyframes pulse{
0%{box-shadow:0 0 0 0 rgba(59,130,246,.6)}
70%{box-shadow:0 0 0 12px rgba(59,130,246,0)}
100%{box-shadow:0 0 0 0 rgba(59,130,246,0)}
}
.btn-soft{
    padding:10px 18px;
    border-radius:12px;
    font-weight:600;
    transition:.3s;
}
.btn-soft:hover{
    transform:translateY(-2px);
}
</style>

@php
$totalIncome = $totalIncome ?? 0;
$totalExpense = $totalExpense ?? 0;
$savings = $savings ?? ($totalIncome - $totalExpense);
$savingRate = $savingRate ?? ($totalIncome > 0 ? round(($savings/$totalIncome)*100,2) : 0);

$monthlyExpenseLabels = $monthlyExpenseLabels ?? [];
$monthlyExpenseTotals = $monthlyExpenseTotals ?? [];
$categoryExpenseLabels = $categoryExpenseLabels ?? [];
$categoryExpenseTotals = $categoryExpenseTotals ?? [];
@endphp

{{-- ================= HEADER ================= --}}
<div class="header-surface px-6 py-6">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">

        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">
                    FinanceAI Dashboard
                </h1>
                <span class="h-3 w-3 rounded-full bg-blue-600 live-dot"></span>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                <span id="liveClock"></span> • Real-time AI powered finance system
            </p>
        </div>

        <div class="flex gap-3">

            @auth
                @if(Route::has('income.create'))
                    <a href="{{ route('income.create') }}"
                       class="btn-soft bg-white border border-slate-300 text-slate-800 hover:bg-slate-50">
                        + Add Income
                    </a>
                @endif

                @if(Route::has('expenses.create'))
                    <a href="{{ route('expenses.create') }}"
                       class="btn-soft bg-blue-600 text-white hover:bg-blue-700">
                        + Add Expense
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="btn-soft bg-blue-600 text-white">
                    Login to Manage Finance
                </a>
            @endauth

        </div>
    </div>
</div>

{{-- ================= DASHBOARD ================= --}}
<div class="max-w-7xl mx-auto px-6 py-12 space-y-14">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <div class="premium-card">
            <p class="text-emerald-500 text-xs font-bold uppercase">
                Total Income
            </p>
            <h2 class="text-4xl font-extrabold mt-2 counter"
                data-target="{{ $totalIncome }}">
                ₹{{ number_format($totalIncome) }}
            </h2>
        </div>

        <div class="premium-card">
            <p class="text-rose-500 text-xs font-bold uppercase">
                Total Expense
            </p>
            <h2 class="text-4xl font-extrabold mt-2 counter"
                data-target="{{ $totalExpense }}">
                ₹{{ number_format($totalExpense) }}
            </h2>
        </div>

        <div class="premium-card kpi-gradient">
            <p class="text-blue-200 text-xs font-bold uppercase">
                Balance
            </p>
            <h2 class="text-5xl font-extrabold mt-2 counter"
                data-target="{{ $savings }}">
                ₹{{ number_format($savings) }}
            </h2>
            <p class="text-sm mt-2 text-blue-100">
                Saving Rate: {{ $savingRate }}%
            </p>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="premium-card lg:col-span-2">
            <h3 class="font-semibold text-lg mb-6">
                Monthly Expense Trend
            </h3>
            <canvas id="monthlyExpenseChart"></canvas>
        </div>

        <div class="premium-card">
            <h3 class="font-semibold text-lg mb-6">
                Expense Categories
            </h3>
            <canvas id="categoryExpenseChart"></canvas>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* LIVE CLOCK */
setInterval(()=>{
    const el = document.getElementById('liveClock');
    if(el){
        el.innerText = new Date().toLocaleTimeString();
    }
},1000);

/* SAFE KPI COUNTER */
document.querySelectorAll('.counter').forEach(counter=>{
    const target = Number(counter.getAttribute('data-target')) || 0;
    if(target <= 0) return;

    let count = 0;
    const step = Math.max(target / 60, 1);

    function update(){
        count += step;
        if(count < target){
            counter.innerText = "₹" + Math.floor(count).toLocaleString();
            requestAnimationFrame(update);
        } else {
            counter.innerText = "₹" + target.toLocaleString();
        }
    }
    update();
});

/* SAFE CHART DATA */
const expenseLabels = @json($monthlyExpenseLabels);
const expenseData = @json($monthlyExpenseTotals);
const categoryLabels = @json($categoryExpenseLabels);
const categoryData = @json($categoryExpenseTotals);

/* LINE CHART */
if(expenseData.length > 0){
    new Chart(document.getElementById('monthlyExpenseChart'),{
        type:'line',
        data:{
            labels:expenseLabels,
            datasets:[{
                label:'Expenses',
                data:expenseData,
                borderColor:'#ef4444',
                backgroundColor:'rgba(239,68,68,.12)',
                fill:true,
                tension:.4
            }]
        },
        options:{
            responsive:true,
            plugins:{legend:{display:false}},
            scales:{y:{beginAtZero:true}}
        }
    });
}

/* DOUGHNUT CHART */
if(categoryData.length > 0){
    new Chart(document.getElementById('categoryExpenseChart'),{
        type:'doughnut',
        data:{
            labels:categoryLabels,
            datasets:[{
                data:categoryData,
                backgroundColor:[
                    '#3b82f6','#22c55e','#ef4444',
                    '#f59e0b','#06b6d4','#8b5cf6'
                ]
            }]
        },
        options:{
            plugins:{legend:{position:'bottom'}}
        }
    });
}
</script>

@endsection
