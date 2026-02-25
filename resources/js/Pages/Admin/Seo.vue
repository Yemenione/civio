<script setup>
import { ref, reactive } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PremiumToast from '@/Components/PremiumToast.vue';

const { t } = useI18n();

const props = defineProps({
    seo: Object, // Keyed by page_name
});

const pages = [
    { name: 'home', label: 'Landing Page' },
    { name: 'dashboard', label: 'User Dashboard' },
    { name: 'pricing', label: 'Pricing Page' },
    { name: 'editor', label: 'Resume Editor' },
];

const selectedPage = ref('home');
const locales = ['en', 'ar', 'fr'];

const forms = reactive({});
pages.forEach(p => {
    const data = props.seo[p.name] || {};
    forms[p.name] = useForm({
        page_name: p.name,
        title: data.title || { en: '', ar: '', fr: '' },
        description: data.description || { en: '', ar: '', fr: '' },
        keywords: data.keywords || { en: '', ar: '', fr: '' },
    });
});

const submit = (pageName) => {
    forms[pageName].post(route('admin.seo.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by flash
        }
    });
};
</script>

<template>
    <Head title="SEO Management — Admin" />

    <div class="min-h-screen bg-slate-900 text-white">
        <!-- Header -->
        <div class="bg-slate-800/60 border-b border-white/10 px-6 py-4">
            <h1 class="text-xl font-bold text-white">SEO & Content Suite</h1>
            <p class="text-slate-400 text-xs mt-1">Manage Meta Tags and Keywords per language for every page.</p>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8 flex gap-8">
            <!-- Sidebar Navigation -->
            <div class="w-64 flex-shrink-0 space-y-2">
                <button
                    v-for="p in pages"
                    :key="p.name"
                    @click="selectedPage = p.name"
                    :class="selectedPage === p.name ? 'bg-indigo-600 text-white' : 'hover:bg-white/5 text-slate-400'"
                    class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium transition-all"
                >
                    {{ p.label }}
                </button>
            </div>

            <!-- Content Area -->
            <div class="flex-1 bg-slate-800/50 border border-white/10 rounded-2xl p-8">
                <div v-if="selectedPage" class="space-y-8">
                    <div class="flex items-center justify-between border-b border-white/5 pb-4">
                        <h2 class="text-lg font-bold">{{ pages.find(p => p.name === selectedPage).label }} SEO</h2>
                    </div>

                    <form @submit.prevent="submit(selectedPage)" class="space-y-10">
                        <div v-for="lang in locales" :key="lang" class="bg-slate-900/50 border border-white/5 rounded-xl p-6 relative">
                            <span class="absolute top-4 right-6 text-[10px] font-black uppercase tracking-widest text-indigo-400 bg-indigo-400/10 px-2 py-0.5 rounded-full">
                                {{ lang }}
                            </span>

                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1 block">SEO Title ({{ lang }})</label>
                                    <input
                                        v-model="forms[selectedPage].title[lang]"
                                        type="text"
                                        class="w-full bg-slate-800 border border-white/10 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1 block">Meta Description ({{ lang }})</label>
                                    <textarea
                                        v-model="forms[selectedPage].description[lang]"
                                        rows="3"
                                        class="w-full bg-slate-800 border border-white/10 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 resize-none"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1 block">Keywords ({{ lang }})</label>
                                    <input
                                        v-model="forms[selectedPage].keywords[lang]"
                                        type="text"
                                        placeholder="Comma separated..."
                                        class="w-full bg-slate-800 border border-white/10 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button
                                type="submit"
                                :disabled="forms[selectedPage].processing"
                                class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3 rounded-xl font-bold text-sm transition-all disabled:opacity-50 shadow-lg shadow-indigo-600/20"
                            >
                                {{ forms[selectedPage].processing ? 'Saving...' : 'Save Meta Settings' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <PremiumToast />
    </div>
</template>
