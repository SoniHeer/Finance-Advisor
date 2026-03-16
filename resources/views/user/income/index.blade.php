@extends('layouts.app')

@section('content')

@php
$total = (float)($stats['total'] ?? 0);
$currentMonth = (float)($stats['currentMonth'] ?? 0);
$average = (float)($stats['average'] ?? 0);

/* Build mini monthly chart from collection (UI only) */
$monthly = $incomes
    ->groupBy(fn($i) => $i->created_at?->format('M Y'))
    ->map(fn($group) => $group->sum('amount'))
    ->take(6);

$chartLabels = $monthly->keys()->values();
$chartValues = $monthly->values();
@endphp

<div class="max-w-[1500px] mx-auto px-6 py-12 space-y-12">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">

        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Income Intelligence
            </h1>
            <p class="text-slate-500 mt-2">
                Monitor, optimize and track your personal income performance.
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('user.incomes.create') }}"
               class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg transition">
                + Add Income
            </a>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid md:grid-cols-3 gap-6">

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-md border hover:shadow-xl transition">
            <p class="text-sm text-slate-500">Total Income</p>
            <h2 class="text-3xl font-bold text-emerald-600 mt-2">
                ₹{{ number_format($total,2) }}
            </h2>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-md border hover:shadow-xl transition">
            <p class="text-sm text-slate-500">This Month</p>
            <h2 class="text-3xl font-bold mt-2">
                ₹{{ number_format($currentMonth,2) }}
            </h2>
        </div>

        <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-2xl p-6 shadow-lg">
            <p class="text-sm opacity-80">Average Entry</p>
            <h2 class="text-3xl font-bold mt-2">
                ₹{{ number_format($average,2) }}
            </h2>
        </div>

    </div>

    {{-- MINI TREND CHART (UI ONLY) --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-md p-8 border">
        <h3 class="font-semibold text-lg mb-6">
            Income Trend (Last Months)
        </h3>
        <div class="h-[300px]">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    {{-- SEARCH --}}
    <div class="flex justify-between items-center">
        <input id="searchInput"
               type="text"
               placeholder="Search income source..."
               class="w-full md:w-1/3 px-5 py-3 rounded-xl border
                      focus:ring-2 focus:ring-indigo-500
                      outline-none shadow-sm dark:bg-slate-800 dark:border-slate-700">
    </div>

    {{-- TABLE --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 dark:bg-slate-800
                               text-slate-600 dark:text-slate-300
                               uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left cursor-pointer">Source</th>
                        <th class="px-6 py-4 text-left">Date</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody id="incomeTable" class="divide-y dark:divide-slate-800">

                @forelse($incomes as $income)

                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <td class="px-6 py-4 font-medium income-source">
                            {{ $income->source }}
                        </td>

                        <td class="px-6 py-4 text-slate-500">
                            {{ $income->created_at?->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4 text-right font-semibold text-emerald-600">
                            ₹{{ number_format((float)$income->amount, 2) }}
                        </td>

                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('user.incomes.edit', $income->id) }}"
                               class="text-indigo-600 hover:text-indigo-800">
                                Edit
                            </a>

                            <form action="{{ route('user.incomes.destroy', $income->id) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Delete this income?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-rose-600 hover:text-rose-800">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4 text-slate-400">
                                <div class="text-6xl">📈</div>
                                <p>No income records found.</p>
                                <a href="{{ route('user.incomes.create') }}"
                                   class="text-emerald-600 hover:underline">
                                    Add your first income
                                </a>
                            </div>
                        </td>
                    </tr>

                @endforelse

                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $incomes->links() }}
    </div>

</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($chartLabels);
const values = @json($chartValues);

if (labels.length > 0) {
    new Chart(document.getElementById('incomeChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Income',
                data: values,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.15)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
</script>

{{-- SEARCH FILTER --}}
<script>
document.getElementById('searchInput')?.addEventListener('input', function () {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#incomeTable tr').forEach(row => {
        const source = row.querySelector('.income-source');
        if (!source) return;
        row.style.display = source.innerText.toLowerCase().includes(val) ? '' : 'none';
    });
});
</script>

@endsection
