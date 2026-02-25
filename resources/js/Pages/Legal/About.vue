<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('about_us')" />

    <GuestLayout>
        <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen transition-colors duration-500">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800/40 backdrop-blur-xl border border-black/5 dark:border-white/10 overflow-hidden shadow-sm sm:rounded-[24px] p-8">
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-6 uppercase tracking-tight">{{ t('about_us') }}</h1>
                    <div class="prose prose-slate dark:prose-invert max-w-none">
                        <p class="text-slate-600 dark:text-slate-300 mb-6 text-lg leading-relaxed">
                            We are a passionate team dedicated to helping people build professional resumes effortlessly. Our goal is to provide a simple, powerful tool that empowers job seekers in Yemen, France, and around the world.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 text-lg leading-relaxed">
                            Our platform combines user-friendly design with advanced AI technology to ensure your resume stands out.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
