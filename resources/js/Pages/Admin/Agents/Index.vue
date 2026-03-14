<script setup>
import { reactive, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/Admin/PageHeader.vue';
import TableShell from '@/Components/Admin/TableShell.vue';
import StatusBadge from '@/Components/Admin/StatusBadge.vue';

const props = defineProps({
    agents: Object,
    filters: Object,
});

const state = reactive({
    search: props.filters.search ?? '',
    is_active: props.filters.is_active ?? '',
});

const editingAgent = ref(null);

const createForm = useForm({
    name: '',
    referral_code: '',
    commission_rate: 0,
    is_active: true,
});

const editForm = useForm({
    name: '',
    referral_code: '',
    commission_rate: 0,
    is_active: true,
});

const applyFilters = () => {
    router.get(route('admin.agents.index'), state, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const submitCreate = () => {
    createForm.post(route('admin.agents.store'), {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
};

const startEdit = (agent) => {
    editingAgent.value = agent;
    editForm.name = agent.name;
    editForm.referral_code = agent.referral_code;
    editForm.commission_rate = agent.commission_rate;
    editForm.is_active = !!agent.is_active;
};

const submitEdit = () => {
    editForm.put(route('admin.agents.update', editingAgent.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            editingAgent.value = null;
            editForm.reset();
        },
    });
};

const toggleAgent = (agent) => {
    router.patch(route('admin.agents.toggle', agent.id), {}, { preserveScroll: true });
};
</script>

<template>
    <AdminLayout title="Agent Management">
        <PageHeader
            title="Agent Management"
            description="Manage referral agents, activation states, and downstream registration performance."
        />

        <section class="grid gap-6 xl:grid-cols-3">
            <div class="admin-card xl:col-span-2">
                <div class="mb-3 grid gap-3 md:grid-cols-3">
                    <input v-model="state.search" type="text" placeholder="Search agent or code..." class="admin-input" @keyup.enter="applyFilters" />
                    <select v-model="state.is_active" class="admin-input">
                        <option value="">All statuses</option>
                        <option value="true">active</option>
                        <option value="false">inactive</option>
                    </select>
                    <button @click="applyFilters" class="rounded-lg bg-admin-accent px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Filter</button>
                </div>

                <TableShell>
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Agent</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Referral</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Performance</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="agent in agents.data" :key="agent.id">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900">{{ agent.name }}</p>
                                <StatusBadge :value="agent.is_active ? 'approved' : 'inactive'" />
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-mono text-xs text-slate-700">{{ agent.referral_code }}</p>
                                <p class="text-xs text-slate-500">{{ agent.commission_rate }}% commission</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                <p>Total registrations: {{ agent.registrations_count }}</p>
                                <p>Completed: {{ agent.completed_registrations_count }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button @click="startEdit(agent)" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Edit</button>
                                    <button @click="toggleAgent(agent)" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ agent.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!agents.data.length">
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No agents found.</td>
                        </tr>
                    </tbody>
                </TableShell>
            </div>

            <div class="space-y-6">
                <form class="admin-card space-y-3" @submit.prevent="submitCreate">
                    <h2 class="text-lg font-semibold text-slate-900">Create Agent</h2>
                    <input v-model="createForm.name" class="admin-input w-full" placeholder="Agent name" />
                    <input v-model="createForm.referral_code" class="admin-input w-full" placeholder="Referral code" />
                    <input v-model="createForm.commission_rate" type="number" step="0.01" class="admin-input w-full" placeholder="Commission rate" />
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input v-model="createForm.is_active" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                        Active
                    </label>
                    <button class="w-full rounded-lg bg-admin-accent px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Create Agent</button>
                </form>

                <form v-if="editingAgent" class="admin-card space-y-3" @submit.prevent="submitEdit">
                    <h2 class="text-lg font-semibold text-slate-900">Edit Agent</h2>
                    <input v-model="editForm.name" class="admin-input w-full" />
                    <input v-model="editForm.referral_code" class="admin-input w-full" />
                    <input v-model="editForm.commission_rate" type="number" step="0.01" class="admin-input w-full" />
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input v-model="editForm.is_active" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                        Active
                    </label>
                    <div class="flex gap-2">
                        <button class="flex-1 rounded-lg bg-admin-accent px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
                        <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" @click="editingAgent = null">Cancel</button>
                    </div>
                </form>
            </div>
        </section>
    </AdminLayout>
</template>
