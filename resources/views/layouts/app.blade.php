<!DOCTYPE html>
<html lang="en"
      x-data="layout()"
      x-init="init()"
      :class="{ 'dark': dark }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'FinanceAI Enterprise')</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: '#6366f1'
                    },
                    boxShadow: {
                        soft: '0 20px 60px rgba(15,23,42,.08)',
                        ultra: '0 30px 100px rgba(15,23,42,.12)'
                    }
                }
            }
        }
    </script>

    {{-- Chart.js (LIVE DATA ENGINE) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Alpine --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- FontAwesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @stack('styles')

    <style>
        html { scroll-behavior: smooth; }

        body {
            background: #f8fafc;
            transition: background .3s ease;
        }

        .dark body {
            background: #0f172a;
        }

        .content-wrapper {
            background: white;
            border-radius: 28px;
            box-shadow: 0 30px 120px rgba(15,23,42,.08);
            transition: background .3s ease;
        }

        .dark .content-wrapper {
            background: #1e293b;
        }

        .scroll-container {
            height: calc(100vh - 80px);
            overflow-y: auto;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }
    </style>
</head>

<body class="text-slate-800 dark:text-slate-200 antialiased">

{{-- NAVBAR --}}
@include('partials.navbar')

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside
        class="fixed lg:static
               inset-y-0 left-0
               w-72
               bg-white dark:bg-slate-900
               border-r border-slate-200 dark:border-slate-800
               transform transition-all duration-300 z-40"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        @include('partials.sidebar')

    </aside>

    {{-- Overlay --}}
    <div class="fixed inset-0 bg-black/40 z-30 lg:hidden"
         x-show="sidebarOpen"
         x-transition.opacity
         @click="sidebarOpen = false">
    </div>

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col">

        <main class="flex-1 p-6 md:p-12 scroll-container">

            <div class="max-w-[1700px] mx-auto">

                <div class="content-wrapper p-8 md:p-14">

                    @yield('content')

                </div>

            </div>

        </main>

    </div>

</div>

{{-- ENTERPRISE CORE SCRIPT --}}
<script>
function layout() {
    return {
        dark: false,
        sidebarOpen: false,

        init() {
            this.dark = localStorage.getItem('dark') === 'true'
                || window.matchMedia('(prefers-color-scheme: dark)').matches;

            this.$watch('dark', value => {
                localStorage.setItem('dark', value);
            });
        }
    }
}
</script>

@stack('scripts')

</body>
</html>
