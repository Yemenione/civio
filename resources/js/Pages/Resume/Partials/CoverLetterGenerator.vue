<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useI18n } from 'vue-i18n';
import { usePage } from '@inertiajs/vue3';
import { useNotify } from '@/composables/useNotify';

const props = defineProps(['resume']);
const { t } = useI18n();
const notify = useNotify();
const page = usePage();
const isPro = true; // Now free for everyone

const handleClick = () => {
    show.value = true;
}

const show = ref(false);
const jobDescription = ref('');
const coverLetter = ref('');
const variants = ref([]);
const loading = ref(false);

const generate = async () => {
    if (!jobDescription.value) return;
    loading.value = true;
    variants.value = [];
    
    try {
        const response = await axios.post(route('cover-letters.ai-generate'), {
            job_title: props.resume.job_title,
            company_name: jobDescription.value, // Using job description as company name context if company isn't provided separately
            tone: 'professional',
            resume_id: props.resume.id,
            variants: 3
        });
        
        if (response.data.variants?.length) {
            variants.value = response.data.variants;
            coverLetter.value = response.data.variants[0]; // Set first one by default
        } else {
            coverLetter.value = response.data.content;
        }
    } catch (e) {
        notify.error(t('cl_gen_error'));
    } finally {
        loading.value = false;
    }
};

const selectVariant = (v) => {
    coverLetter.value = v;
};

const copyToClipboard = () => {
    navigator.clipboard.writeText(coverLetter.value);
    notify.success(t('cl_copied'));
};
</script>

<template>
    <div class="laser-btn-wrapper shadow-lg shadow-purple-500/20 active:scale-95 transition-transform hover:scale-105 relative">
        <button @click="handleClick" class="laser-btn-content px-3 py-1.5 flex items-center gap-2 text-white overflow-visible">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
             <span class="text-xs font-black uppercase tracking-tight italic">✨ {{ t('ai_assist') }} CL</span>
        </button>
    </div>

    <Modal :show="show" @close="show = false">
        <div class="p-6 bg-white dark:bg-gray-800">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">{{ t('cl_gen_title') }}</h2>
            
            <div v-if="!coverLetter">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('cl_gen_desc') }}</label>
                <textarea v-model="jobDescription" rows="6" class="w-full border-gray-300 dark:bg-gray-900 dark:text-white rounded-md shadow-sm mb-4" :placeholder="t('cl_gen_placeholder')"></textarea>
                
                <div class="flex justify-end gap-2">
                    <button @click="show = false" class="px-4 py-2 text-gray-600">{{ t('cancel') }}</button>
                    <button @click="generate" :disabled="loading || !jobDescription" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 disabled:opacity-50">
                        {{ loading ? t('cl_gen_loading') : t('cl_gen_button') }}
                    </button>
                </div>
            </div>

            <div v-else>
                <!-- Variants Selector if available -->
                <div v-if="variants.length > 1" class="flex gap-2 mb-4 overflow-x-auto pb-2">
                    <button 
                        v-for="(v, i) in variants" :key="i"
                        @click="selectVariant(v)"
                        :class="[coverLetter === v ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 hover:bg-slate-200']"
                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap"
                    >
                        Option {{ i + 1 }}
                    </button>
                </div>

                <div class="bg-gray-100 dark:bg-gray-900 p-6 rounded-2xl whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200 h-80 overflow-y-auto mb-4 border border-white/10 shadow-inner font-serif leading-relaxed">
                    {{ coverLetter }}
                </div>
                <div class="flex justify-end gap-3">
                    <button @click="coverLetter = ''; variants = []" class="px-4 py-2 text-slate-500 hover:text-white text-xs font-black uppercase tracking-widest transition-colors">{{ t('back') }}</button>
                    <PrimaryButton @click="copyToClipboard">{{ t('cl_copy_clipboard') }}</PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>
