@extends('layouts.landing')

@section('content')

<!-- ================= HERO ================= -->
<section class="relative py-32 overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-purple-50">

    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-indigo-200/40 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-purple-200/40 blur-3xl rounded-full"></div>

    <div class="relative max-w-5xl mx-auto px-6 text-center fade-up">

        <h1 class="text-5xl md:text-6xl font-extrabold text-slate-900">
            We Are Building the Future of
            <span class="block bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Financial Intelligence
            </span>
        </h1>

        <p class="mt-8 text-xl text-slate-600 leading-relaxed max-w-3xl mx-auto">
            FinanceAI empowers families and professionals with real-time insights,
            AI-driven forecasting, and structured financial growth systems.
        </p>

    </div>
</section>


<!-- ================= MISSION + VISION ================= -->
<section class="py-28 bg-white">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-20 items-center">

        <div class="fade-up">
            <h2 class="text-4xl font-bold text-slate-900">Our Mission</h2>
            <p class="mt-6 text-lg text-slate-600 leading-relaxed">
                To simplify complex financial systems using automation and AI,
                allowing users to focus on growth rather than tracking spreadsheets.
            </p>
        </div>

        <div class="fade-up">
            <h2 class="text-4xl font-bold text-slate-900">Our Vision</h2>
            <p class="mt-6 text-lg text-slate-600 leading-relaxed">
                A world where financial decisions are intelligent, predictive,
                and collaborative — powered by technology that understands behavior.
            </p>
        </div>

    </div>
</section>


<!-- ================= STATS ================= -->
<section class="py-24 bg-slate-50 text-center">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-16">

        <div class="stat-card fade-up">
            <h3 class="counter text-4xl font-extrabold text-indigo-600" data-target="100">0</h3>
            <p class="mt-2 text-slate-600">Secure Architecture</p>
        </div>

        <div class="stat-card fade-up">
            <h3 class="counter text-4xl font-extrabold text-indigo-600" data-target="24">0</h3>
            <p class="mt-2 text-slate-600">Hours Monitoring</p>
        </div>

        <div class="stat-card fade-up">
            <h3 class="counter text-4xl font-extrabold text-indigo-600" data-target="AI">AI</h3>
            <p class="mt-2 text-slate-600">Powered Insights</p>
        </div>

    </div>
</section>


<!-- ================= TEAM ================= -->
<section class="py-28 bg-white text-center">
    <div class="max-w-6xl mx-auto px-6">

        <h2 class="text-4xl font-bold text-slate-900 mb-16 fade-up">
            Leadership & Vision
        </h2>

        <div class="grid md:grid-cols-3 gap-12">

            <div class="team-card fade-up">
                <div class="avatar bg-indigo-600">F</div>
                <h3 class="mt-6 text-xl font-semibold">Founder</h3>
                <p class="text-slate-500 text-sm mt-2">
                    Product strategist & AI finance architect.
                </p>
            </div>

            <div class="team-card fade-up">
                <div class="avatar bg-purple-600">T</div>
                <h3 class="mt-6 text-xl font-semibold">Technology</h3>
                <p class="text-slate-500 text-sm mt-2">
                    Infrastructure & secure systems engineering.
                </p>
            </div>

            <div class="team-card fade-up">
                <div class="avatar bg-pink-600">G</div>
                <h3 class="mt-6 text-xl font-semibold">Growth</h3>
                <p class="text-slate-500 text-sm mt-2">
                    Scaling intelligent finance worldwide.
                </p>
            </div>

        </div>
    </div>
</section>


<!-- ================= ROADMAP ================= -->
<section class="py-28 bg-slate-50">
    <div class="max-w-4xl mx-auto px-6 text-center">

        <h2 class="text-4xl font-bold text-slate-900 mb-16 fade-up">
            Our Roadmap
        </h2>

        <div class="space-y-10 text-left">

            <div class="roadmap-item fade-up">
                <h3 class="font-semibold text-indigo-600">Phase 1</h3>
                <p class="text-slate-600">Core financial tracking system.</p>
            </div>

            <div class="roadmap-item fade-up">
                <h3 class="font-semibold text-indigo-600">Phase 2</h3>
                <p class="text-slate-600">AI behavior analytics & alerts.</p>
            </div>

            <div class="roadmap-item fade-up">
                <h3 class="font-semibold text-indigo-600">Phase 3</h3>
                <p class="text-slate-600">Predictive wealth forecasting.</p>
            </div>

        </div>

    </div>
</section>


<!-- ================= CTA ================= -->
<section class="py-32 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-center">

    <h2 class="text-5xl font-bold fade-up">
        Join the Financial Intelligence Revolution
    </h2>

    <div class="mt-12 fade-up">
        <a href="{{ route('register') }}"
           class="px-12 py-5 bg-white text-indigo-600 font-bold rounded-2xl shadow-xl hover:scale-105 transition">
            Start Free Today
        </a>
    </div>

</section>


<style>
.stat-card{
    background:white;
    padding:2.5rem;
    border-radius:24px;
    box-shadow:0 20px 60px rgba(0,0,0,.05);
    transition:.3s;
}
.stat-card:hover{ transform:translateY(-6px); }

.team-card{
    background:white;
    padding:3rem 2rem;
    border-radius:28px;
    box-shadow:0 20px 60px rgba(0,0,0,.05);
    transition:.3s;
}
.team-card:hover{ transform:translateY(-6px); }

.avatar{
    width:80px;
    height:80px;
    margin:0 auto;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-weight:700;
    font-size:24px;
}

.roadmap-item{
    background:white;
    padding:2rem;
    border-radius:20px;
    box-shadow:0 20px 60px rgba(0,0,0,.05);
}

.fade-up{
    opacity:0;
    transform:translateY(40px);
    transition:.7s ease;
}
.fade-up.visible{
    opacity:1;
    transform:translateY(0);
}
</style>


<script>
/* Counter animation */
document.querySelectorAll('.counter').forEach(c=>{
    const target = parseInt(c.dataset.target);
    if(!isNaN(target)){
        let count=0;
        const step=Math.max(target/60,1);
        function update(){
            count+=step;
            if(count<target){
                c.innerText=Math.floor(count);
                requestAnimationFrame(update);
            }else{
                c.innerText=target + "%";
            }
        }
        update();
    }
});

/* Scroll reveal */
const obs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{
        if(e.isIntersecting){
            e.target.classList.add('visible');
        }
    });
});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));
</script>

@endsection
