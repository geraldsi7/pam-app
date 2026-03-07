<template>
    <AuthenticatedLayout>
        <Head :title="business.company_name + ' - Business Profile'" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Back Button -->
                <div class="mb-6">
                    <Link
                        href="/matchmaking"
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Directory
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Header -->
                    <div class="px-6 py-4 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ business.company_name }}</h1>
                                    <p class="text-gray-600">{{ business.country }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span
                                    :class="[
                                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                        business.attendance_mode === 'in_person'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-blue-100 text-blue-800'
                                    ]"
                                >
                                    {{ business.attendance_mode === 'in_person' ? 'In Person' : 'Online' }}
                                </span>
                                <span
                                    :class="[
                                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                        business.can_view_details
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-orange-100 text-orange-800'
                                    ]"
                                >
                                    {{ business.can_view_details ? 'Connected' : 'Request Access' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message for Access Denied -->
                    <div v-if="!business.can_view_details" class="px-6 py-8 text-center">
                        <div class="bg-orange-50 border border-orange-200 rounded-md p-6">
                            <svg class="mx-auto h-12 w-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-orange-800">Access Restricted</h3>
                            <div class="mt-1 text-sm text-orange-700">
                                <p v-if="business.has_pending_request">
                                    Your access request is pending approval from {{ business.company_name }}.
                                </p>
                                <p v-else>
                                    To view detailed contact information, you need to request access. Once approved, you'll be able to see emails, phone numbers, and attendee details.
                                </p>
                            </div>
                            <div class="mt-6">
                                <button
                                    v-if="!business.has_pending_request"
                                    @click="requestAccess"
                                    :disabled="loading"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Request Access
                                </button>
                                <span
                                    v-else
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-orange-700 bg-orange-100 rounded-md"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Request Pending
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Business Details -->
                    <div v-else class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Company Information -->
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 mb-6">Company Information</h2>

                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ business.company_name }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Country</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ business.country }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Business Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ business.origin }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Company Size</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ business.company_size || 'Not specified' }}</dd>
                                    </div>

                                    <div v-if="business.website">
                                        <dt class="text-sm font-medium text-gray-500">Website</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <a :href="business.website" target="_blank" class="text-indigo-600 hover:text-indigo-500">
                                                {{ business.website }}
                                            </a>
                                        </dd>
                                    </div>

                                    <div v-if="business.street_address">
                                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ business.street_address }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Industries</dt>
                                        <dd class="mt-1">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    v-for="industry in business.industries"
                                                    :key="industry"
                                                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800"
                                                >
                                                    {{ industry }}
                                                </span>
                                            </div>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Contact Information -->
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 mb-6">Contact Information</h2>

                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <a :href="'mailto:' + business.email" class="text-indigo-600 hover:text-indigo-500">
                                                {{ business.email }}
                                            </a>
                                        </dd>
                                    </div>

                                    <div v-if="business.phone">
                                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <a :href="'tel:' + business.phone" class="text-indigo-600 hover:text-indigo-500">
                                                {{ business.phone }}
                                            </a>
                                        </dd>
                                    </div>
                                </dl>

                                <!-- Attendees -->
                                <div class="mt-8">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Attendees</h3>
                                    <div class="space-y-4">
                                        <div
                                            v-for="attendee in business.attendees"
                                            :key="attendee.email"
                                            class="bg-gray-50 rounded-lg p-4"
                                        >
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-900">
                                                        {{ attendee.first_name }} {{ attendee.last_name }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600">{{ attendee.designation }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-2 space-y-1">
                                                <p class="text-sm text-gray-600">
                                                    <a :href="'mailto:' + attendee.email" class="text-indigo-600 hover:text-indigo-500">
                                                        {{ attendee.email }}
                                                    </a>
                                                </p>
                                                <p v-if="attendee.phone" class="text-sm text-gray-600">
                                                    <a :href="'tel:' + attendee.phone" class="text-indigo-600 hover:text-indigo-500">
                                                        {{ attendee.phone }}
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    business: Object,
});

const loading = ref(false);

const requestAccess = async () => {
    loading.value = true;
    try {
        const response = await fetch(route('matchmaking.request', props.business.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });

        const data = await response.json();

        if (response.ok) {
            // Refresh the page to show updated status
            router.reload();
        } else {
            $page.props.flash = { error: data.error };
        }
    } catch (error) {
        $page.props.flash = { error: 'An error occurred while sending the request.' };
    } finally {
        loading.value = false;
    }
};
</script>