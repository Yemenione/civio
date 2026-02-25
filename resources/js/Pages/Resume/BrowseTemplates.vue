<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import ResumePreview from '@/Components/ResumePreview.vue';
import { getDummyResume } from '@/utils/dummyResume.js';

const props = defineProps({
    templates: { type: Array, required: true }
});

const { t } = useI18n();

const gridContainer = ref(null);
const cardWidth = ref(300); // Default fallback width

onMounted(() => {
    if (gridContainer.value) {
        const observer = new ResizeObserver((entries) => {
            for (let entry of entries) {
                if (entry.contentRect.width > 0) {
                    cardWidth.value = entry.contentRect.width;
                }
            }
        });
        
        const firstCard = gridContainer.value.querySelector('.template-card');
        if (firstCard) {
            observer.observe(firstCard);
        }
    }
});

const calculateScale = computed(() => {
    return cardWidth.value / 800; // 800px is the base width of the template
});

const selectedCategory = ref('All');
const categories = ['All', 'standard', 'creative', 'rtl'];

const filteredTemplates = computed(() => {
    if (selectedCategory.value === 'All') return props.templates;
    return props.templates.filter(t => t.category === selectedCategory.value);
});

const selectTemplate = (slug) => {
    router.post(route('resumes.create'), { template: slug });
};

const getCategoryLabel = (cat) => {
    if (cat === 'standard') return t('professional') || 'Professional';
    if (cat === 'creative') return t('creative') || 'Creative';
    if (cat === 'rtl') return t('arabic_rtl') || 'Arabic Pro';
    return t('all_templates') || 'All';
};
</script>

<template>
    <Head :title="t('template_picker') || 'Choose Template'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="py-6 text-center">
                <h2 class="text-3xl md:text-5xl font-black text-white tracking-tighter mb-4 italic">
                    {{ t('template_picker') || 'Choose your Masterpiece' }}
                </h2>
                <p class="text-slate-400 text-sm md:text-base font-medium max-w-2xl mx-auto uppercase tracking-widest opacity-80">
                    {{ t('template_picker_desc') || 'Select a high-fidelity template to start your career evolution.' }}
                </p>
            </div>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <!-- Filters -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
                <button 
                    v-for="cat in categories" 
                    :key="cat"
                    @click="selectedCategory = cat"
                    :class="[
                        'px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all',
                        selectedCategory === cat 
                            ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-500/20 scale-105' 
                            : 'bg-white/5 text-slate-400 hover:text-white hover:bg-white/10'
                    ]"
                >
                    {{ getCategoryLabel(cat) }}
                </button>
            </div>

            <!-- Template Grid -->
            <div ref="gridContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <div 
                    v-for="tpl in filteredTemplates" 
                    :key="tpl.slug"
                    class="template-card group relative bg-slate-900/40 backdrop-blur-xl border border-white/5 rounded-3xl overflow-hidden hover:border-indigo-500/50 transition-all duration-700 hover:-translate-y-2"
                >
                    <!-- Preview Mock -->
                    <div class="aspect-[210/297] bg-white w-full relative overflow-hidden ring-1 ring-slate-900/5 flex items-start justify-center">
                        <!-- Scaled actual template -->
                        <div class="absolute top-0 left-0 w-[800px] h-[1131px] pointer-events-none" :style="{ transform: `scale(${calculateScale})`, transformOrigin: 'top left' }">
                            <ResumePreview 
                                :resume="{...getDummyResume(), template: tpl.slug, design_options: { accent_color: '#4f46e5' }}" 
                                :editable="false" 
                            />
                        </div>

                        <!-- Hover Action -->
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center p-8 text-center flex-col gap-4 z-20">
                            <span v-if="tpl.is_premium" class="text-[9px] bg-amber-500 text-black px-3 py-1 font-black uppercase tracking-[0.2em] rounded-full shadow-xl">{{ t('premium_style') || 'Premium' }}</span>
                            <button 
                                @click="selectTemplate(tpl.slug)"
                                class="laser-btn-wrapper w-full active:scale-95 transition-all"
                            >
                                <div class="laser-btn-content py-3 text-white text-[10px] font-black uppercase tracking-[0.2em]">
                                    {{ t('use_template') || 'Use Template' }}
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-6 border-t border-white/5 flex items-center justify-between">
                        <div>
                            <h4 class="text-white font-black uppercase tracking-widest text-xs group-hover:text-indigo-400 transition-colors">{{ tpl.name }}</h4>
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">{{ getCategoryLabel(tpl.category) }}</p>
                        </div>
                        <div v-if="tpl.is_premium" class="text-amber-500">
                             <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.laser-btn-wrapper {
    position: relative;
    background: linear-gradient(90deg, #6366f1, #a855f7);
    padding: 1px;
    border-radius: 12px;
}
.laser-btn-content {
    background: #0f172a;
    border-radius: 11px;
}
</style>
