<script setup>
import { onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import ResumePreview from '@/Components/ResumePreview.vue';

const props = defineProps({
    resume: { type: Object, required: true }
});

onMounted(() => {
    // Wait for everything to render perfectly before printing
    setTimeout(() => {
        window.print();
        // Optional: Close or go back after printing
        // window.onafterprint = () => window.history.back();
    }, 1000);
});
</script>

<template>
    <div class="print-container">
        <Head :title="resume.first_name + ' ' + resume.last_name + ' - Resume'" />
        
        <div class="a4-wrapper">
            <ResumePreview :resume="resume" />
        </div>

        <!-- Print controls overlay (hidden during print) -->
        <div class="no-print print-overlay">
            <div class="flex items-center gap-4 bg-slate-900/90 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/10 shadow-2xl">
                <div class="flex flex-col">
                    <span class="text-white font-bold text-sm">Print Preview Mode</span>
                    <span class="text-slate-400 text-xs">A4 High Fidelity Rendering</span>
                </div>
                <div class="h-8 w-px bg-white/10 mx-2"></div>
                <button @click="window.print()" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg font-bold text-xs transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print / Save PDF
                </button>
                <button @click="window.history.back()" class="text-slate-400 hover:text-white px-4 py-2 rounded-lg font-bold text-xs transition-all">
                    Back to Editor
                </button>
            </div>
        </div>
    </div>
</template>

<style>
/* Reset for print */
@page {
    size: A4 portrait;
    margin: 0;
}

body {
    background: #f1f5f9;
    margin: 0;
    padding: 0;
}

.print-container {
    display: flex;
    justify-content: center;
    background: #f1f5f9;
    min-height: 100vh;
}

.a4-wrapper {
    width: 210mm;
    min-height: 297mm;
    background: white;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.print-overlay {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 100;
}

@media print {
    .no-print {
        display: none !important;
    }
    
    body, .print-container {
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .a4-wrapper {
        width: 100% !important;
        height: 100% !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Force background colors/images to print */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
