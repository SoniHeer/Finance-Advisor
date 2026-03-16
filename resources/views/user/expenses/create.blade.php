@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-16">

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ERRORS --}}
    @if($errors->any())
        <div class="mb-8 bg-rose-50 border border-rose-200 text-rose-600 px-6 py-5 rounded-2xl shadow-sm text-sm">
            <ul class="space-y-2">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-12">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white">
                Add Expense
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Smart • Real-time • Financial Engine
            </p>
        </div>

        <a href="{{ route('user.expenses.index') }}"
           class="text-sm text-slate-500 hover:text-slate-900 dark:hover:text-white transition">
            ← Back
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        {{-- ================= FORM ================= --}}
        <div class="lg:col-span-2">

            <form method="POST"
                  action="{{ route('user.expenses.store') }}"
                  id="expenseForm"
                  class="bg-white dark:bg-slate-900
                         border border-slate-200 dark:border-slate-700
                         rounded-3xl p-12 shadow-2xl space-y-12">

                @csrf

                {{-- EXPENSE TYPE --}}
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-indigo-600 mb-6">
                        Expense Type
                    </label>

                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['personal'=>'👤 Personal','family'=>'👨‍👩‍👧‍👦 Family'] as $value=>$label)
                            <label class="cursor-pointer">
                                <input type="radio"
                                       name="expense_type"
                                       value="{{ $value }}"
                                       {{ old('expense_type','personal')==$value?'checked':'' }}
                                       class="hidden peer">

                                <div class="rounded-2xl p-6 text-center font-semibold border
                                            border-slate-200 dark:border-slate-700
                                            peer-checked:border-indigo-600
                                            peer-checked:ring-4 peer-checked:ring-indigo-200
                                            transition">
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- FAMILY SELECT --}}
                <div id="familyBox"
                     class="{{ old('expense_type')=='family'?'':'hidden' }}">

                    <label class="block text-xs uppercase tracking-widest font-bold text-slate-600 mb-4">
                        Select Family
                    </label>

                    <select id="familySelect"
                            name="family_id"
                            class="w-full rounded-2xl border
                                   border-slate-300 dark:border-slate-700
                                   px-5 py-4 dark:bg-slate-800 dark:text-white">
                        <option value="">Choose family</option>

                        @foreach($families ?? [] as $family)
                            <option value="{{ $family->id }}"
                                {{ old('family_id')==$family->id?'selected':'' }}>
                                {{ $family->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- AMOUNT --}}
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-rose-600 mb-4">
                        Amount
                    </label>

                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            ₹
                        </span>

                        <input id="amountInput"
                               type="number"
                               name="amount"
                               step="0.01"
                               min="0.01"
                               required
                               value="{{ old('amount') }}"
                               class="w-full pl-10 pr-4 py-5 text-2xl font-bold
                                      rounded-2xl border
                                      border-slate-300 dark:border-slate-700
                                      focus:ring-2 focus:ring-rose-500
                                      dark:bg-slate-800 dark:text-white">
                    </div>

                    <div class="flex justify-between mt-3 text-sm">
                        <span id="amountPreview" class="text-slate-500"></span>
                        <span id="amountBadge"
                              class="hidden px-3 py-1 rounded-full text-xs font-semibold"></span>
                    </div>
                </div>

                {{-- TITLE --}}
                <div>
                    <input id="titleInput"
                           type="text"
                           name="title"
                           required
                           maxlength="150"
                           value="{{ old('title') }}"
                           placeholder="Groceries / Rent / Fuel"
                           class="w-full rounded-2xl border
                                  border-slate-300 dark:border-slate-700
                                  px-6 py-5 text-lg
                                  dark:bg-slate-800 dark:text-white">

                    <div class="text-xs text-slate-500 mt-2 text-right">
                        <span id="charCount">0</span> / 150
                    </div>
                </div>

                {{-- DATE --}}
                <div>
                    <input type="date"
                           name="expense_date"
                           required
                           value="{{ old('expense_date', now()->toDateString()) }}"
                           class="w-full rounded-2xl border
                                  border-slate-300 dark:border-slate-700
                                  px-5 py-4 dark:bg-slate-800 dark:text-white">
                </div>

                {{-- CATEGORY --}}
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-slate-600 mb-6">
                        Category
                    </label>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach(['Food'=>'🍔','Travel'=>'🚕','Shopping'=>'🛍️','Bills'=>'🧾'] as $key=>$icon)
                            <label class="cursor-pointer">
                                <input type="radio"
                                       name="category"
                                       value="{{ $key }}"
                                       {{ old('category','Food')==$key?'checked':'' }}
                                       class="hidden peer">

                                <div class="rounded-2xl p-6 text-center border
                                            border-slate-200 dark:border-slate-700
                                            peer-checked:border-rose-600
                                            peer-checked:ring-4 peer-checked:ring-rose-200
                                            transition">
                                    <div class="text-2xl">{{ $icon }}</div>
                                    <div class="font-semibold mt-2">{{ $key }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- SUBMIT --}}
                <button type="submit"
                        id="submitBtn"
                        class="w-full py-5 rounded-2xl
                               bg-gradient-to-r from-rose-500 to-pink-600
                               text-white font-semibold text-lg
                               hover:scale-[1.02] active:scale-95
                               transition shadow-xl">
                    <span class="submitText">Save Expense</span>
                    <span class="loadingText hidden">Saving...</span>
                </button>

            </form>

        </div>

        {{-- ================= SIDEBAR ================= --}}
        <aside class="space-y-8">

            {{-- SMART INSIGHT --}}
            <div class="bg-white dark:bg-slate-900
                        border border-slate-200 dark:border-slate-700
                        rounded-3xl p-8 shadow-lg">

                <h3 class="font-bold mb-3">🤖 Smart Insight</h3>
                <p id="aiInsight"
                   class="text-sm text-slate-600 dark:text-slate-300">
                    This expense will update balance & analytics instantly.
                </p>

            </div>

            {{-- RECENT EXPENSES --}}
            <div class="bg-white dark:bg-slate-900
                        border border-slate-200 dark:border-slate-700
                        rounded-3xl p-8 shadow-lg">

                <h4 class="text-xs uppercase font-bold text-slate-500 mb-5">
                    Recent Expenses
                </h4>

                @forelse($recentExpenses ?? [] as $exp)
                    <div class="flex justify-between text-sm mb-3">
                        <span>{{ $exp->title }}</span>
                        <span class="text-rose-600 font-semibold">
                            -₹{{ number_format($exp->amount,2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm">
                        No recent expenses.
                    </p>
                @endforelse

            </div>

        </aside>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const amountInput = document.getElementById('amountInput');
    const amountPreview = document.getElementById('amountPreview');
    const amountBadge = document.getElementById('amountBadge');
    const titleInput = document.getElementById('titleInput');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitBtn');

    function formatINR(num){
        return new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR'}).format(num);
    }

    amountInput.addEventListener('input', function(){
        let value = Number(this.value || 0);
        if(value < 0) value = 0;
        this.value = value;

        amountPreview.innerText = value ? formatINR(value) : '';

        if(value > 10000){
            amountBadge.className = "px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700";
            amountBadge.innerText = "High Expense";
            amountBadge.classList.remove('hidden');
        } else {
            amountBadge.classList.add('hidden');
        }
    });

    titleInput.addEventListener('input', function(){
        charCount.innerText = this.value.length;
    });

    submitBtn.closest('form').addEventListener('submit', function(){
        submitBtn.disabled = true;
        submitBtn.querySelector('.submitText').classList.add('hidden');
        submitBtn.querySelector('.loadingText').classList.remove('hidden');
    });

});
</script>

@endsection
