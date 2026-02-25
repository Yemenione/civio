<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PremiumNavbar from '@/Components/PremiumNavbar.vue';
import AiCopilot from '@/Components/AiCopilot.vue';
import ResumePreview from '@/Components/ResumePreview.vue';
import { useI18n } from 'vue-i18n';
import { onMounted, onUnmounted, ref, computed } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    stats: {
        type: Object,
        default: () => ({ resumesCount: 10000, templatesCount: 6, languagesCount: 3 })
    }
});

const { t } = useI18n();
const isScrolled = ref(false);
const scrollProgress = ref(0);

const isCopilotOpen = ref(false);
const initialCopilotPrompt = ref('');

const gridContainer = ref(null);
const cardWidth = ref(300);

const calculateScale = computed(() => {
    return cardWidth.value / 800;
});

import { getDummyResume } from '@/utils/dummyResume.js';
const dummyResume = getDummyResume();

const showcaseTemplates = [
    { id: 'classic',      nameKey: 'classic',  color: '#1e293b', pro: false },
    { id: 'modern',       nameKey: 'modern',   color: '#4f46e5', pro: true },
    { id: 'minimal',      nameKey: 'minimal',  color: '#475569', pro: false },
];

const openCopilot = () => {
    isCopilotOpen.value = true;
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
    
    // Calculate scroll progress percentage
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    scrollProgress.value = (winScroll / height) * 100;
};

// Inject Schema.org JSON-LD structured data into <head>
let ldScript;
onMounted(() => {
    ldScript = document.createElement('script');
    ldScript.type = 'application/ld+json';
    ldScript.text = JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'WebApplication',
        'name': 'Civio',
        'url': window.location.origin,
        'description': t('seo_ld_description'),
        'applicationCategory': 'BusinessApplication',
        'operatingSystem': 'Web Browser',
        'inLanguage': ['en', 'ar', 'fr', 'de', 'es', 'it', 'nl', 'pt'],
        'offers': { '@type': 'Offer', 'price': '0', 'priceCurrency': 'USD', 'availability': 'https://schema.org/InStock' },
        'featureList': [
            t('seo_feature_rtl'), t('seo_feature_ai'), t('seo_feature_pdf'),
            t('seo_feature_templates'), t('seo_feature_ats'), t('seo_feature_cover'),
            t('seo_feature_gdpr'), t('seo_feature_multi')
        ],
        'author': { '@type': 'Organization', 'name': 'Civio' }
    });
    document.head.appendChild(ldScript);
    window.addEventListener('scroll', handleScroll);

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
onUnmounted(() => { 
    if (ldScript) document.head.removeChild(ldScript); 
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <Head :title="t('seo_title')">
        <meta name="description" :content="t('seo_description')" />
        <meta name="keywords" :content="t('seo_keywords')" />
        <meta name="robots" content="index, follow" />
        <meta name="author" content="Civio" />
        <!-- Open Graph -->
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="Civio" />
        <meta property="og:title" :content="t('seo_og_title')" />
        <meta property="og:description" :content="t('seo_og_description')" />
        <meta property="og:image" content="/og-image.png" />
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="@civio_app" />
        <meta name="twitter:title" :content="t('seo_twitter_title')" />
        <meta name="twitter:description" :content="t('seo_twitter_description')" />
        <meta name="twitter:image" content="/og-image.png" />
        <!-- hreflang for multilingual SEO -->
        <link rel="alternate" hreflang="en" href="/?lang=en" />
        <link rel="alternate" hreflang="ar" href="/?lang=ar" />
        <link rel="alternate" hreflang="fr" href="/?lang=fr" />
        <link rel="alternate" hreflang="de" href="/?lang=de" />
        <link rel="alternate" hreflang="es" href="/?lang=es" />
        <link rel="alternate" hreflang="it" href="/?lang=it" />
        <link rel="alternate" hreflang="nl" href="/?lang=nl" />
        <link rel="alternate" hreflang="pt" href="/?lang=pt" />
        <link rel="alternate" hreflang="x-default" href="/" />
    </Head>

    <div class="min-h-screen bg-slate-100 dark:bg-slate-950 selection:bg-indigo-500 selection:text-white overflow-x-hidden font-sans transition-colors duration-300">
        <!-- Laser Scroll Progress -->
        <div class="laser-scroll-progress" :style="{ width: scrollProgress + '%' }"></div>
        <!-- Background Gradients -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/20 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-purple-600/20 rounded-full blur-[140px]"></div>
            <div class="absolute top-[40%] left-[50%] w-[30%] h-[30%] bg-teal-600/10 rounded-full blur-[100px]"></div>
            
            <!-- Matrix Falling Letters Decors -->
            <div class="falling-letters-column left-10 opacity-20">
                <div v-for="i in 20" :key="'l1-'+i" class="falling-letter" :style="{ animationDuration: (Math.random() * 5 + 5) + 's', animationDelay: (Math.random() * 10) + 's' }">
                    {{ String.fromCharCode(33 + Math.floor(Math.random() * 94)) }}
                </div>
            </div>
            <div class="falling-letters-column right-10 opacity-20">
                <div v-for="i in 20" :key="'r1-'+i" class="falling-letter" :style="{ animationDuration: (Math.random() * 5 + 5) + 's', animationDelay: (Math.random() * 10) + 's' }">
                    {{ String.fromCharCode(33 + Math.floor(Math.random() * 94)) }}
                </div>
            </div>
        </div>


        <PremiumNavbar 
            :links="[
                { label: t('features'), href: '#features', active: false },
                { label: t('template'), href: '#templates', active: false },
                { label: t('pricing'), href: route('pricing'), active: route().current('pricing') },
                { label: t('cover_letters', 'Cover Letters'), href: route('cover-letters.index'), active: route().current('cover-letters.*') },
                { label: t('ats_checker', 'ATS Checker'), href: route('analysis.index'), active: route().current('analysis.index') },
            ]"
            :transparent="true"
        />

        <!-- Hero Section -->
        <main class="relative z-10 pt-40 pb-20 px-6">
            <div class="max-w-6xl mx-auto">
                <!-- Badge -->
                <div class="flex justify-center mb-6">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-sm font-medium">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                        </span>
                        {{ t('pricing_hero_badge') }}
                    </div>
                </div>

                <!-- Headline -->
                <h1 class="text-center text-5xl md:text-7xl lg:text-8xl font-black tracking-tight mb-8 leading-[0.9] animate-in slide-in-from-bottom-8 duration-1000">
                    <span class="bg-clip-text text-transparent bg-gradient-to-b from-slate-900 via-slate-800 to-slate-500 dark:from-white dark:via-white dark:to-slate-500">
                        Build Your Future
                    </span>
                    <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 italic font-serif">
                        in Minutes
                    </span>
                </h1>

                <p class="text-center max-w-2xl mx-auto text-lg text-slate-600 dark:text-slate-400 mb-10 leading-relaxed font-bold">
                    {{ t('pricing_hero_subtitle') }}
                </p>

                <!-- AI Builder Input -->
                <div class="max-w-3xl mx-auto mb-16 relative">
                    <form @submit.prevent="openCopilot" class="relative group" v-if="!isCopilotOpen">
                        <div class="absolute -inset-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl blur opacity-30 group-focus-within:opacity-60 transition duration-1000 group-focus-within:duration-200"></div>
                        <div class="relative bg-slate-900/90 backdrop-blur-xl rounded-2xl border border-white/20 p-2 flex flex-col sm:flex-row items-center justify-between shadow-2xl overflow-hidden gap-2 sm:gap-0">
                            <div class="hidden sm:flex pl-4 pr-1 items-center justify-center pointer-events-none rtl:pr-4 rtl:pl-1">
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500 transition-colors"></span>
                                </span>
                            </div>
                            <input 
                                v-model="initialCopilotPrompt"
                                type="text" 
                                :placeholder="t('ai_prompt_placeholder', 'E.g. Build a modern resume for a Senior Software Engineer...')" 
                                class="flex-1 bg-transparent border-none text-white focus:ring-0 placeholder:text-slate-500 text-sm sm:text-lg py-3 px-3 w-full text-center sm:text-left rtl:sm:text-right" 
                            />
                            <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3.5 sm:py-4 rounded-xl font-black uppercase tracking-widest text-xs sm:text-sm transition-all flex items-center justify-center gap-3 shrink-0 shadow-lg shadow-indigo-500/30 active:scale-95 group-hover:bg-indigo-500 border border-indigo-400/20">
                                <span>✨</span>
                                <span>{{ t('start_building', 'Start Building') }}</span>
                            </button>
                        </div>
                    </form>
                    
                    <transition
                        enter-active-class="transition-all duration-700 ease-out"
                        enter-from-class="opacity-0 translate-y-8 scale-95"
                        enter-to-class="opacity-100 translate-y-0 scale-100"
                        leave-active-class="transition-all duration-300 ease-in"
                        leave-from-class="opacity-100 translate-y-0 scale-100"
                        leave-to-class="opacity-0 translate-y-8 scale-95"
                    >
                        <div v-if="isCopilotOpen" class="w-full relative shadow-2xl shadow-indigo-900/50 rounded-2xl border border-white/10 bg-slate-900 overflow-hidden">
                            <AiCopilot 
                                :isOpen="isCopilotOpen" 
                                :initialPrompt="initialCopilotPrompt"
                                @close="isCopilotOpen = false; initialCopilotPrompt = ''" 
                            />
                        </div>
                    </transition>
                </div>

                <!-- Stats Strip -->
                <div class="flex flex-wrap justify-center items-center gap-x-10 gap-y-6 mb-20 px-4">
                    <div class="text-center min-w-[120px]">
                        <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ stats.resumesCount >= 10000 ? (Math.floor(stats.resumesCount/1000) + 'K+') : stats.resumesCount }}</p>
                        <p class="text-xs sm:text-sm text-slate-500">{{ t('resumes_created', 'Resumes Created') }}</p>
                    </div>
                    <div class="h-10 w-px bg-slate-200 dark:bg-white/10 hidden sm:block"></div>
                    <div class="text-center min-w-[120px]">
                        <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ stats.templatesCount }}</p>
                        <p class="text-xs sm:text-sm text-slate-500">{{ t('premium_templates', 'Premium Templates') }}</p>
                    </div>
                    <div class="h-10 w-px bg-slate-200 dark:bg-white/10 hidden md:block"></div>
                    <div class="text-center min-w-[120px]">
                        <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ stats.languagesCount }}</p>
                        <p class="text-xs sm:text-sm text-slate-500">{{ t('languages', 'Languages') }}</p>
                    </div>
                    <div class="h-10 w-px bg-slate-200 dark:bg-white/10 hidden sm:block"></div>
                    <div class="text-center min-w-[120px]">
                        <p class="text-3xl font-bold text-slate-900 dark:text-white">100%</p>
                        <p class="text-xs sm:text-sm text-slate-500">{{ t('ats_friendly', 'ATS Friendly') }}</p>
                    </div>
                </div>

                <!-- Mock CV Preview: The "AI Editor" visualization -->
                <div class="relative max-w-5xl mx-auto">
                    <!-- Ambient Glow -->
                    <div class="absolute -inset-4 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-[2.5rem] blur-3xl opacity-20 animate-pulse"></div>
                    
                    <!-- Main Window Container -->
                    <div class="relative bg-slate-900/40 backdrop-blur-2xl rounded-[2.5rem] border border-white/10 shadow-2xl overflow-hidden group">
                        <!-- Laser Scan Line (Sophisticated Version) -->
                        <div class="laser-scan-line bg-gradient-to-r from-transparent via-indigo-400 to-transparent h-[1px] opacity-50"></div>
                        
                        <!-- Window Chrome (Top Bar) -->
                        <div class="flex items-center justify-between px-6 py-4 bg-slate-950/50 border-b border-white/5">
                            <div class="flex items-center gap-2">
                                <div class="w-3.5 h-3.5 rounded-full bg-red-500/80 shadow-[0_0_10px_rgba(239,68,68,0.4)]"></div>
                                <div class="w-3.5 h-3.5 rounded-full bg-yellow-500/80 shadow-[0_0_10px_rgba(234,179,8,0.4)]"></div>
                                <div class="w-3.5 h-3.5 rounded-full bg-green-500/80 shadow-[0_0_10px_rgba(34,197,94,0.4)]"></div>
                                <div class="ml-4 px-3 py-1 bg-slate-900 rounded-md border border-white/5 text-[10px] text-slate-500 font-mono tracking-wider">
                                    civio-editor-v2.0.app
                                </div>
                            </div>
                            <!-- Mock AI Status -->
                            <div class="flex items-center gap-2">
                                <span class="flex h-2 w-2 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400">{{ t('analyzing') }}</span>
                            </div>
                        </div>

                        <!-- Editor Content Area -->
                        <div class="flex flex-col lg:flex-row" style="min-height: 480px;">
                            <!-- Sophisticated Sidebar -->
                            <div class="w-full lg:w-24 bg-slate-950/60 border-b lg:border-b-0 lg:border-r border-white/5 flex flex-row lg:flex-col items-center py-4 lg:py-6 gap-3 lg:gap-5 px-4 lg:px-0 overflow-x-auto hide-scrollbar">
                                <div v-for="(icon, i) in [
                                    {char: '👤', label: 'Info', active: true},
                                    {char: '💼', label: 'Work', active: false},
                                    {char: '🎓', label: 'Edu', active: false},
                                    {char: '⚡', label: 'AI', active: false},
                                    {char: '🌐', label: 'Langs', active: false},
                                    {char: '🎨', label: 'Style', active: false},
                                ]"
                                    :key="i"
                                    :class="icon.active ? 'bg-indigo-600/20 text-indigo-400 border-indigo-500/30' : 'text-slate-500 hover:text-slate-300 border-transparent'"
                                    class="w-10 h-10 lg:w-12 lg:h-12 flex-shrink-0 rounded-2xl border flex flex-col items-center justify-center cursor-pointer transition-all hover:scale-110 active:scale-95 group/icon relative"
                                >
                                    <span class="text-xl mb-0.5">{{ icon.char }}</span>
                                    <span class="text-[7px] font-black uppercase tracking-tighter">{{ icon.label }}</span>
                                    <!-- Icon Laser Indicator -->
                                    <div v-if="icon.active" class="absolute -bottom-1 left-1/2 -translate-x-1/2 lg:left-[-4px] lg:top-1/2 lg:-translate-y-1/2 lg:-translate-x-0 w-6 h-1 lg:w-1 lg:h-6 bg-indigo-500 rounded-full shadow-[0_0_10px_#6366f1]"></div>
                                </div>
                            </div>

                            <!-- Dynamic Form Area (left panel) -->
                            <div class="flex-1 p-6 lg:p-10 space-y-6">
                                <div class="space-y-2">
                                    <div class="h-2 w-20 bg-indigo-500/30 rounded-full"></div>
                                    <h4 class="text-xl font-bold text-white tracking-tight">{{ t('personal_details') }}</h4>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <div class="text-[9px] font-black uppercase text-slate-500 tracking-widest">{{ t('fullname') }}</div>
                                        <div class="h-11 bg-slate-900/80 border border-white/5 rounded-xl px-4 flex items-center shadow-inner">
                                            <div class="typing-line w-3/4"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="text-[9px] font-black uppercase text-slate-500 tracking-widest">{{ t('jobtitle') }}</div>
                                        <div class="h-11 bg-slate-900/80 border border-white/5 rounded-xl px-4 flex items-center shadow-inner">
                                            <div class="typing-line w-5/6"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="text-[9px] font-black uppercase text-slate-500 tracking-widest">{{ t('summary') }}</div>
                                    <div class="h-32 bg-slate-900/80 border border-white/5 rounded-xl p-4 shadow-inner space-y-3">
                                        <div class="typing-line w-full" style="animation-delay: 0.5s"></div>
                                        <div class="typing-line w-full" style="animation-delay: 1s"></div>
                                        <div class="typing-line w-3/4" style="animation-delay: 1.5s"></div>
                                    </div>
                                </div>

                                <!-- AI Action Buttons Mockup -->
                                <div class="flex gap-3 pt-4">
                                    <div class="px-4 py-2 bg-indigo-600/10 border border-indigo-500/20 text-indigo-400 rounded-lg text-xs font-bold pulse-glow">
                                        🪄 AI Refine Summary
                                    </div>
                                    <div class="px-4 py-2 bg-slate-800 border border-white/10 text-slate-400 rounded-lg text-xs font-bold">
                                        Check Grammar
                                    </div>
                                </div>
                            </div>

                            <!-- High-Fidelity Preview Area (right panel) -->
                            <div class="flex-1 p-10 bg-slate-950/30 hidden lg:flex items-center justify-center relative">
                                <!-- Floating AI Badge -->
                                <div class="absolute top-14 right-14 z-30 float-badge">
                                    <div class="px-4 py-2 bg-white rounded-xl shadow-2xl flex items-center gap-3 border border-indigo-100">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">98</div>
                                        <div>
                                            <div class="text-[9px] font-black uppercase text-slate-400 leading-none">{{ t('ats_score') }}</div>
                                            <div class="text-[10px] font-bold text-slate-900">Highly Optimized</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Floating Match Badge -->
                                <div class="absolute bottom-14 left-6 z-30 float-badge" style="animation-delay: -2s">
                                    <div class="px-3 py-1.5 bg-indigo-600 rounded-full text-white text-[9px] font-bold flex items-center gap-2 shadow-2xl shadow-indigo-500/50">
                                        <span>✦</span> AI Match: Job Discovery
                                    </div>
                                </div>

                                <!-- The Resume Sheet Mockup -->
                                <div class="w-full h-full bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] flex overflow-hidden transform group-hover:scale-[1.02] transition-transform duration-700">
                                    <div class="w-[30%] bg-slate-900 h-full p-4 flex flex-col gap-4">
                                        <div class="w-full aspect-square bg-slate-800 rounded-xl mb-2"></div>
                                        <div class="space-y-1.5">
                                            <div class="h-1 bg-indigo-500/50 rounded w-full"></div>
                                            <div class="h-0.5 bg-slate-700 rounded w-3/4"></div>
                                            <div class="h-0.5 bg-slate-700 rounded w-5/6"></div>
                                        </div>
                                        <div class="mt-auto space-y-1.5">
                                            <div class="h-1 bg-slate-700 rounded w-2/3"></div>
                                            <div class="h-1 bg-slate-700 rounded w-1/2"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 p-6 space-y-5">
                                        <div class="space-y-2">
                                            <div class="h-4 bg-slate-900 rounded w-2/3"></div>
                                            <div class="h-2 bg-slate-200 rounded w-1/2"></div>
                                        </div>
                                        <div class="h-[1px] bg-slate-100 w-full"></div>
                                        <div class="space-y-3">
                                            <div class="h-2 bg-indigo-100 rounded w-1/4"></div>
                                            <div class="space-y-1.5">
                                                <div class="h-1.5 bg-slate-100 rounded w-full"></div>
                                                <div class="h-1.5 bg-slate-100 rounded w-full"></div>
                                                <div class="h-1.5 bg-slate-100 rounded w-4/5"></div>
                                            </div>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="h-2 bg-indigo-100 rounded w-1/4"></div>
                                            <div class="space-y-1.5">
                                                <div class="h-1.5 bg-slate-100 rounded w-full"></div>
                                                <div class="h-1.5 bg-slate-100 rounded w-5/6"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Features Section: Extreme Premium Grade -->
        <section id="features" class="py-32 relative z-10 border-t border-white/5">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center mb-20">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-black uppercase tracking-[0.2em] mb-4">
                        ✦ Advanced Features ✦
                    </div>
                    <h2 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight" v-html="t('how_it_works')"></h2>
                    <p class="text-slate-400 max-w-2xl mx-auto text-lg leading-relaxed">{{ t('pricing_hero_subtitle') }}</p>
                </div>

                <!-- Feature Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <!-- Feature: Multi-Language (Indigo/Blue) -->
                    <div class="laser-btn-wrapper laser-blue rounded-[2.5rem] group/card transition-all hover:scale-[1.02] active:scale-[0.98] duration-500 shadow-2xl shadow-blue-500/10">
                        <div class="laser-btn-content p-10 bg-slate-900/40 backdrop-blur-2xl rounded-[2.5rem] flex flex-col h-full border border-white/5">
                            <div class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-8 border border-blue-500/20 group-hover/card:shadow-[0_0_20px_rgba(59,130,246,0.3)] transition-all duration-500 relative overflow-hidden">
                                <div class="absolute inset-0 bg-blue-500/20 blur-xl opacity-0 group-hover/card:opacity-100 transition-opacity"></div>
                                <!-- Glowing Globe SVG -->
                                <svg class="w-10 h-10 text-blue-400 group-hover/card:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-4 uppercase tracking-tighter">{{ t('multi_lang') }}</h3>
                            <p class="text-slate-400 leading-relaxed font-medium mb-8">{{ t('multi_lang_desc') }}</p>
                            <div class="mt-auto flex items-center gap-2 text-blue-400 text-[10px] font-black uppercase tracking-[0.25em] opacity-60 group-hover/card:opacity-100 transition-all transform translate-y-1 group-hover/card:translate-y-0">
                                {{ t('languages') }} →
                            </div>
                        </div>
                    </div>

                    <!-- Feature: AI Assistant (Purple) -->
                    <div class="laser-btn-wrapper rounded-[2.5rem] group/card transition-all hover:scale-[1.02] active:scale-[0.98] duration-500 shadow-2xl shadow-purple-500/10">
                        <div class="laser-btn-content p-10 bg-slate-900/40 backdrop-blur-2xl rounded-[2.5rem] flex flex-col h-full border border-white/5">
                            <div class="w-16 h-16 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-8 border border-purple-500/20 group-hover/card:shadow-[0_0_20px_rgba(168,85,247,0.3)] transition-all duration-500 relative overflow-hidden">
                                <div class="absolute inset-0 bg-purple-500/20 blur-xl opacity-0 group-hover/card:opacity-100 transition-opacity"></div>
                                <!-- Glowing AI SVG -->
                                <svg class="w-10 h-10 text-purple-400 group-hover/card:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2zM9 7h1m4 0h1m-5 4h1m4 0h1"/>
                                </svg>
                            </div>
                            <div class="flex items-center gap-2 mb-4">
                                <h3 class="text-2xl font-black text-white uppercase tracking-tighter">{{ t('ai_assistant_title') }}</h3>
                                <span class="px-2 py-0.5 bg-indigo-500/20 border border-indigo-500/30 rounded text-[8px] text-indigo-300 font-bold uppercase tracking-widest">{{ t('ai_assistant_badge') }}</span>
                            </div>
                            <p class="text-slate-400 leading-relaxed font-medium mb-8">{{ t('ai_assistant_desc') }}</p>
                            <div class="mt-auto flex items-center gap-2 text-purple-400 text-[10px] font-black uppercase tracking-[0.25em] opacity-60 group-hover/card:opacity-100 transition-all transform translate-y-1 group-hover/card:translate-y-0">
                                {{ t('generate') }} →
                            </div>
                        </div>
                    </div>

                    <!-- Feature: Premium Themes (Emerald) -->
                    <div class="laser-btn-wrapper laser-emerald rounded-[2.5rem] group/card transition-all hover:scale-[1.02] active:scale-[0.98] duration-500 shadow-2xl shadow-emerald-500/10">
                        <div class="laser-btn-content p-10 bg-slate-900/40 backdrop-blur-2xl rounded-[2.5rem] flex flex-col h-full border border-white/5">
                            <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-8 border border-emerald-500/20 group-hover/card:shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all duration-500 relative overflow-hidden">
                                <div class="absolute inset-0 bg-emerald-500/20 blur-xl opacity-0 group-hover/card:opacity-100 transition-opacity"></div>
                                <!-- Glowing Palette SVG -->
                                <svg class="w-10 h-10 text-emerald-400 group-hover/card:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-4 uppercase tracking-tighter">{{ t('premium_themes') }}</h3>
                            <p class="text-slate-400 leading-relaxed font-medium mb-8">{{ t('premium_themes_desc') }}</p>
                            <div class="mt-auto flex items-center gap-2 text-emerald-400 text-[10px] font-black uppercase tracking-[0.25em] opacity-60 group-hover/card:opacity-100 transition-all transform translate-y-1 group-hover/card:translate-y-0">
                                {{ t('template_picker') }} →
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- ─── Templates Showcase ─────────────────────────────── -->
        <section id="templates" class="py-24 relative z-10 border-t border-white/10">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-14">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-purple-500/10 border border-purple-500/30 text-purple-300 text-sm rounded-full mb-4">
                        🎨 {{ t('premium_themes') }}
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ t('template_picker') }}</h2>
                    <p class="text-slate-400 max-w-lg mx-auto">{{ t('premium_themes_desc') }}</p>
                </div>

                <div ref="gridContainer" class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-10">
                    <div 
                        v-for="tpl in showcaseTemplates" 
                        :key="tpl.id"
                        class="template-card group cursor-pointer relative bg-slate-900/40 backdrop-blur-xl border border-white/5 rounded-3xl overflow-hidden hover:border-indigo-500/50 transition-all duration-700 hover:-translate-y-2 shadow-2xl"
                    >
                        <div class="aspect-[210/297] w-full relative overflow-hidden bg-slate-900/50 ring-1 ring-slate-900/5 flex items-start justify-center p-0">
                            <!-- Scaled actual template -->
                            <div class="absolute top-0 left-0 w-[800px] h-[1131px] pointer-events-none" :style="{ transform: `scale(${calculateScale})`, transformOrigin: 'top left' }">
                                <ResumePreview 
                                    :resume="{...dummyResume, template: tpl.id, design_options: { accent_color: tpl.color }}" 
                                    :editable="false" 
                                />
                            </div>
                            
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center p-8 text-center flex-col gap-4">
                                <span v-if="tpl.pro" class="text-[9px] bg-amber-500 text-black px-3 py-1 font-black uppercase tracking-[0.2em] rounded-full shadow-xl">Premium Style</span>
                                <Link 
                                    :href="route('register')"
                                    class="laser-btn-wrapper w-full active:scale-95 transition-all"
                                >
                                    <div class="laser-btn-content py-3 text-white text-[10px] font-black uppercase tracking-[0.2em]">
                                        {{ t('edit', 'Edit') }}
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-5 border-t border-white/5 flex items-center justify-between bg-slate-800/90 relative z-10">
                            <span class="text-sm font-bold text-white uppercase tracking-widest">{{ t(tpl.nameKey) }}</span>
                            <span v-if="tpl.pro" class="text-xs px-2 py-0.5 bg-indigo-500/20 text-indigo-400 rounded-full font-bold uppercase tracking-wider">{{ t('plan_pro') }}</span>
                            <span v-else class="text-xs px-2 py-0.5 bg-green-500/20 text-green-400 rounded-full font-bold uppercase tracking-wider">{{ t('plan_free') }}</span>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-10">
                    <div class="laser-btn-wrapper laser-silver active:scale-95 transition-all">
                        <Link :href="route('register')" class="laser-btn-content px-6 py-3 text-sm text-white font-medium">
                            Start with any template — Free →
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── How It Works ──────────────────────────────────── -->
        <section class="py-24 relative z-10 border-t border-white/10">
            <div class="max-w-5xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-black text-white mb-4 uppercase tracking-tighter">{{ t('trusted_by') }}</h2>
                    <p class="text-slate-500 font-medium">{{ t('join_pros') }}</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 relative">
                    <!-- Connecting line -->
                    <div class="hidden md:block absolute top-10 left-[20%] right-[20%] h-0.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-opacity-30"></div>

                    <div v-for="(step, i) in [
                        { num: '01', icon: '📝', title: 'Sign up for free', desc: 'Create your account in 30 seconds — no credit card required.', color: 'indigo' },
                        { num: '02', icon: '⚡', title: 'Fill in your details', desc: 'Use our AI assistant to write compelling content in any language.', color: 'purple' },
                        { num: '03', icon: '📄', title: 'Download & apply', desc: 'Export your professional PDF and start applying immediately.', color: 'pink' },
                    ]" :key="i" class="text-center relative">
                        <div class="relative inline-flex w-20 h-20 items-center justify-center rounded-2xl bg-slate-800 border border-white/10 text-3xl mb-5 shadow-xl group-hover:scale-105">
                            {{ step.icon }}
                            <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xs font-bold flex items-center justify-center shadow">
                                {{ i + 1 }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">{{ t('step_' + (i + 1) + '_title') }}</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">{{ step.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── Testimonials ──────────────────────────────────── -->
        <section class="py-24 relative z-10 border-t border-white/10">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center mb-14">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-yellow-500/10 border border-yellow-500/30 text-yellow-300 text-sm rounded-full mb-4">
                        ⭐ Loved by job seekers worldwide
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white">Real people, real results</h2>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div v-for="testimonial in [
                        { name: 'Ahmed Al-Rashid', role: 'Software Engineer at Google', flag: '🇸🇦', quote: 'أفضل أداة لبناء السيرة الذاتية تدعم اللغة العربية! ساعدتني على الحصول على وظيفتي.', stars: 5 },
                        { name: 'Marie Dupont', role: 'Product Designer · Paris', flag: '🇫🇷', quote: 'Incroyable! Les templates sont magnifiques et l\'IA m\'a aidé à rédiger des phrases percutantes.', stars: 5 },
                        { name: 'Carlos Rivera', role: 'Marketing Manager · Madrid', flag: '🇪🇸', quote: 'I got 3 interview callbacks within a week of using Civio. The ATS score feature is a game-changer!', stars: 5 },
                    ]" :key="testimonial.name"
                        class="p-6 rounded-2xl bg-white/5 border border-white/10 hover:border-white/20 transition-all"
                    >
                        <!-- Stars -->
                        <div class="flex gap-0.5 mb-4">
                            <span v-for="s in testimonial.stars" :key="s" class="text-yellow-400 text-sm">★</span>
                        </div>
                        <!-- Quote -->
                        <p class="text-slate-300 text-sm leading-relaxed mb-5 italic">"{{ testimonial.quote }}"</p>
                        <!-- Author -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-lg">
                                {{ testimonial.flag }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ testimonial.name }}</p>
                                <p class="text-xs text-slate-500">{{ testimonial.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── Pricing Teaser ────────────────────────────────── -->
        <section class="py-20 relative z-10 border-t border-white/10">
            <div class="max-w-4xl mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-white mb-3">Simple Pricing</h2>
                    <p class="text-slate-400">Start free, upgrade when ready.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Free -->
                    <div class="p-7 rounded-2xl bg-slate-800/50 border border-white/10 hover:border-indigo-500/30 transition-all">
                        <p class="text-slate-400 text-sm font-medium mb-1">Free</p>
                        <p class="text-4xl font-bold text-white mb-1">$0</p>
                        <p class="text-slate-500 text-sm mb-6">Forever</p>
                        <ul class="space-y-2 text-sm text-slate-400 mb-6 font-medium">
                            <li class="flex items-center gap-2"><span class="text-green-400">✓</span> 1 Resume</li>
                            <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Basic Templates</li>
                            <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Standard PDF</li>
                        </ul>
                        <div class="laser-btn-wrapper laser-silver w-full active:scale-95 transition-all mt-4">
                            <Link :href="route('register')" class="laser-btn-content w-full py-2.5 text-white text-sm font-medium">
                                Start Free
                            </Link>
                        </div>
                    </div>
                    <!-- Pro -->
                    <div class="p-7 rounded-2xl bg-gradient-to-b from-indigo-600/20 to-purple-600/10 border border-indigo-500/40 relative transform scale-105 shadow-2xl shadow-indigo-500/20 z-10">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-indigo-600 text-white text-[8px] font-black uppercase tracking-widest rounded-full">MOST POPULAR</div>
                        <p class="text-indigo-400 text-sm font-medium mb-1">Pro</p>
                        <p class="text-4xl font-bold text-white mb-1">$5</p>
                        <p class="text-slate-500 text-sm mb-6">per month</p>
                        <ul class="space-y-2 text-sm text-slate-300 mb-6 font-bold">
                            <li class="flex items-center gap-2"><span class="text-indigo-400">✦</span> Unlimited Resumes</li>
                            <li class="flex items-center gap-2"><span class="text-indigo-400">✦</span> All Premium Templates</li>
                            <li class="flex items-center gap-2"><span class="text-indigo-400">✦</span> AI Power & ATS Checker</li>
                            <li class="flex items-center gap-2"><span class="text-indigo-400">✦</span> 8+ Languages & RTL</li>
                        </ul>
                        <div class="laser-btn-wrapper w-full active:scale-95 transition-all mt-4">
                            <Link :href="route('pricing')" class="laser-btn-content w-full py-2.5 text-white text-sm font-bold uppercase tracking-widest">
                                Upgrade Now →
                            </Link>
                        </div>
                    </div>
                    <!-- Lifetime -->
                    <div class="p-7 rounded-2xl bg-slate-800/50 border border-white/10 hover:border-purple-500/30 transition-all">
                        <p class="text-purple-400 text-sm font-medium mb-1">Lifetime</p>
                        <p class="text-4xl font-bold text-white mb-1">$29</p>
                        <p class="text-slate-500 text-sm mb-6">One-time payment</p>
                        <ul class="space-y-2 text-sm text-slate-400 mb-6 font-medium">
                            <li class="flex items-center gap-2"><span class="text-purple-400">✓</span> All Pro Features</li>
                            <li class="flex items-center gap-2"><span class="text-purple-400">✓</span> Forever Access</li>
                            <li class="flex items-center gap-2"><span class="text-purple-400">✓</span> No Subscriptions</li>
                        </ul>
                        <div class="laser-btn-wrapper laser-purple w-full active:scale-95 transition-all mt-4">
                            <Link :href="route('pricing')" class="laser-btn-content w-full py-2.5 text-white text-sm font-medium">
                                Get Lifetime
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── FAQ ───────────────────────────────────────────── -->
        <section class="py-20 relative z-10 border-t border-white/10">
            <div class="max-w-3xl mx-auto px-6">
                <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-12">Frequently asked questions</h2>
                <div class="space-y-3">
                    <details v-for="faq in [
                        { q: 'Is Civio really free?', a: 'Yes! Our Free plan lets you create 3 resumes with 2 templates and PDF export — no credit card needed.' },
                        { q: 'Does it support Arabic (RTL)?', a: 'Absolutely. Civio has built-in Right-to-Left support for Arabic, making it the best resume builder for Arabic speakers.' },
                        { q: 'Which languages are supported?', a: 'English, Arabic, French, and coming soon: Spanish, German, Italian, Portuguese, and Dutch.' },
                        { q: 'Is my data safe & GDPR compliant?', a: 'Yes. We are fully GDPR compliant. Your data is encrypted, never sold, and you can request deletion at any time from your profile settings.' },
                        { q: 'Can I cancel my Pro subscription anytime?', a: 'Yes. Cancel anytime with no questions asked. Your Pro features remain active until the end of the billing period.' },
                        { q: 'What is ATS Score?', a: 'ATS (Applicant Tracking System) is software used by companies to filter resumes. Our checker scores your resume 0-100 and gives tips to improve visibility.' },
                    ]" :key="faq.q"
                        class="group rounded-xl bg-white/5 border border-white/10 hover:border-white/20 overflow-hidden transition-all"
                    >
                        <summary class="flex items-center justify-between p-5 cursor-pointer text-white font-medium select-none list-none">
                            <span>{{ faq.q }}</span>
                            <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <p class="px-5 pb-5 text-slate-400 text-sm leading-relaxed border-t border-white/5">{{ faq.a }}</p>
                    </details>
                </div>
            </div>
        </section>

        <!-- ─── Final CTA ──────────────────────────────────────── -->
        <section class="py-24 relative z-10 border-t border-white/10">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <div class="relative p-12 rounded-3xl overflow-hidden border border-white/10">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 via-purple-600/10 to-transparent"></div>
                    <div class="relative z-10 py-6">
                        <div class="text-6xl mb-6 float-animation">🚀</div>
                        <h2 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight tracking-tighter">
                            {{ t('cta_ready_title') || 'Ready to land your' }}
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 italic font-serif">
                                {{ t('cta_dream_job') || 'dream job?' }}
                            </span>
                        </h2>
                        <p class="text-slate-400 text-xl mb-10 max-w-2xl mx-auto leading-relaxed">
                            {{ t('cta_subtitle') || 'Join 10,000+ professionals who got hired faster with Civio.' }}
                        </p>
                        
                        <div class="flex flex-col sm:flex-row justify-center items-center gap-8 mb-8">
                            <div class="laser-btn-wrapper shadow-[0_0_50px_rgba(99,102,241,0.3)] active:scale-95 transition-all group/cta">
                                <Link :href="route('pricing')" class="laser-btn-content px-12 py-6 text-white font-black text-xs uppercase tracking-[0.3em] flex items-center gap-3">
                                    <span>{{ t('upgrade_plan') }}</span>
                                    <span class="group-hover/cta:translate-x-1 transition-transform">→</span>
                                </Link>
                            </div>
                            <div class="laser-btn-wrapper laser-silver active:scale-95 transition-all group/tpl">
                                <Link href="#templates" class="laser-btn-content px-10 py-5 text-white font-bold text-lg flex items-center gap-3">
                                    <span>{{ t('template_picker') }}</span>
                                    <span class="opacity-50 group-hover/tpl:rotate-12 transition-transform">🎨</span>
                                </Link>
                            </div>
                        </div>
                        
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-[0.2em] opacity-50">
                            {{ t('cta_legal_info') || 'No credit card · GDPR Compliant · Cancel anytime' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── Footer ─────────────────────────────────────────── -->
        <footer class="pt-24 pb-12 border-t border-white/5 relative z-10 bg-slate-950 overflow-hidden">
            <!-- Footer Glow -->
            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full h-96 bg-indigo-600/10 blur-[150px] pointer-events-none"></div>
 
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-20">
                    <!-- Brand Column -->
                    <div class="md:col-span-4">
                        <Link href="/" class="flex items-center gap-3 mb-6">
                            <img src="/images/logo.png" alt="Logo" class="w-10 h-10 object-contain" />
                            <span class="text-2xl font-black text-white">CIVIO</span>
                        </Link>
                        <p class="text-slate-400 text-sm leading-relaxed mb-8 max-w-xs">
                            The next-gen AI resume builder designed to help you land your dream job with stunning design and advanced tools.
                        </p>
                        <div class="flex items-center gap-4">
                            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 border border-white/10 text-slate-400 hover:text-white hover:bg-indigo-600 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 border border-white/10 text-slate-400 hover:text-white hover:bg-indigo-400 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 border border-white/10 text-slate-400 hover:text-white hover:bg-pink-600 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        </div>
                    </div>
 
                    <!-- Navigation Groups -->
                    <div class="md:col-span-8 grid grid-cols-2 sm:grid-cols-3 gap-8">
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-[0.2em] text-white mb-6">{{ t('features') }}</h4>
                            <ul class="space-y-4">
                                <li><a href="#" class="text-slate-500 hover:text-white transition-colors text-sm font-medium">{{ t('ai_assist') }}</a></li>
                                <li><a href="#" class="text-slate-500 hover:text-white transition-colors text-sm font-medium">{{ t('template') }}</a></li>
                                <li><a href="#" class="text-slate-500 hover:text-white transition-colors text-sm font-medium">{{ t('ats_score') }}</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-[0.2em] text-white mb-6">Company</h4>
                            <ul class="space-y-4">
                                <li><Link :href="route('about')" class="text-slate-500 hover:text-white transition-colors text-sm font-medium">{{ t('about_us') }}</Link></li>
                                <li><Link :href="route('pricing')" class="text-slate-500 hover:text-white transition-colors text-sm font-medium">{{ t('pricing') }}</Link></li>
                                <li><Link :href="route('contact')" class="text-slate-500 hover:text-white transition-colors text-sm font-medium">{{ t('contact_us') }}</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-[0.2em] text-white mb-6">Legal</h4>
                            <ul class="space-y-4">
                                <li><Link :href="route('privacy')" class="text-slate-500 hover:text-white transition-colors text-sm font-medium">{{ t('privacy_policy') }}</Link></li>
                                <li><Link :href="route('terms')" class="text-slate-500 hover:text-white transition-colors text-sm font-medium">{{ t('terms_of_service') }}</Link></li>
                            </ul>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <h4 class="text-white font-bold text-sm uppercase tracking-widest mb-6">Newsletter</h4>
                            <p class="text-slate-500 text-xs mb-4">Get the latest career tips and updates.</p>
                            <div class="flex gap-2">
                                <input type="email" placeholder="Email" class="bg-white/5 border-white/10 rounded-lg text-xs w-full focus:ring-indigo-500 focus:border-indigo-500 text-white" />
                                <button class="p-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Bottom bar -->
                <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <p class="text-[11px] text-slate-500 font-medium tracking-wide">
                            © {{ new Date().getFullYear() }} CIVIO. ALL RIGHTS RESERVED.
                        </p>
                        <div class="flex items-center gap-4 text-[11px] text-slate-600">
                            <Link :href="route('privacy')" class="hover:text-slate-400 transition-colors">PRIVACY POLICY</Link>
                            <span>/</span>
                            <Link :href="route('terms')" class="hover:text-slate-400 transition-colors">TERMS OF SERVICE</Link>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] text-slate-600 flex items-center gap-1.5 font-bold uppercase tracking-tighter">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            System Operational
                        </span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>


