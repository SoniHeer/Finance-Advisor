@extends('layouts.app')

@section('content')

@php
    $user = auth()->user();

    // Safe role
    $role = $user->role ?? 'user';

    // Safe blocked flag
    $isBlocked = property_exists($user, 'is_blocked')
        ? $user->is_blocked
        : false;

    // Email verification safe check
    $emailVerified = method_exists($user, 'hasVerifiedEmail')
        ? $user->hasVerifiedEmail()
        : true;

@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 py-16">

<div class="max-w-7xl mx-auto px-6">

<div class="grid grid-cols-1 xl:grid-cols-2 gap-16">

{{-- ===================================================== --}}
{{-- PROFILE CARD --}}
{{-- ===================================================== --}}
<div class="relative bg-white rounded-[40px] shadow-xl border p-12">

    {{-- Glow --}}
    <div class="absolute -top-20 -right-20 w-72 h-72 bg-indigo-200/40 blur-[120px] pointer-events-none"></div>

    {{-- Avatar --}}
    <div class="relative text-center">

        <div class="mx-auto h-32 w-32 rounded-full
                    bg-gradient-to-br from-indigo-600 to-cyan-500
                    text-white flex items-center justify-center
                    text-5xl font-black shadow-2xl tracking-widest">

            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}

        </div>

        <h2 class="mt-8 text-2xl font-extrabold text-slate-800">
            {{ $user->name }}
        </h2>

        <p class="text-slate-500 mt-2">
            {{ $user->email }}
        </p>

        {{-- Role Badge --}}
        <div class="mt-6">

            <span class="px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase
                {{ $role === 'admin' ? 'bg-rose-100 text-rose-600'
                    : ($role === 'manager'
                        ? 'bg-emerald-100 text-emerald-600'
                        : 'bg-sky-100 text-sky-600') }}">

                {{ strtoupper($role) }}

            </span>

        </div>

    </div>

    {{-- Status Cards --}}
    <div class="mt-12 grid grid-cols-2 gap-6 text-sm">

        <div class="bg-slate-50 p-6 rounded-2xl border text-center">
            <p class="uppercase text-xs text-slate-500 tracking-wider">
                Account Status
            </p>
            <p class="mt-2 font-bold text-lg
                {{ $isBlocked ? 'text-rose-600' : 'text-emerald-600' }}">
                {{ $isBlocked ? 'Blocked' : 'Active' }}
            </p>
        </div>

        <div class="bg-slate-50 p-6 rounded-2xl border text-center">
            <p class="uppercase text-xs text-slate-500 tracking-wider">
                Member Since
            </p>
            <p class="mt-2 font-bold text-lg text-slate-700">
                {{ optional($user->created_at)->format('M Y') }}
            </p>
        </div>

    </div>

    {{-- Security Indicator --}}
    <div class="mt-10 text-center">

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full
            {{ $emailVerified ? 'bg-emerald-50 text-emerald-600'
                               : 'bg-amber-50 text-amber-600' }}">

            <span class="text-sm">
                {{ $emailVerified ? 'Email Verified' : 'Email Not Verified' }}
            </span>

        </div>

    </div>

</div>



{{-- ===================================================== --}}
{{-- EDIT FORM --}}
{{-- ===================================================== --}}
<div class="bg-white rounded-[40px] shadow-xl border p-12">

    <div class="mb-10">
        <h1 class="text-3xl font-black text-slate-800">
            Account Settings
        </h1>
        <p class="text-slate-500 mt-2">
            Manage and update your profile securely.
        </p>
    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-8 p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700">
            {{ session('success') }}
        </div>
    @endif


    {{-- Error Block --}}
    @if($errors->any())
        <div class="mb-8 p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600">
            <ul class="space-y-2 text-sm">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST"
          action="{{ route('user.profile.update') }}"
          class="space-y-8">

        @csrf


        {{-- Name --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                Full Name
            </label>
            <input type="text"
                   name="name"
                   required
                   value="{{ old('name', $user->name) }}"
                   class="mt-3 w-full rounded-2xl border px-5 py-3 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 outline-none transition">
        </div>


        {{-- Email --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                Email Address
            </label>
            <input type="email"
                   name="email"
                   required
                   value="{{ old('email', $user->email) }}"
                   class="mt-3 w-full rounded-2xl border px-5 py-3 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 outline-none transition">
        </div>


        {{-- Password --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                New Password
            </label>
            <input type="password"
                   name="password"
                   placeholder="Leave blank if unchanged"
                   class="mt-3 w-full rounded-2xl border px-5 py-3 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 outline-none transition">
        </div>


        {{-- Confirm --}}
        <div>
            <input type="password"
                   name="password_confirmation"
                   placeholder="Confirm new password"
                   class="w-full rounded-2xl border px-5 py-3 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 outline-none transition">
        </div>


        {{-- Buttons --}}
        <div class="pt-6 flex flex-col sm:flex-row gap-6">

            <button type="submit"
                class="flex-1 bg-gradient-to-r from-indigo-600 to-cyan-600
                       text-white font-semibold py-3 rounded-2xl shadow-lg
                       hover:scale-[1.02] transition duration-300">

                Save Changes

            </button>

            <a href="{{ route('user.dashboard') }}"
               class="text-center px-6 py-3 rounded-2xl border font-semibold text-slate-600 hover:bg-slate-50 transition">

                Cancel

            </a>

        </div>

    </form>

</div>

</div>
</div>
</div>

@endsection
