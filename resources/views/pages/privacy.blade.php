@extends('layouts.landing')

@section('content')

<section class="py-28 bg-white">
    <div class="max-w-6xl mx-auto px-6 space-y-20">

        <!-- ================= HEADER ================= -->
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-5xl font-extrabold text-slate-900">
                Global Privacy Policy
            </h1>

            <p class="mt-6 text-slate-500 text-sm">
                Last Updated: {{ date('F d, Y') }}
            </p>

            <p class="mt-8 text-lg text-slate-600 leading-relaxed">
                FinanceAI is committed to protecting your personal and financial data.
                This policy outlines how we collect, use, store, and safeguard information
                in compliance with global privacy regulations.
            </p>
        </div>

        <!-- ================= DATA COLLECTION ================= -->
        <section>
            <h2 class="section-title">1. Information We Collect</h2>

            <ul class="policy-list mt-6">
                <li>Account information (name, email, login credentials)</li>
                <li>Financial records (income, expenses, budgets)</li>
                <li>Usage data (analytics, device, browser type)</li>
                <li>Communication data (contact form submissions)</li>
            </ul>
        </section>

        <!-- ================= PURPOSE ================= -->
        <section>
            <h2 class="section-title">2. How We Use Information</h2>

            <ul class="policy-list mt-6">
                <li>To provide financial tracking services</li>
                <li>To generate AI-based insights</li>
                <li>To improve system performance</li>
                <li>To enhance security and prevent fraud</li>
                <li>To comply with legal obligations</li>
            </ul>
        </section>

        <!-- ================= GDPR ================= -->
        <section>
            <h2 class="section-title">3. GDPR (EU Compliance)</h2>

            <p class="policy-text mt-6">
                For users in the European Economic Area (EEA), FinanceAI acts as a Data Controller.
                You have the following rights:
            </p>

            <ul class="policy-list mt-4">
                <li>Right to Access your personal data</li>
                <li>Right to Rectification</li>
                <li>Right to Erasure ("Right to be Forgotten")</li>
                <li>Right to Data Portability</li>
                <li>Right to Restrict Processing</li>
                <li>Right to Object to Processing</li>
            </ul>
        </section>

        <!-- ================= CCPA ================= -->
        <section>
            <h2 class="section-title">4. CCPA (California Residents)</h2>

            <p class="policy-text mt-6">
                Under the California Consumer Privacy Act (CCPA), California residents
                have the right to:
            </p>

            <ul class="policy-list mt-4">
                <li>Request disclosure of collected personal data</li>
                <li>Request deletion of personal data</li>
                <li>Opt-out of data selling (FinanceAI does NOT sell data)</li>
                <li>Non-discrimination for exercising privacy rights</li>
            </ul>
        </section>

        <!-- ================= DATA RETENTION ================= -->
        <section>
            <h2 class="section-title">5. Data Retention</h2>

            <p class="policy-text mt-6">
                We retain user data only as long as necessary to provide services
                or comply with legal requirements. Users may request permanent
                deletion of their account at any time.
            </p>
        </section>

        <!-- ================= COOKIES ================= -->
        <section>
            <h2 class="section-title">6. Cookies & Tracking</h2>

            <p class="policy-text mt-6">
                FinanceAI uses essential cookies for authentication and analytics
                cookies for improving user experience. Users may control cookies
                through browser settings.
            </p>
        </section>

        <!-- ================= INTERNATIONAL TRANSFER ================= -->
        <section>
            <h2 class="section-title">7. International Data Transfers</h2>

            <p class="policy-text mt-6">
                If data is transferred outside your country, we ensure appropriate
                safeguards such as standard contractual clauses and secure hosting.
            </p>
        </section>

        <!-- ================= SECURITY ================= -->
        <section>
            <h2 class="section-title">8. Security Measures</h2>

            <p class="policy-text mt-6">
                We implement encryption, secure server infrastructure,
                role-based access control, and regular monitoring to protect data integrity.
            </p>
        </section>

        <!-- ================= USER RIGHTS REQUEST ================= -->
        <section>
            <h2 class="section-title">9. Exercising Your Rights</h2>

            <p class="policy-text mt-6">
                To exercise privacy rights, please contact us through our
                <a href="{{ route('contact') }}" class="text-indigo-600 hover:underline">
                    Contact Page
                </a>.
                We respond to verified requests within 30 days.
            </p>
        </section>

        <!-- ================= CONTACT ================= -->
        <section>
            <h2 class="section-title">10. Contact & Data Protection Officer</h2>

            <p class="policy-text mt-6">
                If you have questions regarding this Privacy Policy,
                contact FinanceAI’s Privacy Team at:
            </p>

            <p class="policy-text mt-4">
                📧 support@financeai.com  
                📍 Global Operations  
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
