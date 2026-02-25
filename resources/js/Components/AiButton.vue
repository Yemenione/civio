<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import { usePage } from '@inertiajs/vue3';
import { useNotify } from '@/composables/useNotify';

const props = defineProps(['context', 'language']);
const emit = defineEmits(['generated']);
const { t, locale } = useI18n();
const notify = useNotify();

const showPanel = ref(false);
const prompt = ref('');
const loading = ref(false);
const isPro = true; // Feature is now free for everyone

const handleClick = () => {
    showPanel.value = !showPanel.value;
};

// Map locale codes to full language names for the AI
const getLangName = (code) => {
    const map = { ar: 'Arabic', en: 'English', fr: 'French', es: 'Spanish', de: 'German' };
    return map[code] || 'English';
};

const generate = async (mode = 'generate') => {
    if (mode === 'generate' && !prompt.value) return;
    
    loading.value = true;
    try {
        const targetLang = getLangName(props.language || locale.value);
        const requestData = { 
            prompt: prompt.value || `Generate ${props.context} for ${targetLang}`,
            context: mode === 'translate' ? 'translate' : (mode === 'improve' ? 'improve' : props.context),
            language: targetLang
        };

        const response = await axios.post(route('ai.generate'), requestData);
        emit('generated', response.data.result);
        showPanel.value = false;
        prompt.value = '';
    } catch (e) {
        notify.error('AI Error: ' + (e.response?.data?.error || e.message));
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div>
        <!-- Trigger Button -->
        <div class="laser-btn-wrapper">
            <button
                @click="handleClick"
                type="button"
                class="laser-btn-content px-2 py-1 text-[9px] font-black uppercase tracking-[0.1em] text-white overflow-visible flex items-center gap-1"
            >
                ✨ {{ t('ai_assist') }}
                <svg :class="['w-3 h-3 transition-transform duration-200', showPanel ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Inline AI Panel — slides open below button -->
        <transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-80"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-80"
            leave-to-class="opacity-0 max-h-0"
        >
            <div v-if="showPanel" class="overflow-hidden">
                <div class="mt-3 rounded-xl border border-indigo-200 dark:border-indigo-500/20 bg-indigo-50 dark:bg-indigo-950/40 backdrop-blur-sm p-4 shadow-lg shadow-indigo-500/5">
                    <!-- Header -->
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-lg bg-indigo-500/20 flex items-center justify-center">
                            <span class="text-xs">✨</span>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-indigo-300">{{ t('ai_assist') }}</span>
                        <button
                            @click="showPanel = false"
                            type="button"
                            class="ml-auto text-slate-600 hover:text-slate-400 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Input -->
                    <input
                        v-model="prompt"
                        type="text"
                        @keyup.enter="generate"
                        :placeholder="t('ai_prompt_placeholder', { context: context })"
                        class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-colors"
                        :autofocus="showPanel"
                    />

                    <!-- Actions -->
                    <div class="flex flex-col gap-3 mt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-600">{{ t('press_enter_to_generate') || 'Press Enter or click Generate' }}</span>
                            <button
                                @click="showPanel = false"
                                type="button"
                                class="text-slate-500 hover:text-slate-300 text-[10px] font-bold uppercase tracking-widest transition-colors"
                            >
                                {{ t('cancel') || 'Cancel' }}
                            </button>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="laser-btn-wrapper grow">
                                <button
                                    @click="generate('generate')"
                                    :disabled="loading || !prompt"
                                    type="button"
                                    class="laser-btn-content px-4 py-1.5 text-white flex items-center justify-center gap-1.5 disabled:opacity-40"
                                >
                                    <svg v-if="loading" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span class="text-[10px] font-black uppercase tracking-[0.15em]">
                                        {{ loading ? (t('generating') || 'Generating…') : (t('generate') || 'Generate') }}
                                    </span>
                                </button>
                            </div>
                            
                            <button
                                @click="generate('improve')"
                                :disabled="loading"
                                type="button"
                                class="bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border border-indigo-500/30 transition-all active:scale-95 disabled:opacity-40"
                            >
                                ✨ {{ t('ai_improve') }}
                            </button>

                            <button
                                @click="generate('translate')"
                                :disabled="loading"
                                type="button"
                                class="bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border border-slate-200 dark:border-slate-600 transition-all active:scale-95 disabled:opacity-40"
                            >
                                🌍 {{ t('ai_translate_to', { lang: getLangName(language || locale) }) }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
