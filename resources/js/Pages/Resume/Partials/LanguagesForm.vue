<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import TextInput from '@/Components/TextInput.vue';
import SmartAssistant from '@/Components/SmartAssistant.vue';

import { useNotify } from '@/composables/useNotify';

const props = defineProps(['resume']);
const emit = defineEmits(['next']);
const { t } = useI18n();
const notify = useNotify();

const proficiencyLevels = [
    t('proficiency_native'),
    t('proficiency_fluent'),
    t('proficiency_advanced'),
    t('proficiency_intermediate'),
    t('proficiency_basic')
];

const form = useForm({
    name: '',
    proficiency: t('proficiency_fluent'),
});

const submit = () => {
    form.post(route('resumes.languages.store', props.resume.id), {
        onSuccess: () => {
            form.reset();
            props.resume.languages = [...usePage().props.resume.languages];
        },
        preserveScroll: true,
    });
};

const handleAiLanguage = (text) => {
    form.name = text;
};

import { usePage } from '@inertiajs/vue3';

const remove = (id) => {
    if (confirm(t('delete') + '?')) {
        router.delete(route('resumes.languages.destroy', [props.resume.id, id]), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="space-y-8">
        <!-- Existing Languages -->
        <div v-if="resume.languages?.length" class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-4 rounded-2xl border border-black/5 dark:border-white/10 space-y-5 overflow-hidden shadow-sm transition-all hover:border-indigo-500/20">
            <h3 class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-900 dark:text-white ml-2">{{ t('linguistic_arsenal') }}</h3>
            <div class="flex flex-wrap gap-4">
                <div
                    v-for="lang in resume.languages"
                    :key="lang.id"
                    class="group relative bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 px-4 py-2 rounded-2xl flex items-center gap-3 hover:border-indigo-500/50 transition-all duration-300 overflow-hidden"
                >
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div>
                    <div>
                        <p class="text-[11px] font-bold text-white">{{ lang.name }}</p>
                        <p class="text-[8px] font-black uppercase tracking-widest text-slate-500">{{ lang.proficiency }}</p>
                    </div>
                    <button @click="remove(lang.id)" class="ml-2 text-slate-600 hover:text-red-400 transition-colors">&times;</button>
                </div>
            </div>
        </div>

        <!-- Add Form -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-6 bg-gradient-to-br from-white/5 to-transparent overflow-hidden transition-all hover:border-indigo-500/20 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.25em]">{{ t('add_language') }}</h4>
                    <p class="text-[8px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ t('bridge_boundaries') }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1 ml-1 min-w-0">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 truncate">{{ t('language_name') }} *</label>
                            <SmartAssistant v-model="form.name" context="languages" :language="resume.language" @generated="handleAiLanguage" variant="compact" class="!w-auto flex-shrink-0" />
                        </div>
                        <TextInput v-model="form.name" :placeholder="t('eg_english')" required class="!py-2 !text-xs" />
                    </div>
                    <div class="w-full">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1 mb-1 block">{{ t('proficiency') }}</label>
                        <select
                            v-model="form.proficiency"
                            class="relative z-10 w-full rounded-2xl border border-black/5 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white py-2 px-3 shadow-inner backdrop-blur-md focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 text-xs font-medium"
                        >
                            <option class="bg-white dark:bg-slate-900" v-for="level in proficiencyLevels" :key="level" :value="level">{{ level }}</option>
                        </select>
                    </div>
                </div>
                
                <!-- Save & Navigation -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-6 border-t border-black/5 dark:border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="laser-btn-wrapper shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all hover:-translate-y-1">
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
