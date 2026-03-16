@extends('layouts.app')

@section('content')

@php
$originalAmount = (float) $income->amount;
@endphp

<div class="max-w-4xl mx-auto px-6 py-16">

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ERRORS --}}
    @if($errors->any())
        <div class="mb-8 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-5 rounded-2xl shadow-sm">
            <ul class="list-disc pl-5 space-y-2 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="mb-14">
        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
            Edit Income
        </h1>
        <p class="text-slate-500 mt-3">
            Securely update your income entry with real-time impact preview.
        </p>
    </div>

    {{-- FORM --}}
    <form method="POST"
          action="{{ route('user.incomes.update', $income->id) }}"
          class="bg-white dark:bg-slate-900
                 border border-slate-200 dark:border-slate-700
                 shadow-2xl rounded-3xl p-12 space-y-12 transition">

        @csrf
        @method('PUT')

        {{-- AMOUNT --}}
        <div class="space-y-3">
            <label class="text-xs uppercase font-bold tracking-widest text-emerald-600">
                Amount
            </label>

            <div class="relative">
                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                    ₹
                </span>

                <input id="amountInput"
                       type="number"
                       step="0.01"
                       name="amount"
                       value="{{ old('amount', $income->amount) }}"
                       required
                       class="w-full pl-12 pr-6 py-5 text-2xl font-bold
                              border border-slate-300 dark:border-slate-700
                              rounded-2xl focus:ring-2 focus:ring-emerald-500
                              dark:bg-slate-800 dark:text-white
                              outline-none transition-all duration-200">
            </div>

            <div class="flex justify-between items-center">
                <p id="amountPreview" class="text-sm text-slate-500"></p>
                <span id="changeBadge"
                      class="text-xs font-semibold px-3 py-1 rounded-full hidden"></span>
            </div>
        </div>

        {{-- SOURCE --}}
        <div class="space-y-3">
            <label class="text-xs uppercase font-bold tracking-widest text-blue-600">
                Source
            </label>

            <input id="sourceInput"
                   type="text"
                   name="source"
                   value="{{ old('source', $income->source) }}"
                   maxlength="60"
                   required
                   class="w-full px-6 py-5 text-lg
                          border border-slate-300 dark:border-slate-700
                          rounded-2xl focus:ring-2 focus:ring-blue-500
                          dark:bg-slate-800 dark:text-white
                          outline-none transition">

            <div class="flex justify-between text-xs text-slate-500">
                <span>Describe income source clearly</span>
                <span id="charCount">0 / 60</span>
            </div>
        </div>

        {{-- DATE --}}
        <div class="space-y-3">
            <label class="text-xs uppercase font-bold tracking-widest text-indigo-600">
                Income Date
            </label>

            <input type="date"
                   name="income_date"
                   value="{{ old('income_date', optional($income->income_date)->format('Y-m-d')) }}"
                   required
                   class="w-full px-6 py-5 text-lg
                          border border-slate-300 dark:border-slate-700
                          rounded-2xl focus:ring-2 focus:ring-indigo-500
                          dark:bg-slate-800 dark:text-white
                          outline-none transition">
        </div>

        {{-- AI INSIGHT PANEL --}}
        <div class="bg-gradient-to-br from-slate-50 to-white
                    dark:from-slate-800 dark:to-slate-900
                    border border-slate-200 dark:border-slate-700
                    rounded-2xl p-8 shadow-inner">

            <h4 class="font-bold mb-3 flex items-center gap-2">
                🤖 Smart Financial Insight
            </h4>

            <p id="aiText"
               class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                Modify the amount to preview financial impact instantly.
            </p>

        </div>

        {{-- ACTIONS --}}
        <div class="flex flex-col md:flex-row gap-5 pt-6">

            <button type="submit"
                class="flex-1 py-5 rounded-2xl
                       bg-gradient-to-r from-emerald-500 to-blue-600
                       text-white text-lg font-semibold
                       hover:scale-[1.02] active:scale-[0.98]
                       transition-all duration-200 shadow-xl">
                Save Changes
            </button>

            <a href="{{ route('user.incomes.index') }}"
               class="text-center px-10 py-5 rounded-2xl
                      border border-slate-300 dark:border-slate-700
                      text-slate-600 dark:text-slate-300
                      hover:bg-slate-100 dark:hover:bg-slate-800
                      transition">
                Cancel
            </a>

        </div>

    </form>
</div>

{{-- UX SCRIPT --}}
<script>
const amountInput = document.getElementById('amountInput');
const preview = document.getElementById('amountPreview');
const aiText = document.getElementById('aiText');
const changeBadge = document.getElementById('changeBadge');
const sourceInput = document.getElementById('sourceInput');
const charCount = document.getElementById('charCount');

const originalAmount = {{ $originalAmount }};

function formatINR(num){
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(num);
}

function updateAmountPreview() {

    const value = Number(amountInput.value || 0);
    preview.innerText = value ? "Preview: " + formatINR(value) : "";

    const diff = value - originalAmount;

    if (diff > 0) {
        changeBadge.className = "text-xs font-semibold px-3 py-1 rounded-full bg-emerald-100 text-emerald-700";
        changeBadge.innerText = "+" + formatINR(diff);
        changeBadge.classList.remove("hidden");
        aiText.innerText = "Income increased. Positive impact on savings projection.";
    }
    else if (diff < 0) {
        changeBadge.className = "text-xs font-semibold px-3 py-1 rounded-full bg-rose-100 text-rose-700";
        changeBadge.innerText = formatINR(diff);
        changeBadge.classList.remove("hidden");
        aiText.innerText = "Income decreased. Monitor future cashflow carefully.";
    }
    else {
        changeBadge.classList.add("hidden");
        aiText.innerText = "No financial impact detected yet.";
    }
}

amountInput.addEventListener('input', updateAmountPreview);

sourceInput.addEventListener('input', function(){
    charCount.innerText = this.value.length + " / 60";
});

updateAmountPreview();
charCount.innerText = sourceInput.value.length + " / 60";
</script>

@endsection
