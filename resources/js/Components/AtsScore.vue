<script setup>
/**
 * AtsScore — displays an ATS compatibility score (0-100).
 * Props:
 *   score: 0-100 (required)
 *   size: 'sm' | 'md' | 'lg'
 */
import { computed } from 'vue';

const props = defineProps({
    score: { type: Number, required: true },
    size:  { type: String,  default: 'md' },
});

const color = computed(() => {
    if (props.score >= 80) return { ring: '#22c55e', text: 'text-green-400',  label: 'Excellent', bg: 'bg-green-500/10' };
    if (props.score >= 60) return { ring: '#f59e0b', text: 'text-amber-400',  label: 'Good',      bg: 'bg-amber-500/10' };
    if (props.score >= 40) return { ring: '#f97316', text: 'text-orange-400', label: 'Fair',      bg: 'bg-orange-500/10' };
    return                        { ring: '#ef4444', text: 'text-red-400',    label: 'Needs Work', bg: 'bg-red-500/10' };
});

const radius   = 40;
const circumference = 2 * Math.PI * radius;
const dashOffset = computed(() => circumference * (1 - props.score / 100));

const sizes = {
    sm: { svg: 80,  r: 28, stroke: 6,  textSize: 'text-sm',  labelSize: 'text-xs' },
    md: { svg: 120, r: 40, stroke: 8,  textSize: 'text-xl',  labelSize: 'text-xs' },
    lg: { svg: 160, r: 60, stroke: 10, textSize: 'text-2xl', labelSize: 'text-sm' },
};

const dim = computed(() => sizes[props.size] ?? sizes.md);
const cx  = computed(() => dim.value.svg / 2);
const circ = computed(() => 2 * Math.PI * dim.value.r);
const offset = computed(()=> circ.value * (1 - props.score / 100));
</script>

<template>
    <div class="flex flex-col items-center gap-2">
        <div :class="['rounded-full p-2', color.bg]">
            <svg :width="dim.svg" :height="dim.svg" viewBox="0 0 120 120">
                <!-- Background track -->
                <circle
                    :cx="cx"
                    cy="60"
                    :r="dim.r"
                    fill="none"
                    stroke="rgba(255,255,255,0.08)"
                    :stroke-width="dim.stroke"
                />
                <!-- Progress arc -->
                <circle
                    :cx="cx"
                    cy="60"
                    :r="dim.r"
                    fill="none"
                    :stroke="color.ring"
                    :stroke-width="dim.stroke"
                    stroke-linecap="round"
                    :stroke-dasharray="circ"
                    :stroke-dashoffset="offset"
                    transform="rotate(-90 60 60)"
                    style="transition: stroke-dashoffset 1s ease;"
                />
                <!-- Score text -->
                <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
                    :class="[dim.textSize, 'font-bold fill-white']"
                    style="font-family: system-ui, sans-serif;">
                    {{ score }}
                </text>
            </svg>
        </div>
        <div class="text-center">
            <p :class="[color.text, dim.labelSize, 'font-semibold']">{{ color.label }}</p>
            <p class="text-xs text-slate-500 mt-0.5">ATS Score</p>
        </div>
    </div>
</template>
