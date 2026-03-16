@extends('layouts.app')

@section('content')

<div class="max-w-[1400px] mx-auto px-6 pb-24">

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ERRORS --}}
    @if($errors->any())
        <div class="mb-8 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-5 rounded-2xl shadow-sm">
            <ul class="space-y-2 text-sm">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-14">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Add Income
            </h1>
            <p class="text-sm text-slate-500 mt-2">
                Secure • Intelligent • Real-time Synced
            </p>
        </div>

        <a href="{{ route('user.incomes.index') }}"
           class="text-sm text-slate-500 hover:text-slate-900 dark:hover:text-white transition">
            ← Back
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-14">

        {{-- FORM --}}
        <div class="lg:col-span-2">

            <form action="{{ route('user.incomes.store') }}"
                  method="POST"
                  class="bg-white dark:bg-slate-900
                         border border-slate-200 dark:border-slate-700
                         shadow-2xl rounded-3xl p-12 space-y-12 transition">
                @csrf

                {{-- STEP INDICATOR --}}
                <div class="flex gap-4 text-xs uppercase tracking-widest text-slate-400">
                    <span class="text-indigo-600 font-bold">Step 1: Type</span>
                    <span>Step 2: Details</span>
                    <span>Step 3: Confirm</span>
                </div>

                {{-- INCOME TYPE --}}
                <div>
                    <label class="text-xs font-bold uppercase tracking-widest text-indigo-600">
                        Income Type
                    </label>

                    <div class="grid grid-cols-2 gap-5 mt-5">

                        @foreach(['personal' => '👤 Personal', 'family' => '👨‍👩‍👧‍👦 Family'] as $value => $label)

                            <label>
                                <input type="radio"
                                       name="income_type"
                                       value="{{ $value }}"
                                       {{ old('income_type','personal') == $value ? 'checked' : '' }}
                                       class="hidden peer">

                                <div class="rounded-2xl p-6 text-center font-semibold cursor-pointer
                                            border border-slate-200 dark:border-slate-700
                                            peer-checked:bg-indigo-50 peer-checked:border-indigo-500
                                            dark:peer-checked:bg-indigo-900/30
                                            hover:shadow-md transition">
                                    {{ $label }}
                                </div>
                            </label>

                        @endforeach
                    </div>
                </div>

                {{-- FAMILY BOX --}}
                <div id="familyBox"
                     class="transition-all duration-300 {{ old('income_type') == 'family' ? '' : 'hidden' }}">

                    <label class="text-xs font-bold uppercase tracking-widest text-blue-600">
                        Select Family
                    </label>

                    <select id="familySelect"
                            name="family_id"
                            class="w-full mt-4 rounded-2xl border
                                   border-slate-300 dark:border-slate-700
                                   px-5 py-5 text-lg
                                   dark:bg-slate-800 dark:text-white
                                   focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Choose family</option>
                        @foreach($families as $family)
                            <option value="{{ $family->id }}"
                                {{ old('family_id') == $family->id ? 'selected' : '' }}>
                                {{ $family->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- AMOUNT --}}
                <div>
                    <label class="text-xs font-bold uppercase tracking-widest text-emerald-600">
                        Amount
                    </label>

                    <div class="relative mt-5">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl">
                            ₹
                        </span>

                        <input id="amountInput"
                               type="number"
                               step="0.01"
                               name="amount"
                               value="{{ old('amount') }}"
                               required
                               class="w-full pl-12 pr-6 py-6 text-2xl font-bold
                                      border border-slate-300 dark:border-slate-700
                                      rounded-2xl focus:ring-2 focus:ring-emerald-500
                                      dark:bg-slate-800 dark:text-white
                                      outline-none transition-all duration-200">
                    </div>

                    <div class="flex justify-between items-center mt-3 text-sm">
                        <span id="amountPreview" class="text-slate-500"></span>
                        <span id="confidenceLevel"
                              class="px-3 py-1 rounded-full text-xs font-semibold hidden"></span>
                    </div>
                </div>

                {{-- DATE --}}
                <div>
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-600">
                        Income Date
                    </label>

                    <input type="date"
                           name="income_date"
                           value="{{ old('income_date', now()->toDateString()) }}"
                           required
                           class="w-full mt-4 rounded-2xl border
                                  border-slate-300 dark:border-slate-700
                                  px-5 py-5 text-lg
                                  dark:bg-slate-800 dark:text-white
                                  focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                {{-- SOURCE --}}
                <div>
                    <input id="sourceInput"
                           type="text"
                           name="source"
                           value="{{ old('source') }}"
                           maxlength="60"
                           placeholder="Income Source"
                           required
                           class="w-full rounded-2xl border
                                  border-slate-300 dark:border-slate-700
                                  px-6 py-5 text-lg
                                  dark:bg-slate-800 dark:text-white
                                  focus:ring-2 focus:ring-indigo-500 transition">

                    <div class="flex justify-between text-xs text-slate-500 mt-2">
                        <span>Describe income clearly</span>
                        <span id="charCount">0 / 60</span>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <button type="submit"
                        class="w-full py-6 rounded-2xl
                               bg-gradient-to-r from-emerald-500 to-blue-600
                               text-white font-semibold text-lg
                               hover:scale-[1.02] active:scale-[0.98]
                               transition-all duration-200 shadow-xl">
                    Save Income
                </button>

            </form>
        </div>

        {{-- SIDEBAR --}}
        <div class="space-y-10">

            {{-- SMART PANEL --}}
            <div class="bg-white dark:bg-slate-900
                        border border-slate-200 dark:border-slate-700
                        shadow-2xl rounded-3xl p-10">

                <h3 class="font-bold mb-4 flex items-center gap-2">
                    🤖 Smart Insight
                </h3>

                <p id="aiInsight"
                   class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    Add income to strengthen your financial stability.
                </p>
            </div>

            {{-- RECENT --}}
            <div class="bg-white dark:bg-slate-900
                        border border-slate-200 dark:border-slate-700
                        shadow-2xl rounded-3xl p-10">

                <h4 class="text-xs font-bold text-slate-500 uppercase mb-6 tracking-widest">
                    Recent Income
                </h4>

                @forelse($recentIncome as $inc)
                    <div class="flex justify-between mb-4 text-sm">
                        <span>{{ $inc->source }}</span>
                        <span class="text-emerald-600 font-bold">
                            +₹{{ number_format($inc->amount, 2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-500 text-sm">
                        No income yet
                    </p>
                @endforelse
            </div>

        </div>
    </div>
</div>

{{-- UX SCRIPT --}}
<script>
const radios = document.querySelectorAll('input[name="income_type"]');
const familyBox = document.getElementById('familyBox');
const familySelect = document.getElementById('familySelect');
const amountInput = document.getElementById('amountInput');
const amountPreview = document.getElementById('amountPreview');
const aiInsight = document.getElementById('aiInsight');
const confidence = document.getElementById('confidenceLevel');
const sourceInput = document.getElementById('sourceInput');
const charCount = document.getElementById('charCount');

function formatINR(num){
    return new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR'}).format(num);
}

radios.forEach(radio=>{
    radio.addEventListener('change',function(){
        if(this.value==='family'){
            familyBox.classList.remove('hidden');
            familySelect.setAttribute('required','required');
            aiInsight.innerText='Family income strengthens shared dashboard.';
        }else{
            familyBox.classList.add('hidden');
            familySelect.removeAttribute('required');
            aiInsight.innerText='Personal income impacts only your analytics.';
        }
    });
});

amountInput.addEventListener('input',function(){
    const value=Number(this.value||0);
    amountPreview.innerText=value?"Preview: "+formatINR(value):"";

    if(value>50000){
        confidence.className="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700";
        confidence.innerText="High Impact";
        confidence.classList.remove('hidden');
    }else if(value>10000){
        confidence.className="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700";
        confidence.innerText="Moderate Impact";
        confidence.classList.remove('hidden');
    }else{
        confidence.classList.add('hidden');
    }
});

sourceInput.addEventListener('input',function(){
    charCount.innerText=this.value.length+" / 60";
});

charCount.innerText=sourceInput.value.length+" / 60";
</script>

@endsection
