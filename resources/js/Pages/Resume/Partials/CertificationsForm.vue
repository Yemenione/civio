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

const form = useForm({
    name: '',
    issued_by: '',
    issue_date: '',
    expiry_date: '',
    credential_url: '',
});

const submit = () => {
    form.post(route('resumes.certifications.store', props.resume.id), {
        onSuccess: () => {
            form.reset();
            props.resume.certifications = [...usePage().props.resume.certifications];
        },
        preserveScroll: true,
    });
};

const handleAiCertification = (text) => {
    form.name = text;
};

import { usePage } from '@inertiajs/vue3';

const remove = (id) => {
    if (confirm(t('delete') + '?')) {
        useForm({}).delete(route('resumes.certifications.destroy', [props.resume.id, id]), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="space-y-8">
        <!-- Existing Certifications -->
        <div v-if="resume.certifications?.length" class="space-y-4">
            <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white ml-2">{{ t('verified_honors') }}</h3>
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="cert in resume.certifications"
                    :key="cert.id"
                    class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-4 rounded-2xl border border-black/5 dark:border-white/10 relative group hover:border-amber-500/30 transition-all duration-500 overflow-hidden shadow-sm"
                >
                    <button @click="remove(cert.id)" class="absolute top-4 ltr:right-4 rtl:left-4 text-slate-400 hover:text-red-500 transition-colors bg-slate-100 dark:bg-white/5 w-7 h-7 rounded-full flex items-center justify-center font-bold z-20">&times;</button>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div>
                            <p class="font-black text-slate-900 dark:text-white text-sm tracking-tight">{{ cert.name }}</p>
                            <p class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest mt-0.5">{{ cert.issued_by }}</p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[8px] font-black uppercase text-slate-500 bg-slate-100 dark:bg-white/5 px-1.5 py-0.5 rounded-full">{{ cert.issue_date }} — {{ cert.expiry_date || t('perpetual') }}</span>
                            </div>
                            <a v-if="cert.credential_url" :href="cert.credential_url" target="_blank" class="mt-2 text-[9px] font-black uppercase tracking-widest text-indigo-400 hover:text-indigo-300 flex items-center gap-1.5 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                {{ t('authenticate') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Form -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-6 bg-gradient-to-br from-amber-50/50 dark:from-white/5 to-transparent overflow-hidden shadow-sm transition-all hover:border-amber-500/20">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                        <h4 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.25em]">{{ t('add_certification') }}</h4>
                        <p class="text-[8px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ t('validate_excellence') }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1 ml-1 min-w-0">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 truncate">{{ t('certification_name') }} *</label>
                            <SmartAssistant v-model="form.name" context="certifications" :language="resume.language" @generated="handleAiCertification" variant="compact" class="!w-auto flex-shrink-0" />
                        </div>
                        <TextInput v-model="form.name" :placeholder="t('eg_aws_certification')" required class="!py-2 !text-xs" />
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1 mb-1 block">{{ t('issued_by') }}</label>
                        <TextInput v-model="form.issued_by" :placeholder="t('eg_amazon')" class="!py-2 !text-xs" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1 mb-1 block">{{ t('issue_date') }}</label>
                        <TextInput type="month" v-model="form.issue_date" class="!py-2 !text-xs" />
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1 mb-1 block">{{ t('expiry_date') }}</label>
                        <TextInput type="month" v-model="form.expiry_date" class="!py-2 !text-xs" />
                    </div>
                </div>

                <div>
                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ltr:ml-1 rtl:mr-1 mb-1 block">{{ t('credential_url') }}</label>
                    <TextInput v-model="form.credential_url" type="url" placeholder="https://..." class="!py-2 !text-xs" />
                </div>

                <!-- Save & Navigation -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-6 border-t border-black/5 dark:border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="laser-btn-wrapper shadow-2xl shadow-amber-500/20 active:scale-95 transition-all hover:-translate-y-1">
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
