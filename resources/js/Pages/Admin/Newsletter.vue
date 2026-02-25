<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PremiumToast from '@/Components/PremiumToast.vue';

const { t } = useI18n();

const props = defineProps({
    subscribers: Object,
});

const exportList = () => {
    router.post(route('admin.newsletter.export'));
};
</script>

<template>
    <Head title="Newsletter — Admin" />

    <div class="min-h-screen bg-slate-900 text-white">
        <!-- Header -->
        <div class="bg-slate-800/60 border-b border-white/10 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">Newsletter Station</h1>
                <p class="text-slate-400 text-xs mt-1">Manage your subscriber base and export lists for campaigns.</p>
            </div>
            <button 
                @click="exportList"
                class="bg-white text-black hover:bg-slate-200 px-6 py-2.5 rounded-xl font-black uppercase tracking-widest text-[10px] transition-all flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV List
            </button>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="bg-slate-800/40 border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 text-[10px] uppercase font-black tracking-[0.2em] border-b border-white/5">
                            <th class="px-6 py-4">Subscriber</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Source</th>
                            <th class="px-6 py-4">Dated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr v-for="sub in subscribers.data" :key="sub.id" class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">{{ sub.email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span 
                                    :class="sub.status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-700/50 text-slate-500'"
                                    class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"
                                >
                                    {{ sub.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-xs">
                                {{ sub.source || 'Generic' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs font-mono">
                                {{ new Date(sub.created_at).toLocaleDateString() }}
                            </td>
                        </tr>
                        <tr v-if="!subscribers.data?.length">
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 italic">No subscribers yet. Start growing your list!</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center" v-if="subscribers.links?.length > 3">
                <!-- Simple Next/Prev would go here -->
            </div>
        </div>

        <PremiumToast />
    </div>
</template>
