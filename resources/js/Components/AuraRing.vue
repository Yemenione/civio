<script setup>
import { computed } from 'vue';

const props = defineProps({
    score: {
        type: Number,
        default: 0
    },
    size: {
        type: Number,
        default: 120
    }
});

const radius = computed(() => (props.size / 2) - 8);
const circumference = computed(() => 2 * Math.PI * radius.value);
const offset = computed(() => circumference.value - (props.score / 100) * circumference.value);

const color = computed(() => {
    if (props.score < 40) return '#f43f5e'; // red
    if (props.score < 70) return '#fbbf24'; // amber
    return '#10b981'; // emerald
});
</script>

<template>
    <div class="relative flex items-center justify-center transition-all duration-700" :style="{ width: size + 'px', height: size + 'px' }">
        <!-- SVG Ring -->
        <svg :width="size" :height="size" class="transform -rotate-90 overflow-visible">
            <!-- Background circle -->
            <circle
                class="text-slate-200 dark:text-slate-800"
                stroke-width="6"
                stroke="currentColor"
                fill="transparent"
                :r="radius"
                :cx="size / 2"
                :cy="size / 2"
            />
            <!-- Progress circle -->
            <circle
                class="transition-all duration-1000 ease-out"
                stroke-width="8"
                :stroke-dasharray="circumference"
                :stroke-dashoffset="offset"
                stroke-linecap="round"
                :stroke="color"
                fill="transparent"
                :r="radius"
                :cx="size / 2"
                :cy="size / 2"
                :style="{ filter: `drop-shadow(0 0 6px ${color}80)` }"
            />
        </svg>

        <!-- Center Content -->
        <div class="absolute flex flex-col items-center justify-center text-center pointer-events-none">
            <span class="font-black text-slate-900 dark:text-white leading-none tracking-tighter" :style="{ fontSize: (size * 0.22) + 'px' }">{{ score }}%</span>
            <span class="uppercase tracking-[0.2em] text-slate-500 font-black mt-1" :style="{ fontSize: (size * 0.07) + 'px' }">Strength</span>
        </div>
        
        <!-- Animated Glow -->
        <div class="absolute inset-0 rounded-full blur-xl opacity-10 animate-pulse pointer-events-none" :style="{ backgroundColor: color }"></div>
    </div>
</template>
