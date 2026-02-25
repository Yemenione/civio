<script setup>
import { ref, onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';

const props = defineProps({
    resume: { type: Object, required: true },
    active: { type: Boolean, default: false }
});

const emit = defineEmits(['apply-suggestion']);
const { t, locale } = useI18n();

const score = ref(0);
const analyzing = ref(false);
const auditData = ref(null);
const appliedIds = ref(new Set());

const startAnalysis = async () => {
    analyzing.value = true;
    score.value = 0;
    auditData.value = null;
    
    try {
        const response = await axios.post(route('ai.audit'), {
            resume: props.resume,
            language: locale.value === 'ar' ? 'Arabic' : (locale.value === 'fr' ? 'French' : 'English')
        });
        
        auditData.value = response.data;
        score.value = response.data.score || 0;
    } catch (error) {
        console.error('Audit failed:', error);
    } finally {
        analyzing.value = false;
    }
};

const handleApply = (section, id, suggestion) => {
    emit('apply-suggestion', { section, id, suggestion });
    appliedIds.value.add(id || section);
};

watch(() => props.active, (newVal) => {
    if (newVal && !auditData.value) startAnalysis();
});

onMounted(() => {
    if (props.active) startAnalysis();
});
</script>

<template>
    <div class="h-full flex flex-col bg-[#0f172a]/95 backdrop-blur-3xl overflow-hidden border-l border-white/5">
        <!-- Header Section -->
        <div class="p-8 border-b border-white/5 bg-gradient-to-b from-indigo-500/10 to-transparent">
            <div class="flex items-center gap-5 mb-8">
                <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-indigo-500/40 transform -rotate-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white tracking-tighter uppercase italic leading-tight">Civio Audit</h2>
                    <p class="text-indigo-400 text-[10px] font-black uppercase tracking-[0.3em] mt-1">Advanced Resume Intelligence</p>
                </div>
            </div>

            <!-- Main Score Visualization -->
            <div v-if="!analyzing && auditData" class="grid grid-cols-2 gap-4">
                <div class="bg-white/5 p-5 rounded-[24px] border border-white/5 flex flex-col items-center justify-center text-center">
                    <div class="relative w-20 h-20 flex items-center justify-center mb-2">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="5" fill="transparent" class="text-white/5" />
                            <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="5" fill="transparent" :stroke-dasharray="2 * Math.PI * 36" :stroke-dashoffset="(1 - score / 100) * 2 * Math.PI * 36" class="text-indigo-500 transition-all duration-1000" />
                        </svg>
                        <span class="absolute text-xl font-black text-white italic">{{ score }}%</span>
                    </div>
                    <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Overall Polish</span>
                </div>

                <div v-if="auditData.ats" class="bg-white/5 p-5 rounded-[24px] border border-white/5 flex flex-col items-center justify-center text-center">
                    <div class="relative w-20 h-20 flex items-center justify-center mb-2">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="5" fill="transparent" class="text-white/5" />
                            <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="5" fill="transparent" :stroke-dasharray="2 * Math.PI * 36" :stroke-dashoffset="(1 - auditData.ats.score / 100) * 2 * Math.PI * 36" class="text-emerald-500 transition-all duration-1000" />
                        </svg>
                        <span class="absolute text-xl font-black text-white italic">{{ auditData.ats.score }}%</span>
                    </div>
                    <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">ATS Readiness</span>
                </div>
            </div>
            
            <button v-if="!analyzing && auditData" @click="startAnalysis" class="w-full mt-6 bg-white/5 hover:bg-white/10 text-indigo-400 py-3 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2 border border-white/5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Re-Run Full Analysis
            </button>
        </div>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
            <!-- Loading State -->
            <div v-if="analyzing" class="h-full flex flex-col items-center justify-center gap-8 py-20">
                <div class="relative">
                    <div class="w-20 h-20 rounded-full border-4 border-indigo-500/10 animate-ping"></div>
                    <div class="absolute inset-0 w-20 h-20 rounded-full border-t-4 border-indigo-500 animate-spin"></div>
                </div>
                <div class="text-center space-y-2">
                    <p class="text-xl font-black text-white tracking-tight animate-pulse uppercase italic">Scanning...</p>
                    <p class="text-slate-500 text-[9px] font-black uppercase tracking-[0.3em]">Processing with AI Engine</p>
                </div>
            </div>

            <!-- Results State -->
            <div v-else-if="auditData" class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
                
                <!-- ATS Intelligence Section -->
                <div v-if="auditData.ats" class="space-y-6">
                    <h3 class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.4em] flex items-center gap-3">
                        <span class="w-8 h-px bg-emerald-500/30"></span>
                        ATS Keywords
                    </h3>
                    
                    <div v-if="auditData.ats.missing_keywords?.length" class="flex flex-wrap gap-2">
                        <span v-for="kw in auditData.ats.missing_keywords" :key="kw" class="px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-[10px] font-bold text-emerald-400 shadow-lg">
                            # {{ kw }}
                        </span>
                    </div>

                    <div v-if="auditData.ats.parsing_issues?.length" class="bg-rose-500/5 border border-rose-500/20 p-5 rounded-[28px] space-y-3">
                        <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Fix Parsing Obstacles
                        </p>
                        <ul class="space-y-2">
                            <li v-for="issue in auditData.ats.parsing_issues" :key="issue" class="text-xs text-slate-400 leading-relaxed font-medium pl-4 relative">
                                <div class="absolute left-0 top-2 w-1.5 h-1.5 rounded-full bg-rose-500/40"></div>
                                {{ issue }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Content Polish Section -->
                <div class="space-y-6">
                    <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] flex items-center gap-3">
                        <span class="w-8 h-px bg-indigo-500/30"></span>
                        Content Polish
                    </h3>

                    <!-- Summary -->
                    <div v-if="auditData.sections?.summary" class="glass-card group p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] font-black text-indigo-300 uppercase tracking-widest italic">Professional Summary</span>
                            <button 
                                v-if="!appliedIds.has('summary')"
                                @click="handleApply('summary', 'summary', auditData.sections.summary.suggestion)" 
                                class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-xl active:scale-95"
                            >
                                Apply
                            </button>
                            <span v-else class="text-emerald-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Active
                            </span>
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed italic font-medium">"{{ auditData.sections.summary.suggestion }}"</p>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="tip in auditData.sections.summary.tips" :key="tip" class="px-2.5 py-1 bg-white/5 rounded-lg text-[9px] text-slate-500 font-bold">
                                💡 {{ tip }}
                            </span>
                        </div>
                    </div>

                    <!-- Experiences -->
                    <div v-for="exp in auditData.sections?.experiences" :key="exp.id" class="glass-card group p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] font-black text-indigo-300 uppercase tracking-widest italic truncate max-w-[200px]">{{ exp.job_title || 'Work Experience' }}</span>
                            <button 
                                v-if="!appliedIds.has(exp.id)"
                                @click="handleApply('experiences', exp.id, exp.suggestion)" 
                                class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-xl active:scale-95"
                            >
                                Apply
                            </button>
                            <span v-else class="text-emerald-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Active
                            </span>
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed italic font-medium">"{{ exp.suggestion }}"</p>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="tip in exp.tips" :key="tip" class="px-2.5 py-1 bg-white/5 rounded-lg text-[9px] text-slate-500 font-bold">
                                ⚡ {{ tip }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="!analyzing" class="h-full flex flex-col items-center justify-center text-center py-20 px-6">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-6 text-slate-700">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke-width="1.5"/></svg>
                </div>
                <h3 class="text-white font-black uppercase tracking-widest mb-2">Ready for Audit</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">Click the button above to start your AI-powered ATS and quality analysis.</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.glass-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 32px;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.glass-card:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(99, 102, 241, 0.3);
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
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
