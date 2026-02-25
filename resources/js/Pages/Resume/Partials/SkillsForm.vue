<script setup>
import { ref, watch, onMounted } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import TextInput from '@/Components/TextInput.vue';
import SmartAssistant from '@/Components/SmartAssistant.vue';

const props = defineProps(['resume']);
const emit = defineEmits(['next']);
const { t } = useI18n();

const form = useForm({
    name:  '',
    level: t('proficiency_intermediate'),
});

const submit = () => {
    form.post(route('resumes.skills.store', props.resume.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            props.resume.skills = [...usePage().props.resume.skills];
        },
    });
};

const handleAiSkill = (text) => {
    form.name = text;
};

const deleteSkill = (id) => {
    if (confirm(t('delete') + '?')) {
        router.delete(route('resumes.skills.destroy', [props.resume.id, id]), {
            preserveScroll: true,
        });
    }
};

const suggestions = ref([
    'Leadership', 'Project Management', 'Communication',
    'Problem Solving', 'Teamwork', 'Critical Thinking'
]);

const loadingSuggestions = ref(false);
const lastFetchedJob = ref('');

const fetchSuggestions = async () => {
    const jobTitle = props.resume.jobtitle || props.resume.job_title;
    if (!jobTitle || jobTitle.length < 3 || jobTitle === lastFetchedJob.value) return;

    loadingSuggestions.value = true;
    try {
        const response = await axios.post(route('ai.generate'), {
            prompt: jobTitle,
            context: 'skills',
            language: props.resume.language || 'English'
        });

        if (response.data.result) {
            const skills = response.data.result.split(',').map(s => s.trim().replace(/\.$/, '')).filter(s => s.length > 0);
            if (skills.length > 3) {
                suggestions.value = skills;
                lastFetchedJob.value = jobTitle;
            }
        }
    } catch (e) {
        console.error('Failed to fetch skill suggestions:', e);
    } finally {
        loadingSuggestions.value = false;
    }
};

watch(() => props.resume.jobtitle || props.resume.job_title, () => {
    fetchSuggestions();
});

onMounted(() => {
    fetchSuggestions();
});

const levelColors = {
    [t('proficiency_beginner')]:     'bg-slate-600/50 text-slate-300',
    [t('proficiency_intermediate')]: 'bg-indigo-600/30 text-indigo-300',
    [t('proficiency_advanced')]:     'bg-purple-600/30 text-purple-300',
    [t('proficiency_expert')]:       'bg-green-600/30 text-green-300',
};
</script>

<template>
    <div class="space-y-8">
        <!-- Existing Skills -->
        <div v-if="resume.skills?.length" class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-4 rounded-2xl border border-black/5 dark:border-white/10 space-y-5 shadow-sm transition-all hover:border-indigo-500/20 overflow-hidden">
            <h3 class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-900 dark:text-white ml-2">{{ t('weaponry_expertise') }}</h3>
            <div class="flex flex-wrap gap-2.5">
                <div
                    v-for="skill in resume.skills"
                    :key="skill.id"
                    class="group flex items-center gap-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 text-slate-900 dark:text-white px-3 py-1.5 rounded-xl text-[10px] font-bold hover:border-indigo-500/50 transition-all duration-300 hover:-translate-y-1 shadow-sm"
                >
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div>
                    <span>{{ skill.name }}</span>
                    <span v-if="skill.level" class="text-[8px] uppercase tracking-tighter text-slate-500 dark:text-slate-500 font-black">· {{ skill.level }}</span>
                    <button @click="deleteSkill(skill.id)" class="ml-1 text-slate-400 dark:text-slate-600 hover:text-red-500 font-bold transition-colors">&times;</button>
                </div>
            </div>
        </div>
        <p v-else class="text-[9px] text-slate-500 italic uppercase tracking-widest ml-4 font-black opacity-50">{{ t('no_skills_yet') }}</p>

        <!-- Suggestions -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-5 shadow-sm overflow-hidden transition-all hover:border-blue-500/20">
            <div class="flex items-center justify-between ml-2">
                <div class="flex items-center gap-2">
                    <p class="text-[10px] text-slate-500 uppercase tracking-[0.25em] font-black">{{ t('quick_add') }}</p>
                    <span class="text-[8px] text-emerald-500 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20 font-black tracking-tight uppercase">ATS & SEO</span>
                </div>
                <button v-if="!loadingSuggestions" @click="lastFetchedJob = ''; fetchSuggestions()" type="button" class="text-[8px] text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 font-black uppercase tracking-widest flex items-center gap-1 transition-all">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ t('refresh') || 'Refresh' }}
                </button>
                <div v-else class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                    <span class="text-[8px] text-indigo-400 font-black uppercase tracking-widest">{{ t('generating') || 'Analyzing Job...' }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="s in suggestions"
                    :key="s"
                    type="button"
                    @click="form.name = s"
                    class="bg-slate-50 dark:bg-white/5 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 border border-black/5 dark:border-white/5 hover:border-indigo-500/30 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-tight transition-all duration-300 shadow-sm"
                >
                    {{ s }}
                </button>
            </div>
        </div>

        <!-- Add Form -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-6 bg-gradient-to-br from-indigo-50/50 dark:from-white/5 to-transparent shadow-sm overflow-hidden transition-all hover:border-indigo-500/20">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.25em]">{{ t('add_skill') }}</h4>
                    <p class="text-[8px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ t('quantum_leap') }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="flex flex-col gap-3">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1 ml-1 min-w-0">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 truncate">{{ t('skill_name', 'Skill Name') }}</label>
                            <SmartAssistant v-model="form.name" context="skills" :language="resume.language" @generated="handleAiSkill" variant="compact" class="!w-auto flex-shrink-0" />
                        </div>
                        <TextInput v-model="form.name" :placeholder="t('skills_placeholder_eg')" required class="!py-2 !text-xs" />
                    </div>
                    <div class="w-full">
                        <select
                            v-model="form.level"
                            class="relative z-10 w-full rounded-xl border border-black/5 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white py-2 px-3 shadow-sm dark:shadow-xl backdrop-blur-md focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 text-xs font-medium"
                        >
                            <option class="bg-white dark:bg-slate-900">{{ t('proficiency_beginner') }}</option>
                            <option class="bg-white dark:bg-slate-900">{{ t('proficiency_intermediate') }}</option>
                            <option class="bg-white dark:bg-slate-900">{{ t('proficiency_advanced') }}</option>
                            <option class="bg-white dark:bg-slate-900">{{ t('proficiency_expert') }}</option>
                        </select>
                    </div>
                </div>
                
                <!-- Save & Navigation -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-6 border-t border-black/5 dark:border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="laser-btn-wrapper shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all hover:-translate-y-1">
                            <button type="submit" :disabled="form.processing" class="laser-btn-content px-4 py-2 text-white">
                                <span class="text-[9px] font-black uppercase tracking-widest whitespace-nowrap">{{ t('add') }}</span>
                            </button>
                        </div>
                        <transition enter-active-class="transition-all duration-500" enter-from-class="opacity-0 -translate-x-2" leave-active-class="transition-all duration-500" leave-to-class="opacity-0 translate-x-2">
                            <div v-if="form.recentlySuccessful" class="flex items-center gap-2 text-emerald-400">
                                <div class="w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-[8px] font-black uppercase tracking-widest">{{ t('saved') }}</span>
                            </div>
                        </transition>
                    </div>

                    <button @click.prevent="emit('next')" type="button" class="group flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-900 dark:bg-white/5 text-white border border-white/10 hover:bg-slate-800 transition-all active:scale-95">
                        <span class="text-[9px] font-black uppercase tracking-widest">{{ t('next', 'Next') }}</span>
                        <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
