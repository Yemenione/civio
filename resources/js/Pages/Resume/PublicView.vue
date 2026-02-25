<script setup>
import { Head } from '@inertiajs/vue3';
import ResumePreview from '@/Components/ResumePreview.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    resume: Object,
});

// Check if we are in PDF mode
const isPdf = new URLSearchParams(window.location.search).get('pdf') === '1';
</script>

<template>
    <Head :title="resume.title" />

    <div :class="['min-h-screen flex flex-col items-center px-4 sm:px-6', isPdf ? 'bg-white p-0' : 'bg-slate-100 py-12']">
        <!-- Badge -->
        <div v-if="!isPdf" class="mb-8 flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm border border-slate-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-bold text-slate-600 uppercase tracking-widest">Live Portfolio</span>
        </div>

        <div :class="['w-full max-w-[850px] bg-white overflow-hidden', isPdf ? '' : 'shadow-2xl shadow-indigo-500/10 rounded-xl']">
            <ResumePreview :resume="resume" />
        </div>

        <div v-if="!isPdf" class="mt-12 text-center text-slate-400 text-xs">
            Powered by <span class="font-bold text-indigo-500">Civio AI</span> • © {{ new Date().getFullYear() }}
        </div>
    </div>
</template>

<style>
/* Ensure the public view looks premium */
body {
    background-color: #f8fafc;
}
</style>
