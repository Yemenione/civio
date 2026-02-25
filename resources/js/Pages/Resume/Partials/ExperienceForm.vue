<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import InputLabel from '@/Components/InputLabel.vue';
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
    company:     '',
    job_title:   '',
    start_date:  '',
    end_date:    '',
    current:     false,
    description: '',
});

const submit = () => {
    form.post(route('resumes.experience.store', props.resume.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            // Sync the updated experience list from the server props
            props.resume.experiences = [...usePage().props.resume.experiences];
        },
    });
};

// Live Preview Sync for temporary "New" experience item being typed
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

watch(form, (newVal) => {
    // We can't easily sync an unsaved item into the main list without complex mapping,
    // but we can at least make sure that any updates to the existing list are reflected
    // and provide feedback. For now, we'll focus on ensuring the main list stays in sync.
}, { deep: true });

const deleteExp = (id) => {
    if (confirm(t('delete') + '?')) {
        router.delete(route('resumes.experience.destroy', [props.resume.id, id]), {
            preserveScroll: true,
            onSuccess: () => {
                // Force sync if needed, though Inertia should handle it via props
            }
        });
    }
};

const handleAiDescription = (text) => {
    form.description = text;
};

const ta = 'w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-colors';
</script>

<template>
    <div class="space-y-8">
        <!-- Existing Experiences -->
        <div v-if="resume.experiences?.length" class="space-y-4">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white ml-2">{{ t('legacy_milestones') }}</h3>
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="exp in resume.experiences"
                    :key="exp.id"
                    class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-4 rounded-2xl border border-black/5 dark:border-white/10 relative group hover:border-indigo-500/30 transition-all duration-500 overflow-hidden shadow-sm"
                >
                    <button @click="deleteExp(exp.id)" class="absolute top-4 ltr:right-4 rtl:left-4 text-slate-400 hover:text-red-500 transition-colors bg-slate-100 dark:bg-white/5 w-7 h-7 rounded-full flex items-center justify-center font-bold z-20">&times;</button>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-black text-slate-900 dark:text-white text-sm tracking-tight">{{ exp.job_title }}</p>
                            <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mt-0.5">{{ exp.company }}</p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[8px] font-black uppercase text-slate-500 bg-slate-100 dark:bg-white/5 px-1.5 py-0.5 rounded-full">{{ exp.start_date }} — {{ exp.current ? t('current') : exp.end_date }}</span>
                            </div>
                            <p v-if="exp.description" class="mt-2 text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium opacity-80 whitespace-pre-wrap italic">{{ exp.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Form -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-6 bg-gradient-to-br from-indigo-50/50 dark:from-white/5 to-transparent overflow-hidden shadow-sm transition-all hover:border-indigo-500/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]">{{ t('add_experience') }}</h4>
                        <p class="text-[8px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ t('define_next_chapter') }}</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <InputLabel :value="t('jobtitle')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1" />
                        <TextInput v-model="form.job_title" :placeholder="t('jobtitle')" required class="!py-2 !text-xs" />
                    </div>
                    <div>
                        <InputLabel :value="t('company')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1" />
                        <TextInput v-model="form.company" :placeholder="t('company')" required class="!py-2 !text-xs" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <InputLabel :value="t('start_date')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1" />
                        <TextInput type="date" v-model="form.start_date" class="!py-2 !text-xs" />
                    </div>
                    <div>
                        <InputLabel :value="t('end_date')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1" />
                        <div class="flex flex-wrap items-center gap-3">
                            <TextInput v-if="!form.current" type="date" v-model="form.end_date" class="min-w-[140px] flex-1 !py-2 !text-xs" />
                            <label class="flex items-center gap-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 cursor-pointer select-none group focus-within:ring-2 focus-within:ring-indigo-500/20 whitespace-nowrap">
                                <input type="checkbox" v-model="form.current"
                                    class="w-3.5 h-3.5 rounded-lg border-slate-300 dark:border-white/10 bg-white dark:bg-white/5 text-indigo-600 focus:ring-indigo-500/10"
                                />
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">{{ t('current') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="flex flex-col gap-4">
                        <InputLabel :value="t('description')" class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 ml-1" />
                        
                        <!-- Smart Assistant Row -->
                        <div class="flex flex-wrap items-center gap-3 bg-slate-100/50 dark:bg-black/40 p-3 rounded-[20px] border border-black/5 dark:border-white/5 shadow-inner">
                             <SmartAssistant v-model="form.description" context="description" :language="resume.language" @generated="handleAiDescription" variant="compact" />
                        </div>
                    </div>
                    <textarea 
                        v-model="form.description" 
                        class="relative z-10 w-full rounded-[16px] border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 px-4 py-3 text-xs focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 min-h-[120px] leading-relaxed shadow-inner" 
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
