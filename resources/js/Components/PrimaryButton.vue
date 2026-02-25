<script setup>
defineProps({
    isLaser: {
        type: Boolean,
        default: true
    },
    shimmer: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <div v-if="isLaser" class="laser-btn-wrapper">
        <button
            class="laser-btn-content inline-flex items-center px-6 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-white transition-all duration-300 hover:bg-slate-800 focus:outline-none active:scale-95"
            :class="{ 'shimmer-effect': shimmer }"
        >
            <slot />
        </button>
    </div>
    <button
        v-else
        class="inline-flex items-center rounded-full border border-transparent bg-indigo-600 px-5 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-white transition duration-150 ease-in-out hover:bg-indigo-500 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-900 dark:bg-indigo-500 dark:hover:bg-indigo-400 shadow-lg shadow-indigo-500/30"
        :class="{ 'shimmer-effect': shimmer }"
    >
        <slot />
    </button>
</template>
