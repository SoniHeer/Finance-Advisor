@props([
    'route' => null,
    'url' => null,
    'icon' => 'fa-circle',
    'label' => 'Menu',
    'badge' => null,
    'badgeColor' => 'indigo',
    'permission' => null,
    'role' => null,
    'active' => null,
    'target' => null,
    'disabled' => false,
    'external' => false,
])

@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Permission Check
    |--------------------------------------------------------------------------
    */
    $allowed = true;

    if ($permission && $user) {
        $perms = is_array($permission) ? $permission : [$permission];
        foreach ($perms as $perm) {
            if (! $user->can($perm)) {
                $allowed = false;
                break;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Role Check
    |--------------------------------------------------------------------------
    */
    if ($role && $user) {
        $roles = is_array($role) ? $role : [$role];
        if (! in_array($user->role, $roles)) {
            $allowed = false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Route / URL Resolution
    |--------------------------------------------------------------------------
    */
    $routeExists = $route && Route::has($route);
    $href = '#';

    if ($routeExists) {
        $href = route($route);
    } elseif ($url) {
        $href = $url;
    }

    /*
    |--------------------------------------------------------------------------
    | Active Detection
    |--------------------------------------------------------------------------
    */
    $patterns = [];

    if ($active) {
        $patterns = is_array($active) ? $active : [$active];
    } elseif ($route) {
        $patterns = [$route, $route . '.*'];
    }

    $isActive = false;
    foreach ($patterns as $pattern) {
        if (request()->routeIs($pattern)) {
            $isActive = true;
            break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Badge Color System
    |--------------------------------------------------------------------------
    */
    $badgeMap = [
        'indigo' => 'bg-indigo-600',
        'red'    => 'bg-rose-600',
        'green'  => 'bg-emerald-600',
        'yellow' => 'bg-amber-500',
        'purple' => 'bg-purple-600',
        'blue'   => 'bg-blue-600',
    ];

    $badgeClass = $badgeMap[$badgeColor] ?? $badgeMap['indigo'];

    /*
    |--------------------------------------------------------------------------
    | Class Builder
    |--------------------------------------------------------------------------
    */
    $baseClass = "group relative flex items-center gap-3 px-4 py-3
                  rounded-xl text-sm font-medium transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-indigo-500";

    if ($disabled) {
        $stateClass = "opacity-40 cursor-not-allowed";
    } elseif ($isActive) {
        $stateClass = "bg-indigo-50 text-indigo-700
                       dark:bg-indigo-900/40 dark:text-indigo-300
                       font-semibold";
    } else {
        $stateClass = "text-slate-600 hover:bg-slate-100 hover:text-indigo-600
                       dark:text-slate-300 dark:hover:bg-slate-800";
    }
@endphp


@if($allowed && ($routeExists || $url))
<a href="{{ $disabled ? '#' : $href }}"
   @if($target || $external)
       target="{{ $target ?? '_blank' }}"
   @endif
   @if($external)
       rel="noopener noreferrer"
   @endif
   @if($disabled)
       aria-disabled="true"
   @endif
   aria-current="{{ $isActive ? 'page' : 'false' }}"
   title="{{ $label }}"
   class="{{ $baseClass }} {{ $stateClass }}">

    {{-- ICON --}}
    <i class="fa-solid {{ $icon }}
              w-5 text-center transition-all duration-200
              {{ $isActive
                    ? 'text-indigo-600 dark:text-indigo-300'
                    : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-500'
              }}">
    </i>

    {{-- LABEL --}}
    <span class="truncate">
        {{ $label }}
    </span>

    {{-- BADGE --}}
    @if($badge)
        <span class="ml-auto {{ $badgeClass }}
                     text-white text-xs font-semibold
                     px-2 py-0.5 rounded-full shadow-sm">
            {{ $badge }}
        </span>
    @endif

    {{-- ACTIVE INDICATOR --}}
    @if($isActive)
        <span class="absolute left-0 top-1/2 -translate-y-1/2
                     h-6 w-1 bg-indigo-600 rounded-r-full">
        </span>
    @endif

</a>
@endif
