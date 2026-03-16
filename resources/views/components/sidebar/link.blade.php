@props([
    'route' => null,
    'url' => null,
    'icon' => 'fa-circle',
    'label' => 'Menu',
    'badge' => null,
    'badgeColor' => 'blue',
    'match' => null,
    'permission' => null,
    'role' => null,
    'target' => null,
    'disabled' => false,
])

@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Permission Check
    |--------------------------------------------------------------------------
    */
    $hasPermission = true;

    if ($permission && $user) {
        $permissions = is_array($permission) ? $permission : [$permission];
        foreach ($permissions as $perm) {
            if (! $user->can($perm)) {
                $hasPermission = false;
                break;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Role Check
    |--------------------------------------------------------------------------
    */
    $hasRole = true;

    if ($role && $user) {
        $roles = is_array($role) ? $role : [$role];
        $hasRole = in_array($user->role, $roles);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Handling
    |--------------------------------------------------------------------------
    */
    $routeExists = $route ? Route::has($route) : false;
    $href = $routeExists ? route($route) : ($url ?? '#');

    /*
    |--------------------------------------------------------------------------
    | Active Detection
    |--------------------------------------------------------------------------
    */
    $patterns = [];

    if ($match) {
        $patterns = is_array($match) ? $match : [$match];
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
    | Visibility
    |--------------------------------------------------------------------------
    */
    $visible = $hasPermission && $hasRole;

    /*
    |--------------------------------------------------------------------------
    | Badge Color System
    |--------------------------------------------------------------------------
    */
    $badgeColors = [
        'blue'   => 'bg-blue-600',
        'red'    => 'bg-rose-600',
        'green'  => 'bg-emerald-600',
        'yellow' => 'bg-amber-500',
        'purple' => 'bg-purple-600',
        'gray'   => 'bg-slate-500',
    ];

    $badgeClass = $badgeColors[$badgeColor] ?? $badgeColors['blue'];
@endphp


@if($visible && ($routeExists || $url))

<a href="{{ $disabled ? '#' : $href }}"
   @if($target) target="{{ $target }}" @endif
   @if($disabled) aria-disabled="true" @endif
   aria-current="{{ $isActive ? 'page' : 'false' }}"
   class="group relative flex items-center gap-3
          px-4 py-2.5 rounded-xl
          text-sm font-medium
          transition-all duration-200
          focus:outline-none focus:ring-2 focus:ring-blue-500
          {{ $disabled
                ? 'opacity-40 cursor-not-allowed'
                : ($isActive
                    ? 'bg-blue-50 text-blue-700 font-semibold dark:bg-blue-900/30 dark:text-blue-400'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800')
          }}">

    {{-- ICON --}}
    <i class="fa-solid {{ $icon }}
              transition-all duration-200
              {{ $isActive
                    ? 'text-blue-600 dark:text-blue-400'
                    : 'text-slate-400 group-hover:text-blue-600 dark:text-slate-500'
              }}">
    </i>

    {{-- LABEL --}}
    <span class="truncate sidebar-text">
        {{ $label }}
    </span>

    {{-- BADGE --}}
    @if($badge)
        <span class="ml-auto {{ $badgeClass }}
                     text-white text-xs
                     font-semibold px-2 py-0.5
                     rounded-full shadow-sm">
            {{ $badge }}
        </span>
    @endif

    {{-- ACTIVE INDICATOR BAR --}}
    @if($isActive)
        <span class="absolute left-0 top-1/2 -translate-y-1/2
                     h-6 w-1 bg-blue-600
                     rounded-r-full"></span>
    @endif

</a>

@endif
