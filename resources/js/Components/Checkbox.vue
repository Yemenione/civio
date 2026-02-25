<template>
    <input
        type="checkbox"
        :id="id"
        v-model="proxyChecked"
        class="rounded border-slate-600 bg-slate-800 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-slate-900 cursor-pointer"
    />
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: null,
    },
    id: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:checked']);

const proxyChecked = computed({
    get() {
        return props.checked;
    },
    set(val) {
        emit('update:checked', val);
    },
});
</script>
