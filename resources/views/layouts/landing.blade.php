<!DOCTYPE html>
<html lang="en" id="htmlRoot" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Dynamic Title --}}
    <title>@yield('title', config('app.name','FinanceAI'))</title>

    {{-- SEO --}}
    <meta name="description" content="FinanceAI - Enterprise-grade financial intelligence platform.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Prevent Dark Mode Flicker --}}
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CDN (Temporary — Replace with Vite in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: '#4f46e5'
                    }
                }
            }
        }
    </script>

    {{-- Custom Styles --}}
    <style>
        body { font-family: 'Inter', sans-serif; }

        .glass-nav {
            backdrop-filter: blur(18px);
            background: rgba(255,255,255,.85);
            border-bottom: 1px solid rgba(15,23,42,.06);
            transition: all .3s ease;
        }

        .dark .glass-nav {
            background: rgba(15,23,42,.85);
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        .nav-scrolled {
            box-shadow: 0 15px 40px rgba(15,23,42,.08);
        }

        .btn-primary {
            background: linear-gradient(to right,#6366f1,#8b5cf6);
            color: white;
            padding: 10px 22px;
            border-radius: 12px;
            font-weight: 600;
            transition: .3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99,102,241,.35);
        }

        .btn-outline {
            border: 1px solid rgba(15,23,42,.15);
            padding: 9px 18px;
            border-radius: 12px;
            font-weight: 500;
            transition: .3s ease;
        }

        .btn-outline:hover {
            background: rgba(15,23,42,.05);
        }
    </style>

    {{-- Page Specific Styles --}}
    @stack('styles')
</head>

<body class="bg-white text-slate-900 dark:bg-slate-950 dark:text-white transition-all duration-300">

{{-- ================= NAVBAR ================= --}}
<nav id="navbar" class="glass-nav fixed top-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        {{-- Logo --}}
        <a href="{{ Route::has('home') ? route('home') : '/' }}"
           class="text-2xl font-extrabold tracking-tight text-indigo-600">
            FinanceAI
        </a>

        {{-- Desktop Menu --}}
        <div class="hidden md:flex items-center gap-8 text-sm font-medium">

            @php $current = Route::currentRouteName(); @endphp

            @if(Route::has('features'))
                <a href="{{ route('features') }}"
                   class="{{ $current=='features' ? 'text-indigo-600 font-semibold' : 'hover:text-indigo-600' }}">
                    Features
                </a>
            @endif

            @if(Route::has('pricing'))
                <a href="{{ route('pricing') }}"
                   class="{{ $current=='pricing' ? 'text-indigo-600 font-semibold' : 'hover:text-indigo-600' }}">
                    Pricing
                </a>
            @endif

            @if(Route::has('about'))
                <a href="{{ route('about') }}"
                   class="{{ $current=='about' ? 'text-indigo-600 font-semibold' : 'hover:text-indigo-600' }}">
                    About
                </a>
            @endif

            {{-- Dark Mode --}}
            <button onclick="toggleDark()"
                class="px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                🌙
            </button>

            {{-- Auth Links --}}
            @auth
                <a href="{{ Route::has('user.dashboard') ? route('user.dashboard') : '#' }}"
                   class="btn-outline">
                    Dashboard
                </a>
            @else
                @if(Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-outline">Login</a>
                @endif
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
                @endif
            @endauth

        </div>

        {{-- Mobile Toggle --}}
        <button onclick="toggleMenu()" class="md:hidden text-2xl">☰</button>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobileMenu"
         class="hidden md:hidden px-6 pb-6 bg-white dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 transition-all duration-300">
        <div class="flex flex-col gap-4 text-sm font-medium pt-4">

            @if(Route::has('features'))
                <a href="{{ route('features') }}">Features</a>
            @endif
            @if(Route::has('pricing'))
                <a href="{{ route('pricing') }}">Pricing</a>
            @endif
            @if(Route::has('about'))
                <a href="{{ route('about') }}">About</a>
            @endif

            @guest
                @if(Route::has('login'))
                    <a href="{{ route('login') }}">Login</a>
                @endif
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary text-center">Get Started</a>
                @endif
            @else
                <a href="{{ Route::has('user.dashboard') ? route('user.dashboard') : '#' }}">
                    Dashboard
                </a>
            @endguest

        </div>
    </div>
</nav>


{{-- ================= PAGE CONTENT ================= --}}
<main class="pt-28">
    @yield('content')
</main>


{{-- ================= SCRIPTS ================= --}}
<script>
function toggleDark() {
    const html = document.documentElement;
    html.classList.toggle('dark');
    localStorage.setItem('theme',
        html.classList.contains('dark') ? 'dark' : 'light');
}

function toggleMenu() {
    document.getElementById('mobileMenu').classList.toggle('hidden');
}

window.addEventListener('scroll', function() {
    const nav = document.getElementById('navbar');
    nav.classList.toggle('nav-scrolled', window.scrollY > 10);
});
</script>

{{-- Page Specific Scripts --}}
@stack('scripts')

</body>
</html>
