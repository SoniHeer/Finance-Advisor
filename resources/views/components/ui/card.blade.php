@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'value' => null,
    'trend' => null,          // +12% or -8%
    'padding' => 'p-6',
    'variant' => 'default',   // default | soft | elevated | gradient | glass
    'accent' => null,
    'hover' => true,
    'loading' => false,
])

@php

$variants = [
    'default'  => 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm',
    'soft'     => 'bg-slate-50 dark:bg-slate-800/50 border border-transparent',
    'elevated' => 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl',
    'gradient' => 'bg-gradient-to-br from-white to-slate-100 dark:from-slate-900 dark:to-slate-800 border border-slate-200 dark:border-slate-800 shadow-lg',
    'glass'    => 'bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/20 dark:border-white/10 shadow-2xl',
];

$base = $variants[$variant] ?? $variants['default'];

$accents = [
    'blue'   => 'border-l-4 border-blue-600',
    'green'  => 'border-l-4 border-emerald-600',
    'red'    => 'border-l-4 border-rose-600',
    'purple' => 'border-l-4 border-purple-600',
    'amber'  => 'border-l-4 border-amber-500',
];

$accentClass = $accent && isset($accents[$accent]) ? $accents[$accent] : '';

$hoverClass = $hover
    ? 'transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl'
    : '';

$trendColor = null;
if ($trend) {
    $trendColor = str_contains($trend, '-') ? 'text-rose-600' : 'text-emerald-600';
}

@endphp

<div {{ $attributes->merge([
        'class' => "relative rounded-2xl overflow-hidden $base $accentClass $hoverClass $padding"
    ]) }}>

    {{-- Loading Overlay --}}
    @if($loading)
        <div class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 z-20 flex items-center justify-center">
            <div class="h-6 w-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>
    @endif

    {{-- Header --}}
    @if($title || isset($actions))
        <div class="flex items-start justify-between mb-5">

            <div class="flex items-center gap-3">

                @if($icon)
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                        <i class="fa-solid {{ $icon }} text-blue-600 dark:text-blue-400"></i>
                    </div>
                @endif

                <div>
                    @if($title)
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            {{ $title }}
                        </h3>
                    @endif

                    @if($subtitle)
                        <p class="text-xs text-slate-400 mt-1">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>

            </div>

            @isset($actions)
                <div>
                    {{ $actions }}
                </div>
            @endisset

        </div>
    @endif

    {{-- KPI Value --}}
    @if($value)
        <div class="flex items-end justify-between">

            <div class="text-3xl font-bold text-slate-900 dark:text-white">
                {{ $value }}
            </div>

            @if($trend)
                <div class="text-sm font-medium {{ $trendColor }}">
                    {{ $trend }}
                </div>
            @endif

        </div>
    @endif

    {{-- Slot Content --}}
    @if(!$value)
        <div>
            {{ $slot }}
        </div>
    @endif

    {{-- Footer --}}
    @isset($footer)
        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 text-sm text-slate-500">
            {{ $footer }}
        </div>
    @endisset

</div>
