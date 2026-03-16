@props([
    'title' => null,
    'icon' => null,
    'color' => null,
    'permission' => null,
    'role' => null,
    'divider' => true,
])

@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Visibility Control (Role + Permission)
    |--------------------------------------------------------------------------
    */
    $visible = true;

    if ($permission && $user) {
        $perms = is_array($permission) ? $permission : [$permission];
        foreach ($perms as $perm) {
            if (! $user->can($perm)) {
                $visible = false;
                break;
            }
        }
    }

    if ($role && $user) {
        $roles = is_array($role) ? $role : [$role];
        if (! in_array($user->role, $roles)) {
            $visible = false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Color System
    |--------------------------------------------------------------------------
    */
    $colorMap = [
        'indigo' => 'text-indigo-500',
        'blue'   => 'text-blue-500',
        'green'  => 'text-emerald-500',
        'red'    => 'text-rose-500',
        'yellow' => 'text-amber-500',
        'purple' => 'text-purple-500',
    ];

    $colorClass = $colorMap[$color] ?? 'text-slate-400';
@endphp


@if($visible && filled($title))

<div class="relative group">

    {{-- Optional Divider --}}
    @if($divider)
        <div class="border-t border-slate-200 dark:border-slate-800
                    my-6 first:hidden">
        </div>
    @endif

    {{-- Section Label --}}
    <div class="flex items-center gap-2
                px-3 py-3
                text-xs font-semibold uppercase
                tracking-[0.15em]
                {{ $colorClass }}
                transition-all duration-200
                opacity-80 group-hover:opacity-100">

        @if($icon)
            <i class="fa-solid {{ $icon }} text-[10px] opacity-70"></i>
        @endif

        <span class="sidebar-text truncate">
            {{ $title }}
        </span>

    </div>

</div>

@endif
