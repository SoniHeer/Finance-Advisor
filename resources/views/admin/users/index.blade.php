@extends('layouts.app')

@section('content')

@php
    $stats = $stats ?? [
        'total'   => 0,
        'active'  => 0,
        'blocked' => 0,
        'admins'  => 0,
    ];

    $users = $users ?? collect();

    $health = $stats['total'] > 0
        ? round(($stats['active'] / $stats['total']) * 100)
        : 0;

    $healthColor = match (true) {
        $health >= 80 => 'text-emerald-600',
        $health >= 60 => 'text-indigo-600',
        $health >= 40 => 'text-amber-600',
        default       => 'text-rose-600',
    };
@endphp

<div class="max-w-7xl mx-auto px-6 py-12 space-y-14 bg-slate-50 min-h-screen">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">

        <div>
            <h1 class="text-4xl font-black text-slate-900">
                Admin Control Matrix
            </h1>
            <p class="text-sm text-slate-500 mt-2">
                Enterprise User Governance Console
            </p>
        </div>

        <div class="text-sm text-slate-600 bg-white px-5 py-3 rounded-xl shadow-sm border">
            Total Users:
            <span class="font-bold text-slate-900">
                {{ number_format($stats['total']) }}
            </span>
        </div>

    </div>

    {{-- KPI GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

        <x-ui.card title="Active Users" accent="green" variant="elevated">
            <div class="text-4xl font-extrabold text-emerald-600">
                {{ number_format($stats['active']) }}
            </div>
        </x-ui.card>

        <x-ui.card title="Blocked Users" accent="red" variant="elevated">
            <div class="text-4xl font-extrabold text-rose-600">
                {{ number_format($stats['blocked']) }}
            </div>
        </x-ui.card>

        <x-ui.card title="Admin Accounts" accent="purple" variant="elevated">
            <div class="text-4xl font-extrabold text-indigo-600">
                {{ number_format($stats['admins']) }}
            </div>
        </x-ui.card>

        <x-ui.card title="Governance Health" variant="gradient">
            <div class="text-4xl font-black {{ $healthColor }}">
                {{ $health }}%
            </div>
        </x-ui.card>

    </div>

    {{-- FILTER BAR --}}
    <x-ui.card variant="soft">

        <form method="GET" class="flex flex-wrap gap-4 items-center">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search name or email..."
                   class="flex-1 min-w-[220px] px-4 py-2 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">

            <select name="status"
                    class="px-4 py-2 border rounded-xl focus:outline-none">
                <option value="">All Status</option>
                <option value="active" @selected(request('status')=='active')>
                    Active
                </option>
                <option value="blocked" @selected(request('status')=='blocked')>
                    Blocked
                </option>
            </select>

            <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition">
                Apply
            </button>

            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 text-sm text-slate-500 hover:text-slate-900">
                    Reset
                </a>
            @endif

        </form>

    </x-ui.card>

    {{-- TABLE SECTION --}}
    <x-ui.card padding="p-0" variant="glass">

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-slate-100 sticky top-0 text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-6 py-4 text-left">User</th>
                        <th class="px-6 py-4 text-left">Email</th>
                        <th class="px-6 py-4 text-center">Role</th>
                        <th class="px-6 py-4 text-center">Joined</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                @forelse($users as $user)

                    @php
                        $isAdmin = method_exists($user,'isAdmin') && $user->isAdmin();
                    @endphp

                    <tr class="hover:bg-slate-50 transition">

                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900">
                                {{ e($user->name) }}
                            </div>
                            <div class="text-xs text-slate-500">
                                ID #{{ $user->id }}
                            </div>
                        </td>

                        <td class="px-6 py-4 text-slate-700">
                            {{ e($user->email) }}
                        </td>

                        <td class="px-6 py-4 text-center">

                            @if($isAdmin)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">
                                    Admin
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-200 text-slate-700">
                                    User
                                </span>
                            @endif

                        </td>

                        <td class="px-6 py-4 text-center text-slate-600">
                            {{ optional($user->created_at)->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4 text-center">

                            @if($user->is_blocked)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-600">
                                    Blocked
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-600">
                                    Active
                                </span>
                            @endif

                        </td>

                        <td class="px-6 py-4 text-right space-x-2">

                            @if(auth()->id() !== $user->id)

                                <form action="{{ route('admin.users.block', $user) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Change user status?')">
                                    @csrf
                                    @method('PATCH')

                                    <button class="px-3 py-1.5 rounded-lg text-xs font-semibold transition
                                        {{ $user->is_blocked
                                            ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                                            : 'bg-rose-100 text-rose-700 hover:bg-rose-200' }}">
                                        {{ $user->is_blocked ? 'Unblock' : 'Block' }}
                                    </button>
                                </form>

                                @if(!$isAdmin)
                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Delete permanently?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="px-3 py-1.5 rounded-lg text-xs bg-slate-200 hover:bg-slate-300 transition">
                                            Delete
                                        </button>
                                    </form>
                                @endif

                            @else
                                <span class="text-xs text-slate-400">
                                    Current User
                                </span>
                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center text-slate-500">
                            No users found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </x-ui.card>

    {{-- PAGINATION --}}
    @if(method_exists($users,'links'))
        <div>
            {{ $users->withQueryString()->links() }}
        </div>
    @endif

</div>

@endsection
