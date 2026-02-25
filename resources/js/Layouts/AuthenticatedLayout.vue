<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import PremiumNavbar from '@/Components/PremiumNavbar.vue';
import { useI18n } from 'vue-i18n';
import PremiumToast from '@/Components/PremiumToast.vue';
import PremiumAlert from '@/Components/PremiumAlert.vue';
import PremiumConfirm from '@/Components/PremiumConfirm.vue';

const { t } = useI18n();

const showingNavigationDropdown = ref(false);
const showingMobileMenu = ref(false);
const page = usePage();

watch(() => page.url, () => {
    showingMobileMenu.value = false;
    showingNavigationDropdown.value = false;
});

const props = defineProps({
    fluid: {
        type: Boolean,
        default: false
    }
});

const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.role === 'admin');
const isPro = computed(() => user.value?.plan === 'pro');
const isImpersonating = computed(() => !!page.props.auth?.impersonated_by);
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-300 font-sans selection:bg-indigo-500 selection:text-white transition-colors duration-300">
        <!-- Background Gradients -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-indigo-600/10 rounded-full blur-[160px] animate-pulse"></div>
            <div class="absolute bottom-[20%] right-[-5%] w-[40%] h-[40%] bg-emerald-500/10 rounded-full blur-[140px] animate-pulse" style="animation-duration: 10s;"></div>
            <div class="absolute top-[30%] right-[10%] w-[30%] h-[30%] bg-purple-600/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s; animation-duration: 8s;"></div>
        </div>

        <!-- Impersonation Banner -->
        <div v-if="isImpersonating" class="fixed top-0 left-0 w-full z-[100] bg-amber-500 text-black px-4 py-1.5 text-center text-xs font-bold flex items-center justify-center gap-4 border-b border-black/10 shadow-lg">
            <span>⚠️ {{ t('impersonating_mode') || 'Impersonation Mode Active' }}: {{ user.name }}</span>
            <Link :href="route('admin.impersonate.leave')" method="post" as="button" class="bg-black text-white px-3 py-1 rounded-full hover:bg-black/80 transition-all uppercase tracking-widest text-[10px]">
                {{ t('exit_impersonation') || 'Exit & Return to Admin' }}
            </Link>
        </div>

        <PremiumNavbar 
            :links="[
                { label: t('dashboard'), href: route('dashboard'), active: route().current('dashboard') },
                { label: t('resume_templates'), href: route('resumes.browse'), active: route().current('resumes.browse') },
                { label: t('cover_letters'), href: route('cover-letters.index'), active: route().current('cover-letters.*') },
                { label: t('pricing'), href: route('pricing'), active: route().current('pricing') },
            ]"
            :sticky="true"
        />

        <!-- Page Heading -->
        <header class="relative z-10 pt-24 pb-4" v-if="$slots.header">
            <div :class="[fluid ? 'w-full px-6' : 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8']">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="relative z-10" :class="[fluid ? 'w-full' : 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-12', !$slots.header ? 'pt-20' : '']">
            <slot />
        </main>

        <!-- Global Notifications -->
        <PremiumToast />
        <PremiumAlert />
        <PremiumConfirm />
    </div>
</template>
