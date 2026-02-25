<script setup>
/**
 * Toast — global notification toasts.
 * Usage: import { useToast } from '@/composables/useToast'
 * const toast = useToast();
 * toast.success('Saved!');
 * toast.error('Something went wrong.');
 */
import { ref, onUnmounted } from 'vue';

const toasts = ref([]);
let nextId = 0;

const add = (message, type = 'success', duration = 3500) => {
    const id = ++nextId;
    toasts.value.push({ id, message, type });
    setTimeout(() => remove(id), duration);
};

const remove = (id) => {
    toasts.value = toasts.value.filter(t => t.id !== id);
};

defineExpose({ add, remove });
</script>

<template>
    <Teleport to="body">
        <div class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2.5 items-end pointer-events-none">
            <TransitionGroup name="toast">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="[
                        'flex items-start gap-3 px-4 py-3.5 rounded-xl shadow-2xl shadow-black/30 text-sm font-medium max-w-xs pointer-events-auto cursor-pointer select-none',
                        toast.type === 'success' ? 'bg-slate-800 border border-green-500/30 text-green-300' : '',
                        toast.type === 'error'   ? 'bg-slate-800 border border-red-500/30 text-red-300'   : '',
                        toast.type === 'info'    ? 'bg-slate-800 border border-indigo-500/30 text-indigo-300' : '',
                        toast.type === 'warning' ? 'bg-slate-800 border border-amber-500/30 text-amber-300' : '',
                    ]"
                    @click="remove(toast.id)"
                >
                    <span class="text-base leading-none mt-0.5">
                        {{ toast.type === 'success' ? '✓' : toast.type === 'error' ? '✕' : toast.type === 'warning' ? '⚠' : 'ℹ' }}
                    </span>
                    <span>{{ toast.message }}</span>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
.toast-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.toast-leave-active { transition: all 0.25s ease; }
.toast-enter-from  { opacity: 0; transform: translateX(2rem) scale(0.9); }
.toast-leave-to    { opacity: 0; transform: translateX(1rem); }
</style>
