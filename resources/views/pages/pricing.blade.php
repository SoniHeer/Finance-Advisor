@extends('layouts.landing')

@section('content')

<!-- ================= HERO ================= -->
<section class="relative py-32 text-center overflow-hidden">

    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 animate-gradient"></div>

    <div class="max-w-4xl mx-auto px-6 fade-up">

        <h1 class="text-6xl font-extrabold tracking-tight">
            Elite Pricing
            <span class="block bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Built for Financial Intelligence
            </span>
        </h1>

        <p class="mt-6 text-lg text-slate-600">
            Flexible plans designed for individuals, families, and enterprises.
        </p>

        <!-- Toggle -->
        <div class="mt-12 relative inline-flex bg-white p-2 rounded-full shadow-lg border">
            <div id="toggleIndicator"
                 class="absolute left-1 top-1 h-[calc(100%-8px)] w-1/2 bg-indigo-600 rounded-full transition-all duration-300">
            </div>

            <button id="monthlyBtn"
                    class="relative z-10 px-8 py-2 font-semibold text-white">
                Monthly
            </button>

            <button id="yearlyBtn"
                    class="relative z-10 px-8 py-2 font-semibold text-slate-700">
                Yearly <span class="text-xs ml-1">Save 20%</span>
            </button>
        </div>

    </div>
</section>



<!-- ================= PRICING CARDS ================= -->
<section class="pb-32">

    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-12">

        <!-- STARTER -->
        <div class="price-card fade-up">
            <h3 class="plan-title">Starter</h3>
            <p class="plan-desc">For individuals</p>

            <h2 class="plan-price">Free</h2>

            <ul class="plan-list">
                <li>✔ Unlimited Transactions</li>
                <li>✔ Expense Tracking</li>
                <li>✔ Basic Reports</li>
            </ul>

            <a href="{{ route('register') }}" class="btn-outline mt-8 block text-center">
                Get Started
            </a>
        </div>



        <!-- PRO -->
        <div class="price-card featured fade-up">

            <div class="badge">MOST POPULAR</div>

            <h3 class="plan-title">Pro Advisor</h3>
            <p class="plan-desc">AI-powered system</p>

            <h2 id="proPrice" class="plan-price text-indigo-600 price-animate">
                ₹199
            </h2>

            <p class="text-slate-500 text-sm">per month</p>

            <ul class="plan-list">
                <li>✔ Everything in Starter</li>
                <li>✔ AI Overspending Alerts</li>
                <li>✔ Family Sharing</li>
                <li>✔ Advanced Charts</li>
                <li>✔ PDF Reports</li>
            </ul>

            <a href="{{ route('register') }}" class="btn-primary mt-8 block text-center">
                Upgrade to Pro
            </a>
        </div>



        <!-- ENTERPRISE -->
        <div class="price-card fade-up">
            <h3 class="plan-title">Enterprise</h3>
            <p class="plan-desc">Custom deployment</p>

            <h2 class="plan-price">Custom</h2>

            <ul class="plan-list">
                <li>✔ Everything in Pro</li>
                <li>✔ Role Permissions</li>
                <li>✔ Admin Controls</li>
                <li>✔ Dedicated Support</li>
            </ul>

            <a href="{{ route('contact') }}" class="btn-outline mt-8 block text-center">
                Contact Sales
            </a>
        </div>

    </div>

</section>



<!-- ================= FEATURE COMPARISON ================= -->
<section class="py-24 bg-slate-50">
    <div class="max-w-6xl mx-auto px-6">

        <h2 class="text-4xl font-bold text-center mb-16">
            Compare Plans
        </h2>

        <div class="overflow-x-auto bg-white rounded-3xl shadow-xl">
            <table class="w-full text-left">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-6">Features</th>
                        <th class="p-6">Starter</th>
                        <th class="p-6">Pro</th>
                        <th class="p-6">Enterprise</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="p-6">Unlimited Transactions</td>
                        <td class="p-6">✔</td>
                        <td class="p-6">✔</td>
                        <td class="p-6">✔</td>
                    </tr>
                    <tr class="border-t">
                        <td class="p-6">AI Alerts</td>
                        <td class="p-6">—</td>
                        <td class="p-6">✔</td>
                        <td class="p-6">✔</td>
                    </tr>
                    <tr class="border-t">
                        <td class="p-6">Admin Controls</td>
                        <td class="p-6">—</td>
                        <td class="p-6">—</td>
                        <td class="p-6">✔</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</section>



<!-- ================= FAQ ================= -->
<section class="py-24">

    <div class="max-w-4xl mx-auto px-6">

        <h2 class="text-4xl font-bold text-center mb-16">
            Frequently Asked Questions
        </h2>

        <div class="space-y-6">

            <div class="faq-item">
                <button onclick="toggleFaq(this)">Can I cancel anytime?</button>
                <div class="faq-content">Yes. No hidden charges.</div>
            </div>

            <div class="faq-item">
                <button onclick="toggleFaq(this)">Is my data secure?</button>
                <div class="faq-content">We use bank-grade encryption.</div>
            </div>

        </div>

    </div>

</section>



<!-- ================= STYLES ================= -->
<style>

.animate-gradient {
    background-size: 200% 200%;
    animation: gradientMove 10s ease infinite;
}
@keyframes gradientMove {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

.price-card {
    background: white;
    padding: 3rem;
    border-radius: 32px;
    border: 1px solid rgba(15,23,42,.06);
    box-shadow: 0 40px 120px rgba(0,0,0,.05);
    transition: .4s ease;
}
.price-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 60px 160px rgba(99,102,241,.18);
}
.featured {
    border: 2px solid #6366f1;
    position: relative;
}
.badge {
    position: absolute;
    top: -16px;
    left: 50%;
    transform: translateX(-50%);
    background: #6366f1;
    color: white;
    padding: 6px 18px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: bold;
}
.plan-title {
    font-size: 1.6rem;
    font-weight: 700;
}
.plan-desc {
    color: #64748b;
}
.plan-price {
    font-size: 2.8rem;
    font-weight: 800;
    margin: 20px 0;
}
.price-animate {
    transition: .3s ease;
}
.btn-primary {
    background: linear-gradient(to right,#6366f1,#8b5cf6);
    color: white;
    padding: 14px;
    border-radius: 14px;
}
.btn-outline {
    border: 1px solid rgba(15,23,42,.15);
    padding: 14px;
    border-radius: 14px;
}
.faq-item button {
    width: 100%;
    padding: 16px;
    text-align: left;
    font-weight: 600;
    background: white;
    border-radius: 16px;
}
.faq-content {
    display: none;
    padding: 16px;
    color: #64748b;
}
.fade-up {
    opacity: 0;
    transform: translateY(40px);
    transition: .8s ease;
}
.fade-up.visible {
    opacity: 1;
    transform: translateY(0);
}

</style>



<!-- ================= SCRIPTS ================= -->
<script>

const m = document.getElementById('monthlyBtn');
const y = document.getElementById('yearlyBtn');
const p = document.getElementById('proPrice');
const indicator = document.getElementById('toggleIndicator');

m.onclick = () => {
    indicator.style.left = '4px';
    p.innerText = "₹199";
};

y.onclick = () => {
    indicator.style.left = '50%';
    p.innerText = "₹1999";
};

function toggleFaq(btn) {
    const content = btn.nextElementSibling;
    content.style.display =
        content.style.display === "block" ? "none" : "block";
}

const obs = new IntersectionObserver(e=>{
    e.forEach(x=>{
        if(x.isIntersecting) x.target.classList.add('visible');
    });
});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));

</script>

@endsection
