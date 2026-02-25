<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AiButton from '@/Components/AiButton.vue';
import AiFieldAssistant from '@/Components/AiFieldAssistant.vue';
import SmartAssistant from '@/Components/SmartAssistant.vue';

import { useNotify } from '@/composables/useNotify';

const props = defineProps(['resume']);
const emit = defineEmits(['next']);
const { t } = useI18n();
const notify = useNotify();

const form = useForm({
    title:       '',
    description: '',
    link:        '',
});

const submit = () => {
    form.post(route('resumes.projects.store', props.resume.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            props.resume.projects = [...usePage().props.resume.projects];
        },
    });
};

import { usePage } from '@inertiajs/vue3';

const deleteProj = (id) => {
    if (confirm(t('delete') + '?')) {
        router.delete(route('resumes.projects.destroy', [props.resume.id, id]), {
            preserveScroll: true
        });
    }
};

const ta = 'w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-colors';

const handleAiDescription = (text) => {
    form.description = text;
};
</script>

<template>
    <div class="space-y-8">
        <!-- Existing Projects -->
        <div v-if="resume.projects?.length" class="space-y-4">
            <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white ml-2">{{ t('passion_architectures') }}</h3>
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="project in resume.projects"
                    :key="project.id"
                    class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-4 rounded-2xl border border-black/5 dark:border-white/10 relative group hover:border-indigo-500/30 transition-all duration-500 overflow-hidden shadow-sm"
                >
                    <button @click="deleteProj(project.id)" class="absolute top-4 ltr:right-4 rtl:left-4 text-slate-400 hover:text-red-500 transition-colors bg-slate-100 dark:bg-white/5 w-7 h-7 rounded-full flex items-center justify-center font-bold z-20">&times;</button>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <div>
                            <p class="font-black text-slate-900 dark:text-white text-sm tracking-tight">{{ project.title }}</p>
                            <a v-if="project.link" :href="project.link" target="_blank" class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mt-0.5 hover:underline break-all">{{ project.link }}</a>
                            <p v-if="project.description" class="mt-2 text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium opacity-80 italic whitespace-pre-wrap">{{ project.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p v-else class="text-[10px] text-slate-500 italic uppercase tracking-widest ml-4 font-black opacity-50">{{ t('no_projects_yet') }}</p>

        <!-- Add Form -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-6 bg-gradient-to-br from-emerald-50/50 dark:from-white/5 to-transparent overflow-hidden shadow-sm transition-all hover:border-emerald-500/20">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.25em]">{{ t('add_project') }}</h4>
                    <p class="text-[8px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ t('manifest_vision') }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1 mb-1 block">{{ t('project_name') }} *</label>
                        <TextInput v-model="form.title" :placeholder="t('project_name_placeholder')" required class="!py-2 !text-xs" />
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1 mb-1 block">{{ t('project_link') || 'Project Link' }}</label>
                        <TextInput v-model="form.link" :placeholder="'https://...'" class="!py-2 !text-xs" />
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="flex flex-col gap-4">
                        <label class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 ml-1">{{ t('description') }}</label>
                        
                        <!-- Smart Assistant Row -->
                        <div class="flex flex-wrap items-center gap-3 bg-slate-100/50 dark:bg-black/40 p-3 rounded-[20px] border border-black/5 dark:border-white/5 shadow-inner">
                             <SmartAssistant v-model="form.description" context="description" :language="resume.language" @generated="handleAiDescription" variant="compact" />
                        </div>
                    </div>
                    <textarea 
                        v-model="form.description" 
                        class="relative z-10 w-full rounded-[16px] border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 px-4 py-3 text-xs focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 min-h-[100px] leading-relaxed shadow-inner" 
                        :placeholder="t('description') + '...'"
                    ></textarea>
                </div>

                <!-- Save & Navigation -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-6 border-t border-black/5 dark:border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="laser-btn-wrapper shadow-2xl shadow-emerald-500/20 active:scale-95 transition-all hover:-translate-y-1">
                            <button type="submit" :disabled="form.processing" class="laser-btn-content px-4 py-2 text-white">
                                <span class="text-[9px] font-black uppercase tracking-widest whitespace-nowrap">{{ t('save') }}</span>
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
