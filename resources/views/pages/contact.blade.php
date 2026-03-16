@extends('layouts.landing')

@section('content')

<section class="relative py-32 bg-gradient-to-br from-indigo-50 via-white to-purple-50">

    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-20 items-start">

        <!-- LEFT INFO -->
        <div class="space-y-10">

            <h1 class="text-5xl font-extrabold text-slate-900">
                Contact FinanceAI
            </h1>

            <p class="text-lg text-slate-600">
                Enterprise support. Partnership inquiries.
                We typically respond within 24 hours.
            </p>

            <div class="space-y-6 mt-8">

                <div class="info-row">
                    <div class="info-icon">📧</div>
                    <p>support@financeai.com</p>
                </div>

                <div class="info-row">
                    <div class="info-icon">🌍</div>
                    <p>Global Remote Team</p>
                </div>

                <div class="info-row">
                    <div class="info-icon">⚡</div>
                    <p>Fast Enterprise Response</p>
                </div>

            </div>

            <!-- Google Map -->
            <div class="mt-12 rounded-2xl overflow-hidden shadow-soft">
                <iframe
                    src="https://maps.google.com/maps?q=India&t=&z=4&ie=UTF8&iwloc=&output=embed"
                    width="100%"
                    height="250"
                    class="border-0">
                </iframe>
            </div>

        </div>

        <!-- FORM -->
        <div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}"
                  class="bg-white p-10 rounded-3xl shadow-medium space-y-6">

                @csrf

                <div>
                    <label class="label">Name</label>
                    <input type="text" name="name"
                           value="{{ old('name') }}"
                           class="input-field">
                    @error('name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email"
                           value="{{ old('email') }}"
                           class="input-field">
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="label">Subject</label>
                    <input type="text" name="subject"
                           value="{{ old('subject') }}"
                           class="input-field">
                </div>

                <div>
                    <label class="label">Message</label>
                    <textarea name="message" rows="5"
                              class="input-field">{{ old('message') }}</textarea>
                    @error('message')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">
                    Send Message
                </button>

            </form>

        </div>

    </div>

</section>

<style>
.info-row{
    display:flex;
    align-items:center;
    gap:12px;
    font-weight:500;
    color:#334155;
}
.info-icon{
    background:#6366f1;
    color:white;
    padding:8px;
    border-radius:12px;
}
.label{
    font-size:.9rem;
    font-weight:600;
    margin-bottom:6px;
    display:block;
}
.input-field{
    width:100%;
    padding:14px 16px;
    border:1px solid rgba(15,23,42,.15);
    border-radius:14px;
}
.input-field:focus{
    outline:none;
    border-color:#6366f1;
    box-shadow:0 0 0 3px rgba(99,102,241,.12);
}
.submit-btn{
    width:100%;
    padding:16px;
    border-radius:18px;
    background:linear-gradient(to right,#6366f1,#8b5cf6);
    color:white;
    font-weight:600;
    transition:.3s;
}
.submit-btn:hover{
    transform:translateY(-3px);
    box-shadow:0 15px 40px rgba(99,102,241,.35);
}
.error-text{
    color:#dc2626;
    font-size:.8rem;
}
</style>

@endsection
