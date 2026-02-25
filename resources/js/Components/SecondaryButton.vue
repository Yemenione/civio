<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    isLaser: {
        type: Boolean,
        default: true
    }
});
</script>

<template>
    <div v-if="isLaser" class="laser-btn-wrapper laser-silver">
        <button
            :type="type"
            class="laser-btn-content px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-300 transition-all duration-300 hover:text-white hover:bg-slate-800 focus:outline-none"
        >
            <slot />
        </button>
    </div>
    <button
        v-else
        :type="type"
        class="inline-flex items-center rounded-full border border-gray-300 bg-white px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
    >
        <slot />
    </button>
</template>
