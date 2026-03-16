@extends('layouts.app')

@section('content')

@php
    abort_unless(isset($family) && $family->id, 404);

    $familyName = $family->name;
    $familyId = $family->id;

    $metrics = $metrics ?? [];
    $trend = $trend ?? ['months'=>[],'income'=>[],'expense'=>[]];
    $categories = $categories ?? [];
    $inviteLink = $inviteLink ?? '';

    function metric($array,$key,$default=0){
        return $array[$key] ?? $default;
    }

    $incomeGrowth = metric($metrics,'income_growth');
    $expenseGrowth = metric($metrics,'expense_growth');
@endphp

<div class="max-w-7xl mx-auto px-6 py-14">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">

        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                {{ $familyName }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Enterprise Family Finance Dashboard
            </p>
        </div>

        <div class="flex gap-3">

            @if(Route::has('user.income.create'))
                <a href="{{ route('user.income.create',['family_id'=>$familyId]) }}"
                   class="btn btn-green">
                    + Income
                </a>
            @endif

            @if(Route::has('user.expenses.create'))
                <a href="{{ route('user.expenses.create',['family_id'=>$familyId]) }}"
                   class="btn btn-red">
                    − Expense
                </a>
            @endif

        </div>

    </div>


    {{-- KPI --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-14">

        <x-kpi-card
            title="Total Income"
            value="₹{{ number_format(metric($metrics,'total_income')) }}"
            color="green"
            growth="{{ round($incomeGrowth) }}"
            positive="{{ $incomeGrowth >= 0 }}" />

        <x-kpi-card
            title="Total Expense"
            value="₹{{ number_format(metric($metrics,'total_expense')) }}"
            color="red"
            growth="{{ round($expenseGrowth) }}"
            positive="{{ $expenseGrowth < 0 }}" />

        <x-kpi-card
            title="Net Balance"
            value="₹{{ number_format(metric($metrics,'balance')) }}"
            color="{{ metric($metrics,'balance') >= 0 ? 'green' : 'red' }}" />

        <x-kpi-card
            title="Saving Rate"
            value="{{ round(metric($metrics,'saving_rate')) }}%"
            color="cyan" />

    </div>



    {{-- CHARTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-14">

        <div class="card h-[350px]">
            <h3 class="font-semibold mb-4">12 Month Trend</h3>
            <canvas id="trendChart"></canvas>
        </div>

        <div class="card h-[350px]">
            <h3 class="font-semibold mb-4">Expense Distribution</h3>
            <canvas id="categoryChart"></canvas>
        </div>

    </div>



    {{-- INVITE SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- EMAIL INVITE --}}
        @if(Route::has('user.families.invite'))
        <div class="card">

            <h3 class="font-semibold mb-5">Invite via Email</h3>

            <form method="POST"
                  action="{{ route('user.families.invite',$familyId) }}"
                  id="inviteForm"
                  class="flex gap-3">

                @csrf

                <input type="email"
                       name="email"
                       required
                       placeholder="Enter member email"
                       class="input">

                <button id="inviteBtn" class="btn btn-blue">
                    <span class="btn-label">Send</span>
                    <span class="btn-loading hidden">Sending...</span>
                </button>

            </form>

        </div>
        @endif


        {{-- LINK INVITE --}}
        <div class="card">

            <h3 class="font-semibold mb-5">Invite via Secure Link</h3>

            <div class="flex gap-3">

                <input id="inviteLink"
                       readonly
                       value="{{ $inviteLink }}"
                       class="input text-sm">

                <button type="button"
                        onclick="copyInviteLink()"
                        class="btn btn-outline">
                    Copy
                </button>

            </div>

        </div>

    </div>

</div>


{{-- STYLES --}}
<style>
.card{
    background:white;
    padding:2rem;
    border-radius:1rem;
    box-shadow:0 20px 45px rgba(0,0,0,.08);
}
.btn{
    padding:.7rem 1.4rem;
    border-radius:.6rem;
    font-weight:600;
    transition:.2s;
    color:white;
}
.btn-green{background:#10b981}
.btn-red{background:#ef4444}
.btn-blue{background:#2563eb}
.btn-outline{background:#f1f5f9;color:#334155}
.btn:hover{transform:translateY(-2px)}
.input{
    flex:1;
    padding:.65rem;
    border:1px solid #e2e8f0;
    border-radius:.6rem;
}
</style>



{{-- CHART SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded',function(){

    const trendLabels = @json($trend['months']);
    const trendIncome = @json($trend['income']);
    const trendExpense = @json($trend['expense']);

    if(document.getElementById('trendChart')){
        new Chart(document.getElementById('trendChart'),{
            type:'line',
            data:{
                labels:trendLabels,
                datasets:[
                    {
                        label:'Income',
                        data:trendIncome,
                        borderColor:'#10b981',
                        backgroundColor:'rgba(16,185,129,.1)',
                        tension:.4
                    },
                    {
                        label:'Expense',
                        data:trendExpense,
                        borderColor:'#ef4444',
                        backgroundColor:'rgba(239,68,68,.1)',
                        tension:.4
                    }
                ]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false
            }
        });
    }

    const categoryLabels = @json(array_keys($categories));
    const categoryValues = @json(array_values($categories));

    if(document.getElementById('categoryChart')){
        new Chart(document.getElementById('categoryChart'),{
            type:'doughnut',
            data:{
                labels:categoryLabels,
                datasets:[{
                    data:categoryValues,
                    backgroundColor:[
                        '#10b981','#3b82f6','#f59e0b','#ef4444',
                        '#8b5cf6','#06b6d4','#f97316'
                    ]
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false
            }
        });
    }

});


function copyInviteLink(){
    const link = document.getElementById('inviteLink').value;
    if(!link) return;

    navigator.clipboard?.writeText(link);
    alert('Invite link copied');
}
</script>

@endsection
