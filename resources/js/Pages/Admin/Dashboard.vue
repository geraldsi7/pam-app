<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import KpiCard from '@/Components/Admin/KpiCard.vue';
import PageHeader from '@/Components/Admin/PageHeader.vue';
import StatusBadge from '@/Components/Admin/StatusBadge.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    kpis: Object,
    pendingRegistrations: Array,
    pendingPayments: Array,
    recentActivity: Array,
});

const formatAmount = (amount) => Number(amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
</script>

<template>
    <AdminLayout title="Admin Dashboard">
        <PageHeader
            title="Operations Command Center"
            description="Track registration throughput, payment verification pressure, and operational actions."
        />

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <KpiCard title="Total Registrations" :value="kpis.registrations_total" />
            <KpiCard title="Pending Registrations" :value="kpis.registrations_pending" />
            <KpiCard title="Completed Registrations" :value="kpis.registrations_completed" />
            <KpiCard title="Payments Pending Verification" :value="kpis.payments_pending_verification" />
            <KpiCard title="Active Agents" :value="kpis.agents_total" />
            <KpiCard title="Users" :value="kpis.users_total" />
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="admin-card">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Pending Registrations</h2>
                    <Link :href="route('admin.registrations.index', { status: 'payment_pending' })" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        View all
                    </Link>
                </div>
                <div class="space-y-3">
                    <div
                        v-for="registration in pendingRegistrations"
                        :key="registration.id"
                        class="rounded-xl border border-slate-200 p-3"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ registration.business?.company_name || registration.email }}</p>
                                <p class="text-xs text-slate-500">{{ registration.reference_code }}</p>
                            </div>
                            <StatusBadge :value="registration.status" />
                        </div>
                    </div>
                    <p v-if="!pendingRegistrations.length" class="text-sm text-slate-500">No pending registrations right now.</p>
                </div>
            </div>

            <div class="admin-card">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Bank Verification Queue</h2>
                    <Link :href="route('admin.payments.index', { method: 'bank_deposit', status: 'pending' })" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Open queue
                    </Link>
                </div>
                <div class="space-y-3">
                    <div
                        v-for="payment in pendingPayments"
                        :key="payment.id"
                        class="rounded-xl border border-slate-200 p-3"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ payment.registration?.business?.company_name || payment.registration?.email }}</p>
                                <p class="text-xs text-slate-500">{{ payment.transaction_reference }}</p>
                            </div>
                            <div class="text-right">
                                <StatusBadge :value="payment.status" />
                                <p class="mt-1 text-xs text-slate-500">${{ formatAmount(payment.amount) }}</p>
                            </div>
                        </div>
                    </div>
                    <p v-if="!pendingPayments.length" class="text-sm text-slate-500">No pending bank deposit submissions.</p>
                </div>
            </div>
        </section>

        <section class="admin-card">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Recent Activity</h2>
                <Link :href="route('admin.activity.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Full log</Link>
            </div>
            <div class="space-y-3">
                <div
                    v-for="item in recentActivity"
                    :key="item.id"
                    class="rounded-xl border border-slate-200 p-3"
                >
                    <p class="text-sm text-slate-900">{{ item.description || item.action }}</p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ item.actor?.name || 'System' }} · {{ new Date(item.created_at).toLocaleString() }}
                    </p>
                </div>
                <p v-if="!recentActivity.length" class="text-sm text-slate-500">No activity has been recorded yet.</p>
            </div>
        </section>
    </AdminLayout>
</template>
