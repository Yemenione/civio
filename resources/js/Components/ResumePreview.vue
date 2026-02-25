<script setup>
import { computed, defineAsyncComponent, ref } from 'vue';
import ResumeSkeleton from './ResumeSkeleton.vue';

const props = defineProps({
    resume: { type: Object, required: true },
    scale:  { type: Number, default: 1 },
    editable: { type: Boolean, default: false }
});

const emit = defineEmits(['section-click', 'update-field', 'reorder-sections']);

const asyncTpl = (loader) => defineAsyncComponent({
    loader,
    loadingComponent: ResumeSkeleton,
    delay: 200
});

// Dynamic Component Resolver
// Instead of a hardcoded map, we try to load the component dynamically based on the DB 'component' field
// If that fails or is empty, we fallback to logic based on slug/template name.
const TemplateComponent = computed(() => {
    // 1. Try to use the component name directly if provided by backend (Phase 2 feature)
    // In a real scenario, you can't dynamically import arbitrary strings in Vite without glob
    // So we use a glob import to map all templates
});

// Vite Glob Import for all templates in the Templates directory
const modules = import.meta.glob('./Templates/*.vue');

const DynamicTemplate = computed(() => {
    // Priority 1: Use the explicit 'component' field from the resume/template relation if available
    // (Assuming backend sends it, or we infer it).
    // Since props.resume usually just has 'template' slug, we need to map slug -> ComponentName
    // OR we rely on the hardcoded fallback for now, BUT we make it smarter.

    let componentName = 'AtsStandardTemplate.vue';

    // Map slugs to filenames (Legacy/Fallback Support)
    // This part can be replaced by an API call or props if we pass the full Template object
    const slug = (props.resume?.template || props.resume?.theme || 'classic').toLowerCase();

    const slugMap = {
        'classic':   'AtsStandardTemplate.vue',
        'modern':    'EuroSidebarTemplate.vue',
        'minimal':   'MinimalistProTemplate.vue',
        'creative':  'PrismTemplate.vue',
        'executive': 'ExecutiveMasterTemplate.vue',
        'ivy':       'IvyLeagueTemplate.vue',
        'tech':      'TechMatrixTemplate.vue',
        'grid':      'ModernGridTemplate.vue',
        'mena':      'MenaRoyalTemplate.vue',
        // RTL Logic check
        'rtl':       'MenaRoyalTemplate.vue',
    };

    // If exact match
    if (slugMap[slug]) {
        componentName = slugMap[slug];
    } else {
        // Keyword search (Legacy Logic preserved)
        if (slug.includes('rtl') || slug.includes('arabic')) componentName = 'MenaRoyalTemplate.vue';
        else if (slug.includes('grid')) componentName = 'ModernGridTemplate.vue';
        else if (slug.includes('creative') || slug.includes('prism')) componentName = 'PrismTemplate.vue';
        else if (slug.includes('exec')) componentName = 'ExecutiveMasterTemplate.vue';
        else if (slug.includes('tech')) componentName = 'TechMatrixTemplate.vue';
        else if (slug.includes('modern')) componentName = 'EuroSidebarTemplate.vue';
    }

    // Phase 2: If we start passing the 'component_file' directly from DB in props.resume.template_meta
    if (props.resume?.template_meta?.component) {
        componentName = props.resume.template_meta.component + '.vue';
    }

    // Return the async component from the glob
    const loader = modules[`./Templates/${componentName}`] || modules['./Templates/AtsStandardTemplate.vue'];
    return asyncTpl(loader);
});


const ghostResume = computed(() => {
    const r = { ...props.resume };
    r.fullname = r.fullname || `${r.first_name || ''} ${r.last_name || ''}`.trim();
    r.jobtitle = r.jobtitle || r.job_title;
    r.city     = r.city || r.location?.split(',')[0] || '';
    r.country  = r.country || r.location?.split(',')[1]?.trim() || '';
    if (!r.fullname) r.fullname = "Your Name";
    if (!r.jobtitle) r.jobtitle = "Professional Title";
    if (!r.email) r.email = "email@example.com";
    if (!r.phone) r.phone = "+1 555 0000";
    if (!r.summary) r.summary = "A highly skilled professional with a proven track record...";
    return r;
});

const hexToRgb = (hex) => {
    if (!hex || !hex.startsWith('#')) return '79, 70, 229';
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `${r}, ${g}, ${b}`;
};

const globalStyles = computed(() => {
    const options = props.resume.design_options || {};
    return {
        '--resume-font-size': options.font_size || '10pt',
        '--resume-line-height': options.line_height || 1.5,
        '--accent': options.accent_color || '#4f46e5',
        '--accent-rgb': hexToRgb(options.accent_color || '#4f46e5'),
        '--resume-font-family': options.font_family || 'Inter, sans-serif',
        '--resume-padding-top': (options.margins?.top ?? 20) + 'mm',
        '--resume-padding-bottom': (options.margins?.bottom ?? 20) + 'mm',
        '--resume-padding-left': (options.margins?.left ?? 25) + 'mm',
        '--resume-padding-right': (options.margins?.right ?? 25) + 'mm',
    };
});
</script>

<template>
    <div class="resume-preview-container relative overflow-hidden bg-slate-200" :style="globalStyles">
        <component 
            :is="DynamicTemplate"
            :resume="ghostResume" 
            :editable="editable" 
            @section-click="$emit('section-click', $event)" 
            @update-field="(section, field, id, value) => $emit('update-field', section, field, id, value)"
            @reorder-sections="$emit('reorder-sections', $event)"
        />
    </div>
</template>

<style>
.resume-preview-wrapper {
    font-size: var(--resume-font-size);
    line-height: var(--resume-line-height);
    font-family: var(--resume-font-family);
    color: #1e293b;
    background: white;
    padding: var(--resume-padding-top) var(--resume-padding-right) var(--resume-padding-bottom) var(--resume-padding-left);
    box-sizing: border-box;
    width: 100%;
    margin: 0 auto;
    box-shadow: 0 0 40px rgba(0,0,0,0.1);
}
.editable-section { position: relative; border-radius: 4px; transition: all 0.2s; }
.editable-section:hover { outline: 2px dashed rgba(99, 102, 241, 0.4); background: rgba(99, 102, 241, 0.02); cursor: pointer; }
</style>
