<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import { usePage } from '@inertiajs/vue3';
import { useNotify } from '@/composables/useNotify';

const props = defineProps({
    modelValue: String,
    context: String,
    language: String,
});

const emit = defineEmits(['update:modelValue']);
const { t } = useI18n();
const notify = useNotify();
const page = usePage();

const loading = ref(false);
const isPro = true; // Now free for everyone

const improve = async () => {
    if (!props.modelValue || props.modelValue.length < 5) {
        notify.alert(t('error') || 'Error', t('too_short') || 'Text is too short to improve.', 'info');
        return;
    }

    loading.value = true;
    try {
        const response = await axios.post(route('ai.generate'), { 
            prompt: props.modelValue,
            context: 'improve',
            language: props.language || page.props.resume?.language || 'English'
        });
        emit('update:modelValue', response.data.result);
        notify.success(t('ai_improved') || 'Text improved by AI!');
    } catch (e) {
        notify.error('AI Error: ' + (e.response?.data?.error || e.message));
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="flex items-center gap-2">
        <button
            @click.prevent="improve"
            :disabled="loading"
            type="button"
            class="group flex items-center gap-1.5 px-2 py-1 rounded-md bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/10 transition-all active:scale-95 disabled:opacity-50"
            :title="t('ai_improve_desc')"
        >
            <svg v-if="!loading" class="w-3 h-3 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <svg v-else class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-[9px] font-black uppercase tracking-widest">{{ loading ? (t('generating')) : (t('ai_improve')) }}</span>
        </button>
    </div>
</template>
