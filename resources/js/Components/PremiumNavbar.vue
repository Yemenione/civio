<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    transparent: {
        type: Boolean,
        default: false
    },
    links: {
        type: Array,
        default: () => []
    },
    sticky: {
        type: Boolean,
        default: true
    }
});

const isScrolled = ref(false);
const showingMobileMenu = ref(false);
const showingUserDropdown = ref(false);
let dropdownTimeout = null;

const openDropdown = () => {
    if (dropdownTimeout) clearTimeout(dropdownTimeout);
    showingUserDropdown.value = true;
};

const closeDropdown = () => {
    dropdownTimeout = setTimeout(() => {
        showingUserDropdown.value = false;
    }, 300); // 300ms delay to prevent accidental closing
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.role === 'admin');

const navClass = computed(() => {
    if (!props.sticky) return 'relative w-full bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800';
    
    const base = 'fixed top-0 left-0 w-full z-50 transition-all duration-300 ';
    if (props.transparent && !isScrolled.value) {
        return base + 'bg-transparent border-transparent py-6';
    }
    return base + 'bg-white/80 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-800/50 py-3 shadow-lg shadow-indigo-500/5';
});

const closeMobileMenu = () => {
    showingMobileMenu.value = false;
};
</script>

<template>
    <nav :class="navClass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo -->
                <div class="flex items-center gap-8">
                    <Link href="/" class="flex items-center gap-3 group">
                        <div class="relative">
                            <div class="absolute inset-0 bg-indigo-500 blur-lg opacity-0 group-hover:opacity-40 transition-opacity"></div>
                            <img src="/images/logo.png" alt="Logo" class="relative w-9 h-9 object-contain group-hover:scale-110 transition-transform duration-500" />
                        </div>
                        <span class="text-xl font-black tracking-tighter bg-clip-text text-transparent bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 dark:from-white dark:via-white dark:to-slate-300 uppercase">
                            CIVIO
                        </span>
                    </Link>

                    <!-- Center: Desktop Nav Links -->
                    <div class="hidden lg:flex items-center gap-1">
                        <template v-for="link in links" :key="link.label">
                            <component 
                                :is="link.href.startsWith('#') ? 'a' : Link"
                                :href="link.href"
                                :class="[
                                    'px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-[0.15em] transition-all duration-300 relative group/link',
                                    link.active 
                                        ? 'text-indigo-600 dark:text-white bg-indigo-50 dark:bg-white/10' 
                                        : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-white/5'
                                ]"
                            >
                                <span class="relative z-10">{{ link.label }}</span>
                                <div class="absolute bottom-1.5 left-4 right-4 h-0.5 bg-indigo-500 scale-x-0 group-hover/link:scale-x-100 transition-transform origin-left rounded-full" v-if="!link.active"></div>
                            </component>
                        </template>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <div class="flex items-center gap-2 hidden sm:flex">
                        <ThemeSwitcher />
                        <LanguageSwitcher />
                    </div>

                    <!-- User Actions -->
                    <div class="flex items-center gap-3">
                        <template v-if="user">
                            <div class="relative" @mouseleave="closeDropdown">
                                <button 
                                    @mouseenter="openDropdown"
                                    class="flex items-center gap-3 pl-3 pr-2 py-1.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-indigo-500/30 transition-all group"
                                >
                                    <div class="w-7 h-7 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="hidden md:block text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">{{ user.name }}</span>
                                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- User Dropdown -->
                                <transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition duration-150 ease-in"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                >
                                    <div 
                                        v-show="showingUserDropdown" 
                                        @mouseenter="openDropdown"
                                        class="absolute right-0 top-full pt-2 w-56 z-[60]"
                                    >
                                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden py-2">
                                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 mb-1">
                                            <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter">{{ user.name }}</p>
                                            <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ user.email }}</p>
                                        </div>
                                        <Link :href="route('dashboard')" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            {{ t('dashboard') }}
                                        </Link>
                                        <Link :href="route('profile.edit')" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            {{ t('profile_settings') }}
                                        </Link>
                                        <div v-if="isAdmin" class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                                        <Link v-if="isAdmin" :href="route('admin.dashboard')" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-amber-500 hover:bg-amber-500/5 transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            {{ t('admin_panel') }}
                                        </Link>
                                        <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                                        <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all text-left">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            {{ t('logout') }}
                                        </Link>
                                    </div>
                                </div>
                            </transition>
                            </div>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="hidden sm:block text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white transition-colors">
                                {{ t('sign_in') }}
                            </Link>
                            <Link :href="route('register')" class="px-6 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-500 transition-all animate-shimmer bg-[length:200%_100%] active:scale-95">
                                {{ t('new_cv', 'Get Started') }}
                            </Link>
                        </template>

                        <!-- Mobile Toggle -->
                        <button @click="showingMobileMenu = true" class="lg:hidden p-2 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div v-show="showingMobileMenu" class="fixed inset-0 z-[100] lg:hidden">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showingMobileMenu = false"></div>
                
                <!-- Content -->
                <div class="absolute right-0 top-0 bottom-0 w-[85%] max-w-sm bg-white dark:bg-slate-950 shadow-2xl flex flex-col">
                    <div class="p-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
                        <span class="text-lg font-black tracking-widest text-slate-900 dark:text-white uppercase">Menu</span>
                        <button @click="showingMobileMenu = false" class="p-2 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6 space-y-2">
                        <template v-for="link in links" :key="link.label">
                            <component 
                                :is="link.href.startsWith('#') ? 'a' : Link"
                                :href="link.href"
                                @click="closeMobileMenu"
                                :class="[
                                    'flex items-center justify-between px-5 py-4 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all',
                                    link.active 
                                        ? 'bg-indigo-50 dark:bg-indigo-600/10 text-indigo-600 dark:text-white' 
                                        : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5'
                                ]"
                            >
                                {{ link.label }}
                                <svg class="w-4 h-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                            </component>
                        </template>
                        
                        <div class="h-px bg-slate-100 dark:bg-slate-800 my-6"></div>
                        
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between px-2">
                                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">{{ t('theme') || 'Appearance' }}</span>
                                <ThemeSwitcher />
                            </div>
                            <div class="flex items-center justify-between px-2">
                                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">{{ t('language') || 'Language' }}</span>
                                <LanguageSwitcher />
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-slate-100 dark:border-slate-800">
                        <template v-if="user">
                            <Link :href="route('dashboard')" @click="closeMobileMenu" class="w-full flex items-center justify-center gap-3 py-4 rounded-2xl bg-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-500/20 active:scale-95 transition-all">
                                {{ t('dashboard') }}
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </Link>
                        </template>
                        <template v-else>
                            <div class="grid grid-cols-2 gap-3">
                                <Link :href="route('login')" @click="closeMobileMenu" class="flex items-center justify-center py-4 rounded-2xl bg-slate-100 dark:bg-white/5 text-[11px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">
                                    {{ t('sign_in') }}
                                </Link>
                                <Link :href="route('register')" @click="closeMobileMenu" class="flex items-center justify-center py-4 rounded-2xl bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20">
                                    {{ t('new_cv', 'Join') }}
                                </Link>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </transition>
    </nav>
</template>

<style scoped>
.animate-shimmer {
    background-image: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.1) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    animation: shimmer 3s infinite linear;
}

@keyframes shimmer {
    from { background-position: -200% 0; }
    to { background-position: 200% 0; }
}
</style>
