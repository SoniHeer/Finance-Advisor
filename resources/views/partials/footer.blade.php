<footer class="mt-32 bg-white border-t border-slate-200">

    {{-- TOP SECTION --}}
    <div class="max-w-7xl mx-auto px-6 py-20">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-14">

            {{-- BRAND --}}
            <div class="lg:col-span-2">

                <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    Finance<span class="text-indigo-600">AI</span>
                </h3>

                <p class="mt-6 text-sm text-slate-500 leading-relaxed max-w-sm">
                    Enterprise-grade financial intelligence platform built for
                    families, professionals, and modern wealth builders.
                    Smart analytics. Predictive insights. Total clarity.
                </p>

                {{-- SOCIAL --}}
                <div class="mt-6 flex gap-4 text-slate-400 text-lg">

                    <a href="#"
                       class="h-10 w-10 flex items-center justify-center
                              rounded-full bg-slate-100
                              hover:bg-indigo-600 hover:text-white
                              transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 5.92a8.3 8.3 0 01-2.36.65 4.1 4.1 0 001.8-2.27 8.2 8.2 0 01-2.6 1A4.1 4.1 0 0016 4a4.1 4.1 0 00-4.1 4.1c0 .32.04.64.1.94A11.65 11.65 0 013 5.15a4.1 4.1 0 001.27 5.47 4.1 4.1 0 01-1.86-.52v.05a4.1 4.1 0 003.3 4.02 4.1 4.1 0 01-1.85.07 4.1 4.1 0 003.83 2.84A8.23 8.23 0 012 18.58a11.62 11.62 0 006.29 1.84c7.55 0 11.68-6.25 11.68-11.68v-.53A8.18 8.18 0 0022 5.92z"/>
                        </svg>
                    </a>

                    <a href="#"
                       class="h-10 w-10 flex items-center justify-center
                              rounded-full bg-slate-100
                              hover:bg-indigo-600 hover:text-white
                              transition">
                        in
                    </a>

                    <a href="#"
                       class="h-10 w-10 flex items-center justify-center
                              rounded-full bg-slate-100
                              hover:bg-indigo-600 hover:text-white
                              transition">
                        GH
                    </a>

                </div>
            </div>


            {{-- PRODUCT --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-800 mb-6">
                    Product
                </h4>

                <ul class="space-y-3 text-sm text-slate-600">

                    @if(Route::has('features'))
                        <li>
                            <a href="{{ route('features') }}"
                               class="hover:text-indigo-600 transition">
                                Features
                            </a>
                        </li>
                    @endif

                    @if(Route::has('pricing'))
                        <li>
                            <a href="{{ route('pricing') }}"
                               class="hover:text-indigo-600 transition">
                                Pricing
                            </a>
                        </li>
                    @endif

                    @if(Route::has('user.reports.index'))
                        <li>
                            <a href="{{ route('user.reports.index') }}"
                               class="hover:text-indigo-600 transition">
                                Reports
                            </a>
                        </li>
                    @endif

                </ul>
            </div>


            {{-- COMPANY --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-800 mb-6">
                    Company
                </h4>

                <ul class="space-y-3 text-sm text-slate-600">

                    @if(Route::has('about'))
                        <li>
                            <a href="{{ route('about') }}"
                               class="hover:text-indigo-600 transition">
                                About
                            </a>
                        </li>
                    @endif

                    @if(Route::has('contact'))
                        <li>
                            <a href="{{ route('contact') }}"
                               class="hover:text-indigo-600 transition">
                                Contact
                            </a>
                        </li>
                    @endif

                </ul>
            </div>


            {{-- LEGAL --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-800 mb-6">
                    Legal
                </h4>

                <ul class="space-y-3 text-sm text-slate-600">

                    @if(Route::has('privacy'))
                        <li>
                            <a href="{{ route('privacy') }}"
                               class="hover:text-indigo-600 transition">
                                Privacy Policy
                            </a>
                        </li>
                    @endif

                    @if(Route::has('terms'))
                        <li>
                            <a href="{{ route('terms') }}"
                               class="hover:text-indigo-600 transition">
                                Terms of Service
                            </a>
                        </li>
                    @endif

                </ul>
            </div>

        </div>
    </div>


    {{-- NEWSLETTER STRIP --}}
    <div class="border-t border-slate-200 bg-slate-50">

        <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row items-center justify-between gap-6">

            <div>
                <h5 class="text-lg font-bold text-slate-900">
                    Stay ahead financially
                </h5>
                <p class="text-sm text-slate-500 mt-1">
                    Get monthly financial insights and AI-driven tips.
                </p>
            </div>

            <form method="POST" action="#" class="flex w-full md:w-auto gap-3">
                @csrf

                <input type="email"
                       placeholder="Enter your email"
                       required
                       class="w-full md:w-72 px-4 py-2.5 rounded-xl border
                              border-slate-300
                              bg-white
                              text-sm focus:ring-2 focus:ring-indigo-500 outline-none">

                <button type="submit"
                        class="px-6 py-2.5 rounded-xl
                               bg-indigo-600 text-white text-sm font-semibold
                               hover:bg-indigo-700 transition">
                    Subscribe
                </button>

            </form>

        </div>
    </div>


    {{-- BOTTOM STRIP --}}
    <div class="border-t border-slate-200">

        <div class="max-w-7xl mx-auto px-6 py-6
                    flex flex-col md:flex-row items-center justify-between
                    text-xs text-slate-500">

            <p>
                © {{ now()->year }} FinanceAI. All rights reserved.
            </p>

            <p class="mt-3 md:mt-0">
                Built with precision • Powered by Artificial Intelligence
            </p>

        </div>
    </div>

</footer>
