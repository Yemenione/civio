<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
import { useNotify } from '@/composables/useNotify';

const notify = useNotify();

const props = defineProps({
    coverLetters: Array,
    resumes: Array,
});

const deleteCL = (id) => {
    notify.confirm(
        'Delete Cover Letter?',
        'Are you sure you want to delete this cover letter? This action cannot be undone.',
        () => router.delete(route('cover-letters.destroy', id))
    );
};
</script>

<template>
    <Head title="Cover Letters" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-white leading-tight">Cover Letters</h2>
                <div class="laser-btn-wrapper shadow-lg shadow-indigo-500/20 active:scale-95 transition-transform hover:scale-105">
                    <Link
                        :href="route('cover-letters.create')"
                        class="laser-btn-content px-5 py-2.5 text-white flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        <span class="text-xs font-black uppercase tracking-[0.2em]">Create New</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Grid -->
                <div v-if="coverLetters.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="cl in coverLetters" :key="cl.id" class="bg-slate-800/60 border border-white/10 rounded-2xl p-6 flex flex-col hover:border-indigo-500/40 transition-all group">
                        <div class="flex-1">
                            <h3 class="font-bold text-white text-lg mb-1 truncate">{{ cl.title }}</h3>
                            <p class="text-indigo-400 text-sm mb-3">{{ cl.company_name || 'No company' }}</p>
                            <p class="text-slate-400 text-sm line-clamp-3 leading-relaxed mb-4">
                                {{ cl.body }}
                            </p>
                        </div>
                        
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-white/5">
                            <span class="text-xs text-slate-500">
                                {{ new Date(cl.updated_at).toLocaleDateString() }}
                            </span>
                            <div class="flex items-center gap-2">
                                <Link :href="route('cover-letters.edit', cl.id)" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-medium rounded-lg transition-colors">
                                    Edit
                                </Link>
                                <button @click="deleteCL(cl.id)" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-medium rounded-lg transition-colors">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-slate-800/50 border border-white/10 rounded-3xl p-16 text-center flex flex-col items-center">
                    <div class="w-20 h-20 bg-indigo-500/15 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">No cover letters yet</h3>
                    <p class="text-slate-400 max-w-sm mb-8 text-sm">Create a personalized cover letter for your next job application using our AI builder.</p>
                    <div class="laser-btn-wrapper shadow-2xl shadow-indigo-500/20 active:scale-95 transition-transform hover:scale-105">
                        <Link :href="route('cover-letters.create')" class="laser-btn-content px-8 py-3 flex items-center gap-2 text-white">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                             <span class="text-sm font-black uppercase tracking-[0.1em]">Create Your First Letter</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
