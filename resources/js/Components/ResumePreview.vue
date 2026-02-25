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

const engineMap = {
    'standard':  asyncTpl(() => import('./Templates/AtsStandardTemplate.vue')),
    'euro':      asyncTpl(() => import('./Templates/EuroSidebarTemplate.vue')),
    'mena':      asyncTpl(() => import('./Templates/MenaRoyalTemplate.vue')),
    'creative':  asyncTpl(() => import('./Templates/CreativeVisualTemplate.vue')),
    'executive': asyncTpl(() => import('./Templates/ExecutiveMasterTemplate.vue')),
    'minimal':   asyncTpl(() => import('./Templates/MinimalistProTemplate.vue')),
    'ivy':       asyncTpl(() => import('./Templates/IvyLeagueTemplate.vue')),
    'tech':      asyncTpl(() => import('./Templates/TechMatrixTemplate.vue')),
    'grid':      asyncTpl(() => import('./Templates/ModernGridTemplate.vue')),
    'prism':     asyncTpl(() => import('./Templates/PrismTemplate.vue')),
};

const TemplateComponent = computed(() => {
    const slug = (props.resume?.template || props.resume?.theme || 'classic').toLowerCase();
    const rtlKeywords = ['arabic-pro', 'cairo', 'dubai', 'riyadh', 'jeddah', 'mecca', 'amman', 'doha', 'kuwait', 'muscat', 'manama', 'beirut', 'casablanca', 'tunis', 'algiers', 'khartoum', 'baghdad', 'damascus', 'sanaa', 'mogadishu', 'djibouti', 'andalus', 'petra', 'babylon', 'carthage', 'palmyra', 'levant', 'hijaz', 'najd', 'oman'];
    const gridKeywords = ['grid', 'magazine', 'layout', 'column', 'asymmetric', 'geometric', 'design-pro', 'architect'];
    const creativeKeywords = ['creative', 'canvalux', 'vision', 'spark', 'canvas', 'studio', 'portfolio', 'artisan', 'vogue', 'impact', 'horizon', 'lumina', 'radiant', 'mosaic', 'spectrum', 'prism', 'aura', 'nova', 'echo', 'zenith', 'apex', 'pinnacle', 'neon', 'vivid', 'flare', 'burst', 'chroma', 'lucid', 'vibrant', 'dynamic', 'prism', 'bloom', 'shine'];
    const modernKeywords = ['modern', 'nextgen', 'forward', 'contemporary', 'sleek', 'edge', 'nexus', 'pulse', 'velocity', 'momentum', 'catalyst', 'vanguard', 'pioneer', 'innovator', 'agile', 'fluid', 'metro', 'urban', 'civic', 'cosmo', 'cyber', 'digital', 'byte', 'pixel', 'vector', 'data', 'cloud', 'network', 'system'];
    const execKeywords = ['executive', 'luxury', 'elite', 'royal', 'gold', 'titan', 'prestige', 'legacy', 'crown', 'emperor', 'diamond', 'obsidian', 'black', 'dark', 'midnight', 'night', 'deep', 'bold', 'strong', 'powerful', 'leader', 'ceo', 'cto', 'vp', 'manager', 'director', 'pro'];
    const minimalKeywords = ['minimal', 'clean', 'pure', 'white', 'simple', 'basic', 'essentials', 'logic', 'zen', 'calm', 'air', 'breath', 'space', 'flat', 'mono', 'grayscale', 'subtle', 'refined', 'linear', 'sharp', 'crisp'];
    const ivyKeywords = ['ivy', 'harvard', 'yale', 'academic', 'legal', 'lawyer', 'consultant', 'times', 'serif', 'classic-ii', 'oxford', 'cambridge', 'princeton', 'standard-v2', 'finance', 'banking'];
    const techKeywords = ['tech', 'matrix', 'dev', 'code', 'software', 'engineer', 'stack', 'terminal', 'console', 'git', 'silicon', 'valley', 'startup', 'product'];

    if (rtlKeywords.some(k => slug.includes(k))) return engineMap['mena'];
    if (gridKeywords.some(k => slug.includes(k))) return engineMap['grid'];
    if (creativeKeywords.some(k => slug.includes(k))) return engineMap['prism'];
    if (execKeywords.some(k => slug.includes(k))) return engineMap['executive'];
    if (minimalKeywords.some(k => slug.includes(k))) return engineMap['minimal'];
    if (ivyKeywords.some(k => slug.includes(k))) return engineMap['ivy'];
    if (techKeywords.some(k => slug.includes(k))) return engineMap['tech'];
    if (modernKeywords.some(k => slug.includes(k))) return engineMap['euro'];
    return engineMap['standard'];
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
            :is="TemplateComponent" 
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
