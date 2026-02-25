<script setup>
import { ref } from 'vue';
import { useNotify } from '@/composables/useNotify';
import { useI18n } from 'vue-i18n';

const { history, clearHistory } = useNotify();
const { t } = useI18n();
const isOpen = ref(false);

const timeAgo = (date) => {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
    return Math.floor(seconds / 86400) + 'd ago';
};
</script>

<template>
    <div class="relative">
        <button 
            @click="isOpen = !isOpen"
            class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white transition-all relative border border-white/5"
            :class="{ 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20': isOpen }"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span v-if="history.length > 0" class="absolute top-2 right-2 w-2 h-2 bg-indigo-500 rounded-full shadow-[0_0_8px_rgba(99,102,241,0.5)]"></span>
        </button>

        <Transition name="slide-up">
            <div v-if="isOpen" class="absolute right-0 mt-4 w-80 bg-slate-900/95 backdrop-blur-2xl border border-white/10 rounded-[2rem] shadow-2xl z-50 overflow-hidden" @click.outside="isOpen = false">
                <div class="p-6 border-b border-white/5 flex items-center justify-between">
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Activity</h3>
                    <button @click="clearHistory" class="text-[10px] uppercase font-bold text-slate-500 hover:text-rose-400 transition-colors">Clear all</button>
                </div>

                <div class="max-h-[24rem] overflow-y-auto custom-scrollbar">
                    <div v-if="history.length === 0" class="p-12 text-center">
                        <div class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-600">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-widest uppercase">No messages yet</p>
                    </div>
                    
                    <div v-for="item in history" :key="item.id" class="p-5 border-b border-white/5 last:border-0 hover:bg-white/5 transition-colors group">
                        <div class="flex items-start gap-4">
                            <div :class="[
                                'w-2 h-2 rounded-full mt-1.5 flex-shrink-0',
                                item.type === 'error' ? 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]' :
                                item.type === 'success' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' :
                                'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]'
                            ]"></div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-300 group-hover:text-white transition-colors leading-relaxed">
                                    {{ item.message }}
                                </p>
                                <p class="text-[10px] font-black uppercase tracking-tighter text-slate-600 mt-2">{{ timeAgo(item.timestamp) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.slide-up-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-up-leave-active { transition: all 0.3s ease-in; }
.slide-up-enter-from { opacity: 0; transform: translateY(1rem) scale(0.95); }
.slide-up-leave-to { opacity: 0; transform: translateY(0.5rem); }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>
