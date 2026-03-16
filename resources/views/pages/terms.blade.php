@extends('layouts.landing')

@section('content')

<section class="py-28 bg-white">
    <div class="max-w-6xl mx-auto px-6 space-y-20">

        <!-- ================= HEADER ================= -->
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-5xl font-extrabold text-slate-900">
                Terms & Conditions
            </h1>

            <p class="mt-6 text-slate-500 text-sm">
                Effective Date: {{ date('F d, Y') }}
            </p>

            <p class="mt-8 text-lg text-slate-600 leading-relaxed">
                These Terms & Conditions govern your use of FinanceAI.
                By accessing or using the platform, you agree to comply
                with the following terms.
            </p>
        </div>

        <!-- ================= ACCEPTANCE ================= -->
        <section>
            <h2 class="section-title">1. Acceptance of Terms</h2>

            <p class="policy-text mt-6">
                By creating an account or using FinanceAI services,
                you confirm that you are at least 18 years of age
                and legally capable of entering into a binding agreement.
            </p>
        </section>

        <!-- ================= ACCOUNT RESPONSIBILITY ================= -->
        <section>
            <h2 class="section-title">2. Account Responsibilities</h2>

            <ul class="policy-list mt-6">
                <li>You are responsible for maintaining account security.</li>
                <li>You must provide accurate and up-to-date information.</li>
                <li>You are responsible for all activity under your account.</li>
                <li>Unauthorized access must be reported immediately.</li>
            </ul>
        </section>

        <!-- ================= PLATFORM USAGE ================= -->
        <section>
            <h2 class="section-title">3. Acceptable Use</h2>

            <ul class="policy-list mt-6">
                <li>Use the platform only for lawful financial tracking.</li>
                <li>Do not attempt to hack, disrupt, or misuse services.</li>
                <li>No reverse engineering or code extraction.</li>
                <li>No automated scraping without authorization.</li>
            </ul>
        </section>

        <!-- ================= PAYMENTS ================= -->
        <section>
            <h2 class="section-title">4. Payments & Subscriptions</h2>

            <p class="policy-text mt-6">
                Certain features may require a paid subscription.
                Fees are billed in advance and are non-refundable
                unless required by applicable law.
            </p>

            <ul class="policy-list mt-4">
                <li>Pricing may change with prior notice.</li>
                <li>Failure to pay may result in suspension.</li>
                <li>You may cancel anytime before renewal.</li>
            </ul>
        </section>

        <!-- ================= DATA OWNERSHIP ================= -->
        <section>
            <h2 class="section-title">5. Data Ownership</h2>

            <p class="policy-text mt-6">
                You retain full ownership of your financial data.
                FinanceAI is granted a limited license to process
                data solely for service functionality.
            </p>
        </section>

        <!-- ================= AI DISCLAIMER ================= -->
        <section>
            <h2 class="section-title">6. AI & Financial Disclaimer</h2>

            <p class="policy-text mt-6">
                AI-generated insights are informational only and
                should not be considered professional financial advice.
                Users remain responsible for financial decisions.
            </p>
        </section>

        <!-- ================= LIMITATION OF LIABILITY ================= -->
        <section>
            <h2 class="section-title">7. Limitation of Liability</h2>

            <p class="policy-text mt-6">
                FinanceAI shall not be liable for indirect,
                incidental, or consequential damages arising
                from platform use.
            </p>
        </section>

        <!-- ================= TERMINATION ================= -->
        <section>
            <h2 class="section-title">8. Termination</h2>

            <p class="policy-text mt-6">
                We reserve the right to suspend or terminate accounts
                that violate these Terms without prior notice.
            </p>
        </section>

        <!-- ================= GOVERNING LAW ================= -->
        <section>
            <h2 class="section-title">9. Governing Law</h2>

            <p class="policy-text mt-6">
                These Terms are governed by applicable international
                commercial law and jurisdictional regulations
                depending on user location.
            </p>
        </section>

        <!-- ================= DISPUTES ================= -->
        <section>
            <h2 class="section-title">10. Dispute Resolution</h2>

            <p class="policy-text mt-6">
                Any disputes arising from these Terms shall be
                resolved through arbitration or mediation
                before formal litigation.
            </p>
        </section>

        <!-- ================= CONTACT ================= -->
        <section>
            <h2 class="section-title">11. Contact</h2>

            <p class="policy-text mt-6">
                For questions regarding these Terms,
                please contact us via the
                <a href="{{ route('contact') }}" class="text-indigo-600 hover:underline">
                    Contact Page
                </a>.
            </p>
        </section>

    </div>
</section>

<style>
.section-title{
    font-size:1.75rem;
    font-weight:700;
    color:#0f172a;
}
.policy-text{
    font-size:1rem;
    color:#475569;
    line-height:1.8;
}
.policy-list{
    list-style-type:disc;
    padding-left:1.5rem;
    color:#475569;
    line-height:1.8;
}
</style>

@endsection
