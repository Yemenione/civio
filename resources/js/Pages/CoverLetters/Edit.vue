<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { useNotify } from '@/composables/useNotify';

const notify = useNotify();
const { t } = useI18n();

const props = defineProps({
    coverLetter: Object,
    resumes: Array,
});

const isEditing = !!props.coverLetter;

const form = useForm({
    title: props.coverLetter?.title || '',
    resume_id: props.coverLetter?.resume_id || '',
    recipient_name: props.coverLetter?.recipient_name || '',
    recipient_title: props.coverLetter?.recipient_title || '',
    company_name: props.coverLetter?.company_name || '',
    job_title: props.coverLetter?.job_title || '',
    body: props.coverLetter?.body || '',
    tone: props.coverLetter?.tone || 'professional',
    language: props.coverLetter?.language || 'en',
});

const isGenerating = ref(false);
const variants = ref([]);
const showVariants = ref(false);

const generateWithAI = async () => {
    if (!form.job_title || !form.company_name) {
        notify.warning('Please enter a Job Title and Company Name first.');
        return;
    }

    isGenerating.value = true;
    variants.value = [];
    showVariants.value = false;

    try {
        const response = await axios.post(route('cover-letters.ai-generate'), {
            job_title: form.job_title,
            company_name: form.company_name,
            tone: form.tone,
            resume_id: form.resume_id,
        });
        
        if (response.data.variants?.length) {
            variants.value = response.data.variants;
            showVariants.value = true;
        } else if (response.data.content) {
            form.body = response.data.content;
            notify.success('AI Letter generated!');
        }
    } catch (e) {
        console.error(e);
        notify.error('AI generation failed. Please try again.');
    } finally {
        isGenerating.value = false;
    }
};

const selectVariant = (content) => {
    form.body = content;
    showVariants.value = false;
    notify.success('Selected variant applied!');
};

const submit = () => {
    if (isEditing) {
        form.put(route('cover-letters.update', props.coverLetter.id));
    } else {
        form.post(route('cover-letters.store'));
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Cover Letter' : 'Create Cover Letter'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <Link :href="route('cover-letters.index')" class="text-slate-400 hover:text-white transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Letters
                </Link>
                <h2 class="font-bold text-xl text-white">{{ isEditing ? 'Edit Letter' : 'New Letter' }}</h2>
                <button @click="submit" :disabled="form.processing" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-full shadow-lg shadow-indigo-500/25 transition-all disabled:opacity-50">
                    {{ isEditing ? 'Save Changes' : 'Create Letter' }}
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-slate-800/60 border border-white/10 rounded-2xl p-6 space-y-4">
                        <h3 class="text-white font-bold text-sm uppercase tracking-wider">Letter Details</h3>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Internal Title</label>
                            <input v-model="form.title" type="text" placeholder="e.g. Google Software Engineer" class="w-full bg-slate-700 border-white/5 rounded-xl text-white text-sm focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Company & Position</label>
                            <div class="space-y-2">
                                <input v-model="form.company_name" type="text" placeholder="Company Name" class="w-full bg-slate-700 border-white/5 rounded-xl text-white text-sm focus:ring-indigo-500 focus:border-indigo-500" />
                                <input v-model="form.job_title" type="text" placeholder="Job Title" class="w-full bg-slate-700 border-white/5 rounded-xl text-white text-sm focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Link to Resume (Optional)</label>
                            <select v-model="form.resume_id" class="w-full bg-slate-700 border-white/5 rounded-xl text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">No Resume Linked</option>
                                <option v-for="resume in resumes" :key="resume.id" :value="resume.id">{{ resume.title }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-indigo-900/20 border border-indigo-500/30 rounded-2xl p-6 space-y-4">
                        <h3 class="text-indigo-400 font-bold text-sm uppercase tracking-wider flex items-center gap-2">
                            <span class="animate-pulse">✨</span> AI Writer
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed">Let AI write a professional letter based on your resume and the target company.</p>
                        
                        <div>
                            <label class="block text-xs font-bold text-indigo-300 uppercase mb-1">Tone</label>
                            <select v-model="form.tone" class="w-full bg-slate-800 border-white/5 rounded-xl text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="professional">Professional</option>
                                <option value="confident">Confident</option>
                                <option value="friendly">Friendly</option>
                            </select>
                        </div>

                        <button @click="generateWithAI" :disabled="isGenerating" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/20 disabled:opacity-50">
                            <span v-if="isGenerating" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            {{ isGenerating ? 'AI Writing...' : 'Write with AI' }}
                        </button>
                    </div>

                    <!-- Triple Vision Selection Panel -->
                    <transition
                        enter-active-class="transition ease-out duration-300 transform"
                        enter-from-class="opacity-0 translate-y-4 scale-95"
                        enter-to-class="opacity-100 translate-y-0 scale-100"
                        leave-active-class="transition ease-in duration-200 transform"
                        leave-from-class="opacity-100 translate-y-0 scale-100"
                        leave-to-class="opacity-0 translate-y-4 scale-95"
                    >
                        <div v-if="showVariants" class="glass-card p-6 rounded-[32px] border border-indigo-500/30 bg-indigo-900/10 space-y-4 shadow-2xl">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-indigo-300 font-black text-[10px] uppercase tracking-[0.2em]">Aura AI: Triple Vision</h3>
                                <button @click="showVariants = false" class="text-slate-500 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                <div v-for="(variant, idx) in variants" :key="idx" 
                                    @click="selectVariant(variant)"
                                    class="group cursor-pointer bg-slate-800/40 hover:bg-indigo-600/20 border border-white/5 hover:border-indigo-500/40 rounded-2xl p-4 transition-all duration-300 active:scale-[0.98]"
                                >
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[8px] font-black uppercase tracking-widest text-indigo-400">Option {{ idx + 1 }}</span>
                                        <div class="w-5 h-5 rounded-full border border-indigo-500/30 flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                    <p class="text-slate-400 group-hover:text-slate-200 text-xs line-clamp-3 leading-relaxed">
                                        {{ variant }}
                                    </p>
                                </div>
                            </div>

                            <p class="text-[8px] text-slate-500 text-center uppercase tracking-tighter">Choose the variant that best matches your target company culture</p>
                        </div>
                    </transition>
                </div>

                <!-- Editor Area -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-2xl min-h-[800px] p-12 text-gray-900 flex flex-col font-serif">
                        <!-- Recipient Section -->
                        <div class="mb-12 space-y-1">
                            <input v-model="form.recipient_name" type="text" placeholder="Recipient Name (or 'Hiring Manager')" class="w-full border-none p-0 text-base font-bold placeholder:text-gray-300 focus:ring-0" />
                            <input v-model="form.recipient_title" type="text" placeholder="Recipient Title" class="w-full border-none p-0 text-sm italic placeholder:text-gray-200 focus:ring-0" />
                            <div class="text-sm font-bold mt-2">{{ form.company_name || 'Company Name' }}</div>
                        </div>

                        <div class="mb-8 text-sm">
                            Dear {{ form.recipient_name || 'Hiring Manager' }},
                        </div>

                        <textarea 
                            v-model="form.body" 
                            placeholder="Type your cover letter here..." 
                            class="flex-1 w-full border-none p-0 text-base leading-relaxed placeholder:text-gray-300 focus:ring-0 resize-none"
                        ></textarea>

                        <div class="mt-12 text-sm">
                            Sincerely,<br><br>
                            <span class="font-bold">Your Name</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
