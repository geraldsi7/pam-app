<script setup>
import { onMounted, ref } from 'vue';

defineProps(['modelValue']);

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <select
        class="text-sm rounded-sm border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-purple-600 dark:focus:ring-purple-600 disabled:cursor-not-allowed disabled:bg-gray-200"
        :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input">
        <slot />
    </select>
</template>
