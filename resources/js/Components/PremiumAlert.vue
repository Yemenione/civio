<script setup>
import { useNotify } from '@/composables/useNotify';
import { useI18n } from 'vue-i18n';

const { activeAlert, closeAlert } = useNotify();
const { t } = useI18n();

const getIcon = (type) => {
    switch (type) {
        case 'success': return 'M5 13l4 4L19 7';
        case 'error':   return 'M6 18L18 6M6 6l12 12';
        case 'warning': return 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
        default:        return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }
};

const getColorClass = (type) => {
    switch (type) {
        case 'success': return 'bg-emerald-500 text-white shadow-emerald-500/30';
        case 'error':   return 'bg-rose-500 text-white shadow-rose-500/30';
        case 'warning': return 'bg-amber-500 text-white shadow-amber-500/30';
        default:        return 'bg-indigo-500 text-white shadow-indigo-500/30';
    }
};
</script>

<template>
    <Teleport to="body">
        <Transition name="alert-fade">
            <div v-if="activeAlert" class="fixed inset-0 z-[10000] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-sm" @click.self="closeAlert">
                <div class="relative w-full max-w-md bg-slate-800 border border-white/10 rounded-[2.5rem] shadow-2xl overflow-hidden p-8 text-center animate-in zoom-in-95 duration-300">
                    <!-- Ambient Glow -->
                    <div class="absolute -top-24 -left-24 w-48 h-48 rounded-full blur-[100px] opacity-20" :class="activeAlert.type === 'error' ? 'bg-rose-500' : 'bg-indigo-500'"></div>
                    
                    <!-- Icon -->
                    <div :class="['mx-auto w-20 h-20 rounded-full flex items-center justify-center mb-8 shadow-2xl transition-transform hover:scale-110 duration-500', getColorClass(activeAlert.type)]">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" :d="getIcon(activeAlert.type)"/>
                        </svg>
                    </div>

                    <h2 class="text-2xl font-black text-white mb-4 tracking-tight uppercase">
                        {{ activeAlert.title || (activeAlert.type === 'error' ? t('error') : t('notice')) }}
                    </h2>
                    
                    <p class="text-slate-400 text-lg mb-10 leading-relaxed font-medium">
                        {{ activeAlert.message }}
                    </p>

                    <div class="laser-btn-wrapper w-full active:scale-[0.98] transition-all">
                        <button @click="closeAlert" class="laser-btn-content w-full py-4 text-white font-black text-xs uppercase tracking-[0.2em]">
                            {{ t('ok') || 'Dismiss' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.alert-fade-enter-active, .alert-fade-leave-active { transition: opacity 0.3s ease; }
.alert-fade-enter-from, .alert-fade-leave-to { opacity: 0; }
</style>
