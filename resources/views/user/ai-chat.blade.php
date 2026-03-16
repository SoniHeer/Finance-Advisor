@extends('layouts.app')

@section('content')

@php
    $csrf = csrf_token();
@endphp

<div class="max-w-6xl mx-auto h-[92vh] flex flex-col">

{{-- HEADER --}}
<div class="flex items-center justify-between mb-6">

    <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl
                    bg-gradient-to-br from-emerald-400 to-cyan-400
                    flex items-center justify-center
                    text-white text-xl font-bold shadow-md">
            🤖
        </div>

        <div>
            <h1 class="text-xl font-extrabold">
                FinanceAI Assistant
            </h1>
            <p class="text-xs text-slate-500">
                AI Financial Intelligence • Secure Session
            </p>
        </div>
    </div>

    <span id="aiStatus"
          class="flex items-center gap-2 text-xs text-slate-500">
        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
        Online
    </span>

</div>

{{-- CHAT PANEL --}}
<div class="flex-1 flex flex-col bg-white border rounded-3xl shadow overflow-hidden">

    {{-- CHAT AREA --}}
    <div id="chatBox"
         class="flex-1 p-10 space-y-6 overflow-y-auto bg-slate-50">

        @foreach($chats as $chat)

            @if($chat->sender === 'user')

                {{-- USER MESSAGE --}}
                <div class="flex justify-end">
                    <div>
                        <div class="bg-cyan-50 border px-6 py-4 rounded-2xl text-sm max-w-xl">
                            {{ $chat->message }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1 text-right">
                            {{ $chat->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>

            @else

                {{-- AI MESSAGE --}}
                <div class="flex gap-4">
                    <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center">
                        🤖
                    </div>

                    <div>
                        <div class="bg-white border px-6 py-4 rounded-2xl text-sm max-w-2xl shadow-sm whitespace-pre-line">
                            {{ $chat->message }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1">
                            {{ $chat->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>

            @endif

        @endforeach

        {{-- Typing Indicator --}}
        <div id="typing" class="hidden flex gap-4 items-center">
            <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center">🤖</div>
            <div class="bg-white border px-4 py-3 rounded-xl shadow-sm text-xs text-slate-400">
                Thinking...
            </div>
        </div>

    </div>

    {{-- INPUT --}}
    <div class="p-6 border-t bg-white">

        <form id="chatForm" class="relative">

            <textarea id="message"
                      rows="1"
                      required
                      class="w-full bg-slate-100 border rounded-2xl px-6 py-4 text-sm resize-none focus:ring-2 focus:ring-emerald-400 outline-none"
                      placeholder="Ask about income, savings, risks…"></textarea>

            <button type="submit"
                    id="sendBtn"
                    class="absolute right-3 bottom-3 h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-400 to-cyan-400 text-white shadow-md">
                ➤
            </button>

        </form>

    </div>

</div>

</div>

<script>
const routeUrl = "{{ route('user.ai.chat.send') }}";
const csrfToken = "{{ $csrf }}";

const chatBox = document.getElementById('chatBox');
const form = document.getElementById('chatForm');
const input = document.getElementById('message');
const typing = document.getElementById('typing');
const sendBtn = document.getElementById('sendBtn');
const aiStatus = document.getElementById('aiStatus');

function scrollBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
}

function autoResize() {
    input.style.height = 'auto';
    input.style.height = input.scrollHeight + 'px';
}

input.addEventListener('input', autoResize);

input.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        form.dispatchEvent(new Event('submit'));
    }
});

form.addEventListener('submit', async e => {

    e.preventDefault();

    const msg = input.value.trim();
    if (!msg) return;

    sendBtn.disabled = true;

    chatBox.insertAdjacentHTML('beforeend', `
        <div class="flex justify-end">
            <div>
                <div class="bg-cyan-50 border px-6 py-4 rounded-2xl text-sm max-w-xl">
                    ${msg}
                </div>
                <div class="text-[10px] text-slate-400 mt-1 text-right">
                    ${new Date().toLocaleTimeString()}
                </div>
            </div>
        </div>
    `);

    input.value = '';
    autoResize();

    typing.classList.remove('hidden');
    aiStatus.innerHTML = '<span class="w-2 h-2 bg-amber-500 rounded-full"></span> Thinking';
    scrollBottom();

    try {

        const res = await fetch(routeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: msg })
        });

        if (!res.ok) throw new Error('Server error');

        const data = await res.json();

        typing.classList.add('hidden');
        aiStatus.innerHTML = '<span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Online';

        chatBox.insertAdjacentHTML('beforeend', `
            <div class="flex gap-4">
                <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center">🤖</div>
                <div>
                    <div class="bg-white border px-6 py-4 rounded-2xl text-sm max-w-2xl shadow-sm whitespace-pre-line">
                        ${data.reply}
                    </div>
                    <div class="text-[10px] text-slate-400 mt-1">
                        ${new Date().toLocaleTimeString()}
                    </div>
                </div>
            </div>
        `);

    } catch (error) {

        typing.classList.add('hidden');
        aiStatus.innerHTML = '<span class="w-2 h-2 bg-rose-500 rounded-full"></span> Error';

        chatBox.insertAdjacentHTML('beforeend', `
            <div class="text-rose-500 text-sm">
                AI service temporarily unavailable.
            </div>
        `);
    }

    sendBtn.disabled = false;
    scrollBottom();
});

scrollBottom();
</script>

@endsection
