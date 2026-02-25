<script setup>
/**
 * SkeletonLoader — animated pulse placeholder.
 * Props:
 *   type: 'card' | 'line' | 'avatar' | 'resume-card'
 *   count: number of repeated items
 */
const props = defineProps({
    type:  { type: String, default: 'card' },
    count: { type: Number, default: 1 },
});
</script>

<template>
    <div>
        <template v-for="i in count" :key="i">

            <!-- Resume card skeleton -->
            <div v-if="type === 'resume-card'"
                class="bg-slate-800/50 border border-white/10 rounded-2xl overflow-hidden animate-pulse mb-4">
                <!-- Preview area -->
                <div class="h-36 bg-slate-700/50"></div>
                <div class="p-4 space-y-2">
                    <div class="h-4 bg-slate-700 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-700/60 rounded w-1/2"></div>
                    <div class="flex gap-2 mt-3">
                        <div class="h-7 bg-slate-700 rounded-lg w-16"></div>
                        <div class="h-7 bg-slate-700 rounded-lg w-16"></div>
                    </div>
                </div>
            </div>

            <!-- Generic card skeleton -->
            <div v-else-if="type === 'card'"
                class="bg-slate-800/50 border border-white/10 rounded-2xl p-5 animate-pulse mb-4 space-y-3">
                <div class="h-5 bg-slate-700 rounded w-2/3"></div>
                <div class="h-3 bg-slate-700/60 rounded w-full"></div>
                <div class="h-3 bg-slate-700/60 rounded w-5/6"></div>
                <div class="h-3 bg-slate-700/60 rounded w-4/6"></div>
            </div>

            <!-- Line skeleton -->
            <div v-else-if="type === 'line'"
                class="h-4 bg-slate-700/50 rounded animate-pulse mb-2"></div>

            <!-- Avatar skeleton -->
            <div v-else-if="type === 'avatar'"
                class="w-10 h-10 bg-slate-700 rounded-full animate-pulse"></div>

        </template>
    </div>
</template>
