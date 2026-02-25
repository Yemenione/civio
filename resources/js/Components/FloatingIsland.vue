<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { usePage, router } from '@inertiajs/vue3';
import { useNotify } from '@/composables/useNotify';

const props = defineProps({
    resume: { type: Object, required: true }
});

const { t } = useI18n();
const notify = useNotify();
const page = usePage();
const emit = defineEmits(['ai-optimize']);

const activePopover = ref(null); // 'templates', 'typography', 'atmosphere', 'structure', 'precision'

const togglePopover = (name) => {
    if (activePopover.value === name) {
        activePopover.value = null;
    } else {
        activePopover.value = name;
    }
};

// --- Templates ---
const templatesList = [
    { id: 'classic',      label: 'Classic',      color: '#374151', pro: false },
    { id: 'modern',       label: 'Modern',       color: '#4f46e5', pro: true },
    { id: 'minimal',      label: 'Minimal',      color: '#0f172a', pro: false },
    { id: 'creative',     label: 'Creative',     color: '#7c3aed', pro: true },
    { id: 'executive',    label: 'Executive',    color: '#1e293b', pro: true },
    { id: 'canvalux',     label: 'CanvaLux',     color: '#be185d', pro: true },
    { id: 'arabicpro',    label: 'Arabic Pro',   color: '#065f46', pro: true },
    { id: 'professional', label: 'Professional', color: '#2563eb', pro: true },
    { id: 'elite',        label: 'Elite Prism',  color: '#0f172a', pro: true },
    { id: 'tech',         label: 'Tech Fusion',  color: '#0891b2', pro: true },
    { id: 'startup',      label: 'Startup',      color: '#db2777', pro: true },
    { id: 'academic',     label: 'Academic',      color: '#475569', pro: true },
    { id: 'elegant',      label: 'Elegant',       color: '#854d0e', pro: true },
    { id: 'obsidian',     label: 'Obsidian Pro',  color: '#0f172a', pro: true },
    { id: 'royal',        label: 'Royal Ivory',   color: '#854d0e', pro: true },
    { id: 'grid',         label: 'Creative Grid', color: '#db2777', pro: true },
    { id: 'titan',        label: 'Titan Bold',    color: '#1e293b', pro: true },
    { id: 'lumina',       label: 'Lumina Glass',  color: '#4f46e5', pro: true },
    { id: 'minimalist-plus', label: 'Minimalist+', color: '#1e293b', pro: true },
];

const selectTemplate = (id, isPro) => {
    const userPro = page.props.auth.user && page.props.auth.user.plan === 'pro';
    if (isPro && !userPro) {
        notify.confirm(
            t('pro_feature_label'),
            t('pro_template_confirm') || "This template is a Pro feature. Upgrade now?",
            () => window.location.href = route('pricing')
        );
        return;
    }
    
    props.resume.template = id;
    props.resume.theme = id;
    router.patch(route('resumes.update', props.resume.id), { 
        template: id,
        theme: id
    }, { preserveScroll: true });
};

// --- Design Options ---
const design = ref({
    font_size: props.resume.design_options?.font_size || '9pt',
    line_height: props.resume.design_options?.line_height || 1.4,
    font_family: props.resume.design_options?.font_family || 'Inter, sans-serif',
    accent_color: props.resume.design_options?.accent_color || '#4f46e5',
    letter_spacing: props.resume.design_options?.letter_spacing || 0,
    section_gap: props.resume.design_options?.section_gap || 20,
    section_radius: props.resume.design_options?.section_radius || 0,
    shadow_intensity: props.resume.design_options?.shadow_intensity || 0,
    page_texture: props.resume.design_options?.page_texture || 'smooth',
    inner_padding: props.resume.design_options?.inner_padding || 0,
    margins: props.resume.design_options?.margins || { top: 20, bottom: 20, left: 25, right: 25 },
    section_order: props.resume.design_options?.section_order || ["summary", "experience", "education", "skills", "languages", "certifications", "projects"],
    hidden_sections: props.resume.design_options?.hidden_sections || [],
});

const fonts = [
    { name: 'Modern Sans', value: 'Inter, system-ui, sans-serif' },
    { name: 'Elegant Serif', value: '"Playfair Display", serif' },
    { name: 'Clean Mono', value: '"Roboto Mono", monospace' },
    { name: 'Geometry', value: '"Outfit", sans-serif' },
    { name: 'Futuristic', value: '"Orbitron", sans-serif' },
    { name: 'Universal', value: '"Montserrat", sans-serif' },
];

let debounceTimer = null;
const updateDesign = () => {
    props.resume.design_options = { ...design.value };
    
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.patch(route('resumes.update', props.resume.id), {
            design_options: design.value
        }, { preserveScroll: true, preserveState: true });
    }, 500); // 500ms debounce
};

const toggleSection = (section) => {
    const hidden = [...design.value.hidden_sections];
    const index = hidden.indexOf(section);
    if (index > -1) hidden.splice(index, 1);
    else hidden.push(section);
    design.value.hidden_sections = hidden;
    updateDesign();
};

// --- Drag and Drop for Section Reordering ---
const dragIndex = ref(null);

const onDragStart = (e, index) => {
    dragIndex.value = index;
    // Slight delay to allow the drag image to capture the dragged element properly
    setTimeout(() => {
        e.target.classList.add('opacity-50');
    }, 0);
};

const onDrop = (e, targetIndex) => {
    e.target.closest('.group').classList.remove('opacity-50');
    if (dragIndex.value === null) return;
    
    const items = [...design.value.section_order];
    const [draggedItem] = items.splice(dragIndex.value, 1);
    items.splice(targetIndex, 0, draggedItem);
    
    design.value.section_order = items;
    updateDesign();
    dragIndex.value = null;
};

const onDragEnd = (e) => {
    e.target.classList.remove('opacity-50');
    dragIndex.value = null;
};

const optimizeWithAi = () => {
    emit('ai-optimize');
};

const instantPrint = () => {
    window.open(route('resumes.print', props.resume.id), '_blank');
};

// Click outside to close
const islandRef = ref(null);
const handleClickOutside = (event) => {
    if (islandRef.value && !islandRef.value.contains(event.target)) {
        activePopover.value = null;
    }
};

onMounted(() => document.addEventListener('mousedown', handleClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside));

</script>

<template>
    <div ref="islandRef" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[200]">
        <!-- Popovers container -->
        <div class="relative w-full">
            <Transition
                enter-active-class="transition duration-500 cubic-bezier(0.34, 1.56, 0.64, 1)"
                enter-from-class="transform scale-75 opacity-0 translate-y-10"
                enter-to-class="transform scale-100 opacity-100 translate-y-0"
                leave-active-class="transition duration-300 ease-in"
                leave-from-class="transform scale-100 opacity-100 translate-y-0"
                leave-to-class="transform scale-75 opacity-0 translate-y-10"
            >
                <div v-if="activePopover" class="absolute bottom-full mb-6 left-1/2 -translate-x-1/2 w-96 bg-slate-900/90 backdrop-blur-3xl border border-white/10 rounded-[40px] shadow-[0_40px_100px_rgba(0,0,0,0.9)] p-6 overflow-hidden">
                    <!-- Glow effect -->
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/10 blur-[60px] rounded-full pointer-events-none"></div>

                    <!-- Templates Popover -->
                    <div v-if="activePopover === 'templates'" class="space-y-4">
                        <div class="flex items-center justify-between px-2 mb-2">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Design Universe</h4>
                            <span class="text-[8px] font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-full">{{ templatesList.length }} Themes</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 max-h-80 overflow-y-auto pr-1 scrollbar-hide">
                            <button 
                                v-for="tpl in templatesList" 
                                :key="tpl.id"
                                @click="selectTemplate(tpl.id, tpl.pro)"
                                class="group relative h-16 rounded-[20px] border transition-all duration-300 overflow-hidden flex flex-col justify-end p-3 active:scale-95"
                                :class="[resume.template === tpl.id ? 'border-indigo-500 bg-indigo-500/10 shadow-[0_0_20px_rgba(99,102,241,0.2)]' : 'border-white/5 bg-white/5 hover:border-white/10']"
                            >
                                <div class="absolute inset-0 opacity-20 group-hover:opacity-30 transition-opacity" :style="{ backgroundColor: tpl.color }"></div>
                                <span class="relative z-10 text-[10px] font-black uppercase text-white tracking-widest">{{ tpl.label }}</span>
                                <span v-if="tpl.pro" class="absolute top-2 right-2 text-[7px] bg-amber-500 text-black px-1.5 py-0.5 rounded-md font-black shadow-lg">PRO</span>
                            </button>
                        </div>
                    </div>

                    <!-- Typography Popover -->
                    <div v-if="activePopover === 'typography'" class="space-y-6">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 px-2">Typography</h4>
                        <div class="space-y-5 px-1">
                            <div>
                                <label class="text-[9px] font-black uppercase text-slate-500 mb-2.5 block tracking-widest">Typeface System</label>
                                <div class="grid grid-cols-1 gap-1.5">
                                    <button 
                                        v-for="f in fonts" 
                                        :key="f.value" 
                                        @click="design.font_family = f.value; updateDesign()"
                                        class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all border text-[11px]"
                                        :class="[design.font_family === f.value ? 'bg-indigo-500 text-white border-indigo-400 shadow-lg' : 'bg-white/5 text-slate-400 border-transparent hover:bg-white/10']"
                                    >
                                        <span :style="{ fontFamily: f.value }">{{ f.name }}</span>
                                        <div v-if="design.font_family === f.value" class="w-1.5 h-1.5 rounded-full bg-white shadow-[0_0_8px_white]"></div>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-3">
                                    <label class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Base Font Size</label>
                                    <span class="text-[10px] text-indigo-400 font-black">{{ design.font_size }}</span>
                                </div>
                                <input type="range" min="7" max="14" step="0.5" 
                                       :value="parseFloat(design.font_size)" 
                                       @input="design.font_size = $event.target.value + 'pt'; updateDesign()"
                                       class="ultra-range w-full">
                            </div>
                        </div>
                    </div>

                    <!-- Aesthetics Popover -->
                    <div v-if="activePopover === 'aesthetics'" class="space-y-6">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 px-2">Aesthetic Identity</h4>
                        <div class="space-y-5 px-1 scrollbar-hide max-h-96 overflow-y-auto">
                            <!-- Accent Color -->
                            <div>
                                <label class="text-[9px] font-black uppercase text-slate-500 mb-3 block tracking-widest">Accent Core</label>
                                <div class="flex items-center gap-4 bg-white/5 p-3 rounded-[20px] border border-white/5">
                                    <div class="relative w-10 h-10 rounded-xl overflow-hidden cursor-pointer shadow-inner">
                                        <input type="color" v-model="design.accent_color" @input="updateDesign" class="absolute inset-x-[-50%] inset-y-[-50%] w-[200%] h-[200%] cursor-pointer">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-white font-mono font-black uppercase tracking-widest">{{ design.accent_color }}</span>
                                        <span class="text-[8px] text-slate-500 font-bold uppercase">{{ t('primary_hue') || 'Vibrant' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Page Texture -->
                            <div>
                                <label class="text-[9px] font-black uppercase text-slate-500 mb-3 block tracking-widest">Surface Geometry</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button 
                                        v-for="tex in ['smooth', 'grain', 'paper']" 
                                        :key="tex"
                                        @click="design.page_texture = tex; updateDesign()"
                                        class="py-2.5 rounded-xl border text-[9px] font-black uppercase tracking-widest transition-all"
                                        :class="[design.page_texture === tex ? 'bg-indigo-500 text-white border-indigo-400 shadow-md' : 'bg-white/5 text-slate-400 border-transparent']"
                                    >
                                        {{ tex }}
                                    </button>
                                </div>
                            </div>

                            <!-- Corner Radius -->
                            <div>
                                <div class="flex justify-between mb-3">
                                    <label class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Section Radius</label>
                                    <span class="text-[10px] text-indigo-400 font-black">{{ design.section_radius }}px</span>
                                </div>
                                <input type="range" min="0" max="40" step="4" v-model="design.section_radius" @input="updateDesign" class="ultra-range w-full">
                            </div>

                            <!-- Shadow Intensity -->
                            <div>
                                <div class="flex justify-between mb-3">
                                    <label class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Depth (Shadows)</label>
                                    <span class="text-[10px] text-indigo-400 font-black">{{ design.shadow_intensity }}%</span>
                                </div>
                                <input type="range" min="0" max="100" step="10" v-model="design.shadow_intensity" @input="updateDesign" class="ultra-range w-full">
                            </div>

                            <!-- Line Height (Moved from Atmosphere) -->
                            <div>
                                <div class="flex justify-between mb-3">
                                    <label class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Line Dynamics</label>
                                    <span class="text-[10px] text-indigo-400 font-black">{{ design.line_height }}</span>
                                </div>
                                <input type="range" min="1" max="2" step="0.1" v-model="design.line_height" @input="updateDesign" class="ultra-range w-full">
                            </div>

                            <!-- Section Gap (Moved from Atmosphere) -->
                            <div>
                                <div class="flex justify-between mb-3">
                                    <label class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Space Between Sections</label>
                                    <span class="text-[10px] text-indigo-400 font-black">{{ design.section_gap }}px</span>
                                </div>
                                <input type="range" min="0" max="60" step="2" v-model="design.section_gap" @input="updateDesign" class="ultra-range w-full">
                            </div>
                        </div>
                    </div>

                    <!-- Precision Popover -->
                    <div v-if="activePopover === 'precision'" class="space-y-6">
                        <div class="flex items-center justify-between px-2">
                             <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Precision Layer</h4>
                             <button @click="design.margins = { top: 20, bottom: 20, left: 25, right: 25 }; updateDesign()" class="text-[8px] font-black text-indigo-400 uppercase tracking-widest hover:text-white transition-colors">Reset</button>
                        </div>
                        <div class="space-y-5 px-1">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[8px] font-black text-slate-500 uppercase mb-2 block tracking-widest">Padding Top</label>
                                    <input type="number" v-model="design.margins.top" @input="updateDesign" class="w-full bg-white/5 border border-white/5 text-white text-[10px] font-black rounded-xl p-2 focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="text-[8px] font-black text-slate-500 uppercase mb-2 block tracking-widest">Padding Bottom</label>
                                    <input type="number" v-model="design.margins.bottom" @input="updateDesign" class="w-full bg-white/5 border border-white/5 text-white text-[10px] font-black rounded-xl p-2 focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="text-[8px] font-black text-slate-500 uppercase mb-2 block tracking-widest">Padding Left</label>
                                    <input type="number" v-model="design.margins.left" @input="updateDesign" class="w-full bg-white/5 border border-white/5 text-white text-[10px] font-black rounded-xl p-2 focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="text-[8px] font-black text-slate-500 uppercase mb-2 block tracking-widest">Padding Right</label>
                                    <input type="number" v-model="design.margins.right" @input="updateDesign" class="w-full bg-white/5 border border-white/5 text-white text-[10px] font-black rounded-xl p-2 focus:ring-1 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-3">
                                    <label class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Inner Page Scale</label>
                                    <span class="text-[10px] text-indigo-400 font-black">{{ design.inner_padding }}mm</span>
                                </div>
                                <input type="range" min="0" max="30" step="1" v-model="design.inner_padding" @input="updateDesign" class="ultra-range w-full">
                            </div>
                        </div>
                    </div>

                    <!-- Structure Popover -->
                    <div v-if="activePopover === 'structure'" class="space-y-4">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 px-2">Structural Engineering</h4>
                        <div class="grid grid-cols-1 gap-1.5 max-h-80 overflow-y-auto pr-1">
                            <button 
                                v-for="(section, index) in design.section_order" 
                                :key="section"
                                draggable="true"
                                @dragstart="onDragStart($event, index)"
                                @dragover.prevent
                                @drop="onDrop($event, index)"
                                @dragend="onDragEnd"
                                @click="toggleSection(section)"
                                class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all border group cursor-move hover:bg-white/10"
                                :class="[design.hidden_sections.includes(section) ? 'bg-white/2 opacity-30 border-transparent italic' : 'bg-white/5 text-white border-white/5 hover:border-white/20', dragIndex === index ? 'opacity-50 border-dashed border-indigo-500 bg-indigo-500/10' : '']"
                            >
                                <div class="flex items-center gap-3 pointer-events-none">
                                    <svg class="w-3.5 h-3.5 text-slate-600 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8h16M4 16h16"/></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">{{ t(section) || section }}</span>
                                </div>
                                <div class="flex items-center gap-2 pointer-events-none">
                                    <button @click.stop="toggleSection(section)" class="pointer-events-auto w-6 h-6 flex items-center justify-center rounded-full bg-white/5 hover:bg-white/20 transition-colors">
                                        <svg v-if="!design.hidden_sections.includes(section)" class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg v-else class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                            </button>
                        </div>
                        <p class="text-[8px] text-slate-600 font-bold uppercase text-center mt-2 tracking-widest flex items-center justify-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                            Drag & Drop to reorder
                        </p>
                    </div>

                </div>
            </Transition>
        </div>

        <!-- Main Ultra Pill Bar -->
        <div class="ultra-pill-container p-2 flex items-center gap-2 group shadow-[0_30px_100px_rgba(0,0,0,0.8)]">
            
            <!-- Quick AI -->
            <button 
                @click="optimizeWithAi"
                class="ultra-btn shadow-[0_0_20px_rgba(99,102,241,0.2)] text-indigo-400 hover:text-white"
                title="AI Optimize"
            >
                <svg class="w-4.5 h-4.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </button>

            <div class="pill-divider"></div>

            <!-- Templates -->
            <button 
                @click="togglePopover('templates')"
                :class="['ultra-btn', activePopover === 'templates' ? 'active-pill' : '']"
            >
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
            </button>

            <!-- Typography -->
            <button 
                @click="togglePopover('typography')"
                :class="['ultra-btn', activePopover === 'typography' ? 'active-pill' : '']"
            >
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4M7 8h10M7 12h4m1 4h4"/></svg>
            </button>

            <!-- Aesthetics -->
            <button 
                @click="togglePopover('aesthetics')"
                :class="['ultra-btn', activePopover === 'aesthetics' ? 'active-pill' : '']"
                title="Aesthetics"
            >
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>

            <!-- Structure -->
            <button 
                @click="togglePopover('structure')"
                :class="['ultra-btn', activePopover === 'structure' ? 'active-pill' : '']"
            >
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            </button>

            <!-- Precision -->
            <button 
                @click="togglePopover('precision')"
                :class="['ultra-btn', activePopover === 'precision' ? 'active-pill' : '']"
            >
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </button>

            <div class="pill-divider"></div>

            <!-- Instant Print -->
            <button 
                @click="instantPrint"
                class="ultra-btn text-slate-400 hover:text-emerald-400 active:scale-95 transition-all"
                title="Instant Export"
            >
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </button>
        </div>
    </div>
</template>

<style scoped>
.ultra-pill-container {
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 9999px;
    height: 56px;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.ultra-btn {
    width: 42px;
    height: 42px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    color: rgba(148, 163, 184, 0.8);
    border: 1px solid transparent;
}

.ultra-btn:hover {
    background: rgba(255, 255, 255, 0.05);
    color: white;
    transform: translateY(-2px);
    border-color: rgba(255, 255, 255, 0.05);
}

.active-pill {
    background: #6366f1 !important;
    color: white !important;
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
    transform: translateY(-4px) scale(1.05) !important;
}

.pill-divider {
    width: 1px;
    height: 20px;
    background: rgba(255, 255, 255, 0.06);
    margin: 0 4px;
}

.ultra-range {
    appearance: none;
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 99px;
    outline: none;
}

.ultra-range::-webkit-slider-thumb {
    appearance: none;
    width: 16px;
    height: 16px;
    background: #6366f1;
    border-radius: 50%;
    cursor: pointer;
    border: 3px solid #0f172a;
    box-shadow: 0 0 15px rgba(99, 102, 241, 0.5);
    transition: all 0.2s ease;
}

.ultra-range::-webkit-slider-thumb:hover {
    transform: scale(1.2);
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.8);
}

::-webkit-scrollbar {
    width: 0px;
    background: transparent;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
