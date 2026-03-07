<template>
    <AuthenticatedLayout>
        <Head title="Matchmaking Requests" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h1 class="ml-2 text-2xl font-medium text-gray-900">
                                Matchmaking Requests
                            </h1>
                        </div>

                        <p class="mt-6 text-gray-500 leading-relaxed">
                            Manage your access requests and respond to requests from other businesses.
                        </p>
                    </div>

                    <div class="p-6">
                        <!-- Tabs -->
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8">
                                <button
                                    @click="activeTab = 'received'"
                                    :class="[
                                        'py-2 px-1 border-b-2 font-medium text-sm',
                                        activeTab === 'received'
                                            ? 'border-indigo-500 text-indigo-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    ]"
                                >
                                    Incoming Requests ({{ receivedRequests.length }})
                                </button>
                                <button
                                    @click="activeTab = 'sent'"
                                    :class="[
                                        'py-2 px-1 border-b-2 font-medium text-sm',
                                        activeTab === 'sent'
                                            ? 'border-indigo-500 text-indigo-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    ]"
                                >
                                    Sent Requests ({{ sentRequests.length }})
                                </button>
                            </nav>
                        </div>

                        <!-- Received Requests Tab -->
                        <div v-if="activeTab === 'received'" class="mt-6">
                            <div v-if="receivedRequests.length === 0" class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-5v2m0 0v2m0-2h2m-2 0h-2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No incoming requests</h3>
                                <p class="mt-1 text-sm text-gray-500">When other businesses request access to your details, they'll appear here.</p>
                            </div>

                            <div v-else class="space-y-4">
                                <div
                                    v-for="request in receivedRequests"
                                    :key="request.id"
                                    class="bg-white border border-gray-200 rounded-lg p-4"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-900">
                                                    {{ request.business.company_name }}
                                                </h3>
                                                <p class="text-sm text-gray-500">
                                                    {{ request.business.country }} • {{ request.business.industries.join(', ') }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Requested {{ formatDate(request.created_at) }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-3">
                                            <span
                                                :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    request.status === 'pending'
                                                        ? 'bg-orange-100 text-orange-800'
                                                        : request.status === 'approved'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800'
                                                ]"
                                            >
                                                {{ request.status.charAt(0).toUpperCase() + request.status.slice(1) }}
                                            </span>

                                            <div v-if="request.status === 'pending'" class="flex space-x-2">
                                                <button
                                                    @click="handleRequest(request, 'approve')"
                                                    :disabled="loading"
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                                                >
                                                    Approve
                                                </button>
                                                <button
                                                    @click="handleRequest(request, 'reject')"
                                                    :disabled="loading"
                                                    class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                                >
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sent Requests Tab -->
                        <div v-if="activeTab === 'sent'" class="mt-6">
                            <div v-if="sentRequests.length === 0" class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No sent requests</h3>
                                <p class="mt-1 text-sm text-gray-500">Requests you've sent to other businesses will appear here.</p>
                            </div>

                            <div v-else class="space-y-4">
                                <div
                                    v-for="request in sentRequests"
                                    :key="request.id"
                                    class="bg-white border border-gray-200 rounded-lg p-4"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-900">
                                                    {{ request.business.company_name }}
                                                </h3>
                                                <p class="text-sm text-gray-500">
                                                    {{ request.business.country }} • {{ request.business.industries.join(', ') }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Requested {{ formatDate(request.created_at) }}
                                                </p>
                                            </div>
                                        </div>

                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                request.status === 'pending'
                                                    ? 'bg-orange-100 text-orange-800'
                                                    : request.status === 'approved'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800'
                                            ]"
                                        >
                                            {{ request.status.charAt(0).toUpperCase() + request.status.slice(1) }}
                                        </span>
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
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    sentRequests: Array,
    receivedRequests: Array,
});

const activeTab = ref('received');
const loading = ref(false);

const handleRequest = async (request, action) => {
    loading.value = true;
    try {
        const response = await fetch(route('matchmaking.handle-request', request.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ action }),
        });

        const data = await response.json();

        if (response.ok) {
            // Update the request status locally
            request.status = action === 'approve' ? 'approved' : 'rejected';
            $page.props.flash = { success: data.message };
        } else {
            $page.props.flash = { error: data.error };
        }
    } catch (error) {
        $page.props.flash = { error: 'An error occurred while processing the request.' };
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>