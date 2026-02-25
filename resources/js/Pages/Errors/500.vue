<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    status: Number,
    message: String,
});
</script>

<template>
    <Head title="500 — Server Error" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white flex items-center justify-center px-6 transition-colors duration-500">
        <!-- Background glows -->
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-red-600/10 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-orange-600/10 rounded-full blur-[140px]"></div>
        </div>

        <div class="relative z-10 text-center max-w-lg">
            <div class="text-[10rem] font-extrabold leading-none text-transparent bg-clip-text bg-gradient-to-b from-red-400 to-orange-500 select-none mb-2">
                {{ status || 500 }}
            </div>

            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">Internal Server Error</h1>
            <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-medium">
                Something went wrong on our end. Our team has been notified. Please try again in a moment.
            </p>

            <div v-if="message" class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-sm text-red-400 text-left font-mono">
                {{ message }}
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <div class="laser-btn-wrapper laser-red shadow-xl shadow-red-500/20 active:scale-95 transition-transform hover:scale-105">
                    <Link href="/" class="laser-btn-content px-8 py-3 text-white flex items-center gap-2">
                        <span class="text-sm font-black uppercase tracking-[0.1em]">← Back to Safety</span>
                    </Link>
                </div>
                <button
                    onclick="window.location.reload()"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 border border-black/5 dark:border-white/20 text-slate-900 dark:text-white font-bold rounded-full text-sm transition-all shadow-sm"
                >
                    Try again ↺
                </button>
            </div>

            <div class="mt-12 text-6xl">
                ⚠️
            </div>
        </div>
    </div>
</template>
