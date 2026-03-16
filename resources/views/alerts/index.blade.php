@extends('layouts.app')

@section('content')

@php
    $income  = $income  ?? 0;
    $expense = $expense ?? 0;

    $severityStyles = [
        'critical' => ['bg'=>'bg-rose-100','text'=>'text-rose-600','icon'=>'🚨','border'=>'border-rose-400'],
        'warning'  => ['bg'=>'bg-amber-100','text'=>'text-amber-600','icon'=>'⚠','border'=>'border-amber-400'],
        'success'  => ['bg'=>'bg-emerald-100','text'=>'text-emerald-600','icon'=>'✔','border'=>'border-emerald-400'],
        'info'     => ['bg'=>'bg-sky-100','text'=>'text-sky-600','icon'=>'ℹ','border'=>'border-sky-400'],
    ];
@endphp

<div class="max-w-7xl mx-auto px-6 py-16 space-y-16 relative overflow-hidden">

    {{-- Background Glow --}}
    <div class="pointer-events-none absolute -top-48 left-1/4 w-[520px] h-[520px] bg-emerald-200/40 blur-[180px]"></div>
    <div class="pointer-events-none absolute -bottom-48 right-1/4 w-[520px] h-[520px] bg-indigo-200/30 blur-[180px]"></div>

    {{-- HEADER --}}
    <header class="relative z-10 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-12">

        <div class="space-y-4 max-w-3xl">
            <div class="flex items-center gap-4">

                <h1 class="text-5xl font-black text-slate-800 tracking-tight">
                    AI Risk Monitoring
                </h1>

                <span class="relative inline-flex items-center">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400/30 animate-ping"></span>
                    <span class="relative px-4 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold tracking-widest">
                        LIVE
                    </span>
                </span>
            </div>

            <p class="text-lg text-slate-600">
                Behavioral finance anomaly detection powered by AI engine.
            </p>
        </div>

        {{-- METRICS --}}
        <div class="flex gap-6 flex-wrap">
            <div class="bg-white rounded-3xl px-7 py-5 border shadow-sm min-w-[170px]">
                <p class="text-xs uppercase tracking-widest text-slate-500">Income</p>
                <p class="text-3xl font-extrabold text-emerald-600 mt-1">
                    ₹{{ number_format($income,2) }}
                </p>
            </div>

            <div class="bg-white rounded-3xl px-7 py-5 border shadow-sm min-w-[170px]">
                <p class="text-xs uppercase tracking-widest text-slate-500">Expense</p>
                <p class="text-3xl font-extrabold text-rose-600 mt-1">
                    ₹{{ number_format($expense,2) }}
                </p>
            </div>
        </div>

    </header>

    {{-- FILTER + SEARCH --}}
    <section class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">

        <form method="GET" class="flex gap-4 flex-wrap">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search alerts..."
                   class="px-4 py-2 rounded-xl border bg-white text-sm">

            <select name="severity"
                    class="px-4 py-2 rounded-xl border bg-white text-sm">
                <option value="">All Severity</option>
                <option value="critical" {{ request('severity')=='critical'?'selected':'' }}>Critical</option>
                <option value="warning"  {{ request('severity')=='warning'?'selected':'' }}>Warning</option>
                <option value="info"     {{ request('severity')=='info'?'selected':'' }}>Info</option>
                <option value="success"  {{ request('severity')=='success'?'selected':'' }}>Success</option>
            </select>

            <button class="px-5 py-2 rounded-xl bg-slate-900 text-white text-sm">
                Filter
            </button>

        </form>

    </section>

    {{-- ALERTS --}}
    <section class="relative z-10 space-y-8">

        @if(isset($alerts) && $alerts->count())

            @foreach($alerts as $alert)

                @php
                    $level = $alert->severity ?? 'info';
                    $style = $severityStyles[$level] ?? $severityStyles['info'];
                @endphp

                <article
                    class="bg-white rounded-[36px] p-8 flex gap-6 items-start
                           border-l-4 {{ $style['border'] }}
                           shadow-sm hover:shadow-md transition"
                    role="alert"
                >

                    <div class="shrink-0">
                        <div class="h-14 w-14 rounded-3xl {{ $style['bg'] }} {{ $style['text'] }}
                                    flex items-center justify-center text-xl">
                            {{ $style['icon'] }}
                        </div>
                    </div>

                    <div class="flex-1 space-y-3">
                        <p class="text-lg font-semibold text-slate-800">
                            {{ $alert->message }}
                        </p>

                        <div class="flex items-center gap-3 text-xs text-slate-500 font-mono">
                            <span>{{ optional($alert->created_at)->diffForHumans() }}</span>
                            <span>•</span>
                            <span>Engine: v2.4</span>
                        </div>
                    </div>

                    <span class="text-[11px] px-4 py-1.5 rounded-full
                        bg-slate-100 text-slate-600 uppercase tracking-widest">
                        {{ ucfirst($alert->status ?? 'active') }}
                    </span>

                </article>

            @endforeach

        @else

            {{-- EMPTY STATE --}}
            <div class="bg-white rounded-[42px] p-20 text-center border shadow-sm">

                <div class="text-7xl mb-6">🧠</div>

                <p class="text-3xl font-extrabold text-slate-800">
                    System Stable
                </p>

                <p class="text-slate-500 mt-4 max-w-lg mx-auto text-lg">
                    No financial anomalies detected.
                </p>

                <a href="{{ route('user.dashboard') }}"
                   class="inline-block mt-8 px-6 py-3 bg-slate-900 text-white rounded-xl text-sm">
                    Back to Dashboard
                </a>

            </div>

        @endif

    </section>

    {{-- PAGINATION --}}
    @if(isset($alerts) && method_exists($alerts, 'links'))
        <div class="mt-12">
            {{ $alerts->withQueryString()->links() }}
        </div>
    @endif

</div>

@endsection
