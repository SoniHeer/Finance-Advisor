@extends('layouts.landing')

@section('content')

<!-- ================= HERO ================= -->
<section class="relative min-h-screen flex items-center overflow-hidden">

    <!-- Animated Background Mesh -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 animate-gradient"></div>

    <!-- Floating Blobs -->
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-indigo-300/30 blur-[160px] rounded-full animate-floatSlow"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-purple-300/30 blur-[160px] rounded-full animate-floatSlow"></div>

    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-24 items-center relative z-10">

        <!-- LEFT -->
        <div class="space-y-10 fade-up">

            <h1 class="text-6xl md:text-7xl font-extrabold leading-tight">
                Financial Intelligence
                <span class="block bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Elite Edition
                </span>
            </h1>

            <p class="text-xl text-slate-600 dark:text-slate-300 max-w-xl">
                Enterprise-grade financial analytics, AI insights,
                and collaborative budgeting for modern families.
            </p>

            <div class="flex gap-6">
                <a href="{{ route('register') }}" class="btn-primary">
                    Start Free
                </a>
                <a href="{{ route('login') }}"
                   class="px-6 py-3 border-2 border-indigo-600 rounded-xl font-semibold">
                    Login
                </a>
            </div>

        </div>

        <!-- RIGHT DASHBOARD PREVIEW -->
        <div class="relative fade-up">

            <div class="dashboard-glass parallax">
                <canvas id="eliteChart"></canvas>
            </div>

        </div>

    </div>
</section>

<!-- ================= KPI STRIP ================= -->
<section class="py-24 bg-white dark:bg-slate-900 text-center">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-12">

        <div class="kpi-card">
            <h3>Total Income</h3>
            <p class="counter" data-target="{{ $totalIncome ?? 0 }}">0</p>
        </div>

        <div class="kpi-card">
            <h3>Total Expense</h3>
            <p class="counter" data-target="{{ $totalExpense ?? 0 }}">0</p>
        </div>

        <div class="kpi-card">
            <h3>Net Balance</h3>
            <p class="counter" data-target="{{ $netBalance ?? 0 }}">0</p>
        </div>

    </div>
</section>

<!-- ================= PRICING ================= -->
<section id="pricing" class="py-28 bg-slate-50 dark:bg-slate-800 text-center">

    <div class="max-w-6xl mx-auto px-6">

        <h2 class="text-5xl font-bold mb-12">
            Elite Pricing
        </h2>

        <div class="inline-flex bg-white rounded-full p-2 shadow-md mb-16">
            <button id="monthlyBtn" class="toggle-btn active">Monthly</button>
            <button id="yearlyBtn" class="toggle-btn">Yearly</button>
        </div>

        <div class="grid md:grid-cols-3 gap-12">

            <div class="pricing-card">
                <h3>Starter</h3>
                <p class="price">Free</p>
                <p>Basic tracking features</p>
            </div>

            <div class="pricing-card highlight">
                <h3>Pro</h3>
                <p id="elitePrice" class="price">₹99/mo</p>
                <p>Full family + AI features</p>
            </div>

            <div class="pricing-card">
                <h3>Enterprise</h3>
                <p class="price">Custom</p>
                <p>Advanced analytics suite</p>
            </div>

        </div>

    </div>
</section>

<!-- ================= CTA ================= -->
<section class="py-32 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-center">

    <h2 class="text-5xl font-bold">
        Build Smarter Financial Systems Today
    </h2>

    <div class="mt-12">
        <a href="{{ route('register') }}"
           class="px-12 py-5 bg-white text-indigo-600 rounded-2xl font-bold shadow-2xl hover:scale-105 transition">
            Get Started Now
        </a>
    </div>

</section>

<!-- ================= FOOTER ================= -->
<footer class="bg-slate-900 text-slate-400 py-16">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-12">

        <div>
            <h3 class="text-white font-bold text-xl mb-4">FinanceAI</h3>
            <p>Elite financial intelligence platform.</p>
        </div>

        <div>
            <h4 class="text-white mb-3">Product</h4>
            <p>Features</p>
            <p>Pricing</p>
        </div>

        <div>
            <h4 class="text-white mb-3">Company</h4>
            <p>About</p>
            <p>Contact</p>
        </div>

        <div>
            <h4 class="text-white mb-3">Legal</h4>
            <p>Privacy</p>
            <p>Terms</p>
        </div>

    </div>

    <div class="text-center mt-12 text-sm">
        © {{ date('Y') }} FinanceAI. All rights reserved.
    </div>
</footer>


<style>
.animate-gradient{
    background-size:200% 200%;
    animation:gradientMove 10s ease infinite;
}
@keyframes gradientMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
@keyframes floatSlow{
    0%,100%{ transform:translateY(0); }
    50%{ transform:translateY(-20px); }
}
.animate-floatSlow{
    animation:floatSlow 14s ease-in-out infinite;
}
.dashboard-glass{
    background:rgba(255,255,255,.7);
    backdrop-filter: blur(20px);
    padding:40px;
    border-radius:32px;
    box-shadow:0 40px 120px rgba(0,0,0,.1);
}
.kpi-card{
    background:white;
    padding:2rem;
    border-radius:24px;
    box-shadow:0 25px 80px rgba(0,0,0,.06);
    transition:.4s;
}
.kpi-card:hover{
    transform:translateY(-8px);
}
.pricing-card{
    background:white;
    padding:3rem;
    border-radius:28px;
    box-shadow:0 25px 80px rgba(0,0,0,.05);
}
.highlight{
    border:2px solid #6366f1;
}
.price{
    font-size:2rem;
    font-weight:800;
    margin:12px 0;
}
.toggle-btn{
    padding:8px 20px;
    border-radius:30px;
}
.toggle-btn.active{
    background:#6366f1;
    color:white;
}
.fade-up{
    opacity:0;
    transform:translateY(40px);
    transition:.8s ease;
}
.fade-up.visible{
    opacity:1;
    transform:translateY(0);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// Chart
new Chart(document.getElementById('eliteChart'), {
    type:'line',
    data:{
        labels:['Jan','Feb','Mar','Apr','May','Jun'],
        datasets:[
            {label:'Income', data:[12000,15000,18000,21000,24000,26000], borderColor:'#6366f1', tension:.4},
            {label:'Expense', data:[8000,9000,11000,12000,14000,16000], borderColor:'#ec4899', tension:.4}
        ]
    }
});

// Counter animation
document.querySelectorAll('.counter').forEach(c=>{
    const target = parseInt(c.dataset.target || 0);
    let count=0;
    const step=Math.max(target/80,1);
    function update(){
        count+=step;
        if(count<target){
            c.innerText=Math.floor(count).toLocaleString();
            requestAnimationFrame(update);
        }else{
            c.innerText=target.toLocaleString();
        }
    }
    update();
});

// Pricing toggle
const m=document.getElementById('monthlyBtn');
const y=document.getElementById('yearlyBtn');
const p=document.getElementById('elitePrice');
m.onclick=()=>{m.classList.add('active');y.classList.remove('active');p.innerText="₹99/mo";}
y.onclick=()=>{y.classList.add('active');m.classList.remove('active');p.innerText="₹999/year";}

// Scroll reveal
const obs=new IntersectionObserver(e=>{
    e.forEach(x=>{if(x.isIntersecting)x.target.classList.add('visible');});
});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));

</script>
@include('partials.footer')

@endsection