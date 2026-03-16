@extends('layouts.app')

@section('content')

@php
    $hasShowRoute = Route::has('user.families.show');
    $hasCreateRoute = Route::has('user.families.create');
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 px-6 py-16">

<div class="max-w-7xl mx-auto">

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="mb-10 bg-emerald-50 border border-emerald-200
                    text-emerald-700 px-6 py-4 rounded-2xl shadow animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-16">

        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                Family Spaces
            </h1>
            <p class="mt-3 text-slate-500">
                Secure shared financial collaboration environments.
            </p>
        </div>

        @if($hasCreateRoute)
            <a href="{{ route('user.families.create') }}"
               class="inline-flex items-center gap-2 px-7 py-3 rounded-2xl
                      bg-gradient-to-r from-emerald-500 to-cyan-500
                      text-white font-semibold shadow-xl
                      hover:scale-105 active:scale-95 transition">
                + Create Family
            </a>
        @endif
    </div>

    {{-- EMPTY STATE --}}
    @if($families->isEmpty())

        <div class="bg-white rounded-3xl border border-slate-200
                    shadow-xl p-16 text-center max-w-2xl mx-auto">

            <div class="text-6xl mb-6">👨‍👩‍👧‍👦</div>

            <h2 class="text-2xl font-bold text-slate-900">
                No Family Spaces Yet
            </h2>

            <p class="text-slate-500 mt-4">
                Create your first shared budgeting workspace and collaborate securely.
            </p>

            @if($hasCreateRoute)
                <a href="{{ route('user.families.create') }}"
                   class="inline-block mt-8 px-8 py-3 rounded-2xl
                          bg-emerald-600 text-white font-semibold
                          hover:bg-emerald-500 transition shadow-lg">
                    Create First Family
                </a>
            @endif

        </div>

    @else

        {{-- QUICK STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-14">

            <div class="bg-white rounded-2xl border border-slate-200
                        p-6 shadow-md">
                👥 Member of
                <span class="font-bold text-indigo-600">
                    {{ $families->count() }}
                </span> family(s)
            </div>

            <div class="bg-white rounded-2xl border border-slate-200
                        p-6 shadow-md">
                🔐 Personal finances remain isolated
            </div>

            <div class="bg-white rounded-2xl border border-slate-200
                        p-6 shadow-md">
                🤖 AI powered shared analytics
            </div>

        </div>

        {{-- GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            @foreach($families as $family)

                @php
                    $role = optional($family->pivot)->role ?? 'member';
                @endphp

                @if($hasShowRoute)
                <a href="{{ route('user.families.show', $family->id) }}"
                   class="group relative overflow-hidden
                          rounded-3xl p-8
                          bg-gradient-to-br from-slate-900 to-slate-800
                          text-white shadow-2xl
                          hover:scale-[1.02] hover:shadow-3xl
                          transition-all duration-300">

                    {{-- Glow --}}
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-20
                                bg-gradient-to-r from-indigo-500 to-cyan-500
                                transition"></div>

                    <div class="relative z-10">

                        <div class="flex justify-between items-center mb-6">

                            <h3 class="text-xl font-bold truncate">
                                {{ $family->name }}
                            </h3>

                            <span class="text-xs px-3 py-1 rounded-full
                                {{ $role === 'owner'
                                    ? 'bg-emerald-500'
                                    : 'bg-slate-600' }}">
                                {{ ucfirst($role) }}
                            </span>

                        </div>

                        <p class="text-slate-300 text-sm mb-8">
                            Shared income, expense management and real-time analytics.
                        </p>

                        <div class="flex justify-between text-xs text-slate-400">

                            <span>
                                Created {{ optional($family->created_at)->format('M Y') }}
                            </span>

                            <span class="group-hover:text-white transition">
                                Open →
                            </span>

                        </div>

                    </div>
                </a>
                @endif

            @endforeach

        </div>

        {{-- PAGINATION --}}
        @if(method_exists($families, 'links'))
            <div class="mt-16">
                {{ $families->links() }}
            </div>
        @endif

    @endif

</div>
</div>

<style>
.animate-fade-in {
    animation: fadeIn .4s ease-in-out;
}
@keyframes fadeIn {
    from { opacity:0; transform:translateY(-6px); }
    to { opacity:1; transform:translateY(0); }
}
</style>

@endsection
