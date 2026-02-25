<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';

const props = defineProps({
    resume: { type: Object, required: true },
    show: { type: Boolean, default: false }
});

const emit = defineEmits(['close', 'apply-suggestion']);
const { t, locale } = useI18n();

const score = ref(0);
const analyzing = ref(true);
const auditData = ref(null);
const appliedIds = ref(new Set());

const startAnalysis = async () => {
    analyzing.value = true;
    score.value = 0;
    
    try {
        const response = await axios.post(route('ai.audit'), {
            resume: props.resume,
            language: locale.value === 'ar' ? 'Arabic' : (locale.value === 'fr' ? 'French' : 'English')
        });
        
        auditData.value = response.data;
        score.value = response.data.score || 0;
        analyzing.value = false;
    } catch (error) {
        console.error('Audit failed:', error);
        // Fallback or error state
        analyzing.value = false;
    }
};

const handleApply = (section, id, suggestion) => {
    emit('apply-suggestion', { section, id, suggestion });
    appliedIds.value.add(id || section);
};

onMounted(() => {
    if (props.show) startAnalysis();
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-950/90 backdrop-blur-2xl animate-in fade-in duration-500" @click="emit('close')"></div>
        
        <!-- Modal -->
        <div class="relative bg-slate-900 w-full max-w-4xl rounded-[40px] border border-white/10 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-500 my-8">
            <!-- Animated Gradient Background -->
            <div class="absolute inset-x-0 -top-40 h-80 bg-indigo-500/20 blur-[100px] animate-pulse"></div>

            <div class="relative p-6 md:p-12">
                <!-- Header -->
                <div class="flex justify-between items-start mb-12">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-black text-white tracking-tighter italic uppercase">{{ t('ai_beauty_score') }}</h2>
                            <p class="text-indigo-400 text-[10px] font-black uppercase tracking-[0.3em] mt-1">{{ t('structural_audit') || 'Advanced Aesthetic & Content Audit' }}</p>
                        </div>
                    </div>
                    <button @click="emit('close')" class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white hover:bg-white/10 transition-all active:scale-95 group">
                        <svg class="w-6 h-6 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <!-- Analysis State -->
                <div v-if="analyzing" class="py-32 flex flex-col items-center gap-10">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full border-4 border-indigo-500/10 animate-ping"></div>
                        <div class="absolute inset-0 w-32 h-32 rounded-full border-t-4 border-indigo-500 animate-spin"></div>
                        <div class="absolute inset-4 rounded-full border border-indigo-500/20 flex items-center justify-center">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div class="text-center space-y-3">
                        <p class="text-2xl font-black text-white tracking-tight animate-pulse uppercase">{{ t('analyzing_geometry') }}</p>
                        <p class="text-slate-500 text-[11px] font-black uppercase tracking-[0.3em]">{{ t('elite_resumes_ref') }}</p>
                    </div>
                </div>

                <!-- Result State -->
                <div v-else-if="auditData" class="space-y-12 animate-in fade-in-up duration-1000">
                    <!-- Score Context Grid -->
                    <div class="grid lg:grid-cols-3 gap-8">
                        <!-- Score Card -->
                        <div class="lg:col-span-1 bg-white/5 p-8 rounded-[40px] border border-white/5 flex flex-col items-center justify-center text-center group">
                            <div class="relative w-40 h-40 flex items-center justify-center mb-6">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="80" cy="80" r="74" stroke="currentColor" stroke-width="10" fill="transparent" class="text-white/5" />
                                    <circle cx="80" cy="80" r="74" stroke="currentColor" stroke-width="10" fill="transparent" :stroke-dasharray="2 * Math.PI * 74" :stroke-dashoffset="(1 - score / 100) * 2 * Math.PI * 74" class="text-indigo-500 transition-all duration-1000 ease-out" />
                                </svg>
                                <span class="absolute text-5xl font-black text-white italic group-hover:scale-110 transition-transform">{{ score }}%</span>
                            </div>
                            <h3 class="text-xl font-black text-white mb-2">{{ t('excellent_vibe') }}</h3>
                            <p class="text-slate-400 text-xs font-medium leading-relaxed px-4">
                                {{ auditData.feedback || t('audit_feedback_placeholder') }}
                            </p>
                        </div>

                        <!-- Main Intelligence Panel -->
                        <div class="lg:col-span-2 space-y-6">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.4em] text-indigo-400 flex items-center gap-3">
                                <span class="w-8 h-px bg-indigo-500/30"></span>
                                {{ t('actionable_intelligence') }}
                                <span class="w-full h-px bg-indigo-500/30"></span>
                            </h4>

                            <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                                <!-- Summary Suggestion -->
                                <div v-if="auditData.sections?.summary" class="bg-white/5 p-6 rounded-[32px] border border-white/5 hover:border-indigo-500/30 transition-all group">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-purple-500/20 flex items-center justify-center text-purple-400 text-sm">✦</div>
                                            <span class="text-xs font-black text-white uppercase italic tracking-widest">{{ t('summary') }}</span>
                                        </div>
                                        <button 
                                            v-if="!appliedIds.has('summary')"
                                            @click="handleApply('summary', 'summary', auditData.sections.summary.suggestion)" 
                                            class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95"
                                        >
                                            {{ t('apply') }}
                                        </button>
                                        <span v-else class="text-emerald-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Applied
                                        </span>
                                    </div>
                                    <p class="text-slate-400 text-xs leading-relaxed font-medium mb-4 italic">"{{ auditData.sections.summary.suggestion }}"</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="tip in auditData.sections.summary.tips" :key="tip" class="px-2.5 py-1 bg-white/5 border border-white/5 rounded-lg text-[9px] text-slate-500 font-bold uppercase tracking-wider">
                                            💡 {{ tip }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Experiences -->
                                <div v-for="exp in auditData.sections?.experiences" :key="exp.id" class="bg-white/5 p-6 rounded-[32px] border border-white/5 hover:border-indigo-500/30 transition-all group">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 text-xs">💼</div>
                                            <span class="text-xs font-black text-white uppercase italic tracking-widest">{{ t('experience') }}</span>
                                        </div>
                                        <button 
                                            v-if="!appliedIds.has(exp.id)"
                                            @click="handleApply('experiences', exp.id, exp.suggestion)" 
                                            class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95"
                                        >
                                            {{ t('apply') }}
                                        </button>
                                        <span v-else class="text-emerald-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Applied
                                        </span>
                                    </div>
                                    <p class="text-slate-400 text-xs leading-relaxed font-medium mb-4 italic">"{{ exp.suggestion }}"</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="tip in exp.tips" :key="tip" class="px-2.5 py-1 bg-white/5 border border-white/5 rounded-lg text-[9px] text-slate-500 font-bold uppercase tracking-wider">
                                            ⚡ {{ tip }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Skills -->
                                <div v-if="auditData.sections?.skills" class="bg-white/5 p-6 rounded-[32px] border border-white/5 hover:border-indigo-500/30 transition-all group">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs">⚡</div>
                                            <span class="text-xs font-black text-white uppercase italic tracking-widest">{{ t('skills') }}</span>
                                        </div>
                                        <button 
                                            v-if="!appliedIds.has('skills')"
                                            @click="handleApply('skills', 'skills', auditData.sections.skills.suggestion)" 
                                            class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95"
                                        >
                                            {{ t('apply') }}
                                        </button>
                                        <span v-else class="text-emerald-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Applied
                                        </span>
                                    </div>
                                    <p class="text-slate-400 text-xs leading-relaxed font-medium mb-4 italic">"{{ auditData.sections.skills.suggestion }}"</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="tip in auditData.sections.skills.tips" :key="tip" class="px-2.5 py-1 bg-white/5 border border-white/5 rounded-lg text-[9px] text-slate-500 font-bold uppercase tracking-wider">
                                            🎯 {{ tip }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button @click="emit('close')" class="flex-1 bg-white hover:bg-slate-100 text-black py-5 rounded-[24px] font-black uppercase tracking-[0.2em] shadow-xl hover:scale-[1.02] active:scale-95 transition-all">
                           {{ t('close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
