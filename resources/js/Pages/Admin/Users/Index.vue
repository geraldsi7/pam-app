<script setup>
import { reactive, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/Admin/PageHeader.vue';
import TableShell from '@/Components/Admin/TableShell.vue';
import StatusBadge from '@/Components/Admin/StatusBadge.vue';

const props = defineProps({
    users: Object,
    filters: Object,
    registrationOptions: Array,
    userTypes: Array,
});

const state = reactive({
    search: props.filters.search ?? '',
    user_type: props.filters.user_type ?? '',
    is_active: props.filters.is_active ?? '',
});

const editingUser = ref(null);

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    user_type: 'member',
    is_active: true,
    registration_id: '',
});

const editForm = useForm({
    name: '',
    email: '',
    password: '',
    user_type: 'member',
    is_active: true,
    registration_id: '',
});

const applyFilters = () => {
    router.get(route('admin.users.index'), state, { preserveState: true, preserveScroll: true, replace: true });
};

const submitCreate = () => {
    createForm.post(route('admin.users.store'), {
        preserveScroll: true,
        onSuccess: () => createForm.reset('name', 'email', 'password', 'registration_id'),
    });
};

const startEdit = (user) => {
    editingUser.value = user;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.password = '';
    editForm.user_type = user.user_type || 'member';
    editForm.is_active = !!user.is_active;
    editForm.registration_id = user.registration_id || '';
};

const submitEdit = () => {
    editForm.put(route('admin.users.update', editingUser.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            editForm.reset();
            editingUser.value = null;
        },
    });
};

const toggleUser = (user) => {
    router.patch(route('admin.users.toggle', user.id), {}, { preserveScroll: true });
};

const removeUser = (user) => {
    if (!window.confirm(`Delete ${user.email}?`)) {
        return;
    }

    router.delete(route('admin.users.destroy', user.id), { preserveScroll: true });
};
</script>

<template>
    <AdminLayout title="User Management">
        <PageHeader
            title="User Management"
            description="Control account lifecycle, account types, and registration linkage."
        />

        <section class="grid gap-6 xl:grid-cols-3">
            <div class="admin-card xl:col-span-2">
                <div class="mb-3 grid gap-3 md:grid-cols-4">
                    <input v-model="state.search" type="text" class="admin-input" placeholder="Search name or email" @keyup.enter="applyFilters" />
                    <select v-model="state.user_type" class="admin-input">
                        <option value="">All types</option>
                        <option v-for="type in userTypes" :key="type" :value="type">{{ type }}</option>
                    </select>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Type / Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Registration</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900">{{ user.name }}</p>
                                <p class="text-xs text-slate-500">{{ user.email }}</p>
                            </td>
                            <td class="px-4 py-3 space-y-1">
                                <StatusBadge :value="user.user_type || 'member'" />
                                <StatusBadge :value="user.is_active ? 'approved' : 'inactive'" />
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-slate-600">{{ user.registration?.reference_code || '-' }}</p>
                                <p class="text-xs text-slate-500">{{ user.registration?.status || '' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button @click="startEdit(user)" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Edit</button>
                                    <button @click="toggleUser(user)" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ user.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button @click="removeUser(user)" class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.data.length">
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No users found.</td>
                        </tr>
                    </tbody>
                </TableShell>
            </div>

            <div class="space-y-6">
                <form class="admin-card space-y-3" @submit.prevent="submitCreate">
                    <h2 class="text-lg font-semibold text-slate-900">Create User</h2>
                    <input v-model="createForm.name" class="admin-input w-full" placeholder="Full name" />
                    <input v-model="createForm.email" type="email" class="admin-input w-full" placeholder="Email address" />
                    <input v-model="createForm.password" type="password" class="admin-input w-full" placeholder="Password (optional)" />
                    <select v-model="createForm.user_type" class="admin-input w-full">
                        <option v-for="type in userTypes" :key="type" :value="type">{{ type }}</option>
                    </select>
                    <select v-model="createForm.registration_id" class="admin-input w-full">
                        <option value="">No registration linkage</option>
                        <option v-for="registration in registrationOptions" :key="registration.id" :value="registration.id">
                            {{ registration.reference_code }}
                        </option>
                    </select>
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input v-model="createForm.is_active" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                        Active
                    </label>
                    <button class="w-full rounded-lg bg-admin-accent px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Create User</button>
                </form>

                <form v-if="editingUser" class="admin-card space-y-3" @submit.prevent="submitEdit">
                    <h2 class="text-lg font-semibold text-slate-900">Edit User</h2>
                    <input v-model="editForm.name" class="admin-input w-full" />
                    <input v-model="editForm.email" type="email" class="admin-input w-full" />
                    <input v-model="editForm.password" type="password" class="admin-input w-full" placeholder="Leave blank to keep current password" />
                    <select v-model="editForm.user_type" class="admin-input w-full">
                        <option v-for="type in userTypes" :key="type" :value="type">{{ type }}</option>
                    </select>
                    <select v-model="editForm.registration_id" class="admin-input w-full">
                        <option value="">No registration linkage</option>
                        <option v-for="registration in registrationOptions" :key="registration.id" :value="registration.id">
                            {{ registration.reference_code }}
                        </option>
                    </select>
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input v-model="editForm.is_active" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                        Active
                    </label>
                    <div class="flex gap-2">
                        <button class="flex-1 rounded-lg bg-admin-accent px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
                        <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" @click="editingUser = null">Cancel</button>
                    </div>
                </form>
            </div>
        </section>
    </AdminLayout>
</template>
