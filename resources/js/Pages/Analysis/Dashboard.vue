<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useI18n } from 'vue-i18n';
import { useNotify } from '@/composables/useNotify';

const { t } = useI18n();
const notify = useNotify();

const form = useForm({
    resume_text: '',
});

const analysis = ref(null);
const isAnalyzing = ref(false);
const isExtracting = ref(false);
const fileInput = ref(null);

const handleFileUpload = async (e) => {
    const file = e.target.files[0];
    if (!file || file.type !== 'application/pdf') return;
    
    isExtracting.value = true;
    try {
        const reader = new FileReader();
        reader.onload = async () => {
            const typedarray = new Uint8Array(reader.result);
            
            // Load PDF.js from CDN dynamically if not available
            if (!window.pdfjsLib) {
                await new Promise((resolve) => {
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
                    script.onload = resolve;
                    document.head.appendChild(script);
                });
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            }
            
            const pdf = await window.pdfjsLib.getDocument(typedarray).promise;
            let fullText = '';
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const textContent = await page.getTextContent();
                fullText += textContent.items.map(s => s.str).join(' ') + '\n';
            }
            form.resume_text = fullText;
        };
        reader.readAsArrayBuffer(file);
    } catch (err) {
        console.error('PDF Extraction Error:', err);
        notify.error('Failed to extract text from PDF');
    } finally {
        isExtracting.value = false;
    }
};

const startAnalysis = async () => {
    isAnalyzing.value = true;
    analysis.value = null;
    
    try {
        const response = await axios.post(route('analysis.analyze'), {
            resume_text: form.resume_text
        });
        analysis.value = response.data;
    } catch (e) {
        console.error(e);
        notify.error(t('analysis_failed'));
    } finally {
        isAnalyzing.value = false;
    }
};
</script>

<template>
    <Head :title="t('ai_resume_analysis')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-900 dark:text-white leading-tight uppercase tracking-widest">{{ t('ai_resume_analysis') }}</h2>
        </template>

        <div class="py-12 bg-white dark:bg-slate-950 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Input Section -->
                <div v-if="!analysis" class="max-w-4xl mx-auto">
                    <div class="bg-slate-50 dark:bg-slate-900/50 backdrop-blur-3xl border border-black/5 dark:border-white/10 rounded-[2.5rem] p-10 shadow-xl">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-14 h-14 bg-indigo-500/10 rounded-2xl flex items-center justify-center border border-indigo-500/20">
                                <span class="text-3xl text-indigo-400">🤖</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ t('ai_auditor') }}</h3>
                                <p class="text-slate-500 dark:text-slate-400 font-medium">{{ t('auditor_paste_desc') }}</p>
                            </div>
                        </div>

                        <!-- PDF Upload Area -->
                        <div 
                            @click="fileInput.click()" 
                            @dragover.prevent 
                            @drop.prevent="handleFileUpload"
                            class="mb-8 border-2 border-dashed border-white/10 hover:border-indigo-500/50 rounded-3xl p-10 flex flex-col items-center justify-center cursor-pointer transition-all bg-white/5 group"
                        >
                            <input type="file" ref="fileInput" @change="handleFileUpload" accept=".pdf" class="hidden" />
                            <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform mb-4">
                                <svg class="w-8 h-8 text-slate-400 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <p class="text-slate-300 font-bold uppercase tracking-widest text-xs">{{ isExtracting ? t('extracting_text') : t('drag_drop_pdf') }}</p>
                            <p class="text-slate-500 text-[10px] mt-2 italic uppercase">Supports PDF only</p>
                        </div>

                        <div class="relative">
                            <textarea 
                                v-model="form.resume_text"
                                rows="12"
                                class="w-full bg-slate-950/50 border-white/5 rounded-2xl p-6 text-slate-300 font-medium placeholder:text-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all mb-8"
                                :placeholder="t('auditor_placeholder')"
                            ></textarea>
                            <div v-if="isExtracting" class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm rounded-2xl flex items-center justify-center z-10">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                                    <span class="text-xs font-black uppercase tracking-[0.2em] text-indigo-400">{{ t('extracting_text') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <button 
                                @click="startAnalysis"
                                :disabled="isAnalyzing || !form.resume_text"
                                class="px-12 py-4 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white rounded-2xl font-black uppercase tracking-[0.2em] transition-all active:scale-95 shadow-2xl shadow-indigo-500/20 flex items-center gap-3"
                            >
                                <span v-if="isAnalyzing">{{ t('auditing_loading') }}</span>
                                <span v-else>{{ t('start_analysis') }}</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div v-else class="animate-in fade-in slide-in-from-bottom-8 duration-700">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <!-- Left: Score Card -->
                        <div class="bg-slate-50 dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-[2.5rem] p-10 flex flex-col items-center justify-center text-center shadow-lg">
                            <div class="relative w-48 h-48 mb-6">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-200 dark:text-slate-800" />
                                    <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="12" fill="transparent" :stroke-dasharray="2 * Math.PI * 80" :stroke-dashoffset="2 * Math.PI * 80 * (1 - analysis.score / 100)" class="text-indigo-500 transition-all duration-1000" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-6xl font-black text-slate-900 dark:text-white">{{ analysis.score }}</span>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ t('ats_score') }}</span>
                                </div>
                            </div>
                            <h4 class="text-xl font-bold uppercase tracking-tight mb-2 text-slate-900 dark:text-white">{{ t('resume_rating') }}</h4>
                            <p class="text-slate-600 dark:text-slate-400 text-sm font-medium">{{ analysis.summary }}</p>
                            
                            <button @click="analysis = null" class="mt-8 text-[10px] font-black uppercase tracking-widest text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">{{ t('analyze_another') }} →</button>
                        </div>

                        <!-- Right: Feedback -->
                        <div class="lg:col-span-2 space-y-8">
                            <!-- Actionable Advice -->
                            <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-[2rem] p-8">
                                <h4 class="text-lg font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-4 flex items-center gap-2">
                                    <span>💡</span> {{ t('key_improvements') }}
                                </h4>
                                <p class="text-slate-700 dark:text-slate-200 font-medium leading-relaxed">{{ analysis.improvements }}</p>
                            </div>

                            <div class="grid md:grid-cols-2 gap-8">
                                <div class="bg-emerald-500/5 border border-emerald-500/20 rounded-[2rem] p-8">
                                    <h5 class="text-sm font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-6 flex items-center gap-2">
                                        <span>✓</span> {{ t('strengths') }}
                                    </h5>
                                    <ul class="space-y-4">
                                        <li v-for="s in analysis.strengths" :key="s" class="text-sm text-slate-600 dark:text-slate-300 font-medium flex gap-3">
                                            <span class="text-emerald-500">•</span> {{ s }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="bg-rose-500/5 border border-rose-500/20 rounded-[2rem] p-8">
                                    <h5 class="text-sm font-black uppercase tracking-widest text-rose-600 dark:text-rose-400 mb-6 flex items-center gap-2">
                                        <span>✕</span> {{ t('critical_gaps') }}
                                    </h5>
                                    <ul class="space-y-4">
                                        <li v-for="w in analysis.weaknesses" :key="w" class="text-sm text-slate-600 dark:text-slate-300 font-medium flex gap-3">
                                            <span class="text-rose-500">•</span> {{ w }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
