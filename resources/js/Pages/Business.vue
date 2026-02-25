<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Head title="Civio for Business" />

    <div class="min-h-screen bg-white dark:bg-slate-950 text-slate-900 dark:text-white selection:bg-indigo-500 overflow-x-hidden font-sans transition-colors duration-300">
        <!-- Hero Section -->
        <header class="relative pt-32 pb-20 px-6">
            <div class="max-w-7xl mx-auto flex flex-col items-center text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold uppercase tracking-widest mb-8 animate-fade-in">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    Civio for Teams
                </div>
                <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6 leading-[1.1] text-slate-900 dark:text-white">
                    Supercharge your <span class="premium-gradient-text">Talent Branding</span>.
                </h1>
                <p class="max-w-2xl text-slate-600 dark:text-slate-400 text-lg md:text-xl leading-relaxed mb-10 font-bold">
                    Empower your employees with professional, high-converting resumes. Managed centrally, branded perfectly.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <Link :href="route('register')" class="px-10 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-indigo-500/20 uppercase tracking-widest text-sm shimmer-effect">
                        Start Business Trial
                    </Link>
                    <a href="#contact" class="px-10 py-4 bg-white/5 hover:bg-white/10 text-white font-black rounded-2xl transition-all border border-white/10 uppercase tracking-widest text-sm">
                        Speak to Sales
                    </a>
                </div>
            </div>
        </header>

        <!-- Features Grid -->
        <section class="py-24 px-6 border-t border-black/5 dark:border-white/5 bg-slate-50 dark:bg-slate-900/50">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="p-8 rounded-3xl bg-white dark:bg-white/5 border border-black/5 dark:border-white/10 hover:border-indigo-500/30 transition-all group shadow-sm hover:shadow-xl">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Team Management</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-medium">Centrally manage user access, templates, and billing for your entire team or agency.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white dark:bg-white/5 border border-black/5 dark:border-white/10 hover:border-purple-500/30 transition-all group shadow-sm hover:shadow-xl">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Brand Integrity</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-medium">Ensure every CV leaving your company follows the same high-quality visual standards.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white dark:bg-white/5 border border-black/5 dark:border-white/10 hover:border-emerald-500/30 transition-all group shadow-sm hover:shadow-xl">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Developer API</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-medium">Integrate our AI resume analysis and generation tools into your own ATS or platform.</p>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.premium-gradient-text {
    background: linear-gradient(to right, #6366f1, #a855f7, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
