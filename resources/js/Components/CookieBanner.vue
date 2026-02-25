<template>
    <!-- Cookie Banner -->
    <Transition
        enter-active-class="transition-all duration-500 ease-out"
        enter-from-class="translate-y-full opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-full opacity-0"
    >
        <div
            v-if="showBanner"
            class="fixed bottom-0 left-0 right-0 z-[9999] p-4 sm:p-6"
        >
            <div class="max-w-5xl mx-auto bg-slate-800/95 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <!-- Text -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xl">🍪</span>
                            <h3 class="font-semibold text-white text-sm sm:text-base">We use cookies</h3>
                        </div>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            We use essential cookies to make our site work. With your consent, we may also use analytics cookies to improve your experience.
                            <a href="/cookies" class="text-indigo-400 hover:text-indigo-300 underline ml-1">Learn more</a>
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                        <button
                            @click="rejectAll"
                            class="px-4 py-2 text-sm text-slate-400 hover:text-white border border-slate-600 hover:border-slate-400 rounded-lg transition-colors"
                        >
                            Reject Optional
                        </button>
                        <button
                            @click="acceptAll"
                            class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg transition-colors shadow-lg shadow-indigo-500/20"
                        >
                            Accept All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const showBanner = ref(false);

const COOKIE_KEY = 'cv_maker_cookie_consent';

onMounted(() => {
    const consent = localStorage.getItem(COOKIE_KEY);
    if (!consent) {
        // Small delay so the banner slides in after page loads
        setTimeout(() => { showBanner.value = true; }, 1500);
    }
});

const acceptAll = () => {
    localStorage.setItem(COOKIE_KEY, JSON.stringify({
        essential: true,
        analytics: true,
        accepted_at: new Date().toISOString(),
        expires: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toISOString(),
    }));
    showBanner.value = false;
};

const rejectAll = () => {
    localStorage.setItem(COOKIE_KEY, JSON.stringify({
        essential: true,
        analytics: false,
        accepted_at: new Date().toISOString(),
        expires: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toISOString(),
    }));
    showBanner.value = false;
};
</script>
