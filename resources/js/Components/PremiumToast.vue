<script setup>
import { useNotify } from '@/composables/useNotify';
const { toasts, dismissToast } = useNotify();

const getIcon = (type) => {
    switch (type) {
        case 'success': return '✓';
        case 'error':   return '✕';
        case 'warning': return '⚠';
        default:        return 'ℹ';
    }
};

const getColor = (type) => {
    switch (type) {
        case 'success': return 'border-emerald-500/30 text-emerald-400 bg-emerald-500/5';
        case 'error':   return 'border-rose-500/30 text-rose-400 bg-rose-500/5';
        case 'warning': return 'border-amber-500/30 text-amber-400 bg-amber-500/5';
        default:        return 'border-indigo-500/30 text-indigo-400 bg-indigo-500/5';
    }
};
</script>

<template>
    <Teleport to="body">
        <div class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 items-end pointer-events-none w-full max-w-sm">
            <TransitionGroup name="premium-toast">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    @click="dismissToast(toast.id)"
                    :class="[
                        'pointer-events-auto cursor-pointer flex items-center gap-4 px-5 py-4 rounded-2xl backdrop-blur-2xl border shadow-2xl transition-all duration-300 group',
                        getColor(toast.type)
                    ]"
                >
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-white/5 border border-white/10 text-lg font-bold">
                        {{ getIcon(toast.type) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-black uppercase tracking-widest opacity-50 mb-0.5">{{ toast.type }}</p>
                        <p class="text-sm font-semibold text-white/90">{{ toast.message }}</p>
                    </div>
                    <button class="opacity-0 group-hover:opacity-100 transition-opacity p-1 hover:bg-white/5 rounded-lg">
                        <svg class="w-4 h-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
.premium-toast-enter-active { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
.premium-toast-leave-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.premium-toast-enter-from { opacity: 0; transform: translateX(4rem) scale(0.9) blur(10px); }
.premium-toast-leave-to { opacity: 0; transform: translateY(-1rem) scale(0.95); }
</style>
