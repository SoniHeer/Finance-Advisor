@extends('layouts.app')

@section('content')

@php
    $familyName = $family->name ?? 'Family';
    $familyId = $family->id ?? null;
    $routeShow = ($familyId && Route::has('user.families.show'))
        ? route('user.families.show', $familyId)
        : '#';

    $inviteCount = $invites->count() ?? 0;
    $averageWait = $avgWait ?? 0;
@endphp

<div class="max-w-6xl mx-auto px-6 py-16">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between gap-8 mb-12">

        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                Pending Invitations
            </h1>

            <p class="text-sm text-slate-600 mt-2">
                {{ $familyName }} · Waiting for members
            </p>

            <p class="text-xs text-slate-500 mt-1">
                {{ now()->format('F Y') }} · {{ $inviteCount }} active
            </p>
        </div>

        <a href="{{ $routeShow }}"
           class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">
            ← Back to Family
        </a>

    </div>

    {{-- INSIGHTS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        <div class="card">
            📧 <strong>{{ $inviteCount }}</strong> pending invites
        </div>

        <div class="card">
            ⏳ Avg wait: <strong>{{ $averageWait }}</strong> hrs
        </div>

        <div class="card">
            🤖 Most joins happen within 24–48 hrs
        </div>

    </div>

    {{-- EMPTY STATE --}}
    @if($inviteCount === 0)

        <div class="bg-white rounded-2xl shadow p-14 text-center">

            <div class="text-5xl mb-5">📭</div>

            <h2 class="text-lg font-semibold text-slate-900">
                No pending invites
            </h2>

            <p class="text-slate-600 text-sm mt-2">
                All invited members have joined.
            </p>

            <a href="{{ $routeShow }}"
               class="inline-block mt-6 px-6 py-3 rounded-xl
                      bg-indigo-600 text-white font-medium
                      hover:bg-indigo-500 transition">
                Back to Family
            </a>

        </div>

    @else

        {{-- GROUPED LIST --}}
        @foreach($grouped ?? [] as $label => $group)

            @if($group->count())

                <section class="mb-10">

                    <h3 class="text-xs uppercase font-semibold
                               tracking-wider text-slate-500 mb-4">
                        {{ $label }}
                    </h3>

                    <div class="bg-white rounded-xl shadow divide-y">

                        @foreach($group as $invite)

                            @php
                                $expired = $invite->expires_at
                                    ? $invite->expires_at->isPast()
                                    : false;
                            @endphp

                            <div class="flex justify-between items-center p-5 hover:bg-slate-50 transition">

                                <div>
                                    <p class="font-medium text-slate-900">
                                        {{ $invite->email }}
                                    </p>

                                    <p class="text-xs text-slate-500 mt-1">
                                        Sent {{ $invite->created_at?->diffForHumans() }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">

                                    <button
                                        type="button"
                                        class="copy-btn"
                                        data-email="{{ $invite->email }}">
                                        Copy
                                    </button>

                                    @if($expired)
                                        <span class="badge badge-expired">
                                            Expired
                                        </span>
                                    @else
                                        <span class="badge badge-pending">
                                            Pending
                                        </span>
                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                </section>

            @endif

        @endforeach

    @endif

</div>

<style>
.card {
    background: white;
    padding: 1.2rem;
    border-radius: 1rem;
    box-shadow: 0 15px 35px rgba(0,0,0,.06);
    font-size: 13px;
}

.badge {
    padding: .35rem .8rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 600;
}

.badge-pending {
    background: rgba(234,179,8,.15);
    color: #b45309;
}

.badge-expired {
    background: rgba(239,68,68,.15);
    color: #dc2626;
}

.copy-btn {
    font-size: 12px;
    font-weight: 600;
    color: #2563eb;
    cursor: pointer;
}

.copy-btn:hover {
    text-decoration: underline;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){

    document.querySelectorAll('.copy-btn').forEach(btn => {

        btn.addEventListener('click', async function () {

            try {
                await navigator.clipboard.writeText(this.dataset.email);
                this.innerText = 'Copied ✓';
            } catch (e) {
                const textarea = document.createElement('textarea');
                textarea.value = this.dataset.email;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.innerText = 'Copied ✓';
            }

            setTimeout(() => {
                this.innerText = 'Copy';
            }, 1500);

        });

    });

});
</script>

@endsection
