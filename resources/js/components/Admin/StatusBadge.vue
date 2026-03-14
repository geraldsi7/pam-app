<script setup>
const props = defineProps({
    value: {
        type: String,
        default: 'pending',
    },
});

const colorMap = {
    completed: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    approved: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    success: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    pending: 'bg-amber-50 text-amber-700 ring-amber-200',
    payment_pending: 'bg-amber-50 text-amber-700 ring-amber-200',
    rejected: 'bg-rose-50 text-rose-700 ring-rose-200',
    cancelled: 'bg-rose-50 text-rose-700 ring-rose-200',
    failed: 'bg-rose-50 text-rose-700 ring-rose-200',
    inactive: 'bg-slate-100 text-slate-700 ring-slate-200',
};

const label = props.value.replaceAll('_', ' ');
</script>

<template>
    <span
        :class="[
            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset',
            colorMap[value] ?? 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        ]"
    >
        {{ label }}
    </span>
</template>
