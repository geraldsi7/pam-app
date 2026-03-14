<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Admin Portal',
    },
});

const page = usePage();

const navItems = computed(() => [
    { label: 'Dashboard', route: 'admin.dashboard', active: route().current('admin.dashboard*') },
    { label: 'Registrations', route: 'admin.registrations.index', active: route().current('admin.registrations.*') },
    { label: 'Payments', route: 'admin.payments.index', active: route().current('admin.payments.*') },
    { label: 'Agents', route: 'admin.agents.index', active: route().current('admin.agents.*') },
    { label: 'Users', route: 'admin.users.index', active: route().current('admin.users.*') },
    { label: 'Activity', route: 'admin.activity.index', active: route().current('admin.activity.*') },
]);
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100">
        <div class="mx-auto flex w-full max-w-[1600px] gap-6 px-4 py-6 md:px-6">
            <aside class="hidden w-64 shrink-0 lg:block">
                <div class="admin-surface sticky top-6 p-4 shadow-panel">
                    <div class="mb-6 border-b border-slate-200 pb-4">
                        <p class="admin-label">Enterprise Console</p>
                        <h2 class="mt-1 text-lg font-semibold text-admin-ink">PAM Admin</h2>
                    </div>

                    <nav class="space-y-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.route"
                            :href="route(item.route)"
                            :class="[
                                'flex items-center rounded-xl px-3 py-2 text-sm font-medium transition',
                                item.active
                                    ? 'bg-indigo-50 text-indigo-700'
                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                            ]"
                        >
                            {{ item.label }}
                        </Link>
                    </nav>
                </div>
            </aside>

            <div class="min-w-0 flex-1">
                <header class="admin-surface mb-6 flex items-center justify-between px-4 py-3 md:px-5">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-admin-muted">Administration</p>
                        <p class="text-sm text-slate-700">High-control operations console</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900">{{ page.props.auth?.user?.name }}</p>
                            <p class="text-xs text-slate-500">{{ page.props.auth?.user?.email }}</p>
                        </div>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-700 transition hover:bg-slate-100"
                        >
                            Logout
                        </Link>
                    </div>
                </header>

                <main class="space-y-6">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
