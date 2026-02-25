<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useNotify } from '@/composables/useNotify';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const notify = useNotify();

const props = defineProps({
    applications: Array,
    resumes: Array,
    counts: Object,
});

const statuses = ['saved', 'applied', 'interview', 'offer', 'rejected', 'withdrawn'];

const showModal = ref(false);
const editingApp = ref(null);

const form = useForm({
    company_name: '',
    job_title: '',
    location: '',
    job_url: '',
    status: 'saved',
    applied_at: '',
    interview_at: '',
    salary_min: '',
    salary_max: '',
    salary_currency: 'USD',
    notes: '',
    excitement: 3,
    resume_id: '',
});

const openModal = (app = null) => {
    editingApp.value = app;
    if (app) {
        form.company_name = app.company_name;
        form.job_title = app.job_title;
        form.location = app.location;
        form.job_url = app.job_url;
        form.status = app.status;
        form.applied_at = app.applied_at;
        form.interview_at = app.interview_at;
        form.salary_min = app.salary_min;
        form.salary_max = app.salary_max;
        form.salary_currency = app.salary_currency;
        form.notes = app.notes;
        form.excitement = app.excitement;
        form.resume_id = app.resume_id;
    } else {
        form.reset();
    }
    showModal.value = true;
};

const submit = () => {
    if (editingApp.value) {
        form.put(route('job-tracker.update', editingApp.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('job-tracker.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const closeModal = () => {
    showModal.value = false;
    editingApp.value = null;
    form.reset();
};

const updateStatus = (app, status) => {
    router.patch(route('job-tracker.status', app.id), { status });
};

const deleteApp = (id) => {
    notify.confirm(
        t('delete_application'),
        t('delete_application_confirm'),
        () => router.delete(route('job-tracker.destroy', id))
    );
};

const getStatusColor = (status) => {
    switch (status) {
        case 'saved': return 'bg-slate-500/20 text-slate-400';
        case 'applied': return 'bg-indigo-500/20 text-indigo-400';
        case 'interview': return 'bg-amber-500/20 text-amber-400';
        case 'offer': return 'bg-emerald-500/20 text-emerald-400';
        case 'rejected': return 'bg-red-500/20 text-red-400';
        case 'withdrawn': return 'bg-slate-700 text-slate-500';
        default: return 'bg-slate-500/10 text-slate-500';
    }
};
</script>

<template>
    <Head :title="t('job_tracker')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-black text-3xl text-slate-900 dark:text-white tracking-tight leading-tight">{{ t('job_tracker') }}</h2>
                <div class="laser-btn-wrapper shadow-lg shadow-indigo-500/20 active:scale-95 transition-transform hover:scale-105">
                    <button @click="openModal()" class="laser-btn-content px-5 py-2.5 text-white flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em]">{{ t('add_application') }}</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Pipeline Summary -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div v-for="status in statuses" :key="status" class="bg-white/40 dark:bg-slate-800/40 backdrop-blur-md border border-white/10 dark:border-white/5 rounded-3xl p-5 text-center transition-all hover:bg-white/60 dark:hover:bg-slate-800/60 shadow-sm">
                        <div class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('status_' + status) }}</div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ counts[status] || 0 }}</div>
                    </div>
                </div>

                <!-- Applications Table/List -->
                <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-xl border border-white/20 dark:border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-white/5 border-b border-black/5 dark:border-white/5">
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">{{ t('company_role') }}</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">{{ t('status') }}</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">{{ t('excitement') }}</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">{{ t('applied') }}</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] text-right">{{ t('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                <tr v-for="app in applications" :key="app.id" class="hover:bg-indigo-50/30 dark:hover:bg-white/5 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="font-black text-slate-900 dark:text-white text-lg tracking-tight">{{ app.company_name }}</div>
                                        <div class="text-sm font-bold text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                                            <span>{{ app.job_title }}</span>
                                            <a v-if="app.job_url" :href="app.job_url" target="_blank" class="text-indigo-500 hover:text-indigo-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                        <div v-if="app.location" class="text-[10px] font-bold text-slate-400 dark:text-slate-600 flex items-center gap-1.5 mt-2 uppercase tracking-wider">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ app.location }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <select 
                                            :value="app.status" 
                                            @change="updateStatus(app, $event.target.value)"
                                            class="text-[10px] font-black px-4 py-2 rounded-full border-none focus:ring-0 transition-all cursor-pointer uppercase tracking-widest shadow-sm"
                                            :class="getStatusColor(app.status)"
                                        >
                                            <option v-for="s in statuses" :key="s" :value="s" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">{{ t('status_' + s).toUpperCase() }}</option>
                                        </select>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex gap-0.5">
                                            <template v-for="i in 5" :key="i">
                                                <svg :class="i <= app.excitement ? 'text-amber-400 fill-current' : 'text-slate-200 dark:text-slate-700 fill-none stroke-current'" class="w-5 h-5 transition-colors" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-sm font-bold text-slate-500 dark:text-slate-400">
                                        {{ app.applied_at ? new Date(app.applied_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) : '-' }}
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0">
                                            <button @click="openModal(app)" class="p-2.5 bg-slate-100 dark:bg-white/5 hover:bg-indigo-500 dark:hover:bg-indigo-600 text-slate-400 dark:text-slate-500 hover:text-white rounded-xl transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button @click="deleteApp(app.id)" class="p-2.5 bg-slate-100 dark:bg-white/5 hover:bg-red-500 text-slate-400 dark:text-slate-500 hover:text-white rounded-xl transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="applications.length === 0">
                                    <td colspan="5" class="px-8 py-24 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-slate-100 dark:bg-white/5 rounded-full flex items-center justify-center text-slate-300 dark:text-slate-700">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                            </div>
                                            <p class="text-slate-500 font-bold max-w-xs leading-relaxed">{{ t('no_applications') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md animate-in fade-in duration-300">
            <div class="bg-white dark:bg-slate-900 border border-black/5 dark:border-white/10 rounded-[2.5rem] w-full max-w-2xl overflow-hidden shadow-[0_32px_64px_-16px_rgba(0,0,0,0.3)] animate-in zoom-in-95 duration-300">
                <div class="px-8 py-6 border-b border-black/5 dark:border-white/5 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ editingApp ? t('edit_application') : t('new_application') }}</h3>
                    <button @click="closeModal" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-200 dark:bg-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white transition-all ring-1 ring-black/5 dark:ring-white/10 hover:rotate-90">✕</button>
                </div>
                
                <form @submit.prevent="submit" class="p-8 grid grid-cols-2 gap-6">
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('company') }}</label>
                        <input v-model="form.company_name" type="text" required class="w-full bg-slate-100 dark:bg-black/20 border-none rounded-2xl text-slate-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all p-3.5" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('job_title') }}</label>
                        <input v-model="form.job_title" type="text" required class="w-full bg-slate-100 dark:bg-black/20 border-none rounded-2xl text-slate-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all p-3.5" />
                    </div>
                    
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('location') }}</label>
                        <input v-model="form.location" type="text" class="w-full bg-slate-100 dark:bg-black/20 border-none rounded-2xl text-slate-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all p-3.5" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('job_url') }}</label>
                        <input v-model="form.job_url" type="url" class="w-full bg-slate-100 dark:bg-black/20 border-none rounded-2xl text-slate-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all p-3.5" />
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('status') }}</label>
                        <select v-model="form.status" class="w-full bg-slate-100 dark:bg-black/20 border-none rounded-2xl text-slate-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all p-3.5 appearance-none">
                            <option v-for="s in statuses" :key="s" :value="s">{{ t('status_' + s).toUpperCase() }}</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('excitement') }} (1-5)</label>
                        <div class="flex items-center gap-4 bg-slate-100 dark:bg-black/20 p-2.5 rounded-2xl">
                            <input v-model="form.excitement" type="range" min="1" max="5" class="flex-1 accent-indigo-500" />
                            <span class="text-lg font-black text-indigo-500 w-4">{{ form.excitement }}</span>
                        </div>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('applied_date') }}</label>
                        <input v-model="form.applied_at" type="date" class="w-full bg-slate-100 dark:bg-black/20 border-none rounded-2xl text-slate-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all p-3.5" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('interview_date') }}</label>
                        <input v-model="form.interview_at" type="date" class="w-full bg-slate-100 dark:bg-black/20 border-none rounded-2xl text-slate-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all p-3.5" />
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">{{ t('notes') }}</label>
                        <textarea v-model="form.notes" rows="4" class="w-full bg-slate-100 dark:bg-black/20 border-none rounded-3xl text-slate-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all p-4 resize-none"></textarea>
                    </div>

                    <div class="col-span-2 pt-4 flex justify-end gap-4">
                        <button type="button" @click="closeModal" class="px-8 py-3 dark:text-slate-400 text-[10px] font-black uppercase tracking-[0.25em] transition-all hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-2xl">{{ t('cancel') }}</button>
                        <div class="laser-btn-wrapper shadow-xl shadow-indigo-500/20 active:scale-95 transition-transform hover:scale-105">
                            <button type="submit" :disabled="form.processing" class="laser-btn-content px-10 py-3 text-white">
                                <span class="text-[10px] font-black uppercase tracking-[0.25em]">{{ editingApp ? t('save_changes') : t('add_application') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
