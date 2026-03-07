<script setup>
defineProps(['value', 'required']);
</script>

<template>
    <label class="block text-sm capitalize font-medium text-gray-700">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span class="text-red-600 dark:text-red-400" v-if="required">*</span>
    </label>
</template>
