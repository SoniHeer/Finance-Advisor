@extends('layouts.app')

@section('content')

@php
$originalAmount = (float)($expense->amount ?? 0);
@endphp

<div class="max-w-5xl mx-auto px-6 py-16">

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ERRORS --}}
    @if($errors->any())
        <div class="mb-8 bg-rose-50 border border-rose-200 text-rose-600 px-6 py-5 rounded-2xl text-sm shadow-sm">
            <ul class="space-y-2">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white">
            Edit Expense
        </h1>
        <p class="text-sm text-slate-500 mt-2">
            {{ $expense->is_personal ? 'Personal expense' : 'Family expense' }}
            • created {{ $expense->created_at ? $expense->created_at->diffForHumans() : '—' }}
        </p>
    </div>

    <form method="POST"
          action="{{ route('user.expenses.update', $expense->id) }}"
          id="expenseForm"
          class="bg-white dark:bg-slate-900
                 border border-slate-200 dark:border-slate-700
                 shadow-2xl rounded-3xl p-12 space-y-12 transition">

        @csrf
        @method('PUT')

        {{-- TITLE --}}
        <div>
            <label class="text-xs uppercase tracking-widest font-bold text-slate-600">
                Title
            </label>

            <input id="titleInput"
                   type="text"
                   name="title"
                   maxlength="150"
                   required
                   value="{{ old('title', $expense->title) }}"
                   class="w-full mt-4 rounded-2xl border
                          border-slate-300 dark:border-slate-700
                          px-6 py-4 text-lg
                          dark:bg-slate-800 dark:text-white">

            <div class="text-xs text-slate-500 mt-2 flex justify-between">
                <span>Keep title clear and short</span>
                <span id="charCount">0 / 150</span>
            </div>
        </div>

        {{-- CATEGORY --}}
        <div>
            <label class="text-xs uppercase tracking-widest font-bold text-slate-600">
                Category
            </label>

            <select name="category"
                    required
                    class="w-full mt-4 rounded-2xl border
                           border-slate-300 dark:border-slate-700
                           px-6 py-4 text-lg
                           dark:bg-slate-800 dark:text-white">

                @foreach(['Food','Travel','Bills','Shopping','Other'] as $cat)
                    <option value="{{ $cat }}"
                        {{ old('category', $expense->category) === $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach

            </select>
        </div>

        {{-- AMOUNT --}}
        <div>
            <label class="text-xs uppercase tracking-widest font-bold text-rose-600">
                Amount
            </label>

            <div class="relative mt-4">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                    ₹
                </span>

                <input id="amountInput"
                       type="number"
                       name="amount"
                       min="0.01"
                       step="0.01"
                       required
                       value="{{ old('amount', $originalAmount) }}"
                       class="w-full pl-10 pr-4 py-5 text-2xl font-bold
                              rounded-2xl border
                              border-slate-300 dark:border-slate-700
                              focus:ring-2 focus:ring-rose-500
                              dark:bg-slate-800 dark:text-white">
            </div>

            <div class="flex justify-between items-center mt-3 text-sm">
                <span id="amountPreview" class="text-slate-500"></span>
                <span id="impactBadge"
                      class="px-3 py-1 rounded-full text-xs font-semibold hidden"></span>
            </div>

            <div class="flex gap-3 mt-4">
                <button type="button" onclick="adjust(100)"
                        class="px-4 py-2 border rounded-full text-sm">+100</button>
                <button type="button" onclick="adjust(500)"
                        class="px-4 py-2 border rounded-full text-sm">+500</button>
                <button type="button" onclick="adjust(-500)"
                        class="px-4 py-2 border rounded-full text-sm">−500</button>
            </div>
        </div>

        {{-- DATE --}}
        <div>
            <label class="text-xs uppercase tracking-widest font-bold text-slate-600">
                Expense Date
            </label>

            <input type="date"
                   name="expense_date"
                   required
                   value="{{ old('expense_date', optional($expense->expense_date)->format('Y-m-d')) }}"
                   class="w-full mt-4 rounded-2xl border
                          border-slate-300 dark:border-slate-700
                          px-6 py-4 dark:bg-slate-800 dark:text-white">
        </div>

        {{-- AI BOX --}}
        <div class="bg-slate-50 dark:bg-slate-800
                    border border-slate-200 dark:border-slate-700
                    rounded-2xl p-8 shadow-inner">

            <h4 class="font-bold mb-3">🤖 Smart Insight</h4>
            <p id="aiText"
               class="text-sm text-slate-600 dark:text-slate-300">
                Editing {{ $expense->is_personal ? 'personal' : 'family' }} expense.
            </p>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-between items-center pt-8 border-t dark:border-slate-700">

            <a href="{{ route('user.expenses.index') }}"
               class="text-sm text-slate-500 hover:text-slate-900 dark:hover:text-white">
                ← Back
            </a>

            <button type="submit"
                    id="submitBtn"
                    class="px-10 py-5 rounded-2xl
                           bg-gradient-to-r from-rose-500 to-pink-600
                           text-white font-semibold text-lg
                           hover:scale-[1.02] active:scale-95
                           transition shadow-xl">
                <span class="submitText">Update Expense</span>
                <span class="loadingText hidden">Updating...</span>
            </button>

        </div>

    </form>
</div>

{{-- SCRIPT --}}
<script>
const amountInput = document.getElementById('amountInput');
const amountPreview = document.getElementById('amountPreview');
const aiText = document.getElementById('aiText');
const impactBadge = document.getElementById('impactBadge');
const titleInput = document.getElementById('titleInput');
const charCount = document.getElementById('charCount');
const form = document.getElementById('expenseForm');
const submitBtn = document.getElementById('submitBtn');

const originalAmount = {{ $originalAmount }};

function formatINR(num){
    return new Intl.NumberFormat('en-IN',{
        style:'currency',
        currency:'INR'
    }).format(num);
}

function adjust(val){
    let newVal = (Number(amountInput.value) || 0) + val;
    if(newVal < 0) newVal = 0;
    amountInput.value = newVal;
    updateAI();
}

function updateAI(){
    const current = Number(amountInput.value || 0);
    amountPreview.innerText = current ? "Preview: " + formatINR(current) : "";

    const diff = current - originalAmount;

    if(diff > 0){
        impactBadge.className = "px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700";
        impactBadge.innerText = "+" + formatINR(diff);
        impactBadge.classList.remove("hidden");
        aiText.innerText = 'Expense increased. Balance impact worsened.';
    }
    else if(diff < 0){
        impactBadge.className = "px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700";
        impactBadge.innerText = formatINR(diff);
        impactBadge.classList.remove("hidden");
        aiText.innerText = 'Expense reduced. Positive impact on balance.';
    }
    else{
        impactBadge.classList.add("hidden");
        aiText.innerText = 'No financial change detected.';
    }
}

titleInput.addEventListener('input',function(){
    charCount.innerText = this.value.length + " / 150";
});

amountInput.addEventListener('input', updateAI);

form.addEventListener('submit', function(){
    submitBtn.disabled = true;
    submitBtn.querySelector('.submitText').classList.add('hidden');
    submitBtn.querySelector('.loadingText').classList.remove('hidden');
});

updateAI();
charCount.innerText = titleInput.value.length + " / 150";
</script>

@endsection
