@php
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Enterprise Navigation Structure
    |--------------------------------------------------------------------------
    */

    $menu = [

        [
            'section' => null,
            'items' => [
                ['route' => 'user.dashboard',         'match' => 'user.dashboard*',        'icon' => 'fa-chart-line',   'label' => 'Dashboard'],
                ['route' => 'user.incomes.index',     'match' => 'user.incomes.*',         'icon' => 'fa-wallet',       'label' => 'Income'],
                ['route' => 'user.expenses.index',    'match' => 'user.expenses.*',        'icon' => 'fa-receipt',      'label' => 'Expenses'],
                ['route' => 'user.families.index',    'match' => 'user.families.*',        'icon' => 'fa-people-group', 'label' => 'Family Budget'],

                // Reports FIXED
                [
                    'route' => 'user.reports.index',
                    'match' => ['user.reports.*', 'reports.*'],
                    'icon'  => 'fa-chart-pie',
                    'label' => 'Reports'
                ],

                ['route' => 'user.notifications.index','match' => 'user.notifications.*',  'icon' => 'fa-bell',         'label' => 'Notifications'],
                ['route' => 'user.ai.chat',           'match' => 'user.ai.*',              'icon' => 'fa-robot',        'label' => 'AI Assistant'],
            ],
        ],

        [
            'section' => 'Account',
            'items' => [
                ['route' => 'user.profile.index',         'match' => 'user.profile.*',          'icon' => 'fa-user',        'label' => 'Profile'],
                ['route' => 'user.profile.password.form', 'match' => 'user.profile.password.*', 'icon' => 'fa-lock',        'label' => 'Change Password'],
                ['route' => 'user.profile.subscription',  'match' => 'user.profile.subscription*','icon'=>'fa-credit-card','label'=>'Subscription'],
            ],
        ],
    ];

    // Admin Section
    if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
        $menu[] = [
            'section' => 'Admin',
            'items' => [
                ['route' => 'admin.dashboard',        'match' => 'admin.dashboard*',    'icon' => 'fa-shield-halved', 'label' => 'Admin Dashboard'],
                ['route' => 'admin.users.index',      'match' => 'admin.users.*',       'icon' => 'fa-users',         'label' => 'Users'],
                ['route' => 'admin.activities.index', 'match' => 'admin.activities.*',  'icon' => 'fa-clipboard-list','label' => 'Activities'],
            ],
        ];
    }
@endphp


{{-- ================= OVERLAY ================= --}}
<div x-show="sidebarOpen"
     x-transition.opacity
     class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 lg:hidden"
     @click="sidebarOpen = false">
</div>


{{-- ================= SIDEBAR ================= --}}
<aside
    class="fixed lg:static
           inset-y-0 left-0
           w-72
           bg-white dark:bg-slate-900
           border-r border-slate-200 dark:border-slate-800
           shadow-xl lg:shadow-none
           transform
           transition-all duration-300 ease-in-out
           z-50 flex flex-col"

    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    {{-- HEADER --}}
    <div class="flex items-center justify-between
                px-6 py-5
                border-b border-slate-200 dark:border-slate-800">

        <a href="{{ route('user.dashboard') }}"
           class="flex items-center gap-3 group">

            <div class="h-10 w-10 rounded-xl
                        bg-gradient-to-br
                        from-indigo-600 to-blue-600
                        flex items-center justify-center
                        text-white font-bold shadow-md
                        group-hover:scale-105 transition">
                FA
            </div>

            <span class="font-bold text-lg text-slate-800 dark:text-white">
                FinanceAI
            </span>
        </a>

        <button class="lg:hidden"
                @click="sidebarOpen = false">
            <i class="fa-solid fa-xmark text-lg text-slate-500"></i>
        </button>
    </div>


    {{-- NAVIGATION --}}
    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-8">

        @foreach($menu as $group)

            @if($group['section'])
                <p class="px-3 text-xs uppercase tracking-widest
                          text-slate-400">
                    {{ $group['section'] }}
                </p>
            @endif

            <div class="space-y-1">

                @foreach($group['items'] as $item)

                    @php
                        $match = $item['match'];
                        $active = is_array($match)
                            ? collect($match)->contains(fn($m) => request()->routeIs($m))
                            : request()->routeIs($match);
                    @endphp

                    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                       class="relative flex items-center gap-3
                              px-4 py-3 rounded-xl text-sm font-medium
                              transition-all duration-200 group
                              {{ $active
                                  ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
                                  : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">

                        {{-- Active Indicator --}}
                        @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2
                                         h-8 w-1 bg-indigo-600 rounded-r-full">
                            </span>
                        @endif

                        <i class="fa-solid {{ $item['icon'] }}
                                  w-5 text-center
                                  {{ $active ? '' : 'group-hover:scale-110 transition' }}">
                        </i>

                        <span>{{ $item['label'] }}</span>

                    </a>

                @endforeach

            </div>

        @endforeach

    </div>


    {{-- FOOTER --}}
    <div class="px-6 py-4 border-t
                border-slate-200 dark:border-slate-800
                text-xs text-slate-400">

        <div class="flex items-center justify-between">
            <span>© {{ now()->year }}</span>
            <span class="font-semibold">FinanceAI</span>
        </div>

        <div class="mt-2 text-[10px]">
            Enterprise Build v1.0
        </div>
    </div>

</aside>
