<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="404 — Page Not Found" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white flex items-center justify-center px-6 transition-colors duration-500">
        <!-- Background glows -->
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/15 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-purple-600/15 rounded-full blur-[140px]"></div>
        </div>

        <div class="relative z-10 text-center max-w-lg">
            <!-- Big 404 -->
            <div class="text-[10rem] font-extrabold leading-none text-transparent bg-clip-text bg-gradient-to-b from-indigo-400 to-purple-600 select-none mb-2">
                404
            </div>

            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">Page not found</h1>
            <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-medium">
                Oops! The page you're looking for doesn't exist or has been moved.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <div class="laser-btn-wrapper shadow-xl shadow-indigo-500/30 active:scale-95 transition-transform hover:scale-105">
                    <Link href="/" class="laser-btn-content px-8 py-3 text-white flex items-center gap-2">
                        <span class="text-sm font-black uppercase tracking-[0.1em]">← Back to Home</span>
                    </Link>
                </div>
                <Link
                    :href="route('dashboard')"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 border border-black/5 dark:border-white/20 text-slate-900 dark:text-white font-bold rounded-full text-sm transition-all shadow-sm"
                >
                    Go to Dashboard
                </Link>
            </div>

            <!-- Fun graphic -->
            <div class="mt-12 text-6xl animate-bounce">
                🔍
            </div>
        </div>
    </div>
</template>
