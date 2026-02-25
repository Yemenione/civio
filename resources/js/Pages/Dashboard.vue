<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ResumeCard from '@/Components/ResumeCard.vue';
import { useI18n } from 'vue-i18n';
import AuraRing from '@/Components/AuraRing.vue';
import ResumePreview from '@/Components/ResumePreview.vue';
import { getDummyResume } from '@/utils/dummyResume.js';
// removed templates.js import

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    resumes: Array,
    user:    Object,
    recentApplications: Array, // Expected from controller or computed here if available
    templates: Array,
});

// Compute overall ATS score based on resume completeness (demo calculation)
const computeAts = (resume) => {
    let score = 0;
    if (!resume) return 0;
    
    // Weighted logic matching the Editor
    if (resume.first_name) score += 5;
    if (resume.last_name)  score += 5;
    if (resume.email)      score += 5;
    if (resume.phone)      score += 5;
    if (resume.job_title)  score += 10;
    if (resume.summary)    score += 10;
    
    if (resume.experiences?.length) score += 15;
    if (resume.education?.length)   score += 15;
    if (resume.skills?.length)      score += 10;
    
    if (resume.projects?.length)       score += 10;
    if (resume.languages?.length)      score += 5;
    if (resume.certifications?.length) score += 5;
    
    return Math.min(score, 100);
};

const deleteResume = (id) => {
    router.delete(route('resumes.destroy', id), { preserveScroll: true });
};

const duplicateResume = (id) => {
    router.post(route('resumes.duplicate', id), {}, { preserveScroll: true });
};

const stats = computed(() => [
    { label: t('resumes'), value: props.resumes?.length || 0, icon: '📄', color: 'indigo' },
    { label: t('plan'), value: (props.user?.plan || page.props.auth.user?.plan) === 'pro' ? 'PRO' : 'FREE', icon: '💎', color: 'amber' },
    { label: t('latest_ats'), value: props.resumes?.length > 0 ? computeAts(props.resumes[0]) + '%' : '—', icon: '🚀', color: 'emerald' },
    { label: t('languages'), value: '8', icon: '🌍', color: 'purple' },
]);
</script>

<template>
    <Head :title="t('dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 py-4">
                <div class="space-y-1">
                    <h2 class="font-black text-2xl md:text-4xl text-slate-900 dark:text-white tracking-tighter">
                        {{ t('welcome_back') }}, <span class="premium-gradient-text">{{ page.props.auth.user?.name || 'User' }}</span>
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-bold tracking-wide opacity-80 uppercase">
                        {{ t('dashboard_subtitle') }}
                    </p>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="laser-btn-wrapper shadow-[0_0_30px_rgba(99,102,241,0.2)] active:scale-95 transition-all hover:-translate-y-1">
                        <Link
                            :href="route('resumes.browse')"
                            class="laser-btn-content px-6 py-3 sm:px-8 sm:py-4 flex items-center gap-3 text-white"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            <span class="text-xs font-black uppercase tracking-[0.25em]">{{ t('new_resume') }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

                <!-- Enhanced Stats Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" v-if="resumes.length > 0">
                    <div 
                        v-for="stat in stats" 
                        :key="stat.label"
                        class="relative group bg-white dark:bg-slate-900/50 backdrop-blur-xl p-4 rounded-2xl border border-black/5 dark:border-white/5 hover:border-indigo-500/30 transition-all duration-500 overflow-hidden shadow-sm hover:shadow-xl"
                    >
                        <!-- Decorative background glow -->
                        <div class="absolute -right-10 -top-10 w-24 h-24 bg-indigo-500/5 blur-[40px] group-hover:bg-indigo-500/10 transition-colors duration-500"></div>
                        
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-500 mb-0.5 group-hover:text-indigo-500 transition-colors">{{ stat.label }}</p>
                                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ stat.value }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/5 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 group-hover:bg-indigo-500/10 transition-all duration-500">
                                {{ stat.icon }}
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Template Gallery (Empty State or Bottom Section) -->
                <!-- We show this at the top if 0 resumes are present. -->
                <div v-if="resumes.length === 0" class="mt-8 relative z-10">
                    <div class="text-center mb-10">
                        <div class="w-20 h-20 bg-white dark:bg-white/5 rounded-[24px] flex items-center justify-center mx-auto mb-6 border border-black/5 dark:border-white/10 shadow-xl">
                            <img src="/images/logo.png" alt="Logo" class="w-10 h-10 object-contain dark:grayscale dark:opacity-50" />
                        </div>
                        <h3 class="text-3xl md:text-5xl font-black text-slate-900 dark:text-white mb-4 tracking-tighter italic">
                            {{ t('no_resumes_yet') || 'Your future starts here.' }}
                        </h3>
                        <p class="text-slate-500 dark:text-slate-400 max-w-xl mx-auto leading-relaxed text-sm font-bold opacity-80 uppercase tracking-widest">
                            {{ t('empty_dashboard_desc') || "Select a premium template to start building." }}
                        </p>
                    </div>

                    <div class="flex overflow-x-auto pb-10 pt-4 -mx-4 px-4 snap-x snap-mandatory gap-6 scrollbar-hide">
                        <div 
                            v-for="tpl in props.templates" 
                            :key="tpl.slug"
                            class="snap-center shrink-0 w-[260px] group relative bg-white dark:bg-slate-900/40 backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-3xl overflow-hidden hover:border-indigo-500/50 transition-all duration-500 hover:-translate-y-2 cursor-pointer shadow-xl"
                        >
                            <div class="aspect-[210/297] bg-white w-full relative overflow-hidden flex items-start justify-center">
                                <div class="absolute top-0 left-0 w-[800px] h-[1131px] pointer-events-none" :style="{ transform: `scale(${260/800})`, transformOrigin: 'top left' }">
                                    <ResumePreview 
                                        :resume="{...getDummyResume(), template: tpl.slug, design_options: { accent_color: '#4f46e5' }}" 
                                        :editable="false" 
                                    />
                                </div>
                                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center p-6 text-center z-20">
                                    <button 
                                        @click="router.post(route('resumes.create'), { template: tpl.slug })"
                                        class="laser-btn-wrapper w-full active:scale-95 transition-all"
                                    >
                                        <div class="laser-btn-content py-3 text-white text-[10px] font-black uppercase tracking-[0.2em]">
                                            {{ t('use_template') }}
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div class="p-5 border-t border-black/5 dark:border-white/5 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-md flex justify-between items-center z-30">
                                <div>
                                    <h4 class="text-slate-900 dark:text-white font-black uppercase tracking-widest text-[10px] truncate max-w-[150px]">{{ tpl.name }}</h4>
                                    <p class="text-[8px] text-slate-500 font-bold uppercase tracking-widest mt-1">{{ tpl.category }}</p>
                                </div>
                                <span v-if="tpl.is_premium" class="text-[8px] bg-amber-500 text-black px-2 py-0.5 font-black uppercase tracking-widest rounded-full">{{ t('pro') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pro upsell banner (free plan + 2+ resumes) -->
                <div v-if="user?.plan !== 'pro' && resumes.length >= 2"
                    class="relative overflow-hidden bg-gradient-to-r from-indigo-900/40 via-purple-900/40 to-slate-900/40 backdrop-blur-3xl border border-indigo-500/30 rounded-[30px] p-8 flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/20 blur-[100px] group-hover:bg-indigo-500/30 transition-colors duration-700"></div>
                    
                    <div class="relative z-10 text-center md:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/20 text-amber-600 dark:text-amber-500 rounded-full text-[10px] font-black tracking-widest uppercase mb-4 border border-amber-500/20">
                            {{ t('pro_feature_label') || 'LIMIT REACHED' }}
                        </div>
                        <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-2 italic">
                            {{ t('upsell_unlimited') || 'Go Pro for unlimited possibilities' }}
                        </p>
                        <p class="text-slate-600 dark:text-slate-400 text-sm font-medium leading-relaxed max-w-lg">
                            {{ t('upsell_desc') || 'Unlock unlimited resumes, high-fidelity PDF exports, and premium AI-powered editing tools.' }}
                        </p>
                    </div>
                    
                    <div class="laser-btn-wrapper laser-emerald shadow-2xl shadow-emerald-500/20 active:scale-95 transition-all">
                        <Link :href="route('pricing')" class="laser-btn-content px-10 py-4 text-white font-black text-xs uppercase tracking-[0.2em]">
                            {{ t('upgrade_plan') }}
                        </Link>
                    </div>
                </div>

                <!-- Resume Grid -->
                <div v-if="resumes.length > 0">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-black/5 dark:border-white/5">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-[0.2em] uppercase italic">{{ t('my_resumes') }}</h3>
                            <div class="px-2 py-0.5 bg-indigo-500/10 rounded-full text-[9px] font-bold text-indigo-600 dark:text-indigo-400 tracking-widest uppercase">
                                {{ resumes.length }}
                            </div>
                        </div>
                        
                        <!-- Potential filter buttons could go here -->
                        <div class="hidden sm:flex items-center gap-2">
                             <button class="p-2.5 bg-white/5 border border-white/5 rounded-xl text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                             </button>
                             <button class="p-2.5 bg-white/5 border border-white/5 rounded-xl text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                             </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                        <ResumeCard
                            v-for="resume in resumes"
                            :key="resume.id"
                            :resume="resume"
                            @delete="deleteResume"
                            @duplicate="duplicateResume"
                        />
                        <!-- Add new card -->
                        <Link
                            :href="route('resumes.browse')"
                            class="relative group bg-white dark:bg-white/5 border-2 border-dashed border-black/10 dark:border-white/10 hover:border-indigo-500/50 rounded-[40px] flex flex-col items-center justify-center gap-6 p-10 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-500 min-h-[350px] overflow-hidden shadow-sm"
                        >
                            <div class="absolute inset-x-0 bottom-0 h-1 bg-indigo-500/0 group-hover:bg-indigo-500/30 transition-all duration-500"></div>
                            
                            <div class="w-20 h-20 rounded-[30px] bg-slate-50 dark:bg-white/5 group-hover:bg-indigo-500/20 flex items-center justify-center transition-all duration-500 group-hover:scale-110 group-hover:-rotate-12 group-hover:shadow-[0_0_30px_rgba(99,102,241,0.2)]">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div class="text-center">
                                <span class="block text-lg font-black text-slate-900 dark:text-white opacity-40 group-hover:opacity-100 transition-opacity duration-500 tracking-tight italic">{{ t('new_resume') }}</span>
                                <span class="block text-xs font-bold uppercase tracking-[0.2em] mt-1 opacity-20 group-hover:opacity-60 transition-opacity duration-500">{{ t('start_from_zero') }}</span>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Bottom Template Gallery (If user has resumes) -->
                <div v-if="resumes.length > 0" class="mt-16 pt-12 border-t border-black/5 dark:border-white/5">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-[0.2em] uppercase italic">{{ t('start_from_template') || 'Explore Premium Templates' }}</h3>
                        </div>
                        <Link :href="route('resumes.browse')" class="hidden sm:inline-block text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">
                            {{ t('view_all_templates') || 'View Complete Library' }} &rarr;
                        </Link>
                    </div>
                    
                    <div class="flex overflow-x-auto pb-10 -mx-4 px-4 snap-x snap-mandatory gap-6 scrollbar-hide">
                        <div 
                            v-for="tpl in props.templates" 
                            :key="tpl.slug"
                            class="snap-center shrink-0 w-[220px] group relative bg-white dark:bg-slate-900/40 backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-[24px] overflow-hidden hover:border-indigo-500/50 transition-all duration-500 hover:-translate-y-2 cursor-pointer shadow-xl"
                        >
                            <div class="aspect-[210/297] bg-white w-full relative overflow-hidden flex items-start justify-center">
                                <div class="absolute top-0 left-0 w-[800px] h-[1131px] pointer-events-none" :style="{ transform: `scale(${220/800})`, transformOrigin: 'top left' }">
                                    <ResumePreview 
                                        :resume="{...getDummyResume(), template: tpl.slug, design_options: { accent_color: '#4f46e5' }}" 
                                        :editable="false" 
                                    />
                                </div>
                                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center p-6 text-center z-20">
                                    <button 
                                        @click="router.post(route('resumes.create'), { template: tpl.slug })"
                                        class="laser-btn-wrapper w-full active:scale-95 transition-all"
                                    >
                                        <div class="laser-btn-content py-3 text-white text-[10px] font-black uppercase tracking-[0.2em]">
                                            {{ t('use_template') }}
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 border-t border-black/5 dark:border-white/5 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-md flex justify-between items-center z-30">
                                <div>
                                    <h4 class="text-slate-900 dark:text-white font-black uppercase tracking-widest text-[9px] truncate max-w-[130px]">{{ tpl.name }}</h4>
                                </div>
                                <span v-if="tpl.is_premium" class="text-[8px] bg-amber-500 text-black px-2 py-0.5 font-black uppercase tracking-widest rounded-full">{{ t('pro') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.glass-card {
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(12px);
}
</style>
