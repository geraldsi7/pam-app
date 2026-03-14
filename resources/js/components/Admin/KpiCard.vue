<script setup>
defineProps({
    title: String,
    value: [String, Number],
    subtitle: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="admin-card">
        <p class="admin-label">{{ title }}</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-admin-ink">{{ value }}</p>
        <p v-if="subtitle" class="mt-1 text-sm text-admin-muted">{{ subtitle }}</p>
    </div>
</template>
