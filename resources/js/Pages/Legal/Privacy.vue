<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Privacy Policy — Civio">
        <meta name="description" content="Civio Privacy Policy — how we collect, use, and protect your personal data in compliance with GDPR." />
    </Head>

    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white transition-colors duration-500">
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-600/10 rounded-full blur-[120px]"></div>
        </div>

        <!-- Navbar -->
        <nav class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-slate-900/80 border-b border-black/5 dark:border-white/10">
            <div class="max-w-4xl mx-auto px-6 h-14 flex items-center gap-4">
                <div class="laser-btn-wrapper scale-90 transform origin-left shadow-lg shadow-indigo-500/10 active:scale-95 transition-all">
                    <Link href="/" class="laser-btn-content px-4 py-1.5 flex items-center gap-2 text-white">
                        <span class="text-[10px] font-black uppercase tracking-[0.1em]">← Back to Home</span>
                    </Link>
                </div>
                <span class="text-slate-200 dark:text-slate-700">|</span>
                <span class="text-sm text-slate-500 font-medium">Privacy Policy</span>
            </div>
        </nav>

        <main class="relative z-10 max-w-4xl mx-auto px-6 py-16">
            <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-2">Privacy Policy</h1>
            <p class="text-slate-500 text-sm mb-10 font-bold uppercase tracking-widest opacity-80">Last updated: February 2026 · Effective: February 2026</p>

            <div class="prose prose-slate dark:prose-invert max-w-none space-y-8 text-slate-600 dark:text-slate-300 leading-relaxed">

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">1. Who We Are</h2>
                    <p>Civio ("we", "us", "our") operates the Civio web application accessible at <strong class="text-white">cvmaker.app</strong>. We are committed to protecting your personal data and complying with the General Data Protection Regulation (GDPR) and applicable data protection laws.</p>
                    <p class="mt-2">For data protection inquiries, contact us at: <strong class="text-indigo-400">privacy@cvmaker.app</strong></p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">2. What Data We Collect</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong class="text-slate-900 dark:text-white">Account data:</strong> Name, email address, password (hashed)</li>
                        <li><strong class="text-slate-900 dark:text-white">Resume content:</strong> Professional history, education, skills, and other CV data you enter</li>
                        <li><strong class="text-slate-900 dark:text-white">Usage data:</strong> Pages visited, features used, browser type, device information</li>
                        <li><strong class="text-slate-900 dark:text-white">Payment data:</strong> Billing details processed by our payment provider (Stripe). We do not store card numbers.</li>
                        <li><strong class="text-slate-900 dark:text-white">Cookie data:</strong> Session cookies required for authentication, and optional analytics cookies (only with your consent)</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">3. Legal Basis for Processing</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong class="text-slate-900 dark:text-white">Contract performance:</strong> To provide the resume builder service you signed up for</li>
                        <li><strong class="text-slate-900 dark:text-white">Legitimate interests:</strong> To improve our service, prevent fraud, and ensure security</li>
                        <li><strong class="text-slate-900 dark:text-white">Consent:</strong> For marketing emails, analytics cookies, and optional features</li>
                        <li><strong class="text-slate-900 dark:text-white">Legal obligation:</strong> To comply with applicable laws (tax records, legal requests)</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">4. How We Use Your Data</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>To create and manage your account</li>
                        <li>To generate, store, and export your resumes</li>
                        <li>To process payments for Pro subscriptions</li>
                        <li>To send transactional emails (receipts, password resets, verification)</li>
                        <li>To improve and personalize the service</li>
                        <li>To comply with legal obligations</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">5. Data Sharing</h2>
                    <p>We do not sell your personal data. We share data only with:</p>
                    <ul class="list-disc pl-5 space-y-2 mt-2">
                        <li><strong class="text-slate-900 dark:text-white">Stripe</strong> — Payment processing (PCI-DSS compliant)</li>
                        <li><strong class="text-slate-900 dark:text-white">Cloud storage provider</strong> — Secure data storage (EU servers)</li>
                        <li><strong class="text-slate-900 dark:text-white">Email service (Mailgun/Postmark)</strong> — Transactional email delivery</li>
                        <li><strong class="text-slate-900 dark:text-white">Law enforcement</strong> — When legally required</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">6. Data Retention</h2>
                    <p>We retain your data for as long as your account is active. If you delete your account, we delete your personal data within 30 days, except where we are required by law to retain it longer (e.g., financial records for 7 years).</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">7. Your Rights (GDPR)</h2>
                    <p>Under GDPR, you have the following rights:</p>
                    <ul class="list-disc pl-5 space-y-2 mt-2">
                        <li><strong class="text-slate-900 dark:text-white">Right of access</strong> — Request a copy of your data</li>
                        <li><strong class="text-slate-900 dark:text-white">Right to rectification</strong> — Correct inaccurate data</li>
                        <li><strong class="text-slate-900 dark:text-white">Right to erasure</strong> — Request deletion of your data ("right to be forgotten")</li>
                        <li><strong class="text-slate-900 dark:text-white">Right to portability</strong> — Receive your data in a machine-readable format</li>
                        <li><strong class="text-slate-900 dark:text-white">Right to restrict processing</strong> — Limit how we use your data</li>
                        <li><strong class="text-slate-900 dark:text-white">Right to object</strong> — Object to processing based on legitimate interests</li>
                        <li><strong class="text-slate-900 dark:text-white">Right to withdraw consent</strong> — Withdraw consent at any time for consent-based processing</li>
                    </ul>
                    <p class="mt-3">To exercise any right, email us at <strong class="text-indigo-400">privacy@cvmaker.app</strong>. We respond within 30 days.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">8. Cookies</h2>
                    <p>We use strictly necessary cookies for authentication and optional analytics cookies (only with your consent). See our <Link :href="route('about')" class="text-indigo-400 hover:text-indigo-300">Cookies Policy</Link> for full details.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">9. Security</h2>
                    <p>We implement industry-standard security measures including HTTPS encryption, password hashing (bcrypt), and regular security audits. However, no online service is 100% secure.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">10. Changes to This Policy</h2>
                    <p>We may update this policy. We will notify you by email or prominent notice at least 30 days before material changes take effect.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">11. Contact & Complaints</h2>
                    <p>Questions? Email <strong class="text-indigo-400">privacy@cvmaker.app</strong>.</p>
                    <p class="mt-2">If you are unhappy with how we handle your data, you have the right to lodge a complaint with your local data protection authority (e.g., CNIL in France, AEPD in Spain, BfDI in Germany).</p>
                </section>
            </div>
        </main>

        <footer class="border-t border-black/5 dark:border-white/10 py-6 px-6 text-center text-sm text-slate-500 relative z-10 font-medium">
            © {{ new Date().getFullYear() }} Civio ·
            <Link :href="route('terms')" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Terms</Link> ·
            <Link :href="route('about')" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Cookies</Link>
        </footer>
    </div>
</template>
