<script setup>
import { useNotify } from '@/composables/useNotify';
import { useI18n } from 'vue-i18n';

const { activeConfirm, resolveConfirm } = useNotify();
const { t } = useI18n();
</script>

<template>
    <Teleport to="body">
        <Transition name="confirm-fade">
            <div v-if="activeConfirm" class="fixed inset-0 z-[10001] flex items-center justify-center p-6 bg-slate-900/90 backdrop-blur-md" @click.self="resolveConfirm(false)">
                <div class="relative w-full max-w-sm bg-slate-800 border border-white/10 rounded-[2.5rem] shadow-2xl overflow-hidden p-8 text-center animate-in zoom-in-95 duration-300">
                    <!-- Icon -->
                    <div class="mx-auto w-16 h-16 rounded-full bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center mb-6">
                        <span class="text-3xl">❓</span>
                    </div>

                    <h2 class="text-xl font-black text-white mb-3 tracking-tight uppercase">
                        {{ activeConfirm.title }}
                    </h2>
                    
                    <p class="text-slate-400 text-sm mb-8 leading-relaxed font-medium">
                        {{ activeConfirm.message }}
                    </p>

                    <div class="flex flex-col gap-3">
                        <div class="laser-btn-wrapper w-full active:scale-[0.98] transition-all">
                            <button @click="resolveConfirm(true)" class="laser-btn-content w-full py-3.5 text-white font-black text-[10px] uppercase tracking-[0.2em]">
                                {{ t('confirm') || 'Yes, Proceed' }}
                            </button>
                        </div>
                        <button @click="resolveConfirm(false)" class="w-full py-3 text-slate-500 hover:text-white font-bold text-[10px] uppercase tracking-widest transition-colors">
                            {{ t('cancel') || 'Cancel' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.confirm-fade-enter-active, .confirm-fade-leave-active { transition: opacity 0.3s ease; }
.confirm-fade-enter-from, .confirm-fade-leave-to { opacity: 0; }
</style>
