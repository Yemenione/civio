<script setup>
import Edit from './Edit.vue';

const props = defineProps({
    resumes: Array,
});
</script>

<template>
    <Edit :resumes="resumes" />
</template>
