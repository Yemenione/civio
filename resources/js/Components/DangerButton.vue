<script setup>
defineProps({
    isLaser: {
        type: Boolean,
        default: true
    }
});
</script>

<template>
    <div v-if="isLaser" class="laser-btn-wrapper laser-red">
        <button
            class="laser-btn-content px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-red-400 transition-all duration-300 hover:text-red-300 hover:bg-slate-800 focus:outline-none"
        >
            <slot />
        </button>
    </div>
    <button
        v-else
        class="inline-flex items-center rounded-full border border-transparent bg-red-600 px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none active:bg-red-700"
    >
        <slot />
    </button>
</template>
