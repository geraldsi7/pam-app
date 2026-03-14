<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/Admin/PageHeader.vue';
import StatusBadge from '@/Components/Admin/StatusBadge.vue';
import TableShell from '@/Components/Admin/TableShell.vue';

const props = defineProps({
    payments: Object,
    filters: Object,
});

const state = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    method: props.filters.method ?? '',
});

const applyFilters = () => {
    router.get(route('admin.payments.index'), state, { preserveState: true, preserveScroll: true, replace: true });
};

const decide = (payment, action) => {
    const notes = window.prompt(`Add ${action} note (optional):`) ?? '';
    router.post(route(`admin.payments.${action}`, payment.id), { notes }, { preserveScroll: true });
};
</script>

<template>
    <AdminLayout title="Payment Operations">
        <PageHeader
            title="Payment Operations"
            description="Verify, reconcile, and resolve payment submissions with enterprise-grade controls."
        />

        <section class="admin-card">
            <div class="grid gap-3 md:grid-cols-4">
                <input v-model="state.search" type="text" class="admin-input" placeholder="Search transaction or ref" @keyup.enter="applyFilters" />
                <select v-model="state.method" class="admin-input">
                    <option value="">All methods</option>
                    <option value="paystack">paystack</option>
                    <option value="bank_deposit">bank_deposit</option>
                </select>
                <select v-model="state.status" class="admin-input">
                    <option value="">All statuses</option>
                    <option value="pending">pending</option>
                    <option value="success">success</option>
                    <option value="failed">failed</option>
                </select>
                <button @click="applyFilters" class="rounded-lg bg-admin-accent px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Filter</button>
            </div>
        </section>

        <TableShell>
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Transaction</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Registration</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Payment</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="payment in payments.data" :key="payment.id">
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-900">{{ payment.transaction_reference || payment.id }}</p>
                        <p class="text-xs text-slate-500">{{ new Date(payment.created_at).toLocaleString() }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-900">{{ payment.registration?.business?.company_name || payment.registration?.email }}</p>
                        <p class="text-xs text-slate-500">{{ payment.registration?.reference_code }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="space-y-1">
                            <StatusBadge :value="payment.method" />
                            <StatusBadge :value="payment.status" />
                            <p class="text-xs font-semibold text-slate-900">${{ Number(payment.amount).toLocaleString() }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <button
                                @click="decide(payment, 'verify')"
                                class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500"
                            >
                                Verify
                            </button>
                            <button
                                @click="decide(payment, 'reject')"
                                class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500"
                            >
                                Reject
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!payments.data.length">
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No payments found.</td>
                </tr>
            </tbody>
        </TableShell>
    </AdminLayout>
</template>
