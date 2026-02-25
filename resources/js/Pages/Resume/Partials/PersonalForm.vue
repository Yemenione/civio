<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import AiButton from '@/Components/AiButton.vue';
import AiFieldAssistant from '@/Components/AiFieldAssistant.vue';
import SmartAssistant from '@/Components/SmartAssistant.vue';

import { useNotify } from '@/composables/useNotify';

const props = defineProps(['resume']);
const emit = defineEmits(['next']);
const { t } = useI18n();
const notify = useNotify();

const page = usePage();
const isPro = true; // Features are now free

const checkProTemplate = () => {
    if (form.theme === 'modern' && !isPro) {
        notify.confirm(
            t('pro_feature_label') || 'PREMIUM FEATURE',
            t('pro_template_confirm') || 'This template is only available for PRO users. Would you like to upgrade?',
            () => window.location.href = route('pricing'),
            () => form.theme = 'classic'
        );
    }
};

const form = useForm({
    title:      props.resume.title || '',
    first_name: props.resume.first_name || '',
    last_name:  props.resume.last_name || '',
    email:      props.resume.email || '',
    phone:      props.resume.phone || '',
    address:    props.resume.address || '',
    city:       props.resume.city || '',
    country:    props.resume.country || '',
    linkedin:   props.resume.linkedin || '',
    twitter:    props.resume.twitter || '',
    github:     props.resume.github || '',
    portfolio:  props.resume.portfolio || '',
    website:    props.resume.website || '',
    birth_date: props.resume.birth_date || '',
    nationality: props.resume.nationality || '',
    job_title:  props.resume.job_title || '',
    summary:    props.resume.summary || '',
    language:   props.resume.language || 'en',
    photo:      props.resume.photo || null,
});

const submit = () => {
    form.put(route('resumes.update', props.resume.id), {
        preserveScroll: true,
    });
};

// Live Preview Sync: Update the props.resume object so changes show instantly in Preview
import { watch } from 'vue';
watch(form, (newVal) => {
    Object.assign(props.resume, newVal);
    // Also sync the composite 'fullname' and 'jobtitle' for templates that use them
    props.resume.fullname = trim((newVal.first_name || '') + ' ' + (newVal.last_name || ''));
    props.resume.jobtitle = newVal.job_title;
}, { deep: true });

const trim = (str) => str.trim();

const handleAiSummary = (text) => {
    form.summary = text;
};

const handlePhotoUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        notify.alert(
            t('error') || 'Error',
            t('photo_too_large') || 'Photo is too large. Max 2MB.',
            'error'
        );
        return;
    }

    const reader = new FileReader();
    reader.onload = (event) => {
        form.photo = event.target.result;
        props.resume.photo = event.target.result;
    };
    reader.readAsDataURL(file);
};

// ——— shared dark input classes ———
const input = 'w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-colors';
</script>

<template>
    <form @submit.prevent="submit" class="space-y-8">
        <!-- Language & General -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-5 shadow-sm overflow-hidden transition-all hover:border-indigo-500/20">
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-7 h-7 rounded-full bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                </div>
                <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">{{ t('language_settings') || 'Language Settings' }}</h3>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel :value="t('language')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1" />
                    <select v-model="form.language" class="w-full rounded-xl border border-black/5 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white py-2 px-4 shadow-sm backdrop-blur-md focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 text-xs">
                        <option value="en" class="bg-white dark:bg-slate-900">English</option>
                        <option value="ar" class="bg-white dark:bg-slate-900">العربية</option>
                        <option value="fr" class="bg-white dark:bg-slate-900">Français</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Identity -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-6 shadow-sm overflow-hidden transition-all hover:border-purple-500/20">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">{{ t('identity') || 'Identity' }}</h3>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <div>
                    <InputLabel :value="t('fullname')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1" />
                    <TextInput id="first_name" type="text" v-model="form.first_name" :placeholder="t('fullname')" class="!py-2 !text-xs" />
                    <InputError class="mt-1" :message="form.errors.first_name" />
                </div>
                <div>
                    <InputLabel :value="t('lastname')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1" />
                    <TextInput id="last_name" type="text" v-model="form.last_name" :placeholder="t('lastname')" class="!py-2 !text-xs" />
                    <InputError class="mt-1" :message="form.errors.last_name" />
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1 ml-1 min-w-0">
                    <InputLabel :value="t('jobtitle')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 truncate" />
                    <SmartAssistant v-model="form.job_title" context="job_title" :language="form.language" variant="compact" class="!w-auto flex-shrink-0" />
                </div>
                <TextInput id="job_title" type="text" v-model="form.job_title" :placeholder="t('jobtitle')" class="!py-2 !text-xs" />
                <InputError class="mt-1" :message="form.errors.job_title" />
            </div>
        </div>

        <!-- Contact -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-6 shadow-sm overflow-hidden transition-all hover:border-emerald-500/20">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">{{ t('contact_info') || 'Contact Information' }}</h3>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <div>
                    <InputLabel :value="t('email')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1" />
                    <TextInput id="email" type="email" v-model="form.email" :placeholder="t('email')" class="!py-2 !text-xs" />
                    <InputError class="mt-1" :message="form.errors.email" />
                </div>
                <div>
                    <InputLabel :value="t('phone')" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1" />
                    <TextInput id="phone" type="text" v-model="form.phone" :placeholder="t('phone')" class="!py-2 !text-xs" />
                    <InputError class="mt-1" :message="form.errors.phone" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <div>
                    <InputLabel :value="t('city') || 'City'" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1" />
                    <TextInput id="city" type="text" v-model="form.city" :placeholder="t('city') || 'City'" class="!py-2 !text-xs" />
                </div>
                <div>
                    <InputLabel :value="t('country') || 'Country'" class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1" />
                    <TextInput id="country" type="text" v-model="form.country" :placeholder="t('country') || 'Country'" class="!py-2 !text-xs" />
                </div>
            </div>
        </div>

        <!-- Summary & AI -->
        <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl p-6 rounded-[28px] border border-black/5 dark:border-white/10 space-y-5 shadow-sm overflow-hidden transition-all hover:border-amber-500/20">
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between ml-1">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-900 dark:text-white">{{ t('summary') }}</h3>
                    </div>
                </div>

                <!-- Smart Assistant Row -->
                <div class="flex items-center gap-4 bg-slate-100/50 dark:bg-black/40 p-3 rounded-[20px] border border-black/5 dark:border-white/5 shadow-inner">
                     <SmartAssistant v-model="form.summary" context="summary" :language="form.language" @generated="handleAiSummary" variant="compact" />
                </div>
            </div>

            <div class="relative group">
                <!-- Shimmer Line -->
                <div class="absolute -top-px left-8 right-8 h-px bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
                
                <textarea
                    id="summary"
                    v-model="form.summary"
                    class="relative z-10 w-full rounded-[24px] border border-black/5 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 px-6 py-4 text-sm focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 min-h-[140px] leading-relaxed shadow-inner"
                    :placeholder="t('summary') + '...'"
                ></textarea>
                <InputError class="mt-1" :message="form.errors.summary" />
            </div>
        </div>

        <!-- Photo Upload -->
        <div class="bg-white dark:bg-slate-800/40 backdrop-blur-xl p-5 rounded-[24px] border border-black/5 dark:border-white/10 flex items-center gap-5 shadow-sm">
            <div class="relative group">
                <div class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 group-hover:border-indigo-500/50 transition-all duration-500 group-hover:scale-105 group-hover:rotate-3 shadow-2xl">
                    <img v-if="resume.photo" :src="resume.photo" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center text-slate-700">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>
                <button @click.prevent="$refs.photoInput.click()" class="absolute -bottom-1.5 -right-1.5 bg-indigo-600 hover:bg-indigo-500 text-white p-2 rounded-xl shadow-2xl shadow-indigo-500/40 transition-all active:scale-90 hover:rotate-12">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>
                <input type="file" ref="photoInput" class="hidden" accept="image/*" @change="handlePhotoUpload" />
            </div>
            <div class="flex-1">
                <h4 class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-900 dark:text-white mb-1">{{ t('photo') || 'Profile Photo' }}</h4>
                <p class="text-[9px] text-slate-500 leading-relaxed font-medium uppercase tracking-tight opacity-70">{{ t('photo_desc') || 'High fidelity portrait. PNG or JPG. Max 2MB.' }}</p>
            </div>
        </div>

        <!-- Save & Navigation -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-6 border-t border-black/5 dark:border-white/5">
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
</template>
