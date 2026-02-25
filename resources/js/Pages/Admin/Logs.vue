<script setup>
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PremiumToast from '@/Components/PremiumToast.vue';

const { t } = useI18n();

const props = defineProps({
    logs: Array,
    logInfo: Object,
});

const refresh = () => {
    router.get(route('admin.logs.index'), {}, { preserveScroll: true, preserveState: true });
};

const toggleMaintenance = () => {
    router.post(route('admin.maintenance.toggle'));
};
</script>

<template>
    <Head title="System Logs — Admin" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Header -->
        <div class="bg-slate-900 border-b border-white/10 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                 <h1 class="text-xl font-bold text-white uppercase tracking-tighter">System Health & Logs</h1>
                 <div class="h-4 w-px bg-white/10"></div>
                 <div class="text-[10px] text-slate-500 font-mono">{{ logInfo.path }} ({{ logInfo.size }})</div>
            </div>
            <div class="flex items-center gap-3">
                <button 
                    @click="toggleMaintenance"
                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-amber-500/10 text-amber-500 border border-amber-500/20 hover:bg-amber-500 hover:text-black"
                >
                    Toggle Maintenance Mode
                </button>
                <button 
                    @click="refresh"
                    class="bg-white/5 hover:bg-white/10 text-white p-2.5 rounded-xl transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>
        </div>

        <div class="max-w-full px-6 py-8">
            <div class="bg-black/40 border border-white/5 rounded-2xl p-6 font-mono text-[11px] leading-relaxed overflow-hidden relative">
                <div class="overflow-y-auto max-h-[70vh] custom-scrollbar space-y-1">
                    <div v-for="(line, idx) in logs" :key="idx" 
                        class="group flex gap-4 p-1 rounded hover:bg-white/5 transition-colors border-b border-white/[0.02]"
                        :class="{
                            'text-red-400 bg-red-500/5': line.includes('ERROR') || line.includes('CRITICAL'),
                            'text-yellow-400 bg-yellow-500/5': line.includes('WARNING'),
                        }"
                    >
                        <span class="text-slate-600 select-none w-8 text-right">{{ logs.length - idx }}</span>
                        <span class="flex-1 whitespace-pre-wrap">{{ line }}</span>
                    </div>
                    <div v-if="!logs.length" class="text-center py-20 text-slate-700 italic">
                        The log file is clear. All systems are operating normally.
                    </div>
                </div>
                
                <!-- Terminal Glow -->
                <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black/60 to-transparent pointer-events-none"></div>
            </div>
        </div>

        <PremiumToast />
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
</style>
