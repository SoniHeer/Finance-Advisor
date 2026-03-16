@extends('layouts.landing')

@section('content')

<section class="min-h-screen relative flex items-center justify-center overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-purple-50">

    <!-- Ambient Animated Background -->
    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-indigo-300/30 rounded-full blur-[160px] animate-float"></div>
    <div class="absolute -bottom-40 -right-40 w-[600px] h-[600px] bg-purple-300/30 rounded-full blur-[160px] animate-float delay-2000"></div>

    <div class="relative z-10 grid lg:grid-cols-2 max-w-6xl w-full shadow-[0_60px_150px_rgba(15,23,42,.18)] rounded-3xl overflow-hidden bg-white">

        <!-- LEFT SIDE -->
        <div class="hidden lg:flex flex-col justify-center px-16 bg-gradient-to-br from-indigo-600 to-purple-600 text-white relative">

            <h2 class="text-5xl font-extrabold leading-tight">
                Unlock Financial
                <span class="block text-indigo-200">Intelligence</span>
            </h2>

            <p class="mt-6 text-indigo-100 text-lg">
                FinanceAI helps you track, optimize and grow
                your financial system with AI precision.
            </p>

            <div class="mt-10 space-y-4 text-sm text-indigo-100">
                <div>✓ Bank-level encryption</div>
                <div>✓ AI-powered insights</div>
                <div>✓ Smart budgeting system</div>
                <div>✓ Real-time analytics</div>
            </div>

        </div>

        <!-- RIGHT FORM -->
        <div class="flex items-center justify-center px-8 py-16">

            <div class="w-full max-w-md animate-fadeIn">

                <h3 class="text-3xl font-bold text-slate-900 text-center">
                    Create Account
                </h3>

                <p class="text-center text-slate-500 mt-2">
                    Start your journey today
                </p>

                <form method="POST"
                      action="{{ route('register.store') }}"
                      class="mt-10 space-y-6"
                      onsubmit="handleSubmit()">
                    @csrf

                    <!-- NAME -->
                    <div>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Full Name"
                               required
                               class="input-style">
                    </div>

                    <!-- EMAIL -->
                    <div>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Email Address"
                               required
                               class="input-style">
                    </div>

                    <!-- PASSWORD -->
                    <div class="relative">
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Password"
                               required
                               onkeyup="updateStrength()"
                               class="input-style pr-12">

                        <button type="button"
                                onclick="togglePassword()"
                                class="absolute right-4 top-3 text-slate-400 hover:text-indigo-600">
                            👁
                        </button>

                        <!-- Strength Bar -->
                        <div class="h-2 bg-slate-200 rounded-full mt-3">
                            <div id="strengthBar"
                                 class="h-2 rounded-full transition-all duration-300"></div>
                        </div>
                    </div>

                    <!-- CONFIRM -->
                    <div>
                        <input type="password"
                               name="password_confirmation"
                               placeholder="Confirm Password"
                               required
                               class="input-style">
                    </div>

                    <!-- TERMS -->
                    <div class="flex items-start gap-3 text-sm text-slate-600">
                        <input type="checkbox" required class="accent-indigo-600 mt-1">
                        <span>
                            I agree to the
                            <a href="{{ route('terms') }}" class="text-indigo-600 hover:underline">Terms</a>
                            &
                            <a href="{{ route('privacy') }}" class="text-indigo-600 hover:underline">Privacy Policy</a>
                        </span>
                    </div>

                    <!-- BUTTON -->
                    <button id="submitBtn"
                            type="submit"
                            class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold hover:scale-[1.02] transition shadow-lg flex items-center justify-center gap-2">

                        <span id="btnText">Create Account</span>

                        <svg id="loader" class="hidden w-5 h-5 animate-spin"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25"
                                    cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75"
                                  fill="currentColor"
                                  d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>

                    </button>

                    <!-- LOGIN -->
                    <p class="text-center text-sm text-slate-500 mt-6">
                        Already have an account?
                        <a href="{{ route('login') }}"
                           class="text-indigo-600 font-semibold hover:underline">
                            Sign In
                        </a>
                    </p>

                </form>

                <!-- TRUST BADGES -->
                <div class="mt-8 text-center text-xs text-slate-400">
                    Secured with 256-bit SSL encryption
                </div>

            </div>

        </div>

    </div>

</section>

<style>
.input-style {
    width:100%;
    padding:14px 16px;
    border-radius:14px;
    border:1px solid #e2e8f0;
    transition:.3s;
}
.input-style:focus {
    outline:none;
    border-color:#6366f1;
    box-shadow:0 0 0 3px rgba(99,102,241,.2);
}
@keyframes float {
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-20px)}
}
.animate-float {
    animation:float 12s ease-in-out infinite;
}
@keyframes fadeIn {
    from {opacity:0; transform:translateY(20px)}
    to {opacity:1; transform:translateY(0)}
}
.animate-fadeIn {
    animation:fadeIn .6s ease forwards;
}
</style>

<script>
function togglePassword(){
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
}

function updateStrength(){
    const val = document.getElementById('password').value;
    const bar = document.getElementById('strengthBar');

    let strength = 0;
    if(val.length > 6) strength += 1;
    if(val.match(/[A-Z]/)) strength += 1;
    if(val.match(/[0-9]/)) strength += 1;
    if(val.match(/[^A-Za-z0-9]/)) strength += 1;

    const width = strength * 25;

    bar.style.width = width + "%";

    if(strength <= 1){
        bar.style.background = "#ef4444";
    } else if(strength == 2){
        bar.style.background = "#f59e0b";
    } else if(strength == 3){
        bar.style.background = "#10b981";
    } else {
        bar.style.background = "#6366f1";
    }
}

function handleSubmit(){
    document.getElementById('btnText').innerText = "Creating...";
    document.getElementById('loader').classList.remove('hidden');
    document.getElementById('submitBtn').classList.add('opacity-80');
}
</script>

@endsection
