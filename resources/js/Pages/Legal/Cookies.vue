<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Cookies Policy — Civio">
        <meta name="description" content="Civio Cookies Policy — what cookies we use, why, and how to control them." />
    </Head>

    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white transition-colors duration-500">
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-teal-600/10 rounded-full blur-[120px]"></div>
        </div>

        <nav class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-slate-900/80 border-b border-black/5 dark:border-white/10">
            <div class="max-w-4xl mx-auto px-6 h-14 flex items-center gap-4">
                <div class="laser-btn-wrapper scale-90 transform origin-left shadow-lg shadow-teal-500/10 active:scale-95 transition-all">
                    <Link href="/" class="laser-btn-content px-4 py-1.5 flex items-center gap-2 text-white">
                        <span class="text-[10px] font-black uppercase tracking-[0.1em]">← Back to Home</span>
                    </Link>
                </div>
                <span class="text-slate-200 dark:text-slate-700">|</span>
                <span class="text-sm text-slate-500 font-medium">Cookies Policy</span>
            </div>
        </nav>

        <main class="relative z-10 max-w-4xl mx-auto px-6 py-16">
            <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-2">Cookies Policy</h1>
            <p class="text-slate-500 text-sm mb-10 font-bold uppercase tracking-widest opacity-80">Last updated: February 2026</p>

            <div class="space-y-8 text-slate-600 dark:text-slate-300 leading-relaxed">

                <section class="p-5 rounded-2xl bg-teal-500/10 border border-teal-500/30">
                    <p class="text-teal-300 text-sm">🍪 We use cookies to make Civio work properly and to improve your experience. You can control optional cookies via our cookie banner at the bottom of the page.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">1. What Are Cookies?</h2>
                    <p>Cookies are small text files stored on your device when you visit a website. They help us remember your preferences, keep you logged in, and understand how you use our service.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">2. Cookies We Use</h2>

                    <div class="space-y-4 mt-3">
                        <div class="p-4 rounded-xl bg-white dark:bg-white/5 border border-black/5 dark:border-white/10 shadow-sm">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full bg-green-500/20 text-green-600 dark:text-green-400 text-[10px] font-black uppercase">Essential</span>
                                <span class="text-slate-900 dark:text-white font-bold text-sm">Always Active</span>
                            </div>
                            <table class="w-full text-sm">
                                <thead><tr class="text-slate-500 text-left"><th class="pb-2 font-medium">Cookie</th><th class="pb-2 font-medium">Purpose</th><th class="pb-2 font-medium">Duration</th></tr></thead>
                                <tbody class="text-slate-400 space-y-1">
                                    <tr><td class="py-1 font-mono text-xs">laravel_session</td><td class="py-1">Keeps you logged in</td><td class="py-1">2 hours</td></tr>
                                    <tr><td class="py-1 font-mono text-xs">XSRF-TOKEN</td><td class="py-1">Security protection (CSRF)</td><td class="py-1">Session</td></tr>
                                    <tr><td class="py-1 font-mono text-xs">cookie_consent</td><td class="py-1">Stores your cookie preferences</td><td class="py-1">365 days</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 rounded-xl bg-white dark:bg-white/5 border border-black/5 dark:border-white/10 shadow-sm">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase">Analytics</span>
                                <span class="text-slate-900 dark:text-white font-bold text-sm">Optional (Consent Required)</span>
                            </div>
                            <p class="text-slate-400 text-sm">We use privacy-friendly analytics (no personal data, no cross-site tracking) to understand how users interact with the site. These cookies are only set after you accept analytics cookies in our banner.</p>
                        </div>

                        <div class="p-4 rounded-xl bg-white dark:bg-white/5 border border-black/5 dark:border-white/10 shadow-sm">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase">Functional</span>
                                <span class="text-slate-900 dark:text-white font-bold text-sm">Preference Cookies</span>
                            </div>
                            <table class="w-full text-sm">
                                <thead><tr class="text-slate-500 text-left"><th class="pb-2 font-medium">Cookie</th><th class="pb-2 font-medium">Purpose</th><th class="pb-2 font-medium">Duration</th></tr></thead>
                                <tbody class="text-slate-400">
                                    <tr><td class="py-1 font-mono text-xs">locale</td><td class="py-1">Remembers your language choice</td><td class="py-1">1 year</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">3. Managing Cookies</h2>
                    <p>You can control cookies in several ways:</p>
                    <ul class="list-disc pl-5 space-y-2 mt-2">
                        <li><strong class="text-white">Cookie banner:</strong> Use the banner at the bottom of the page to accept or reject optional cookies.</li>
                        <li><strong class="text-white">Browser settings:</strong> Most browsers let you block or delete cookies. Note: blocking essential cookies will break the login functionality.</li>
                        <li><strong class="text-slate-900 dark:text-white">Email us:</strong> Contact <strong class="text-indigo-400">privacy@cvmaker.app</strong> to request data deletion related to cookies.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">4. Third-Party Cookies</h2>
                    <p>We use Stripe for payment processing, which may set cookies for fraud prevention. See <a href="https://stripe.com/privacy" target="_blank" class="text-indigo-400 hover:text-indigo-300">Stripe's Privacy Policy</a> for details.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">5. Contact</h2>
                    <p>Questions? Email <strong class="text-indigo-400">privacy@cvmaker.app</strong></p>
                </section>
            </div>
        </main>

        <footer class="border-t border-black/5 dark:border-white/10 py-6 px-6 text-center text-sm text-slate-500 relative z-10 font-medium">
            © {{ new Date().getFullYear() }} Civio ·
            <Link :href="route('privacy')" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Privacy</Link> ·
            <Link :href="route('terms')" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Terms</Link>
        </footer>
    </div>
</template>
