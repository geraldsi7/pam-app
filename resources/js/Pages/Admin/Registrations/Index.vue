<script setup>
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/Admin/PageHeader.vue';
import StatusBadge from '@/Components/Admin/StatusBadge.vue';
import TableShell from '@/Components/Admin/TableShell.vue';

const props = defineProps({
    registrations: Object,
    filters: Object,
});

const state = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    review_status: props.filters.review_status ?? '',
    mode: props.filters.mode ?? '',
    payment_method: props.filters.payment_method ?? '',
});

const applyFilters = () => {
    router.get(route('admin.registrations.index'), state, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilters = () => {
    state.search = '';
    state.status = '';
    state.review_status = '';
    state.mode = '';
    state.payment_method = '';
    applyFilters();
};

const decide = (registration, action) => {
    const notes = window.prompt(`Add ${action} note (optional):`) ?? '';
    router.post(route(`admin.registrations.${action}`, registration.id), { notes }, { preserveScroll: true });
};
</script>

<template>
    <AdminLayout title="Registration Operations">
        <PageHeader
            title="Registration Operations"
            description="Review registration records, inspect operational states, and process approvals or rejections."
        />

        <section class="admin-card">
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                <input v-model="state.search" type="text" placeholder="Search ref, email, company..." class="admin-input" @keyup.enter="applyFilters" />
                <select v-model="state.status" class="admin-input">
                    <option value="">All statuses</option>
                    <option value="pending">pending</option>
                    <option value="payment_pending">payment_pending</option>
                    <option value="completed">completed</option>
                    <option value="cancelled">cancelled</option>
                </select>
                <select v-model="state.review_status" class="admin-input">
                    <option value="">All reviews</option>
                    <option value="pending">pending</option>
                    <option value="approved">approved</option>
                    <option value="rejected">rejected</option>
                </select>
                <select v-model="state.mode" class="admin-input">
                    <option value="">All modes</option>
                    <option value="in_person">in_person</option>
                    <option value="online">online</option>
                </select>
                <select v-model="state.payment_method" class="admin-input">
                    <option value="">All payment methods</option>
                    <option value="paystack">paystack</option>
                    <option value="bank_deposit">bank_deposit</option>
                </select>
            </div>
            <div class="mt-3 flex gap-2">
                <button @click="applyFilters" class="rounded-lg bg-admin-accent px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Apply</button>
                <button @click="resetFilters" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Reset</button>
            </div>
        </section>

        <TableShell>
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Reference</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Company / Contact</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Flow</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Review</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="registration in registrations.data" :key="registration.id" class="hover:bg-slate-50/70">
                    <td class="px-4 py-3 align-top">
                        <p class="font-medium text-slate-900">{{ registration.reference_code }}</p>
                        <p class="text-xs text-slate-500">{{ new Date(registration.created_at).toLocaleDateString() }}</p>
                    </td>
                    <td class="px-4 py-3 align-top">
                        <p class="font-medium text-slate-900">{{ registration.business?.company_name || '-' }}</p>
                        <p class="text-xs text-slate-500">{{ registration.email }}</p>
                    </td>
                    <td class="px-4 py-3 align-top space-y-1">
                        <StatusBadge :value="registration.status" />
                        <p class="text-xs text-slate-500">{{ registration.payment_method || 'not selected' }}</p>
                    </td>
                    <td class="px-4 py-3 align-top">
                        <StatusBadge :value="registration.admin_review_status" />
                    </td>
                    <td class="px-4 py-3 align-top">
                        <div class="flex flex-wrap gap-2">
                            <Link :href="route('admin.registrations.show', registration.id)" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                View
                            </Link>
                            <button
                                @click="decide(registration, 'approve')"
                                class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500"
                            >
                                Approve
                            </button>
                            <button
                                @click="decide(registration, 'reject')"
                                class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500"
                            >
                                Reject
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!registrations.data.length">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No registrations matched this filter.</td>
                </tr>
            </tbody>
        </TableShell>

        <section class="admin-card flex flex-wrap items-center gap-2">
            <template v-for="link in registrations.links" :key="link.label">
                <button
                    v-if="link.url"
                    @click="router.visit(link.url, { preserveScroll: true, preserveState: true })"
                    :class="[
                        'rounded-lg border px-3 py-1.5 text-sm',
                        link.active ? 'border-indigo-300 bg-indigo-50 text-indigo-700' : 'border-slate-300 text-slate-600 hover:bg-slate-100',
                    ]"
                    v-html="link.label"
                />
                <span v-else class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-400" v-html="link.label" />
            </template>
        </section>
    </AdminLayout>
</template>
