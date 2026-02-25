<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const page = usePage();
const { t } = useI18n();

import PremiumToast from '@/Components/PremiumToast.vue';
import PremiumAlert from '@/Components/PremiumAlert.vue';
import PremiumConfirm from '@/Components/PremiumConfirm.vue';
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const navItems = [
    {
        label: t('dashboard'),
        route: 'admin.dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    },
    {
        label: t('users'),
        route: 'admin.users.index',
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
    },
    {
        label: t('subscriptions'),
        route: 'admin.subscriptions.index',
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
    },
    {
        label: t('payments'),
        route: 'admin.payments.index',
        icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'
    },
    {
        label: t('settings'),
        route: 'admin.settings.index',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
    },
    {
        label: t('templates') || 'Templates',
        route: 'admin.templates.index',
        icon: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'
    },
    {
        label: t('coupons'),
        route: 'admin.coupons.index',
        icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'
    },
    {
        label: t('plans') || 'Plans',
        route: 'admin.plans.index',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'
    },
    {
        label: 'SEO & Content',
        route: 'admin.seo.index',
        icon: 'M21 12H3m14-4H7m8-4H9m10 12H5m12 4H7'
    },
    {
        label: 'AI Operations',
        route: 'admin.ai-ops.index',
        icon: 'M13 10V3L4 14h7v7l9-11h-7z'
    },
    {
        label: 'Newsletter',
        route: 'admin.newsletter.index',
        icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
    },
    {
        label: 'System Health',
        route: 'admin.logs.index',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
    },
];

const isActive = (routeName) => {
    try {
        return route().current(routeName);
    } catch {
        return false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-300 flex transition-colors duration-300">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-50 dark:bg-slate-900 border-r border-black/5 dark:border-white/10 flex flex-col flex-shrink-0">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-slate-900 dark:text-white font-bold text-sm">{{ t('admin_panel') }}</div>
                        <div class="text-slate-400 dark:text-slate-50 text-[10px] opacity-20 tracking-widest uppercase">Civio</div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <template v-for="item in navItems" :key="item.route">
                    <Link
                        :href="route(item.route)"
                        :class="[
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
                                isActive(item.route)
                                    ? 'bg-indigo-100 dark:bg-indigo-600/20 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20 dark:border-indigo-500/30'
                                    : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-white'
                        ]"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/>
                        </svg>
                        {{ item.label }}
                    </Link>
                </template>
            </nav>

            <!-- Footer -->
            <div class="p-4 border-t border-black/5 dark:border-white/10">
                <Link :href="route('dashboard')" class="flex items-center gap-2 text-sm text-slate-500 hover:text-indigo-600 dark:hover:text-slate-200 transition-colors mb-4 font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ t('back_to_app') }}
                </Link>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ page.props.auth?.user?.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-slate-900 dark:text-white text-sm font-medium truncate">{{ page.props.auth?.user?.name }}</div>
                        <div class="text-slate-500 text-[10px] uppercase font-bold tracking-tighter truncate">{{ t('administrator') }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="h-16 bg-white dark:bg-slate-900/50 border-b border-black/5 dark:border-white/10 flex items-center justify-between px-8 shadow-sm">
                <slot name="header" />
                <div class="flex items-center gap-4">
                    <ThemeSwitcher />
                    <LanguageSwitcher />
                </div>
            </header>

            <!-- Page -->
            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>
        </div>

        <!-- Global Notifications -->
        <PremiumToast />
        <PremiumAlert />
        <PremiumConfirm />
    </div>
</template>
