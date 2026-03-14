<script setup>
defineProps({
    title: String,
    description: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-admin-ink md:text-3xl">{{ title }}</h1>
            <p v-if="description" class="mt-2 text-sm text-admin-muted">{{ description }}</p>
        </div>
        <div class="flex items-center gap-2">
            <slot name="actions" />
        </div>
    </div>
</template>
