<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import PersonalForm from './Partials/PersonalForm.vue';
import ExperienceForm from './Partials/ExperienceForm.vue';
import EducationForm from './Partials/EducationForm.vue';
import SkillsForm from './Partials/SkillsForm.vue';
import ProjectsForm from './Partials/ProjectsForm.vue';
import LanguagesForm from './Partials/LanguagesForm.vue';
import CertificationsForm from './Partials/CertificationsForm.vue';
import ResumePreview from '@/Components/ResumePreview.vue';
import CoverLetterGenerator from './Partials/CoverLetterGenerator.vue';
import FloatingIsland from '@/Components/FloatingIsland.vue';
import AuraRing from '@/Components/AuraRing.vue';
import PolisherInline from '@/Components/PolisherInline.vue';
import TemplateSelector from './Partials/TemplateSelector.vue';

const props = defineProps(['resume', 'templates']);
const { t } = useI18n();

// Template picker
const availableTemplates = [
    // Standard / ATS
    { id: 'classic',        label: 'Classic ATS',    color: '#334155', textColor: '#f8fafc' },
    { id: 'modern',         label: 'Modern Euro',    color: '#4f46e5', textColor: '#ffffff' },
    
    // Ivy League (Academic/Legal)
    { id: 'harvard',        label: 'Harvard Legacy', color: '#000000', textColor: '#ffffff' },
    { id: 'yale-legal',     label: 'Yale Legal',     color: '#1e3a8a', textColor: '#bfdbfe' },
    { id: 'oxford',         label: 'Oxford Academic',color: '#7f1d1d', textColor: '#fecaca' },

    // Tech / Startup
    { id: 'silicon-valley', label: 'Silicon Valley', color: '#0f172a', textColor: '#38bdf8' },
    { id: 'matrix-dev',     label: 'Matrix Dev',     color: '#022c22', textColor: '#4ade80' },
    { id: 'startup-hero',   label: 'Startup Hero',   color: '#4c1d95', textColor: '#f5d0fe' },

    // Executive / Luxury
    { id: 'executive-gold', label: 'CEO Suite',      color: '#1e293b', textColor: '#fbbf24' },
    { id: 'obsidian-elite', label: 'Obsidian Elite', color: '#000000', textColor: '#94a3b8' },
    
    // Minimalist / Design
    { id: 'swiss-design',   label: 'Swiss Design',   color: '#ffffff', textColor: '#0f172a' },
    { id: 'pure-zen',       label: 'Pure Zen',       color: '#f8fafc', textColor: '#475569' },
    
    // Creative
    { id: 'creative-spark', label: 'Creative Spark', color: '#db2777', textColor: '#ffffff' },
    { id: 'vogue-fashion',  label: 'Vogue Fashion',  color: '#be185d', textColor: '#fff1f2' },
    
    // Regional
    { id: 'arabic-pro',     label: 'Arabic Royal',   color: '#065f46', textColor: '#a7f3d0' },
];

const currentTheme = ref(props.resume.theme || 'classic');

const changeTheme = (themeId) => {
    currentTheme.value = themeId;
    // Reactively update the resume object so preview updates instantly
    props.resume.theme = themeId;
    props.resume.template = themeId;
    // Persist to database
    router.patch(route('resumes.update', props.resume.id), { 
        theme: themeId, 
        template: themeId 
    }, {
        preserveScroll: true,
        preserveState: true,
    });
};

const activeTab = ref('templates');

const tabs = [
    { id: 'templates',      label: 'templates',         icon: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z' },
    { id: 'personal',       label: 'personal_details', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { id: 'experience',     label: 'experience',        icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
    { id: 'education',      label: 'education',         icon: 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z' },
    { id: 'skills',         label: 'skills',            icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z' },
    { id: 'projects',       label: 'projects',          icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z' },
    { id: 'languages',      label: 'languages',         icon: 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129' },
    { id: 'certifications', label: 'certifications',    icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z' },
];

const mode = ref('tab');
const zoomScale = ref(1);

import { watch } from 'vue';
watch(mode, (newMode) => {
    if (newMode === 'design') {
        showLeftSidebar.value = false;
        showRightSidebar.value = false;
    } else {
        showLeftSidebar.value = true;
    }
});

const nextStep = () => {
    const currentIndex = tabs.findIndex(tab => tab.id === activeTab.value);
    if (currentIndex < tabs.length - 1) {
        activeTab.value = tabs[currentIndex + 1].id;
    } else {
        activeTab.value = tabs[0].id;
    }
};

const prevStep = () => {
    const currentIndex = tabs.findIndex(tab => tab.id === activeTab.value);
    if (currentIndex > 0) {
        activeTab.value = tabs[currentIndex - 1].id;
    }
};

const tabProgress = () => {
    return tabs.findIndex(tab => tab.id === activeTab.value) + 1;
};

// Completion Logic
const completionPercentage = computed(() => {
    let score = 0;
    const r = props.resume;
    
    // Personal Details (40%)
    if (r.first_name) score += 5;
    if (r.last_name)  score += 5;
    if (r.email)      score += 5;
    if (r.phone)      score += 5;
    if (r.job_title)  score += 10;
    if (r.summary)    score += 10;
    
    // Core Sections (40%)
    if (r.experiences?.length) score += 15;
    if (r.education?.length)   score += 15;
    if (r.skills?.length)      score += 10;
    
    // Others (20%)
    if (r.projects?.length)       score += 10;
    if (r.languages?.length)      score += 5;
    if (r.certifications?.length) score += 5;
    
    return Math.min(score, 100);
});

const missingElements = computed(() => {
    const missing = [];
    const r = props.resume;

    // Personal Details
    let personalMissing = 0;
    if (!r.first_name) personalMissing += 5;
    if (!r.last_name) personalMissing += 5;
    if (!r.email) personalMissing += 5;
    if (!r.phone) personalMissing += 5;
    if (!r.job_title) personalMissing += 10;
    if (personalMissing > 0) missing.push({ id: 'personal', labelKey: 'personal_details', points: personalMissing });
    
    if (!r.summary) missing.push({ id: 'personal', labelKey: 'summary', points: 10 });
    
    // Core Sections
    if (!r.experiences?.length) missing.push({ id: 'experience', labelKey: 'experience', points: 15 });
    if (!r.education?.length)   missing.push({ id: 'education', labelKey: 'education', points: 15 });
    if (!r.skills?.length)      missing.push({ id: 'skills', labelKey: 'skills', points: 10 });
    
    // Others
    if (!r.projects?.length)       missing.push({ id: 'projects', labelKey: 'projects', points: 10 });
    if (!r.languages?.length)      missing.push({ id: 'languages', labelKey: 'languages', points: 5 });
    if (!r.certifications?.length) missing.push({ id: 'certifications', labelKey: 'certifications', points: 5 });

    return missing;
});

const searchQuery = ref('');
const filteredTabs = computed(() => {
    if (!searchQuery.value) return tabs;
    return tabs.filter(tab => 
        t(tab.label).toLowerCase().includes(searchQuery.value.toLowerCase()) || 
        tab.id.includes(searchQuery.value.toLowerCase())
    );
});

const downloadPdf = () => {
    window.open(route('resumes.print', props.resume.id), '_blank');
};

// Responsive handling
const isMobile = ref(false);
const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024;
};

if (typeof window !== 'undefined') {
    checkMobile();
    window.addEventListener('resize', checkMobile);
}

const showLeftSidebar = ref(true);
const showRightSidebar = ref(false);

const handleSectionClick = (section) => {
    // Open the corresponding section in the left sidebar
    activeTab.value = section;
    if (!showLeftSidebar.value) showLeftSidebar.value = true;
};

// Inline Text Editing Handler
const handleInlineEdit = (section, field, id, value) => {
    // Basic sanitizer
    const sanitizedValue = value.replace(/&nbsp;/g, ' ').trim();
    
    if (section === 'personal') {
        props.resume[field] = sanitizedValue;
        router.patch(route('resumes.update', props.resume.id), {
            [field]: sanitizedValue
        }, { preserveScroll: true, preserveState: true });
    } else {
        // Find item in array
        const list = props.resume[section];
        if (!list) return;
        const item = list.find(i => i.id === id);
        if (item) {
            item[field] = sanitizedValue;
            router.patch(route('resumes.update', props.resume.id), {
                [section]: list
            }, { preserveScroll: true, preserveState: true });
        }
    }
};

// Device Preview Styles
const previewDeviceStyles = computed(() => {
    return `max-w-[210mm] transition-all duration-500 ease-in-out transform origin-top`;
});

const handleAiOptimize = () => {
    showRightSidebar.value = !showRightSidebar.value;
    if (showRightSidebar.value && isMobile.value) {
        showLeftSidebar.value = false;
    }
};

const handleSectionReorder = (newOrder) => {
    props.resume.design_options.section_order = newOrder;
    router.patch(route('resumes.update', props.resume.id), {
        design_options: props.resume.design_options
    }, { preserveScroll: true, preserveState: true });
};

const handleApplySuggestion = ({ section, id, suggestion }) => {
    if (section === 'summary') {
        props.resume.summary = suggestion;
        router.patch(route('resumes.update', props.resume.id), {
            summary: suggestion
        }, { preserveScroll: true, preserveState: true });
    } else if (section === 'experiences') {
        const list = [...props.resume.experiences];
        const item = list.find(i => i.id === id);
        if (item) {
            item.description = suggestion;
            router.patch(route('resumes.update', props.resume.id), {
                experiences: list
            }, { preserveScroll: true, preserveState: true });
        }
    } else if (section === 'skills') {
        // Handle skills text or array
        if (typeof props.resume.skills === 'string') {
            props.resume.skills = suggestion;
        } else {
            props.resume.skills_text = suggestion;
        }
        router.patch(route('resumes.update', props.resume.id), {
            skills_text: suggestion,
            skills: props.resume.skills
        }, { preserveScroll: true, preserveState: true });
    }
};
</script>

<template>
    <Head :title="t('edit')" />

    <AuthenticatedLayout :fluid="true">
        <template #header>
            <div class="flex justify-between items-center px-1 sm:px-4">
                <div class="flex items-center gap-2 sm:gap-6 min-w-0 pr-2">
                    <!-- Integrated Sidebar Toggle -->
                    <button 
                        @click="showLeftSidebar = !showLeftSidebar"
                        class="p-2 rounded-xl bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white transition-all shadow-sm"
                        :title="showLeftSidebar ? 'Hide Forms' : 'Show Forms'"
                    >
                        <svg :class="['w-5 h-5 transition-transform duration-500', showLeftSidebar ? '' : 'rotate-180']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>

                    <div class="flex flex-col min-w-0">
                        <h2 class="font-black text-sm sm:text-2xl text-slate-900 dark:text-white tracking-tighter italic truncate max-w-[100px] sm:max-w-[150px] md:max-w-xs" :title="resume.title">
                            {{ resume.title }}
                        </h2>
                        <div class="flex items-center gap-1 sm:gap-2 mt-0.5 sm:mt-1">
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)] shrink-0"></div>
                            <span class="text-[7px] sm:text-[9px] font-black text-slate-500 uppercase tracking-widest truncate">{{ t('autosaved') || 'Live Syncing' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-1.5 sm:gap-4 shrink-0">
                    <!-- Design Mode Switcher (Repositioned) -->
                    <button 
                        @click="mode = (mode === 'design' ? 'tab' : 'design')"
                        :class="[
                            'px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all active:scale-95 border shadow-xl',
                            mode === 'design' 
                                ? 'bg-indigo-600 text-white border-indigo-500 shadow-indigo-500/20' 
                                : 'bg-white dark:bg-white/5 text-slate-600 dark:text-slate-400 border-black/5 dark:border-white/10 hover:border-indigo-500/50'
                        ]"
                    >
                        <svg :class="['w-4 h-4 transition-all duration-500', mode === 'design' ? 'rotate-90' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="hidden sm:inline">{{ mode === 'design' ? 'Exit Design' : 'Design Studio' }}</span>
                    </button>

                    <div class="hidden sm:block w-px h-6 sm:h-8 bg-black/5 dark:bg-white/5 mx-1 sm:mx-2 shrink-0"></div>

                    <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                        <!-- Combined Smart Audit Button -->
                        <button @click="handleAiOptimize" class="bg-indigo-600/10 hover:bg-indigo-600/20 border border-indigo-500/20 text-indigo-400 px-4 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all active:scale-95 shrink-0 group" title="Smart Audit & ATS">
                            <div class="relative">
                                <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            </div>
                            <span class="hidden md:inline text-[10px] font-black uppercase tracking-widest">{{ t('smart_audit') || 'Smart Audit' }}</span>
                        </button>
                        
                        <!-- Download Button -->
                        <button @click="downloadPdf" class="bg-white text-black hover:bg-slate-200 p-1.5 sm:px-6 sm:py-2.5 rounded-lg sm:rounded-2xl flex items-center justify-center gap-2 transition-all active:scale-95 shadow-xl shrink-0" :title="t('download')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span class="hidden sm:inline text-[10px] font-black uppercase tracking-widest">{{ t('download') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="flex flex-col lg:flex-row h-[calc(100vh-180px)] overflow-hidden bg-slate-50 dark:bg-slate-950 p-2 lg:p-4 gap-4 relative">
            <!-- Sidebar -->
            <Transition
                enter-active-class="transition-all duration-500 ease-out"
                leave-active-class="transition-all duration-500 ease-in"
                enter-from-class="-ml-72 opacity-0"
                leave-to-class="-ml-72 opacity-0"
            >
                <div v-if="showLeftSidebar" class="w-full lg:w-64 bg-white/70 dark:bg-slate-900/40 backdrop-blur-2xl border border-black/5 dark:border-white/10 rounded-[28px] overflow-y-auto no-print flex-shrink-0 flex flex-col z-40 relative shadow-2xl transition-all duration-700">
                    <!-- Aura Completion & Audit Center -->
                    <div class="hidden lg:flex p-6 flex-col items-center border-b border-black/5 dark:border-white/5 bg-gradient-to-b from-slate-50 dark:from-slate-900 to-transparent relative group/aura">
                        <AuraRing :score="completionPercentage" :size="96" />
                        <div class="mt-4 text-center">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-900 dark:text-white/80">{{ t('resume_strength') }}</h3>
                            <p class="text-[9px] text-indigo-400 mt-1.5 font-bold tracking-[0.2em] uppercase">{{ completionPercentage === 100 ? t('masterpiece_ready') : t('in_progress') }}</p>
                        </div>
                        
                        <!-- Mini ATS integration hint -->
                        <div class="absolute bottom-2 right-4 opacity-0 group-hover/aura:opacity-100 transition-opacity">
                             <div class="flex items-center gap-1.5 bg-teal-500/10 px-2 py-0.5 rounded-full border border-teal-500/20">
                                 <span class="text-[8px] font-black text-teal-500 uppercase tracking-widest leading-none">ATS {{ completionPercentage }}%</span>
                             </div>
                        </div>
                    </div>

                    <!-- Audit Center Checklist -->
                    <div v-if="missingElements.length > 0 && mode !== 'wizard'" class="hidden lg:block px-5 py-4 border-b border-black/5 dark:border-white/5 bg-white/40 dark:bg-slate-900/40">
                        <div class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 mb-4 flex items-center justify-between">
                            <span>{{ t('aura_audit_center', 'Aura Audit Center') }}</span>
                            <span class="text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-full">{{ missingElements.length }} {{ t('to_fix', 'to fix') }}</span>
                        </div>
                        <ul class="space-y-2.5">
                            <li v-for="(item, index) in missingElements.slice(0, 3)" :key="item.id + index" 
                                @click="activeTab = item.id" 
                                class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 hover:shadow-lg hover:shadow-indigo-500/10 transition-all cursor-pointer group border border-black/5 dark:border-white/5 hover:border-indigo-500/30">
                                <span class="text-slate-600 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-white transition-colors flex items-center gap-2.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-rose-500 group-hover:animate-ping outline outline-2 outline-rose-500/30"></div>
                                    <span class="font-bold tracking-tight text-[11px]">{{ t('fill', 'Fill') }} {{ t(item.labelKey) }}</span>
                                </span>
                                <span class="text-[8px] font-black text-indigo-400 bg-indigo-500/10 px-2 py-1 rounded-lg group-hover:bg-indigo-500 group-hover:text-white transition-all transform group-hover:scale-110 shadow-sm">+{{ item.points }}%</span>
                            </li>
                        </ul>
                        <button v-if="missingElements.length > 3" @click="activeTab = missingElements[3].id" class="w-full mt-3 text-[8px] text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 uppercase tracking-[0.2em] font-black transition-colors pt-3 pb-1 border-t border-black/5 dark:border-white/5 flex items-center justify-center gap-1 group">
                            {{ t('view_all_missing') }} ({{ missingElements.length - 3 }})
                            <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Search & Navigation -->
                    <div class="px-5 pt-5 pb-2 border-b border-black/5 dark:border-white/5">
                        <div class="relative group/search">
                            <input 
                                v-model="searchQuery" 
                                type="text" 
                                :placeholder="t('search_sections', 'Search sections...')" 
                                class="w-full rounded-xl border border-black/5 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white px-4 py-2 text-[10px] font-bold uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all outline-none pl-9"
                            />
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 group-focus-within/search:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                <!-- Wizard progress -->
                <div v-if="mode === 'wizard'" class="px-5 pt-5 pb-2">
                    <div class="text-[9px] font-extralight uppercase tracking-[0.2em] text-slate-500 mb-2">Step {{ tabProgress() }} of {{ tabs.length }}</div>
                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-full rounded-full transition-all duration-700 shadow-[0_0_15px_rgba(99,102,241,0.6)]" :style="{ width: (tabProgress() / tabs.length * 100) + '%' }"></div>
                    </div>
                </div>

                <div class="px-3 py-2 lg:py-4">
                    <div class="hidden lg:block text-[10px] font-black uppercase tracking-[0.2em] text-slate-600 px-3 mb-4">{{ t('editor_nav') || 'Sections' }}</div>
                    <nav class="flex flex-row lg:flex-col space-x-2 lg:space-x-0 lg:space-y-1 overflow-x-auto lg:overflow-visible scrollbar-hide pb-2 lg:pb-0">
                        <button
                            v-for="tab in filteredTabs"
                            :key="tab.id"
                            @click="mode !== 'wizard' && (activeTab = tab.id)"
                            :disabled="mode === 'wizard'"
                            :class="[
                                'whitespace-nowrap lg:whitespace-normal flex-shrink-0 text-left px-4 py-2 rounded-xl font-medium transition-all flex items-center justify-center lg:justify-start gap-3 text-xs group relative overflow-hidden',
                                activeTab === tab.id
                                    ? 'bg-indigo-600 text-white dark:bg-indigo-600/10 dark:text-white border border-indigo-500/20 shadow-md'
                                    : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-white/2 hover:text-indigo-600 dark:hover:text-slate-300 border border-transparent border-black/5 dark:border-white/5',
                                mode === 'wizard' ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                            ]"
                        >
                            <div v-if="activeTab === tab.id" class="absolute inset-y-0 left-0 w-0.5 bg-indigo-500 rounded-full"></div>
                            <svg :class="['w-3.5 h-3.5 flex-shrink-0 transition-colors', activeTab === tab.id ? 'text-indigo-400' : 'text-slate-600 group-hover:text-slate-400']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon"/>
                            </svg>
                            <span class="tracking-[0.15em] uppercase text-[9px]">{{ t(tab.label) }}</span>
                        </button>
                    </nav>
                </div>
                
                </div>
            </Transition>

            <!-- Form Area -->
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-300 ease-in"
                enter-from-class="opacity-0 scale-95"
                leave-to-class="opacity-0 scale-95"
            >
                <div v-show="showLeftSidebar && (!isMobile || mode === 'tab')" class="w-full lg:w-[28%] lg:min-w-[400px] lg:max-w-[480px] bg-white/70 dark:bg-slate-900/40 backdrop-blur-2xl overflow-y-auto p-6 border border-black/5 dark:border-white/10 rounded-[28px] no-print flex-1 lg:flex-none flex flex-col z-30 transition-all duration-700 shadow-2xl [scrollbar-gutter:stable]">
                    <div class="max-w-3xl mx-auto w-full">
                        <!-- Tab Content -->
                        <div class="mt-4 transition-all duration-500">
                            <Transition name="fade-slide" mode="out-in">
                                <div :key="activeTab" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                                    <TemplateSelector v-if="activeTab === 'templates'" :resume="resume" :templates="templates" @next="nextStep" />
                                    <PersonalForm v-if="activeTab === 'personal'" :resume="resume" @next="nextStep" />
                                    <ExperienceForm v-if="activeTab === 'experience'" :resume="resume" @next="nextStep" />
                                    <EducationForm      v-if="activeTab === 'education'"      :resume="resume" @next="nextStep" />
                                    <SkillsForm         v-if="activeTab === 'skills'"         :resume="resume" @next="nextStep" />
                                    <ProjectsForm       v-if="activeTab === 'projects'"       :resume="resume" @next="nextStep" />
                                    <LanguagesForm      v-if="activeTab === 'languages'"      :resume="resume" @next="nextStep" />
                                    <CertificationsForm v-if="activeTab === 'certifications'" :resume="resume" @next="nextStep" />
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <!-- Wizard navigation -->
                    <div v-if="mode === 'wizard'" class="mt-12 flex justify-between items-center max-w-3xl mx-auto w-full border-t border-white/5 pt-6">
                        <button @click="prevStep" :disabled="activeTab === 'personal'" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] disabled:opacity-30 transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                            {{ t('previous') }}
                        </button>
                        <div class="laser-btn-wrapper" :class="{ 'opacity-30 cursor-not-allowed': activeTab === 'certifications' }">
                            <button @click="nextStep" :disabled="activeTab === 'certifications'" class="laser-btn-content px-8 py-3 text-white flex items-center gap-2">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em]">
                                    {{ activeTab === 'certifications' ? t('finish') : t('next') }}
                                </span>
                                <svg v-if="activeTab !== 'certifications'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>

            <!-- Preview Area -->
            <div v-show="!isMobile || mode === 'design'" class="w-full lg:flex-1 bg-slate-200/50 dark:bg-black/20 rounded-[32px] overflow-y-auto print-full relative flex flex-col transition-all duration-700 border border-black/5 dark:border-white/5 shadow-inner">
                
                <!-- Zoom Control (Only in Design Mode) -->
                <Transition name="fade">
                    <div v-if="mode === 'design' && !showRightSidebar" class="absolute top-6 left-6 z-[60] bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl px-4 py-2 rounded-2xl border border-black/5 dark:border-white/10 flex items-center gap-4 shadow-2xl">
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Zoom</span>
                        <input type="range" min="0.5" max="1.5" step="0.05" v-model="zoomScale" class="w-32 h-1.5 bg-indigo-500/20 rounded-full appearance-none accent-indigo-600 cursor-pointer">
                        <span class="text-[10px] font-black text-indigo-600 min-w-[40px]">{{ Math.round(zoomScale * 100) }}%</span>
                        <button @click="zoomScale = 1" class="text-[8px] font-black text-slate-400 hover:text-indigo-600 uppercase tracking-widest ml-2">Reset</button>
                    </div>
                </Transition>

                <!-- Floating Island -->
                <Transition name="fade-slide"
                    enter-active-class="transition-all duration-700 cubic-bezier(0.34, 1.56, 0.64, 1)"
                    enter-from-class="translate-y-32 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition-all duration-500 ease-in"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="translate-y-32 opacity-0"
                >
                    <FloatingIsland v-if="mode === 'design' && !showRightSidebar" :resume="resume" @ai-optimize="handleAiOptimize" />
                </Transition>
                
                <!-- AI Polisher Inline (Integrated) -->

                <!-- Contextual Edit Popover Removed as per user request -->

                <div class="flex-1 overflow-y-auto p-4 lg:p-12 flex justify-center relative bg-[radial-gradient(circle_at_center,_var(--tw-gradient-from)_0%,_transparent_70%)] from-indigo-500/5 transition-all duration-700">
                    <!-- Device Shell Simulation -->
                    <div 
                        :class="[
                            'w-full shadow-[0_0_100px_rgba(0,0,0,0.7)] min-h-[297mm] rounded-sm relative z-10 sticky top-0 transform origin-top transition-all duration-700 ease-in-out',
                            previewDeviceStyles
                        ]"
                        :style="{ transform: `scale(${zoomScale})` }"
                    >
                        <ResumePreview 
                            :resume="resume" 
                            :editable="true" 
                            @section-click="handleSectionClick" 
                            @update-field="handleInlineEdit"
                            @reorder-sections="handleSectionReorder"
                        />
                    </div>
                </div>
            </div>

            <!-- AI Polisher Sidebar (Right) -->
            <Transition
                enter-active-class="transition-all duration-500 ease-out"
                leave-active-class="transition-all duration-500 ease-in"
                enter-from-class="translate-x-full opacity-0"
                leave-to-class="translate-x-full opacity-0"
            >
                <div v-if="showRightSidebar" class="w-full lg:w-[420px] h-full z-40 relative shadow-[-20px_0_80px_rgba(0,0,0,0.5)] border-l border-white/10">
                    <PolisherInline :resume="resume" :active="showRightSidebar" @apply-suggestion="handleApplySuggestion" />
                    <!-- Close Button for Sidebar -->
                    <button @click="showRightSidebar = false" class="absolute top-4 right-4 text-white/50 hover:text-white transition-colors bg-white/5 p-2 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </Transition>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    .no-print { display: none !important; }
    .print-full {
        width: 100% !important;
        height: 100% !important;
        overflow: visible !important;
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    body, html { height: auto; overflow: visible; }
}
</style>
