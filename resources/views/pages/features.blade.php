@extends('layouts.landing')

@section('content')

<!-- ================= HERO ================= -->
<section class="relative py-32 overflow-hidden">

    <!-- Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50"></div>

    <!-- Floating Glow -->
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-indigo-300/30 blur-[150px] rounded-full animate-floatSlow"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-purple-300/30 blur-[150px] rounded-full animate-floatSlow"></div>

    <div class="max-w-6xl mx-auto px-6 text-center fade-up">

        <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight leading-tight">
            Next-Gen Financial
            <span class="block bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Intelligence System
            </span>
        </h1>

        <p class="mt-6 text-lg text-slate-600 max-w-2xl mx-auto">
            Experience enterprise-grade analytics, automation and AI-driven financial clarity.
        </p>

    </div>

</section>


<!-- ================= FEATURE TABS ================= -->
<section class="py-24 bg-white">

    <div class="max-w-6xl mx-auto px-6">

        <div class="flex justify-center gap-6 mb-16">
            <button onclick="showTab('core')" class="tab-btn active">Core System</button>
            <button onclick="showTab('ai')" class="tab-btn">AI Engine</button>
            <button onclick="showTab('enterprise')" class="tab-btn">Enterprise</button>
        </div>

        <!-- CORE -->
        <div id="core" class="tab-content grid md:grid-cols-3 gap-12">

            <div class="ultra-card">
                <div class="ultra-icon">💰</div>
                <h3>Smart Income Tracking</h3>
                <p>Real-time tracking with structured monthly insights and forecasting.</p>
            </div>

            <div class="ultra-card">
                <div class="ultra-icon">📊</div>
                <h3>Advanced Expense Analytics</h3>
                <p>Category breakdown, charts, and financial reports.</p>
            </div>

            <div class="ultra-card">
                <div class="ultra-icon">👨‍👩‍👧</div>
                <h3>Family Budget Control</h3>
                <p>Collaborate, assign roles and manage shared financial goals.</p>
            </div>

        </div>

        <!-- AI -->
        <div id="ai" class="tab-content hidden grid md:grid-cols-3 gap-12">

            <div class="ultra-card">
                <div class="ultra-icon">🤖</div>
                <h3>AI Overspending Alerts</h3>
                <p>Instant intelligent warnings before you exceed limits.</p>
            </div>

            <div class="ultra-card">
                <div class="ultra-icon">📈</div>
                <h3>Predictive Balance Forecast</h3>
                <p>See your projected financial position ahead of time.</p>
            </div>

            <div class="ultra-card">
                <div class="ultra-icon">🧠</div>
                <h3>Smart Recommendations</h3>
                <p>AI-based spending optimization suggestions.</p>
            </div>

        </div>

        <!-- ENTERPRISE -->
        <div id="enterprise" class="tab-content hidden grid md:grid-cols-3 gap-12">

            <div class="ultra-card">
                <div class="ultra-icon">🔒</div>
                <h3>Role Management</h3>
                <p>Granular access control & admin monitoring.</p>
            </div>

            <div class="ultra-card">
                <div class="ultra-icon">📂</div>
                <h3>Export & Reporting</h3>
                <p>Download PDF and structured reports instantly.</p>
            </div>

            <div class="ultra-card">
                <div class="ultra-icon">⚡</div>
                <h3>High Performance</h3>
                <p>Optimized database queries & live dashboards.</p>
            </div>

        </div>

    </div>

</section>


<!-- ================= STATS ================= -->
<section class="py-24 bg-slate-50 text-center">

    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-16">

        <div>
            <h3 class="counter text-4xl font-extrabold text-indigo-600" data-target="99">0</h3>
            <p class="mt-2 text-slate-600">System Reliability %</p>
        </div>

        <div>
            <h3 class="counter text-4xl font-extrabold text-indigo-600" data-target="100">0</h3>
            <p class="mt-2 text-slate-600">AI Automation Score</p>
        </div>

        <div>
            <h3 class="counter text-4xl font-extrabold text-indigo-600" data-target="24">0</h3>
            <p class="mt-2 text-slate-600">Realtime Sync (hrs)</p>
        </div>

    </div>

</section>



<!-- ================= STYLES ================= -->
<style>
.ultra-card {
    background: #ffffff;
    padding: 2.5rem;
    border-radius: 28px;
    border: 1px solid rgba(15,23,42,.06);
    box-shadow: 0 30px 80px rgba(0,0,0,.05);
    transition: .4s ease;
}
.ultra-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 40px 120px rgba(99,102,241,.15);
}
.ultra-icon {
    font-size: 2rem;
}
.tab-btn {
    padding: 10px 22px;
    border-radius: 30px;
    background: #f1f5f9;
    font-weight: 600;
}
.tab-btn.active {
    background: #6366f1;
    color: white;
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
@keyframes floatSlow{
    0%,100%{ transform:translateY(0); }
    50%{ transform:translateY(-20px); }
}
.animate-floatSlow{
    animation:floatSlow 14s ease-in-out infinite;
}
</style>


<!-- ================= SCRIPTS ================= -->
<script>

/* TAB SWITCH */
function showTab(id){
    document.querySelectorAll('.tab-content').forEach(el=>el.classList.add('hidden'));
    document.getElementById(id).classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(btn=>btn.classList.remove('active'));
    event.target.classList.add('active');
}

/* COUNTER */
document.querySelectorAll('.counter').forEach(c=>{
    const target = parseInt(c.dataset.target);
    let count=0;
    const step=target/60;
    function update(){
        count+=step;
        if(count<target){
            c.innerText=Math.floor(count);
            requestAnimationFrame(update);
        }else{
            c.innerText=target;
        }
    }
    update();
});

/* SCROLL REVEAL */
const obs=new IntersectionObserver(e=>{
    e.forEach(x=>{if(x.isIntersecting)x.target.classList.add('visible');});
});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));

</script>

@endsection
