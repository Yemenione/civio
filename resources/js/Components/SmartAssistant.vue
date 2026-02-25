<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import { usePage } from '@inertiajs/vue3';
import { useNotify } from '@/composables/useNotify';

const props = defineProps({
    modelValue: String,
    context: {
        type: String,
        required: true
    },
    language: String,
    placeholder: String,
    variant: {
        type: String,
        default: 'compact' // 'compact' or 'full'
    }
});

const emit = defineEmits(['update:modelValue', 'generated']);
const { t, locale } = useI18n();
const notify = useNotify();
const page = usePage();

const showActions = ref(false);
const loading = ref(false);
const localPrompt = ref('');

const getLangName = (code) => {
    const map = { ar: 'Arabic', en: 'English', fr: 'French', es: 'Spanish', de: 'German' };
    return map[code] || 'English';
};

const execute = async (mode) => {
    if (mode === 'generate' && !localPrompt.value) return;
    if ((mode === 'improve' || mode === 'translate') && !props.modelValue) {
        notify.alert(t('error'), t('no_content_to_process'), 'info');
        return;
    }

    loading.value = true;
    try {
        const targetLang = getLangName(props.language || locale.value);
        const engineContext = mode === 'improve' ? 'improve' : (mode === 'translate' ? 'translate' : props.context);
        
        const response = await axios.post(route('ai.generate'), { 
            prompt: mode === 'generate' ? localPrompt.value : props.modelValue,
            context: engineContext,
            language: targetLang
        });

        const result = response.data.result;
        
        if (mode === 'generate') {
            emit('generated', result);
            localPrompt.value = '';
        } else {
            emit('update:modelValue', result);
        }
        
        notify.success(t('ai_success') || 'AI Magic Applied!');
        showActions.value = false;
    } catch (e) {
        notify.error('AI Error: ' + (e.response?.data?.error || e.message));
    } finally {
        loading.value = false;
    }
};

const actions = computed(() => [
    { id: 'improve', label: t('ai_improve'), icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z', color: 'text-indigo-400' },
    { id: 'translate', label: t('ai_translate'), icon: 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129', color: 'text-emerald-400' },
]);
</script>

<template>
    <div class="relative block w-full">
        <!-- Main Toggle Button -->
        <div class="flex items-center gap-2 group/smart w-full">
             <div class="laser-btn-wrapper !rounded-full active:scale-95 transition-all shadow-lg hover:shadow-indigo-500/20">
                <button
                    @click="showActions = !showActions"
                    type="button"
                    :class="[
                        'laser-btn-content !rounded-full flex items-center justify-center text-white transition-all',
                        variant === 'compact' ? 'w-6 h-6 p-0 text-[10px]' : 'px-2 py-0.5 gap-1 text-[9px] font-black uppercase tracking-widest'
                    ]"
                >
                    <svg v-if="loading" :class="[variant === 'compact' ? 'w-3 h-3' : 'w-2.5 h-2.5', 'animate-spin']" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <template v-else>
                        <span>✨</span>
                        <span v-if="variant !== 'compact'">{{ t('smart_assistant', 'Smart Assistant') }}</span>
                        <svg v-if="variant !== 'compact'" :class="['w-2 h-2 transition-transform duration-300', showActions ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                    </template>
                </button>
            </div>
            
            <!-- Quick Actions (Horizontal when collapsed) -->
            <div v-if="!showActions && modelValue" class="flex items-center gap-1.5 opacity-0 group-hover/smart:opacity-100 transition-opacity duration-500 overflow-hidden">
                <button v-for="action in actions" :key="action.id" @click="execute(action.id)" :title="action.label" class="p-1.5 rounded-lg bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 hover:border-indigo-500/50 transition-all active:scale-90 flex-shrink-0">
                    <svg :class="['w-3 h-3', action.color]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="action.icon"/></svg>
                </button>
            </div>
        </div>

        <!-- Expansion Panel (Inline) -->
        <transition
            enter-active-class="transition-all duration-500 ease-out"
            enter-from-class="opacity-0 max-h-0 py-0"
            enter-to-class="opacity-100 max-h-[400px] py-4"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="opacity-100 max-h-[400px] py-4"
            leave-to-class="opacity-0 max-h-0 py-0"
        >
            <div v-if="showActions" class="w-full bg-slate-100/50 dark:bg-black/20 border border-indigo-500/20 rounded-[20px] p-3 overflow-hidden mt-2">
                <div class="relative z-10 space-y-3">
                    <!-- Generation Section -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between px-1">
                            <label class="text-[7px] font-black uppercase tracking-[0.2em] text-indigo-400">{{ t('ai_write_from_scratch', 'AI Write') }}</label>
                            <button @click="showActions = false" class="text-slate-400 hover:text-rose-500 transition-colors">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <div class="relative group/input">
                            <input
                                v-model="localPrompt"
                                type="text"
                                :placeholder="placeholder || t('ai_prompt_hint', 'Describe...')"
                                @keyup.enter="execute('generate')"
                                class="w-full rounded-lg border border-black/5 dark:border-white/10 bg-white dark:bg-white/5 text-slate-800 dark:text-white px-2.5 py-1.5 text-[9px] focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 transition-all outline-none pr-8"
                            />
                            <button @click="execute('generate')" :disabled="!localPrompt || loading" class="absolute right-1 top-1/2 -translate-y-1/2 p-1 rounded-md bg-indigo-500 text-white shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all disabled:opacity-30">
                                <svg v-if="loading" class="w-2 h-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <svg v-else class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Direct Actions -->
                    <div class="grid grid-cols-2 gap-2 pt-0.5">
                        <button v-for="action in actions" :key="action.id" 
                            @click="execute(action.id)"
                            :disabled="loading"
                            class="flex items-center gap-1.5 p-1.5 rounded-lg bg-white dark:bg-white/5 border border-black/5 dark:border-white/10 hover:border-indigo-500/40 hover:bg-slate-50 dark:hover:bg-white/10 transition-all group/item disabled:opacity-50"
                        >
                            <div :class="['w-5 h-5 rounded-md bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover/item:scale-110 transition-transform shadow-inner', action.color]">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="action.icon"/></svg>
                            </div>
                            <span class="text-[7px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300 truncate">{{ action.label }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.cubic-bezier {
    transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
}
</style>
