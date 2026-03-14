<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/Admin/PageHeader.vue';
import TableShell from '@/Components/Admin/TableShell.vue';

const props = defineProps({
    activity: Object,
    filters: Object,
});

const state = reactive({
    action: props.filters.action ?? '',
});

const applyFilters = () => {
    router.get(route('admin.activity.index'), state, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};
</script>

<template>
    <AdminLayout title="Activity Log">
        <PageHeader
            title="Operational Activity Log"
            description="Chronological record of administrative actions for traceability."
        />

        <section class="admin-card flex gap-3">
            <input v-model="state.action" type="text" class="admin-input w-full max-w-xs" placeholder="Filter by action key" @keyup.enter="applyFilters" />
            <button @click="applyFilters" class="rounded-lg bg-admin-accent px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Apply</button>
        </section>

        <TableShell>
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">When</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Actor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Action</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="item in activity.data" :key="item.id">
                    <td class="px-4 py-3 text-xs text-slate-600">{{ new Date(item.created_at).toLocaleString() }}</td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-slate-900">{{ item.actor?.name || 'System' }}</p>
                        <p class="text-xs text-slate-500">{{ item.actor?.email || '-' }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ item.action }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ item.description || '-' }}</td>
                </tr>
                <tr v-if="!activity.data.length">
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No activity logs available.</td>
                </tr>
            </tbody>
        </TableShell>
    </AdminLayout>
</template>
