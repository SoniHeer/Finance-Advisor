@props([
    'title' => '',
    'value' => '',
    'color' => 'green',
    'growth' => null,
    'positive' => true,
])

@php
    $colors = [
        'green' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'red'   => 'bg-rose-50 text-rose-700 border-rose-200',
        'cyan'  => 'bg-cyan-50 text-cyan-700 border-cyan-200',
    ];

    $colorClass = $colors[$color] ?? $colors['green'];
@endphp

<div class="rounded-2xl border p-6 shadow-sm {{ $colorClass }}">
    <p class="text-xs uppercase font-semibold opacity-70">
        {{ $title }}
    </p>

    <h3 class="text-2xl font-bold mt-2">
        {{ $value }}
    </h3>

    @if(!is_null($growth))
        <p class="text-xs mt-2">
            @if($positive)
                ▲
            @else
                ▼
            @endif
            {{ abs($growth) }}%
        </p>
    @endif
</div>
