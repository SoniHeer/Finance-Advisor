@extends('layouts.app')

@section('content')

@php
$currentPlan = auth()->user()->plan ?? 'starter';
@endphp

<style>
.card{
    background:#ffffff;
    border:1px solid #e2e8f0;
    box-shadow:0 30px 80px -30px rgba(15,23,42,.15);
}
.card-popular{
    transform:scale(1.05);
}
</style>

<div class="max-w-7xl mx-auto px-6 py-24">

{{-- HERO --}}
<section class="text-center max-w-3xl mx-auto space-y-6">

    <span class="px-5 py-2 rounded-full text-xs font-semibold tracking-widest
        bg-slate-100 border border-slate-200 text-slate-600">
        PRICING
    </span>

    <h1 class="text-5xl font-extrabold text-slate-900 leading-tight">
        Transparent pricing for
        <span class="text-slate-400 block">
            smarter financial decisions
        </span>
    </h1>

    <p class="text-lg text-slate-500">
        Upgrade anytime. Cancel anytime.
        No contracts. No hidden fees.
    </p>

    {{-- BILLING TOGGLE --}}
    <div class="inline-flex relative bg-slate-100 border border-slate-200
        rounded-full p-1 mt-6">

        <div id="indicator"
            class="absolute top-1 left-1 h-[calc(100%-8px)] w-1/2
            bg-white rounded-full shadow transition-all duration-300">
        </div>

        <button id="monthly"
            class="relative z-10 px-8 py-2 text-sm font-semibold">
            Monthly
        </button>

        <button id="yearly"
            class="relative z-10 px-8 py-2 text-sm font-semibold text-slate-500">
            Yearly
            <span class="text-emerald-600 text-xs ml-1">Save 20%</span>
        </button>
    </div>
</section>


{{-- PLANS --}}
<section class="mt-20 grid md:grid-cols-3 gap-10">

{{-- STARTER --}}
<div class="card rounded-3xl p-10">

    <h3 class="text-xl font-bold text-slate-900">Starter</h3>
    <p class="text-sm text-slate-500 mt-2">Perfect for individuals</p>

    <div class="mt-8">
        <span class="text-4xl font-bold">₹0</span>
        <span class="text-slate-400 text-sm">/month</span>
    </div>

    <button disabled
        class="w-full mt-8 py-3 rounded-xl
        bg-slate-100 border border-slate-200
        text-slate-400 font-semibold">
        {{ $currentPlan === 'starter' ? 'Current Plan' : 'Included' }}
    </button>

    <ul class="mt-8 space-y-3 text-sm text-slate-600">
        <li>✔ Unlimited transactions</li>
        <li>✔ Basic reports</li>
        <li>✔ Category tracking</li>
        <li>✔ Secure cloud storage</li>
    </ul>
</div>


{{-- PRO --}}
<div class="card card-popular relative rounded-3xl p-12">

    <div class="absolute -top-4 left-1/2 -translate-x-1/2
        bg-slate-900 text-white text-xs font-semibold
        px-6 py-1 rounded-full">
        MOST POPULAR
    </div>

    <h3 class="text-xl font-bold text-slate-900">Pro Advisor</h3>
    <p class="text-sm text-slate-500 mt-2">
        AI-driven financial intelligence
    </p>

    <div class="mt-10">
        <span id="proPrice"
            data-monthly="199"
            data-yearly="1999"
            class="text-5xl font-bold">
            ₹199
        </span>
        <span id="billingText"
            class="text-slate-400 text-sm">/month</span>
    </div>

    @if($currentPlan !== 'pro')
    <form method="POST" action="{{ route('profile.subscription.upgrade') }}">
        @csrf
        <input type="hidden" name="plan" value="pro">
        <input type="hidden" name="billing" id="billingType" value="monthly">

        <button class="w-full mt-10 py-4 rounded-xl
            bg-slate-900 text-white font-semibold
            hover:bg-slate-800 transition">
            Upgrade to Pro
        </button>
    </form>
    @else
        <button disabled
            class="w-full mt-10 py-4 rounded-xl
            bg-emerald-100 text-emerald-700 font-semibold">
            Current Plan
        </button>
    @endif

    <ul class="mt-10 space-y-3 text-sm text-slate-700">
        <li>✔ Everything in Starter</li>
        <li>✔ AI overspending detection</li>
        <li>✔ Predictive savings insights</li>
        <li>✔ Monthly PDF exports</li>
        <li>✔ Priority email support</li>
    </ul>

</div>


{{-- PREMIUM --}}
<div class="card relative rounded-3xl p-10 opacity-90">

    <div class="absolute inset-0 flex items-center justify-center
        bg-white/70 backdrop-blur rounded-3xl">
        <span class="px-5 py-2 rounded-xl
            bg-slate-100 border border-slate-200
            text-slate-600 text-sm font-semibold">
            Coming Soon
        </span>
    </div>

    <h3 class="text-xl font-bold text-slate-900">Premium</h3>
    <p class="text-sm text-slate-500 mt-2">
        Wealth & investment analytics
    </p>

    <div class="mt-8">
        <span class="text-4xl font-bold">₹399</span>
        <span class="text-slate-400 text-sm">/month</span>
    </div>

</div>

</section>


{{-- TRUST SECTION --}}
<section class="mt-24 text-center max-w-2xl mx-auto">

    <h3 class="text-2xl font-bold text-slate-900">
        100% Secure & Encrypted
    </h3>

    <p class="text-slate-500 mt-3 text-sm">
        Bank-grade encryption. PCI compliant infrastructure.
        Your financial data is fully protected.
    </p>

</section>


{{-- FAQ --}}
<section class="mt-24 max-w-3xl mx-auto space-y-6">

    <h3 class="text-2xl font-bold text-center">
        Frequently Asked Questions
    </h3>

    <div class="space-y-4 text-sm text-slate-600">
        <div>
            <strong>Can I cancel anytime?</strong>
            <p>Yes. No contracts. Cancel anytime from dashboard.</p>
        </div>

        <div>
            <strong>Is my data secure?</strong>
            <p>Yes. We use encrypted storage & secure authentication.</p>
        </div>

        <div>
            <strong>Will pricing increase?</strong>
            <p>No. Your subscription price is locked.</p>
        </div>
    </div>

</section>

</div>


<script>
const monthly = document.getElementById('monthly');
const yearly = document.getElementById('yearly');
const indicator = document.getElementById('indicator');
const price = document.getElementById('proPrice');
const billingText = document.getElementById('billingText');
const billingType = document.getElementById('billingType');

monthly.onclick = () => {
    indicator.style.left = '4px';
    price.innerText = '₹' + price.dataset.monthly;
    billingText.innerText = '/month';
    billingType.value = 'monthly';
};

yearly.onclick = () => {
    indicator.style.left = '50%';
    price.innerText = '₹' + price.dataset.yearly;
    billingText.innerText = '/year';
    billingType.value = 'yearly';
};
</script>

@endsection
