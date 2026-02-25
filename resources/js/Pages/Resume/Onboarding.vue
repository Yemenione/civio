<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    resume: { type: Object, required: true }
});

const { t } = useI18n();
const step = ref(1);

const onboardingData = ref({
    experience_level: props.resume.design_options?.experience_level || 'professional',
    target_role: props.resume.job_title || '',
    
    // Step 3: History
    company1: props.resume.experiences?.[0]?.company || '',
    company1_role: props.resume.experiences?.[0]?.job_title || '',
    company2: props.resume.experiences?.[1]?.company || '',
    company2_role: props.resume.experiences?.[1]?.job_title || '',
    
    // Step 4: Education
    degree: props.resume.education?.[0]?.degree || '',
    institution: props.resume.education?.[0]?.school || '',
    
    // Step 5: Certifications
    cert1_name: props.resume.certifications?.[0]?.name || '',
    cert1_org: props.resume.certifications?.[0]?.issuer || '',
    
    // Step 6: Skills
    skills: props.resume.skills?.map(s => s.name).join(', ') || '',
    
    tone: props.resume.design_options?.tone || 'modern',
    language: props.resume.language === 'ar' ? 'Arabic' : (props.resume.language === 'fr' ? 'French' : (props.resume.language === 'de' ? 'German' : 'English')),
    template: props.resume.template || props.resume.theme || 'classic',
});

const isSaving = ref(false);
const aiProcessingStep = ref(0); // For Step 8 animation

const saveStepData = async () => {
    isSaving.value = true;
    try {
        const payload = {
            job_title: onboardingData.value.target_role,
            template: onboardingData.value.template,
            theme: onboardingData.value.template,
            language: onboardingData.value.language === 'Arabic' ? 'ar' : (onboardingData.value.language === 'French' ? 'fr' : (onboardingData.value.language === 'German' ? 'de' : 'en')),
            design_options: {
                ...props.resume.design_options,
                experience_level: onboardingData.value.experience_level,
                tone: onboardingData.value.tone,
            }
        };

        // Conditional data injection based on what was filled
        if (onboardingData.value.company1) {
            payload.experiences = [
                { company: onboardingData.value.company1, job_title: onboardingData.value.company1_role || onboardingData.value.target_role, start_date: '2022-01-01' },
                ...(onboardingData.value.company2 ? [{ company: onboardingData.value.company2, job_title: onboardingData.value.company2_role || onboardingData.value.target_role, start_date: '2020-01-01' }] : [])
            ];
        }

        if (onboardingData.value.degree) {
            payload.education = [{ school: onboardingData.value.institution || 'University', degree: onboardingData.value.degree, start_date: '2016-01-01', end_date: '2020-01-01' }];
        }

        if (onboardingData.value.cert1_name) {
            payload.certifications = [{ name: onboardingData.value.cert1_name, issuer: onboardingData.value.cert1_org || 'Professional Authority', date: '2023-01-01' }];
        }

        if (onboardingData.value.skills) {
            payload.skills = onboardingData.value.skills.split(',').map(s => ({ name: s.trim(), level: 'Expert' }));
        }

        await router.patch(route('resumes.update', props.resume.id), payload, {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => { isSaving.value = false; }
        });
    } catch (e) {
        console.error(e);
        isSaving.value = false;
    }
};

const nextStep = () => {
    saveStepData();
    if (step.value < 7) {
        step.value++;
    } else if (step.value === 7) {
        startAiMagic();
    } else {
        finish();
    }
};

const skipStep = () => {
    if (step.value < 7) {
        step.value++;
    } else if (step.value === 7) {
        startAiMagic();
    }
};

const startAiMagic = async () => {
    step.value = 8;
    aiProcessingStep.value = 1;
    
    // Simulate complex AI processing
    setTimeout(() => { aiProcessingStep.value = 2; }, 1500);
    setTimeout(() => { aiProcessingStep.value = 3; }, 3000);
    setTimeout(() => { 
        finish(); // Go straight to editor after magic
    }, 5000);
};

const finish = () => {
    router.patch(route('resumes.update', props.resume.id), {
        job_title: onboardingData.value.target_role,
        template: onboardingData.value.template,
        theme: onboardingData.value.template,
        language: onboardingData.value.language === 'Arabic' ? 'ar' : (onboardingData.value.language === 'French' ? 'fr' : (onboardingData.value.language === 'German' ? 'de' : 'en')),
        design_options: {
            ...props.resume.design_options,
            experience_level: onboardingData.value.experience_level,
            tone: onboardingData.value.tone,
        }
    }, {
        onSuccess: () => router.get(route('resumes.edit', props.resume.id))
    });
};

const steps = computed(() => [
    { id: 1, title: t('experience_level'), desc: t('onboarding_step1_desc') },
    { id: 2, title: t('target_role'), desc: t('onboarding_step2_desc') },
    { id: 3, title: t('professional_history'), desc: t('onboarding_step_history_desc') },
    { id: 4, title: t('academic_journey'), desc: t('onboarding_step_education_desc') },
    { id: 5, title: t('credentials_awards'), desc: t('onboarding_step_certifications_desc') },
    { id: 6, title: t('core_expertise'), desc: t('onboarding_step_expertise_desc') },
    { id: 7, title: t('vibe_language'), desc: t('onboarding_step_vibe_desc') },
    { id: 8, title: 'AI Matching', desc: 'Crafting excellence...' },
]);

const skipTemplateStep = () => {
    finish();
};

</script>

<template>
    <Head :title="t('onboarding') || 'Guided Setup'" />

    <AuthenticatedLayout>
        <div class="min-h-[90vh] flex items-center justify-center p-4 bg-slate-50 dark:bg-slate-950/20 transition-colors duration-500">
            <div class="w-full max-w-5xl relative">
                
                <!-- Progress Header -->
                <div class="mb-16 text-center">
                    <div class="flex items-center justify-center gap-4 mb-8">
                        <div 
                            v-for="s in steps" 
                            :key="s.id"
                            class="h-1.5 rounded-full transition-all duration-1000"
                            :class="[step >= s.id ? 'w-16 bg-indigo-600 dark:bg-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.5)]' : 'w-8 bg-black/5 dark:bg-white/10']"
                        ></div>
                    </div>
                    <div class="relative inline-block">
                        <h2 v-if="step < 8" class="text-4xl md:text-7xl font-black text-slate-900 dark:text-white tracking-tighter italic uppercase transition-all duration-700">
                            {{ steps[step-1].title }}
                        </h2>
                        <div class="absolute -right-4 -top-4 w-8 h-8 flex items-center justify-center" v-if="isSaving">
                            <div class="w-4 h-4 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </div>
                    <p v-if="step < 8" class="text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-[0.4em] mt-4 opacity-70">
                        {{ steps[step-1].desc }}
                    </p>
                </div>

                <!-- Step 1: Experience Level -->
                <div v-if="step === 1" class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-in fade-in slide-in-from-bottom-8 duration-1000 ease-out">
                    <button 
                        @click="onboardingData.experience_level = 'student'; nextStep()"
                        class="glass-card group relative p-12 rounded-[40px] border border-black/5 dark:border-white/5 hover:border-indigo-500/50 text-center transition-all duration-500 hover:-translate-y-2 shadow-sm hover:shadow-2xl overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10 w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-3xl mx-auto mb-8 flex items-center justify-center text-4xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-inner">🎓</div>
                        <h4 class="relative z-10 text-slate-900 dark:text-white font-black uppercase tracking-widest text-base mb-3">{{ t('student') }}</h4>
                        <p class="relative z-10 text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-widest leading-relaxed">{{ t('focus_student') }}</p>
                    </button>
                    <button 
                        @click="onboardingData.experience_level = 'professional'; nextStep()"
                        class="glass-card group relative p-12 rounded-[40px] border border-black/5 dark:border-white/5 hover:border-indigo-500/50 text-center transition-all duration-500 hover:-translate-y-2 shadow-sm hover:shadow-2xl overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10 w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-3xl mx-auto mb-8 flex items-center justify-center text-4xl group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500 shadow-inner">💼</div>
                        <h4 class="relative z-10 text-slate-900 dark:text-white font-black uppercase tracking-widest text-base mb-3">{{ t('professional') }}</h4>
                        <p class="relative z-10 text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-widest leading-relaxed">{{ t('focus_professional') }}</p>
                    </button>
                    <button 
                        @click="onboardingData.experience_level = 'executive'; nextStep()"
                        class="glass-card group relative p-12 rounded-[40px] border border-black/5 dark:border-white/5 hover:border-indigo-500/50 text-center transition-all duration-500 hover:-translate-y-2 shadow-sm hover:shadow-2xl overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10 w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-3xl mx-auto mb-8 flex items-center justify-center text-4xl group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 shadow-inner">💎</div>
                        <h4 class="relative z-10 text-slate-900 dark:text-white font-black uppercase tracking-widest text-base mb-3">{{ t('executive') }}</h4>
                        <p class="relative z-10 text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-widest leading-relaxed">{{ t('focus_executive') }}</p>
                    </button>
                </div>

                <!-- Step 2: Target Role -->
                <div v-if="step === 2" class="max-w-2xl mx-auto animate-in fade-in slide-in-from-bottom-8 duration-1000 ease-out">
                    <div class="glass-card p-12 rounded-[48px] border border-black/5 dark:border-white/5 space-y-10 shadow-2xl relative overflow-hidden bg-white/30 dark:bg-slate-900/30">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-transparent to-purple-500/10 pointer-events-none"></div>
                        <div class="relative z-10 space-y-4">
                            <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 ml-4">{{ t('target_role') }}</label>
                            <div class="relative group">
                                <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl blur opacity-20 group-focus-within:opacity-50 transition-all duration-500"></div>
                                <input 
                                    v-model="onboardingData.target_role" 
                                    type="text"
                                    :placeholder="t('target_role_placeholder', 'e.g. Senior Product Designer')"
                                    class="relative w-full bg-white dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-2xl px-8 py-6 text-slate-900 dark:text-white font-black placeholder-slate-400 dark:placeholder-slate-700 focus:border-indigo-500 focus:ring-0 transition-all text-xl shadow-inner italic"
                                    autofocus
                                    @keyup.enter="nextStep"
                                />
                            </div>
                        </div>
                        <div class="relative z-10 flex justify-center">
                            <button @click="nextStep" class="laser-btn-wrapper shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all hover:scale-105 duration-300">
                                <div class="laser-btn-content dark:bg-slate-900 px-16 py-5 text-white text-xs font-black uppercase tracking-[0.4em]">
                                    {{ t('analyze_continue') }}
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: History (History) -->
                <div v-if="step === 3" class="max-w-3xl mx-auto animate-in fade-in slide-in-from-bottom-8 duration-1000 ease-out">
                    <div class="glass-card p-12 rounded-[48px] border border-black/5 dark:border-white/5 space-y-10 shadow-2xl bg-white/30 dark:bg-slate-900/30">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 ml-4">{{ t('most_recent_company') }}</label>
                                <input v-model="onboardingData.company1" type="text" placeholder="Apple, Google..." class="w-full bg-white dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-xl px-6 py-4 text-slate-900 dark:text-white font-bold" />
                            </div>
                            <div class="space-y-4">
                                <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 ml-4">{{ t('previous_company') }}</label>
                                <input v-model="onboardingData.company2" type="text" placeholder="Tesla, Amazon..." class="w-full bg-white dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-xl px-6 py-4 text-slate-900 dark:text-white font-bold" />
                            </div>
                        </div>
                        <div class="flex flex-col items-center gap-6 mt-12">
                            <button @click="nextStep" class="laser-btn-wrapper shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all hover:scale-105 duration-300">
                                <div class="laser-btn-content dark:bg-slate-900 px-16 py-5 text-white text-xs font-black uppercase tracking-[0.4em]">
                                    {{ t('analyze_continue') }}
                                </div>
                            </button>
                            <button @click="skipStep" class="text-slate-400 hover:text-indigo-500 text-[10px] font-black uppercase tracking-widest transition-colors">{{ t('skip_step') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Academic Journey -->
                <div v-if="step === 4" class="max-w-3xl mx-auto animate-in fade-in slide-in-from-bottom-8 duration-1000 ease-out">
                    <div class="glass-card p-12 rounded-[48px] border border-black/5 dark:border-white/5 space-y-10 shadow-2xl bg-white/30 dark:bg-slate-900/30">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 ml-4">{{ t('degree') }}</label>
                                <input v-model="onboardingData.degree" type="text" placeholder="B.Sc Computer Science..." class="w-full bg-white dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-xl px-6 py-4 text-slate-900 dark:text-white font-bold" />
                            </div>
                            <div class="space-y-4">
                                <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 ml-4">{{ t('institution') }}</label>
                                <input v-model="onboardingData.institution" type="text" placeholder="Stanford University..." class="w-full bg-white dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-xl px-6 py-4 text-slate-900 dark:text-white font-bold" />
                            </div>
                        </div>
                        <div class="flex flex-col items-center gap-6 mt-12">
                            <button @click="nextStep" class="laser-btn-wrapper shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all hover:scale-105 duration-300">
                                <div class="laser-btn-content dark:bg-slate-900 px-16 py-5 text-white text-xs font-black uppercase tracking-[0.4em]">
                                    {{ t('analyze_continue') }}
                                </div>
                            </button>
                            <button @click="skipStep" class="text-slate-400 hover:text-indigo-500 text-[10px] font-black uppercase tracking-widest transition-colors">{{ t('skip_step') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Credentials -->
                <div v-if="step === 5" class="max-w-3xl mx-auto animate-in fade-in slide-in-from-bottom-8 duration-1000 ease-out">
                    <div class="glass-card p-12 rounded-[48px] border border-black/5 dark:border-white/5 space-y-10 shadow-2xl bg-white/30 dark:bg-slate-900/30">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 ml-4">{{ t('certificate_name') }}</label>
                                <input v-model="onboardingData.cert1_name" type="text" placeholder="AWS Certified Architect..." class="w-full bg-white dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-xl px-6 py-4 text-slate-900 dark:text-white font-bold" />
                            </div>
                            <div class="space-y-4">
                                <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 ml-4">{{ t('issuing_org') }}</label>
                                <input v-model="onboardingData.cert1_org" type="text" placeholder="Amazon Web Services..." class="w-full bg-white dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-xl px-6 py-4 text-slate-900 dark:text-white font-bold" />
                            </div>
                        </div>
                        <div class="flex flex-col items-center gap-6 mt-12">
                            <button @click="nextStep" class="laser-btn-wrapper shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all hover:scale-105 duration-300">
                                <div class="laser-btn-content dark:bg-slate-900 px-16 py-5 text-white text-xs font-black uppercase tracking-[0.4em]">
                                    {{ t('analyze_continue') }}
                                </div>
                            </button>
                            <button @click="skipStep" class="text-slate-400 hover:text-indigo-500 text-[10px] font-black uppercase tracking-widest transition-colors">{{ t('skip_step') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Core Expertise -->
                <div v-if="step === 6" class="max-w-2xl mx-auto animate-in fade-in slide-in-from-bottom-8 duration-1000 ease-out">
                    <div class="glass-card p-12 rounded-[48px] border border-black/5 dark:border-white/5 space-y-8 shadow-2xl bg-white/30 dark:bg-slate-900/30">
                        <div class="space-y-4 text-center">
                            <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">{{ t('top_skills') }}</label>
                            <textarea 
                                v-model="onboardingData.skills" 
                                rows="3"
                                placeholder="React, Node.js, Project Management, Sales..."
                                class="w-full bg-white dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-2xl px-8 py-6 text-slate-900 dark:text-white font-black placeholder-slate-400 dark:placeholder-slate-700 focus:border-indigo-500 focus:ring-0 transition-all text-lg shadow-inner italic"
                            ></textarea>
                        </div>
                        <div class="flex flex-col items-center gap-6">
                            <button @click="nextStep" class="laser-btn-wrapper shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all hover:scale-105 duration-300">
                                <div class="laser-btn-content dark:bg-slate-900 px-16 py-5 text-white text-xs font-black uppercase tracking-[0.4em]">
                                    {{ t('analyze_continue') }}
                                </div>
                            </button>
                            <button @click="skipStep" class="text-slate-400 hover:text-indigo-500 text-[10px] font-black uppercase tracking-widest transition-colors">{{ t('skip_step') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Step 7: Vibe & Language (Formerly Step 3) -->
                <div v-if="step === 7" class="max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-8 duration-1000 ease-out">
                    <div class="glass-card p-10 md:p-16 rounded-[64px] border border-black/5 dark:border-white/5 space-y-12 shadow-2xl bg-white/30 dark:bg-slate-900/30">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                            <div class="space-y-6">
                                <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 ml-4 drop-shadow-sm">{{ t('content_tone') }}</label>
                                <div class="grid grid-cols-1 gap-3">
                                    <button 
                                        v-for="toneName in ['Formal', 'Modern', 'Creative']" 
                                        :key="toneName"
                                        @click="onboardingData.tone = toneName.toLowerCase()"
                                        class="group relative px-6 py-4 rounded-3xl text-[11px] font-black uppercase tracking-widest transition-all border text-left flex items-center justify-between"
                                        :class="onboardingData.tone === toneName.toLowerCase() ? 'bg-indigo-600 border-indigo-500 text-white shadow-xl shadow-indigo-500/20 scale-105' : 'bg-white dark:bg-white/5 border-black/5 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:border-indigo-500/30 hover:bg-slate-50 dark:hover:bg-white/10'"
                                    >
                                        {{ t('tone_' + toneName.toLowerCase()) }}
                                        <div class="w-2 h-2 rounded-full transition-all duration-500" :class="onboardingData.tone === toneName.toLowerCase() ? 'bg-white scale-150' : 'bg-slate-300 dark:bg-slate-800'"></div>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <label class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 ml-4 drop-shadow-sm">{{ t('content_language') }}</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button 
                                        v-for="l in ['English', 'Arabic', 'French', 'German']" 
                                        :key="l"
                                        @click="onboardingData.language = l"
                                        class="group relative px-6 py-4 rounded-3xl text-[11px] font-black uppercase tracking-widest transition-all border text-center"
                                        :class="onboardingData.language === l ? 'bg-indigo-600 border-indigo-500 text-white shadow-xl shadow-indigo-500/20 scale-105' : 'bg-white dark:bg-white/5 border-black/5 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:border-indigo-500/30 hover:bg-slate-50 dark:hover:bg-white/10'"
                                    >
                                        {{ l }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center pt-8 border-t border-black/5 dark:border-white/5">
                            <button @click="nextStep" class="laser-btn-wrapper shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all hover:scale-105 duration-300">
                                <div class="laser-btn-content dark:bg-slate-900 px-24 py-6 text-white text-sm font-black uppercase tracking-[0.4em] italic">
                                    {{ t('analyze_continue') }}
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 8: AI Magic Processing -->
                <div v-if="step === 8" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950 overflow-hidden">
                    <div class="absolute inset-0">
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-indigo-500/20 rounded-full blur-[120px] animate-pulse"></div>
                        <div class="absolute top-1/4 left-1/4 w-[400px] h-[400px] bg-purple-500/10 rounded-full blur-[100px] animate-bounce duration-[10s]"></div>
                    </div>
                    
                    <div class="relative z-10 text-center max-w-xl px-4">
                        <!-- Holographic Icon -->
                        <div class="w-24 h-24 md:w-32 md:h-32 mx-auto mb-12 relative">
                            <div class="absolute inset-0 bg-indigo-500 rounded-full animate-ping opacity-20"></div>
                            <div class="relative w-full h-full bg-slate-900 border border-white/10 rounded-full flex items-center justify-center text-4xl md:text-5xl shadow-2xl">
                                🤖
                            </div>
                        </div>

                        <h2 class="text-3xl md:text-5xl font-black text-white tracking-tighter italic uppercase mb-8">
                            {{ aiProcessingStep === 1 ? t('processing_your_data') : (aiProcessingStep === 2 ? t('crafting_narrative') : t('optimizing_layout')) }}
                        </h2>

                        <!-- Progress Bar -->
                        <div class="w-full h-2 bg-white/5 rounded-full overflow-hidden mb-6">
                            <div 
                                class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-500 transition-all duration-1000 ease-out shadow-[0_0_20px_rgba(99,102,241,0.5)]"
                                :style="{ width: (aiProcessingStep * 33) + '%' }"
                            ></div>
                        </div>

                        <p class="text-slate-400 text-xs font-black uppercase tracking-[0.4em] animate-pulse">
                            {{ t('ai_matching_desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(40px);
}
.dark .glass-card {
    background: rgba(255, 255, 255, 0.02);
}
.laser-btn-wrapper {
    background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899);
    padding: 2px;
    border-radius: 20px;
}
.laser-btn-content {
    background: #0f172a;
    border-radius: 18px;
    transition: all 0.3s ease;
}
.laser-btn-content:hover {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.2);
    border-radius: 10px;
}
</style>
