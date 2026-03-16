@extends('layouts.app')

@section('content')

@php
    $user = auth()->user();
    $currentPlan = $user->plan ?? 'starter';
    $currentBilling = $user->billing_cycle ?? 'monthly';
@endphp

<div class="max-w-7xl mx-auto px-6 py-24">

    {{-- HERO --}}
    <section class="text-center max-w-3xl mx-auto space-y-8">

        <span class="inline-block px-6 py-2 rounded-full
            bg-slate-100 border text-xs tracking-widest font-semibold text-slate-600">
            PRICING
        </span>

        <h1 class="text-5xl font-extrabold text-slate-900 leading-tight">
            Transparent pricing
            <span class="block text-slate-400">
                built for long-term growth
            </span>
        </h1>

        <p class="text-lg text-slate-500">
            Cancel anytime. Secure billing. No surprises.
        </p>

        {{-- BILLING TOGGLE --}}
        <div class="relative inline-flex bg-slate-100 border rounded-full p-1">

            <div id="indicator"
                class="absolute top-1 bottom-1 w-1/2 bg-white rounded-full shadow transition-all duration-300">
            </div>

            <button type="button"
                id="monthlyBtn"
                class="relative z-10 px-8 py-2 text-sm font-semibold">
                Monthly
            </button>

            <button type="button"
                id="yearlyBtn"
                class="relative z-10 px-8 py-2 text-sm font-semibold text-slate-500">
                Yearly
                <span class="ml-1 text-emerald-600 text-xs">Save 20%</span>
            </button>
        </div>

    </section>

    {{-- PLANS --}}
    <section class="mt-20 grid md:grid-cols-3 gap-10">

        {{-- STARTER --}}
        <div class="bg-white border rounded-3xl p-10 shadow-sm">

            <h3 class="text-xl font-bold">Starter</h3>
            <p class="text-sm text-slate-500 mt-2">
                Perfect for personal finance tracking
            </p>

            <div class="mt-8">
                <span class="text-4xl font-bold">₹0</span>
                <span class="text-slate-400 text-sm">/month</span>
            </div>

            @if($currentPlan === 'starter')
                <button disabled
                    class="w-full mt-8 py-3 rounded-xl
                    bg-slate-100 border text-slate-400 font-semibold">
                    Current Plan
                </button>
            @else
                <form method="POST" action="{{ route('user.profile.subscription') }}">
                    @csrf
                    <input type="hidden" name="plan" value="starter">
                    <button class="w-full mt-8 py-3 rounded-xl
                        bg-slate-900 text-white font-semibold">
                        Switch to Starter
                    </button>
                </form>
            @endif

            <ul class="mt-8 space-y-2 text-sm text-slate-600">
                <li>✔ Unlimited transactions</li>
                <li>✔ Basic analytics</li>
                <li>✔ Category tracking</li>
                <li>✔ Secure cloud sync</li>
            </ul>

        </div>

        {{-- PRO --}}
        <div class="relative bg-white rounded-3xl p-12
            border shadow-lg scale-[1.03]">

            <div class="absolute -top-4 left-1/2 -translate-x-1/2
                bg-slate-900 text-white text-xs px-6 py-1 rounded-full">
                MOST POPULAR
            </div>

            <h3 class="text-xl font-bold">Pro Advisor</h3>
            <p class="text-sm text-slate-500 mt-2">
                AI-powered financial intelligence
            </p>

            <div class="mt-10">
                <span id="proPrice"
                      data-monthly="199"
                      data-yearly="1999"
                      class="text-5xl font-bold">
                    ₹{{ $currentBilling === 'yearly' ? '1999' : '199' }}
                </span>
                <span id="billingText"
                      class="text-slate-400 text-sm">
                    /{{ $currentBilling === 'yearly' ? 'year' : 'month' }}
                </span>
            </div>

            @if($currentPlan === 'pro')
                <button disabled
                    class="w-full mt-10 py-4 rounded-xl
                    bg-emerald-100 text-emerald-700 font-semibold">
                    Current Plan
                </button>
            @else
                <form method="POST" action="{{ route('user.profile.subscription') }}">
                    @csrf
                    <input type="hidden" name="plan" value="pro">
                    <input type="hidden" name="billing" id="billingCycle" value="{{ $currentBilling }}">

                    <button class="w-full mt-10 py-4 rounded-xl
                        bg-slate-900 text-white font-semibold hover:opacity-90 transition">
                        Upgrade to Pro
                    </button>
                </form>
            @endif

            <ul class="mt-10 space-y-2 text-sm text-slate-700">
                <li>✔ Everything in Starter</li>
                <li>✔ AI insights</li>
                <li>✔ Overspending detection</li>
                <li>✔ PDF reports</li>
                <li>✔ Priority support</li>
            </ul>

        </div>

        {{-- PREMIUM --}}
        <div class="relative bg-white border rounded-3xl p-10 opacity-80">

            <div class="absolute inset-0 flex items-center justify-center
                bg-white/70 backdrop-blur rounded-3xl">
                <span class="px-5 py-2 bg-slate-100 border rounded-xl
                    text-slate-600 text-sm font-semibold">
                    Coming Soon
                </span>
            </div>

            <h3 class="text-xl font-bold">Premium</h3>
            <p class="text-sm text-slate-500 mt-2">
                Wealth forecasting & investment AI
            </p>

            <div class="mt-8">
                <span class="text-4xl font-bold">₹399</span>
                <span class="text-slate-400 text-sm">/month</span>
            </div>

        </div>

    </section>

</div>

{{-- JS --}}
<script>
const monthlyBtn = document.getElementById('monthlyBtn');
const yearlyBtn = document.getElementById('yearlyBtn');
const indicator = document.getElementById('indicator');
const price = document.getElementById('proPrice');
const billingText = document.getElementById('billingText');
const billingCycle = document.getElementById('billingCycle');

function setMonthly(){
    indicator.style.left = "4px";
    price.textContent = "₹" + price.dataset.monthly;
    billingText.textContent = "/month";
    if (billingCycle) billingCycle.value = "monthly";
}

function setYearly(){
    indicator.style.left = "calc(50% - 4px)";
    price.textContent = "₹" + price.dataset.yearly;
    billingText.textContent = "/year";
    if (billingCycle) billingCycle.value = "yearly";
}

monthlyBtn?.addEventListener("click", setMonthly);
yearlyBtn?.addEventListener("click", setYearly);

// Initialize based on user billing
@if($currentBilling === 'yearly')
    setYearly();
@else
    setMonthly();
@endif
</script>

@endsection
