<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('contact_us')" />

    <GuestLayout>
        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen transition-colors duration-500">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800/40 backdrop-blur-xl border border-black/5 dark:border-white/10 overflow-hidden shadow-sm sm:rounded-[24px] p-8">
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-6 uppercase tracking-tight">{{ t('contact_us') }}</h1>
                    <div class="prose prose-slate dark:prose-invert max-w-none">
                        <p class="text-slate-600 dark:text-slate-300 mb-6 text-lg leading-relaxed">
                            Have questions or feedback? We'd love to hear from you!
                        </p>
                        <div class="bg-indigo-50 dark:bg-white/5 p-6 rounded-2xl border border-indigo-500/10 transition-all duration-500 hover:border-indigo-500/30">
                            <p class="text-slate-700 dark:text-slate-300 font-bold tracking-tight mb-2 flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                Email: <span class="text-indigo-600 dark:text-indigo-400">support@cvbuilder.com</span>
                            </p>
                            <p class="text-slate-700 dark:text-slate-300 font-bold tracking-tight mb-2 flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                Phone: <span class="text-indigo-600 dark:text-indigo-400">+123 456 7890</span>
                            </p>
                            <p class="text-slate-700 dark:text-slate-300 font-bold tracking-tight flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Address: <span class="text-indigo-600 dark:text-indigo-400">123 Resume St, Job City</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
