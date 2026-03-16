@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
    $unreadAlerts = $unreadAlerts ?? 0;
@endphp

<nav class="sticky top-0 z-40
            bg-white dark:bg-slate-900
            border-b border-slate-200 dark:border-slate-800">

    <div class="max-w-[1600px] mx-auto
                px-4 md:px-6
                h-16 flex items-center justify-between">

        {{-- ================= LEFT ================= --}}
        <div class="flex items-center gap-4">

            {{-- Sidebar Toggle (Alpine controlled) --}}
            <button
                @click="sidebarOpen = true"
                class="lg:hidden p-2 rounded-lg
                       hover:bg-slate-100
                       dark:hover:bg-slate-800 transition">

                <i class="fa-solid fa-bars
                          text-slate-600 dark:text-slate-300"></i>
            </button>

            {{-- Logo --}}
            <a href="{{ route('user.dashboard') }}"
               class="flex items-center gap-3 group">

                <div class="h-9 w-9 rounded-xl
                            bg-gradient-to-br
                            from-indigo-600 to-blue-600
                            flex items-center justify-center
                            text-white font-bold shadow-md
                            group-hover:scale-105 transition">
                    FA
                </div>

                <span class="hidden md:block
                             font-bold text-slate-900 dark:text-white">
                    FinanceAI
                </span>
            </a>
        </div>


        {{-- ================= RIGHT ================= --}}
        @auth
        <div class="flex items-center gap-4">

            {{-- Dark Mode Toggle --}}
            <button
                @click="dark = !dark"
                class="p-2 rounded-lg
                       hover:bg-slate-100 dark:hover:bg-slate-800 transition">

                <i class="fa-solid"
                   :class="dark ? 'fa-sun text-amber-400' : 'fa-moon text-slate-600 dark:text-slate-300'">
                </i>
            </button>


            {{-- Notifications --}}
            @if(Route::has('user.notifications.index'))
            <a href="{{ route('user.notifications.index') }}"
               class="relative p-2 rounded-lg
                      hover:bg-slate-100 dark:hover:bg-slate-800
                      transition">

                <i class="fa-solid fa-bell
                          text-slate-600 dark:text-slate-300"></i>

                @if($unreadAlerts > 0)
                    <span class="absolute -top-1 -right-1
                                 min-w-[18px] px-1.5 py-[1px]
                                 text-[10px] font-bold text-white
                                 bg-indigo-600 rounded-full
                                 text-center">
                        {{ $unreadAlerts }}
                    </span>
                @endif
            </a>
            @endif


            {{-- USER DROPDOWN --}}
            <div x-data="{ open:false }" class="relative">

                <button
                    @click="open = !open"
                    class="flex items-center gap-3
                           px-3 py-2
                           rounded-xl
                           border border-slate-200
                           dark:border-slate-700
                           hover:shadow-sm transition">

                    {{-- Avatar --}}
                    <div class="h-8 w-8 rounded-lg
                                bg-gradient-to-br
                                from-indigo-500 to-blue-600
                                flex items-center justify-center
                                text-white text-sm font-bold">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>

                    <div class="hidden md:block text-left leading-tight">
                        <p class="text-xs font-semibold
                                  text-slate-900 dark:text-white">
                            {{ $user->name ?? 'User' }}
                        </p>

                        <span class="inline-block mt-1
                                     text-[10px]
                                     px-2 py-[2px]
                                     rounded-full
                                     bg-slate-100
                                     dark:bg-slate-800
                                     text-slate-600
                                     dark:text-slate-300">
                            {{ ucfirst($user->role ?? 'member') }}
                        </span>
                    </div>

                    <i class="fa-solid fa-chevron-down
                              text-xs text-slate-400 transition"
                       :class="open ? 'rotate-180' : ''">
                    </i>
                </button>


                {{-- Dropdown Panel --}}
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-56
                           bg-white dark:bg-slate-900
                           border border-slate-200
                           dark:border-slate-700
                           rounded-xl shadow-lg text-sm">

                    {{-- Profile --}}
                    @if(Route::has('user.profile.index'))
                    <a href="{{ route('user.profile.index') }}"
                       class="block px-4 py-2
                              hover:bg-slate-100
                              dark:hover:bg-slate-800 transition">
                        Profile
                    </a>
                    @endif

                    {{-- Change Password --}}
                    @if(Route::has('user.profile.password.form'))
                    <a href="{{ route('user.profile.password.form') }}"
                       class="block px-4 py-2
                              hover:bg-slate-100
                              dark:hover:bg-slate-800 transition">
                        Change Password
                    </a>
                    @endif

                    {{-- Subscription --}}
                    @if(Route::has('user.profile.subscription'))
                    <a href="{{ route('user.profile.subscription') }}"
                       class="block px-4 py-2
                              hover:bg-slate-100
                              dark:hover:bg-slate-800 transition">
                        Subscription
                    </a>
                    @endif

                    {{-- Admin Panel --}}
                    @if($user?->isAdmin() && Route::has('admin.dashboard'))
                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-4 py-2
                              hover:bg-slate-100
                              dark:hover:bg-slate-800 transition">
                        Admin Panel
                    </a>
                    @endif

                    <div class="border-t border-slate-200
                                dark:border-slate-700 my-1"></div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2
                                       text-rose-600
                                       hover:bg-rose-50
                                       dark:hover:bg-rose-900/20
                                       transition">
                            Logout
                        </button>
                    </form>

                </div>
            </div>

        </div>
        @endauth

    </div>
</nav>
