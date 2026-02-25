<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="relative z-10 rounded-xl border border-white/10 bg-white/5 text-white placeholder-slate-500 shadow-xl backdrop-blur-md focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 w-full px-4 py-3 text-sm font-medium"
        v-model="model"
        ref="input"
    />
</template>
