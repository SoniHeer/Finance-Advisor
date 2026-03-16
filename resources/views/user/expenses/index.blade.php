@extends('layouts.app')

@section('content')

@php
$total = (float)($total ?? 0);
$topCategory = $topCategory ?? null;
$latestDate = isset($latest) && $latest ? $latest : null;

/* Frontend-only chart data from current collection */
$categoryData = $expenses->groupBy('category')
    ->map(fn($g) => $g->sum('amount'));

$monthlyData = $expenses->groupBy(
        fn($e) => optional($e->expense_date)->format('M Y')
    )->map(fn($g) => $g->sum('amount'));
@endphp

<div class="max-w-[1600px] mx-auto px-6 py-16 space-y-14">

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col xl:flex-row justify-between gap-6">

        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white">
                Expense Intelligence
            </h1>

            <p class="text-sm text-slate-500 mt-2">
                Personal expenses • Live analytics
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Last updated {{ $latestDate ? $latestDate->diffForHumans() : '—' }}
            </p>
        </div>

        <div class="flex gap-3 items-center">

            {{-- PDF PRESERVED --}}
            @if(Route::has('user.expenses.export.pdf'))
                <a href="{{ route('user.expenses.export.pdf') }}"
                   class="px-5 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:opacity-90 transition">
                    Export PDF
                </a>
            @endif

            <a href="{{ route('user.expenses.create') }}"
               class="px-6 py-3 rounded-xl bg-gradient-to-r
                      from-rose-500 to-pink-600
                      text-white font-semibold hover:scale-[1.03]
                      transition shadow-xl">
                + Add Expense
            </a>
        </div>
    </div>

    {{-- KPI --}}
    <div class="grid md:grid-cols-3 gap-8">

        <div class="bg-white dark:bg-slate-900 border shadow-xl rounded-3xl p-8">
            <p class="text-xs uppercase text-slate-500 font-bold">
                Total Spending
            </p>
            <h2 class="text-3xl font-bold text-rose-600 mt-4">
                ₹{{ number_format($total, 2) }}
            </h2>
        </div>

        <div class="bg-white dark:bg-slate-900 border shadow-xl rounded-3xl p-8">
            <p class="text-xs uppercase text-slate-500 font-bold">
                Top Category
            </p>
            <h2 class="text-xl font-semibold mt-4">
                {{ $topCategory ?? '—' }}
            </h2>
        </div>

        <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-xl rounded-3xl p-8">
            <p class="text-xs uppercase opacity-80 font-bold">
                AI Insight
            </p>
            <p class="text-sm mt-4">
                {{ $topCategory
                    ? "Most spending is in {$topCategory}. Review this category."
                    : "No dominant category detected yet." }}
            </p>
        </div>

    </div>

    {{-- CHARTS --}}
    <div class="grid lg:grid-cols-2 gap-10">

        <div class="bg-white dark:bg-slate-900 border shadow-xl rounded-3xl p-8">
            <h3 class="font-semibold mb-6">Category Breakdown</h3>
            <div class="h-[320px]">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border shadow-xl rounded-3xl p-8">
            <h3 class="font-semibold mb-6">Monthly Expense Trend</h3>
            <div class="h-[320px]">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

    </div>

    {{-- FILTERS (PRESERVED) --}}
    <form method="GET"
          action="{{ route('user.expenses.index') }}"
          class="bg-white dark:bg-slate-900 border shadow rounded-2xl p-6">

        <div class="grid md:grid-cols-5 gap-4">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search title…"
                   class="rounded-xl border px-4 py-2 dark:bg-slate-800">

            <select name="category"
                    class="rounded-xl border px-4 py-2 dark:bg-slate-800">
                <option value="">All Categories</option>
                @foreach(['Food','Travel','Bills','Shopping'] as $cat)
                    <option value="{{ $cat }}"
                        {{ request('category') === $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>

            <input type="date"
                   name="from"
                   value="{{ request('from') }}"
                   class="rounded-xl border px-4 py-2">

            <input type="date"
                   name="to"
                   value="{{ request('to') }}"
                   class="rounded-xl border px-4 py-2">

            <div class="flex gap-2">
                <button class="px-4 py-2 rounded-xl bg-rose-600 text-white text-sm font-semibold">
                    Apply
                </button>

                <a href="{{ route('user.expenses.index') }}"
                   class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-sm font-semibold">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- TABLE (UNCHANGED LOGIC) --}}
    <div class="bg-white dark:bg-slate-900 border shadow-xl rounded-3xl overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-slate-100 dark:bg-slate-800 text-xs uppercase text-slate-500">
                <tr>
                    <th class="p-4 text-left">Title</th>
                    <th class="p-4 text-center">Category</th>
                    <th class="p-4 text-center">Date</th>
                    <th class="p-4 text-right">Amount</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y dark:divide-slate-700">

                @forelse($expenses as $expense)

                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">

                        <td class="p-4 font-semibold">
                            {{ $expense->title }}
                        </td>

                        <td class="p-4 text-center">
                            {{ $expense->category }}
                        </td>

                        <td class="p-4 text-center text-slate-500">
                            {{ optional($expense->expense_date)->format('d M Y') }}
                        </td>

                        <td class="p-4 text-right font-bold text-rose-600">
                            -₹{{ number_format($expense->amount, 2) }}
                        </td>

                        <td class="p-4 text-right space-x-3">

                            <a href="{{ route('user.expenses.edit', $expense->id) }}"
                               class="text-blue-600 text-sm hover:underline">
                                Edit
                            </a>

                            <form method="POST"
                                  action="{{ route('user.expenses.destroy', $expense->id) }}"
                                  class="inline"
                                  onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                <button class="text-rose-600 text-sm hover:underline">
                                    Delete
                                </button>
                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="p-12 text-center text-slate-500">
                            No expenses found.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>

        <div class="p-6 border-t dark:border-slate-700">
            {{ $expenses->appends(request()->query())->links() }}
        </div>

    </div>

</div>

{{-- CHARTS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const categoryLabels = @json($categoryData->keys());
const categoryValues = @json($categoryData->values());

if(categoryLabels.length){
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: { labels: categoryLabels, datasets: [{ data: categoryValues }] },
        options: { responsive:true, maintainAspectRatio:false }
    });
}

const monthlyLabels = @json($monthlyData->keys());
const monthlyValues = @json($monthlyData->values());

if(monthlyLabels.length){
    new Chart(document.getElementById('monthlyChart'), {
        type:'line',
        data:{
            labels:monthlyLabels,
            datasets:[{ label:'Expenses', data:monthlyValues, fill:true, tension:0.4 }]
        },
        options:{ responsive:true, maintainAspectRatio:false }
    });
}

function confirmDelete(){
    return confirm("Are you sure you want to delete this expense?");
}
</script>

@endsection
