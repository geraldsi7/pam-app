<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/Admin/PageHeader.vue';
import StatusBadge from '@/Components/Admin/StatusBadge.vue';

const props = defineProps({
    registration: Object,
});

const decide = (action) => {
    const notes = window.prompt(`Add ${action} note (optional):`) ?? '';
    router.post(route(`admin.registrations.${action}`, props.registration.id), { notes }, { preserveScroll: true });
};
</script>

<template>
    <AdminLayout :title="`Registration ${registration.reference_code}`">
        <PageHeader :title="`Registration ${registration.reference_code}`" description="Detailed registration, payment, and attendee timeline.">
            <template #actions>
                <button @click="decide('approve')" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Approve</button>
                <button @click="decide('reject')" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500">Reject</button>
                <Link :href="route('admin.registrations.index')" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Back</Link>
            </template>
        </PageHeader>

        <section class="grid gap-6 xl:grid-cols-3">
            <div class="admin-card xl:col-span-2">
                <h2 class="text-lg font-semibold text-slate-900">Registration Overview</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="admin-label">Contact Email</p>
                        <p class="mt-1 text-sm text-slate-900">{{ registration.email }}</p>
                    </div>
                    <div>
                        <p class="admin-label">Company</p>
                        <p class="mt-1 text-sm text-slate-900">{{ registration.business?.company_name || '-' }}</p>
                    </div>
                    <div>
                        <p class="admin-label">Registration Status</p>
                        <div class="mt-1"><StatusBadge :value="registration.status" /></div>
                    </div>
                    <div>
                        <p class="admin-label">Review Status</p>
                        <div class="mt-1"><StatusBadge :value="registration.admin_review_status" /></div>
                    </div>
                    <div>
                        <p class="admin-label">Payment Method</p>
                        <p class="mt-1 text-sm text-slate-900">{{ registration.payment_method || '-' }}</p>
                    </div>
                    <div>
                        <p class="admin-label">Agent</p>
                        <p class="mt-1 text-sm text-slate-900">{{ registration.agent?.name || 'None' }}</p>
                    </div>
                </div>
                <div v-if="registration.admin_review_notes" class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="admin-label">Review Notes</p>
                    <p class="mt-1 text-sm text-slate-700">{{ registration.admin_review_notes }}</p>
                </div>
            </div>

            <div class="admin-card">
                <h2 class="text-lg font-semibold text-slate-900">Attendees</h2>
                <div class="mt-3 space-y-2">
                    <div
                        v-for="attendee in registration.business?.attendees || []"
                        :key="attendee.id"
                        class="rounded-lg border border-slate-200 p-3"
                    >
                        <p class="text-sm font-semibold text-slate-900">{{ attendee.first_name }} {{ attendee.last_name }}</p>
                        <p class="text-xs text-slate-500">{{ attendee.email }}</p>
                    </div>
                    <p v-if="!(registration.business?.attendees || []).length" class="text-sm text-slate-500">No attendee records yet.</p>
                </div>
            </div>
        </section>

        <section class="admin-card">
            <h2 class="text-lg font-semibold text-slate-900">Payment Timeline</h2>
            <div class="mt-4 space-y-3">
                <div
                    v-for="payment in registration.payments"
                    :key="payment.id"
                    class="rounded-xl border border-slate-200 p-4"
                >
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ payment.method }}</p>
                            <p class="text-xs text-slate-500">{{ payment.transaction_reference || '-' }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <StatusBadge :value="payment.status" />
                            <p class="text-sm font-semibold text-slate-900">${{ Number(payment.amount).toLocaleString() }}</p>
                        </div>
                    </div>
                </div>
                <p v-if="!registration.payments.length" class="text-sm text-slate-500">No payments captured for this registration.</p>
            </div>
        </section>
    </AdminLayout>
</template>
