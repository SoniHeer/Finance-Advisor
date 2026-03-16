@extends('layouts.landing')

@section('content')

<section class="min-h-screen grid lg:grid-cols-2 relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-purple-50">

    <!-- Animated Ambient Blobs -->
    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-indigo-300/30 rounded-full blur-[160px] animate-float"></div>
    <div class="absolute -bottom-40 -right-40 w-[600px] h-[600px] bg-purple-300/30 rounded-full blur-[160px] animate-float delay-2000"></div>

    <!-- LEFT PANEL -->
    <div class="hidden lg:flex flex-col justify-center px-20 relative z-10">

        <div class="space-y-8 max-w-md">

            <h1 class="text-5xl font-extrabold text-slate-900 leading-tight">
                Welcome back to
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
                    FinanceAI
                </span>
            </h1>

            <p class="text-slate-600 text-lg leading-relaxed">
                Access your intelligent financial system and
                take control of your structured growth.
            </p>

            <div class="space-y-4 text-sm text-slate-600">
                <div class="flex items-center gap-3">
                    <span class="text-indigo-600">✓</span>
                    Real-time analytics
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-indigo-600">✓</span>
                    AI-driven insights
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-indigo-600">✓</span>
                    Bank-grade encryption
                </div>
            </div>

        </div>

    </div>

    <!-- RIGHT LOGIN CARD -->
    <div class="flex items-center justify-center px-6 py-16 relative z-10">

        <div class="w-full max-w-md backdrop-blur-xl bg-white/80 border border-white/40 rounded-3xl shadow-[0_50px_120px_rgba(15,23,42,.15)] p-10 animate-fadeIn">

            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-slate-900">
                    Sign In
                </h2>
                <p class="text-slate-500 mt-2">
                    Access your financial dashboard
                </p>
            </div>

            <!-- ERROR BLOCK -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6" onsubmit="showLoading()">
                @csrf

                <!-- EMAIL -->
                <div class="relative">
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           placeholder="Email Address"
                           class="input-style @error('email') border-red-500 @enderror">
                </div>

                <!-- PASSWORD -->
                <div class="relative">
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           placeholder="Password"
                           class="input-style pr-12 @error('password') border-red-500 @enderror">

                    <button type="button"
                            onclick="togglePassword()"
                            class="absolute right-4 top-3 text-slate-400 hover:text-indigo-600 transition">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <!-- OPTIONS -->
                <div class="flex justify-between items-center text-sm">

                    <!-- Toggle Switch -->
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="remember" class="hidden peer">
                        <div class="w-10 h-5 bg-slate-300 rounded-full relative peer-checked:bg-indigo-600 transition">
                            <div class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full transition peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-slate-600">Remember</span>
                    </label>

                    <a href="#" class="text-indigo-600 hover:underline">
                        Forgot password?
                    </a>

                </div>

                <!-- BUTTON -->
                <button id="loginBtn"
                        type="submit"
                        class="w-full py-3 rounded-xl
                               bg-gradient-to-r from-indigo-600 to-purple-600
                               text-white font-semibold
                               hover:scale-[1.02]
                               transition duration-200 shadow-lg flex items-center justify-center gap-2">

                    <span id="btnText">Sign In</span>

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

                <!-- Divider -->
                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 h-px bg-slate-200"></div>
                    <span class="text-xs text-slate-400">OR</span>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>

                <!-- Social Buttons (UI Ready) -->
                <button type="button"
                        class="w-full py-3 rounded-xl border border-slate-300 hover:bg-slate-50 transition font-medium flex items-center justify-center gap-3">
                    <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 h-5">
                    Continue with Google
                </button>

                <p class="text-center text-sm text-slate-500 mt-6">
                    Don’t have an account?
                    <a href="{{ route('register') }}"
                       class="text-indigo-600 font-semibold hover:underline">
                        Create one
                    </a>
                </p>

            </form>

            <!-- TRUST -->
            <div class="mt-8 text-center text-xs text-slate-400">
                Secured with 256-bit SSL Encryption
            </div>

        </div>

    </div>

</section>

<style>
.input-style{
    width:100%;
    padding:14px 16px;
    border-radius:14px;
    border:1px solid #e2e8f0;
    transition:.3s;
}
.input-style:focus{
    outline:none;
    border-color:#6366f1;
    box-shadow:0 0 0 3px rgba(99,102,241,.2);
}
@keyframes float {
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-20px)}
}
.animate-float{
    animation:float 14s ease-in-out infinite;
}
@keyframes fadeIn {
    from{opacity:0; transform:translateY(20px)}
    to{opacity:1; transform:translateY(0)}
}
.animate-fadeIn{
    animation:fadeIn .6s ease forwards;
}
</style>

<script>
function togglePassword(){
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
}

function showLoading(){
    document.getElementById('btnText').innerText = "Signing in...";
    document.getElementById('loader').classList.remove('hidden');
    document.getElementById('loginBtn').classList.add('opacity-80');
}
</script>

@endsection
