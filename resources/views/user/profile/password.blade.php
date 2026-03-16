@extends('layouts.app')

@section('content')

@php
    $user = auth()->user();
@endphp

<div class="max-w-5xl mx-auto mt-16 px-6">

<div class="bg-white border border-slate-200 rounded-3xl shadow-xl p-12">

    {{-- HEADER --}}
    <div class="mb-12">
        <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-emerald-100 to-cyan-100
                    text-emerald-600 flex items-center justify-center mb-6 text-xl shadow">
            🔐
        </div>

        <h2 class="text-3xl font-extrabold text-slate-800">
            Update Password
        </h2>

        <p class="text-slate-500 text-sm mt-3">
            Updating your password will automatically log out all other sessions.
        </p>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200
                    text-emerald-700 p-4 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- GLOBAL ERRORS --}}
    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200
                    text-rose-600 p-4 rounded-xl text-sm">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('user.profile.password.update') }}"
          id="passwordForm"
          class="space-y-8">
        @csrf
        @method('POST')

        {{-- CURRENT PASSWORD --}}
        <div>
            <label class="block text-xs uppercase font-bold tracking-widest text-slate-500">
                Current Password
            </label>

            <input type="password"
                   name="current_password"
                   required
                   class="w-full mt-2 rounded-xl border px-4 py-3
                          focus:ring-2 focus:ring-emerald-400/30
                          focus:border-emerald-400 outline-none
                          @error('current_password') border-rose-400 @enderror">
        </div>

        {{-- NEW PASSWORD --}}
        <div>
            <label class="block text-xs uppercase font-bold tracking-widest text-slate-500">
                New Password
            </label>

            <div class="relative">
                <input id="password"
                       type="password"
                       name="password"
                       required
                       minlength="8"
                       class="w-full mt-2 rounded-xl border px-4 py-3 pr-12
                              focus:ring-2 focus:ring-emerald-400/30
                              focus:border-emerald-400 outline-none
                              @error('password') border-rose-400 @enderror">

                <button type="button"
                        onclick="toggle('password')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    👁
                </button>
            </div>
        </div>

        {{-- STRENGTH BAR --}}
        <div>
            <div class="flex gap-1 h-1.5 mb-2" id="strengthBar">
                <div class="flex-1 bg-slate-200 rounded-full"></div>
                <div class="flex-1 bg-slate-200 rounded-full"></div>
                <div class="flex-1 bg-slate-200 rounded-full"></div>
                <div class="flex-1 bg-slate-200 rounded-full"></div>
            </div>
            <p id="strengthText" class="text-xs text-slate-500">
                Password strength
            </p>
        </div>

        {{-- CONFIRM --}}
        <div>
            <label class="block text-xs uppercase font-bold tracking-widest text-slate-500">
                Confirm Password
            </label>

            <div class="relative">
                <input id="confirm"
                       type="password"
                       name="password_confirmation"
                       required
                       class="w-full mt-2 rounded-xl border px-4 py-3 pr-12
                              focus:ring-2 focus:ring-emerald-400/30
                              focus:border-emerald-400 outline-none">

                <button type="button"
                        onclick="toggle('confirm')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    👁
                </button>
            </div>

            <p id="matchError" class="text-xs text-rose-500 mt-2 hidden">
                Passwords do not match.
            </p>
        </div>

        {{-- SUBMIT --}}
        <button type="submit"
                id="submitBtn"
                class="w-full bg-gradient-to-r from-emerald-600 to-cyan-600
                       text-white font-semibold py-3 rounded-xl
                       hover:scale-[1.01] transition shadow-md">
            Update Password Securely
        </button>

    </form>

    {{-- FOOTER --}}
    <div class="mt-10 pt-6 border-t text-xs text-slate-500">
        🔒 Passwords are hashed using bcrypt encryption.
        Never share your credentials.
    </div>

</div>
</div>

{{-- ================= JS ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const passwordInput = document.getElementById('password');
    const confirmInput  = document.getElementById('confirm');
    const bars          = document.querySelectorAll('#strengthBar div');
    const text          = document.getElementById('strengthText');
    const form          = document.getElementById('passwordForm');
    const submitBtn     = document.getElementById('submitBtn');
    const matchError    = document.getElementById('matchError');

    if (passwordInput) {
        passwordInput.addEventListener('input', function () {

            let score = 0;
            const val = this.value;

            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            bars.forEach((bar, i) => {
                bar.className = "flex-1 rounded-full " +
                    (i < score ? "bg-emerald-500" : "bg-slate-200");
            });

            const labels = ["Weak", "Fair", "Good", "Strong"];
            text.innerText = score ? "Strength: " + labels[score - 1] : "Password strength";
        });
    }

    if (confirmInput && passwordInput) {
        confirmInput.addEventListener('input', function () {
            if (this.value !== passwordInput.value) {
                matchError.classList.remove('hidden');
            } else {
                matchError.classList.add('hidden');
            }
        });
    }

    if (form) {
        form.addEventListener("submit", function () {
            submitBtn.disabled = true;
            submitBtn.innerText = "Updating...";
        });
    }

});

function toggle(id) {
    const field = document.getElementById(id);
    if (field) {
        field.type = field.type === "password" ? "text" : "password";
    }
}
</script>

@endsection
