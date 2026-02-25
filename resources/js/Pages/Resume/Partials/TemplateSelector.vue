<script setup>
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';

const props = defineProps({
    resume: { type: Object, required: true },
    templates: { type: Array, default: () => [] }
});

const { t } = useI18n();
const activeCategory = ref('all');

// Helper to get actual image or fallback based on component name
const getTemplateImage = (template) => {
    if (template.preview_image) return template.preview_image;
    
    // Fallbacks based on component type if specific images don't exist yet
    const compMap = {
        'ClassicTemplate': '/images/templates/classic.png',
        'ModernTemplate': '/images/templates/modern.png',
        'MinimalTemplate': '/images/templates/classic.png',
        'CreativeTemplate': '/images/templates/modern.png',
        'CanvaLuxTemplate': '/images/templates/modern.png',
        'ArabicProTemplate': '/images/templates/classic.png',
        'TitanTemplate': '/images/templates/titan.png',
        'LuminaTemplate': '/images/templates/lumina.png'
    };
    
    return compMap[template.component] || '/images/templates/classic.png';
};

const categories = [
    { id: 'all', label: t('all_templates') || 'All Templates' },
    { id: 'standard', label: t('professional') || 'Professional' },
    { id: 'creative', label: t('creative') || 'Creative' },
    { id: 'rtl', label: t('arabic_rtl') || 'Arabic & RTL' },
];

const filteredTemplates = computed(() => {
    if (activeCategory.value === 'all') {
        return props.templates;
    }
    return props.templates.filter(t => t.category === activeCategory.value);
});

const selectTemplate = (slug) => {
    router.patch(route('resumes.update', props.resume.id), {
        template: slug,
        theme: slug // using slug as theme for dynamic styling if supported
    }, {
        preserveScroll: true
    });
};
</script>

<template>
    <div class="space-y-6 py-4">
        <div>
            <h3 class="text-3xl font-black text-white tracking-tighter italic">{{ t('template_picker') || 'Choose Template' }}</h3>
            <p class="text-slate-400 text-xs font-black uppercase tracking-[0.2em] mt-2">{{ t('template_desc') || 'Select a premium design to start building your future.' }}</p>
        </div>

        <!-- Categories Filter -->
        <div class="flex flex-wrap gap-2 pt-2">
            <button 
                v-for="cat in categories" 
                :key="cat.id"
                @click="activeCategory = cat.id"
                :class="[
                    'px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all',
                    activeCategory === cat.id 
                        ? 'bg-indigo-600 text-white shadow-[0_0_20px_rgba(79,70,229,0.4)] scale-105' 
                        : 'bg-white/5 text-slate-400 hover:bg-white/10 hover:text-white'
                ]"
            >
                {{ cat.label }}
            </button>
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8 pt-4">
            <div 
                v-for="tpl in filteredTemplates" 
                :key="tpl.id"
                @click="selectTemplate(tpl.slug)"
                class="group relative aspect-[3/4.2] rounded-3xl border-2 cursor-pointer transition-all duration-500 overflow-hidden bg-slate-800 shadow-xl"
                :class="[
                    resume.template === tpl.slug ? 'border-indigo-500 shadow-[0_0_40px_rgba(79,70,229,0.5)] scale-[1.02] z-10' : 'border-white/5 hover:border-white/30 hover:scale-[1.02]'
                ]"
            >
                <!-- Image Preview -->
                <img 
                    :src="getTemplateImage(tpl)" 
                    class="absolute inset-0 w-full h-full object-cover object-top opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700 ease-out"
                    loading="lazy"
                />
                
                <!-- Overlay Gradient -->
                <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-slate-950 via-slate-950/80 to-transparent z-10 transition-opacity group-hover:opacity-95"></div>

                <!-- Label Content -->
                <div class="absolute inset-x-0 bottom-0 p-5 z-20 transform translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                    <span class="text-[8px] font-black uppercase tracking-[0.3em] text-indigo-400 mb-1 block line-clamp-1">{{ tpl.description || t('premium_style') }}</span>
                    <h4 class="text-sm font-black text-white uppercase tracking-tighter italic">{{ tpl.name }}</h4>
                </div>

                <!-- Pro Badge -->
                <div v-if="tpl.is_premium" class="absolute top-4 right-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-[8px] font-black uppercase tracking-[0.2em] px-2.5 py-1 rounded-lg shadow-xl z-20">
                    Pro
                </div>

                <!-- Active Indicator -->
                <div v-if="resume.template === tpl.slug" class="absolute inset-0 bg-indigo-600/10 flex items-center justify-center backdrop-blur-[2px] z-30">
                    <div class="bg-indigo-600 text-white p-2.5 rounded-full shadow-[0_0_30px_rgba(79,70,229,1)] scale-125 animate-in zoom-in-75 duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Empty State -->
        <div v-if="filteredTemplates.length === 0" class="py-12 text-center text-slate-500 text-xs uppercase tracking-widest font-black">
            No templates found in this category.
        </div>

        <div class="flex justify-center pt-8 pb-12">
            <button 
                @click="$emit('next')"
                class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3.5 rounded-full text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-500/20 active:scale-95 transition-all flex items-center gap-3"
            >
                {{ t('start_building') || 'Start Building' }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </div>
</template>
