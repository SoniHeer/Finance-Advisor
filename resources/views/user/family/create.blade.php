@extends('layouts.app')

@section('content')

@php
    abort_unless(Route::has('user.families.store'), 404);
    abort_unless(Route::has('user.families.index'), 404);

    $routeStore = route('user.families.store');
    $routeIndex = route('user.families.index');
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50
            flex items-center justify-center px-6 py-16">

    <div class="w-full max-w-xl">

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200
                        bg-emerald-50 px-6 py-4 text-sm text-emerald-700
                        shadow-md animate-fade-in">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-slate-200
                    rounded-3xl shadow-2xl px-12 py-14">

            {{-- HEADER --}}
            <div class="text-center mb-12">

                <div class="mx-auto mb-6 h-16 w-16 rounded-3xl
                            bg-gradient-to-br from-indigo-600 to-cyan-500
                            flex items-center justify-center
                            text-2xl text-white shadow-xl">
                    👨‍👩‍👧‍👦
                </div>

                <h1 class="text-3xl font-extrabold text-slate-900">
                    Create Family Workspace
                </h1>

                <p class="mt-3 text-sm text-slate-500">
                    Enterprise-level shared financial collaboration
                </p>
            </div>

            {{-- ERRORS --}}
            @if ($errors->any())
                <div class="mb-8 rounded-2xl p-5
                            bg-rose-50 border border-rose-200
                            text-rose-600 text-sm">
                    <ul class="space-y-2">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM --}}
            <form method="POST"
                  action="{{ $routeStore }}"
                  id="familyForm"
                  class="space-y-8">
                @csrf

                {{-- FAMILY NAME --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                        Family Name
                    </label>

                    <input type="text"
                           id="name"
                           name="name"
                           maxlength="100"
                           required
                           value="{{ old('name') }}"
                           class="w-full rounded-2xl px-5 py-4 text-lg
                                  border border-slate-300
                                  focus:border-indigo-600
                                  focus:ring-4 focus:ring-indigo-200
                                  transition">

                    <div class="flex justify-between mt-2 text-xs text-slate-400">
                        <span>Max 100 characters</span>
                        <span><span id="charCount">0</span>/100</span>
                    </div>
                </div>

                {{-- QUICK SUGGESTIONS --}}
                <div class="flex flex-wrap gap-2 text-xs">
                    @foreach(['Family','Household','Finance Hub','Budget Group'] as $suggest)
                        <button type="button"
                                class="suggest-chip"
                                data-suggest="{{ $suggest }}">
                            {{ $suggest }}
                        </button>
                    @endforeach
                </div>

                {{-- SECURITY BLOCK --}}
                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-6 text-xs text-slate-600">
                    🔒 <strong>Security Features</strong>
                    <ul class="mt-3 space-y-1">
                        <li>• Role-based access control</li>
                        <li>• Personal finance isolation</li>
                        <li>• Audit & activity tracking</li>
                        <li>• Encrypted financial data</li>
                    </ul>
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-200">

                    <a href="{{ $routeIndex }}"
                       class="text-sm font-medium text-slate-500 hover:text-slate-900 transition">
                        ← Back
                    </a>

                    <button id="submitBtn"
                            type="submit"
                            class="relative px-10 py-4 rounded-2xl
                                   bg-gradient-to-r from-indigo-600 to-cyan-500
                                   text-white font-semibold text-lg
                                   hover:scale-105 active:scale-95
                                   transition shadow-xl">

                        <span class="submit-label">Create Family</span>
                        <span class="loading-label hidden">Creating...</span>

                    </button>
                </div>

            </form>

            <p class="mt-10 text-center text-xs text-slate-400">
                Next: Invite members → Add income → View analytics dashboard
            </p>

        </div>
    </div>
</div>

<style>
.suggest-chip {
    padding: 6px 14px;
    border-radius: 999px;
    background: #f1f5f9;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: .2s ease;
}
.suggest-chip:hover {
    background: #e0e7ff;
}
.animate-fade-in {
    animation: fadeIn .4s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('familyForm');
    const submitBtn = document.getElementById('submitBtn');
    const nameInput = document.getElementById('name');
    const charCount = document.getElementById('charCount');

    charCount.innerText = nameInput.value.length;

    nameInput.addEventListener('input', function(){
        charCount.innerText = this.value.length;
    });

    form.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.querySelector('.submit-label').classList.add('hidden');
        submitBtn.querySelector('.loading-label').classList.remove('hidden');
    });

    document.querySelectorAll('[data-suggest]').forEach(btn => {
        btn.addEventListener('click', function () {
            const text = this.dataset.suggest;
            nameInput.value = nameInput.value
                ? nameInput.value.trim() + ' ' + text
                : 'My ' + text;
            nameInput.dispatchEvent(new Event('input'));
            nameInput.focus();
        });
    });

});
</script>

@endsection
