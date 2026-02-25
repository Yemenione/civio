<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Terms of Service — Civio">
        <meta name="description" content="Civio Terms of Service — your rights and obligations when using our resume builder platform." />
    </Head>

    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white transition-colors duration-500">
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/10 rounded-full blur-[120px]"></div>
        </div>

        <nav class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-slate-900/80 border-b border-black/5 dark:border-white/10">
            <div class="max-w-4xl mx-auto px-6 h-14 flex items-center gap-4">
                <div class="laser-btn-wrapper scale-90 transform origin-left shadow-lg shadow-purple-500/10 active:scale-95 transition-all">
                    <Link href="/" class="laser-btn-content px-4 py-1.5 flex items-center gap-2 text-white">
                        <span class="text-[10px] font-black uppercase tracking-[0.1em]">← Back to Home</span>
                    </Link>
                </div>
                <span class="text-slate-200 dark:text-slate-700">|</span>
                <span class="text-sm text-slate-500 font-medium">Terms of Service</span>
            </div>
        </nav>

        <main class="relative z-10 max-w-4xl mx-auto px-6 py-16">
            <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-2">Terms of Service</h1>
            <p class="text-slate-500 text-sm mb-10 font-bold uppercase tracking-widest opacity-80">Last updated: February 2026</p>

            <div class="space-y-8 text-slate-600 dark:text-slate-300 leading-relaxed">

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">1. Acceptance of Terms</h2>
                    <p>By accessing or using Civio ("Service"), you agree to be bound by these Terms of Service. If you disagree with any part of the terms, you may not use our Service.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">2. Description of Service</h2>
                    <p>Civio provides an online resume/CV builder with AI-powered assistance, multiple templates, PDF export, and multi-language support. The Service is available on a Free and Pro subscription basis.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">3. Account Registration</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>You must provide accurate and complete information when creating an account.</li>
                        <li>You are responsible for maintaining the security of your password.</li>
                        <li>You must be at least 16 years old to use this Service (18 in some jurisdictions).</li>
                        <li>One person may not maintain more than one free account.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">4. Free and Pro Plans</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong class="text-slate-900 dark:text-white">Free plan:</strong> Up to 3 resumes, 2 templates, PDF export — no time limit.</li>
                        <li><strong class="text-slate-900 dark:text-white">Pro plan:</strong> Unlimited resumes, all 6 templates, AI assistant, ATS checker, cover letter builder, priority support — billed monthly or annually.</li>
                        <li>Pro subscriptions renew automatically. You may cancel at any time.</li>
                        <li>Refunds are provided within 14 days of purchase for annual plans.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">5. Your Content</h2>
                    <p>You retain full ownership of the resume content you create. By using the Service, you grant us a limited license to store and process your content solely to provide the Service. We will never sell or share your resume data.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">6. Prohibited Uses</h2>
                    <p>You may not use the Service to:</p>
                    <ul class="list-disc pl-5 space-y-2 mt-2">
                        <li>Create false or misleading resume content</li>
                        <li>Attempt to gain unauthorized access to any part of the Service</li>
                        <li>Use automated scripts or bots to access the Service</li>
                        <li>Engage in any illegal activity</li>
                        <li>Violate third-party intellectual property rights</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">7. Intellectual Property</h2>
                    <p>The Service, including its logo, design, templates, and AI-generated content suggestions, is owned by Civio and protected by copyright law. You may not copy, modify, or distribute any part of the Service without prior written permission.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">8. Disclaimer of Warranties</h2>
                    <p>The Service is provided "as is" without warranty of any kind. We do not guarantee that the Service will be uninterrupted, error-free, or that resumes created will result in employment.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">9. Limitation of Liability</h2>
                    <p>To the maximum extent permitted by law, Civio shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the Service.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">10. Governing Law</h2>
                    <p>These terms shall be governed by the laws of the European Union and applicable member state law. Disputes shall be resolved in the competent courts of the EU.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">11. Changes to Terms</h2>
                    <p>We reserve the right to modify these terms at any time. We will provide at least 30 days' notice via email for material changes. Continued use of the Service after changes constitutes acceptance.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3">12. Contact</h2>
                    <p>Questions about these Terms? Contact us at <strong class="text-indigo-400">legal@cvmaker.app</strong></p>
                </section>
            </div>
        </main>

        <footer class="border-t border-black/5 dark:border-white/10 py-6 px-6 text-center text-sm text-slate-500 relative z-10 font-medium">
            © {{ new Date().getFullYear() }} Civio ·
            <Link :href="route('privacy')" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Privacy</Link> ·
            <Link :href="route('about')" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Cookies</Link>
        </footer>
    </div>
</template>
