@extends('layouts.app')

@section('content')

@php
    $user = auth()->user();

    if(!$user){
        abort(403);
    }

    $totalIncome  = (float) ($totalIncome  ?? 0);
    $totalExpense = (float) ($totalExpense ?? 0);
    $savings      = $totalIncome - $totalExpense;

    $role = $user->role ?? 'user';
@endphp

<div class="max-w-7xl mx-auto px-6 py-16 space-y-16">

    {{-- ================= HEADER CARD ================= --}}
    <div class="bg-white border border-slate-200 rounded-3xl p-10 shadow-sm">

        <div class="flex flex-col lg:flex-row lg:items-center gap-10">

            {{-- Avatar --}}
            <div class="relative">
                <div class="h-28 w-28 rounded-3xl bg-gradient-to-br from-indigo-500 to-cyan-500
                            flex items-center justify-center
                            text-4xl font-bold text-white shadow-md">
                    {{ strtoupper(substr($user->name ?? 'U',0,1)) }}
                </div>

                @if(!$user->is_blocked)
                    <span class="absolute bottom-1 right-1 h-4 w-4 bg-emerald-500 border-4 border-white rounded-full"></span>
                @else
                    <span class="absolute bottom-1 right-1 h-4 w-4 bg-rose-500 border-4 border-white rounded-full"></span>
                @endif
            </div>

            {{-- User Info --}}
            <div class="flex-1 space-y-2">

                <h1 class="text-3xl font-bold text-slate-800">
                    {{ $user->name }}
                </h1>

                <p class="text-sm text-slate-500">
                    {{ $user->email }}
                </p>

                <p class="text-xs text-slate-400">
                    Joined {{ $user->created_at?->format('M Y') }}
                </p>

                {{-- Role Badge --}}
                <span class="inline-block mt-2 text-xs font-semibold px-3 py-1 rounded-full
                    {{ $role === 'admin' ? 'bg-rose-100 text-rose-600' :
                       ($role === 'manager' ? 'bg-emerald-100 text-emerald-600' :
                        'bg-sky-100 text-sky-600') }}">
                    {{ strtoupper($role) }}
                </span>

            </div>

            {{-- Account Status Card --}}
            <div class="bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 min-w-[200px]">
                <p class="text-xs uppercase font-bold text-slate-500">
                    Account Status
                </p>
                <p class="mt-2 font-semibold
                    {{ $user->is_blocked ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $user->is_blocked ? 'Blocked' : 'Active' }}
                </p>
            </div>

        </div>

    </div>


    {{-- ================= FINANCIAL ANALYTICS ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <div class="bg-white rounded-2xl border p-8 shadow-sm hover:shadow-md transition">
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">
                Lifetime Income
            </p>
            <h3 class="text-3xl font-extrabold text-emerald-600 mt-3">
                ₹{{ number_format($totalIncome,2) }}
            </h3>
        </div>

        <div class="bg-white rounded-2xl border p-8 shadow-sm hover:shadow-md transition">
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">
                Total Expenses
            </p>
            <h3 class="text-3xl font-extrabold text-rose-600 mt-3">
                ₹{{ number_format($totalExpense,2) }}
            </h3>
        </div>

        <div class="bg-white rounded-2xl border p-8 shadow-sm hover:shadow-md transition">
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">
                Net Savings
            </p>
            <h3 class="text-3xl font-extrabold
                {{ $savings >= 0 ? 'text-indigo-600' : 'text-rose-600' }} mt-3">
                ₹{{ number_format($savings,2) }}
            </h3>
        </div>

    </div>


    {{-- ================= SETTINGS PANEL ================= --}}
    <div class="bg-white border rounded-3xl p-10 shadow-sm">

        <h2 class="text-xl font-bold text-slate-800 mb-8">
            Account Settings
        </h2>

        <div class="grid md:grid-cols-3 gap-6">

            @if(Route::has('user.profile.edit'))
                <a href="{{ route('user.profile.edit') }}"
                   class="p-6 rounded-2xl border bg-slate-50 hover:bg-slate-100 transition">
                    <div class="text-lg font-semibold">👤 Edit Profile</div>
                    <p class="text-xs text-slate-500 mt-2">
                        Update personal information
                    </p>
                </a>
            @endif

            @if(Route::has('user.profile.password.form'))
                <a href="{{ route('user.profile.password.form') }}"
                   class="p-6 rounded-2xl border bg-slate-50 hover:bg-slate-100 transition">
                    <div class="text-lg font-semibold">🔒 Change Password</div>
                    <p class="text-xs text-slate-500 mt-2">
                        Secure your account
                    </p>
                </a>
            @endif

            @if(Route::has('user.profile.subscription'))
                <a href="{{ route('user.profile.subscription') }}"
                   class="p-6 rounded-2xl border bg-slate-50 hover:bg-slate-100 transition">
                    <div class="text-lg font-semibold">💳 Subscription</div>
                    <p class="text-xs text-slate-500 mt-2">
                        Manage billing & plan
                    </p>
                </a>
            @endif

        </div>

    </div>

</div>

@endsection
