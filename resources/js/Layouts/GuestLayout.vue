<script setup>
import { Link } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import PremiumToast from '@/Components/PremiumToast.vue';
import PremiumAlert from '@/Components/PremiumAlert.vue';
import PremiumConfirm from '@/Components/PremiumConfirm.vue';
</script>

<template>
    <div class="min-h-screen bg-slate-900 relative overflow-hidden selection:bg-indigo-500 selection:text-white">
        <!-- Background -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute top-[-15%] left-[-15%] w-[50%] h-[50%] bg-indigo-600/15 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-[-15%] right-[-15%] w-[50%] h-[50%] bg-purple-600/15 rounded-full blur-[140px]"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 flex min-h-screen flex-col items-center justify-center px-4 py-12">
            <!-- Logo -->
            <Link href="/" class="flex flex-col items-center gap-4 mb-10 group">
                <img src="/images/logo.png" alt="Logo" class="w-20 h-20 object-contain group-hover:scale-105 transition-all duration-300 drop-shadow-[0_0_15px_rgba(99,102,241,0.5)]" />
                <span class="text-3xl silver-branding">
                    Civio
                </span>
            </Link>

            <!-- Card -->
            <div class="w-full max-w-md">
                <div class="bg-slate-800/60 backdrop-blur-lg border border-white/10 rounded-2xl shadow-2xl p-8">
                    <!-- Language Switcher -->
                    <div class="flex justify-end mb-6">
                        <LanguageSwitcher />
                    </div>

                    <slot />
                </div>

                <!-- Footer links -->
                <p class="text-center mt-6 text-sm text-slate-500">
                    © {{ new Date().getFullYear() }} Civio ·
                    <Link href="/privacy" class="hover:text-slate-300 transition-colors">Privacy</Link> ·
                    <Link href="/terms" class="hover:text-slate-300 transition-colors">Terms</Link>
                </p>
            </div>
        </div>

        <PremiumToast />
        <PremiumAlert />
        <PremiumConfirm />
    </div>
</template>
