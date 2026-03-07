<template>
    <AuthenticatedLayout>
        <Head title="Business Matchmaking" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h1 class="ml-2 text-2xl font-medium text-gray-900">
                                Business Matchmaking Directory
                            </h1>
                        </div>

                        <p class="mt-6 text-gray-500 leading-relaxed">
                            Discover and connect with businesses in your industry. Request access to detailed contact information to expand your network.
                        </p>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input
                                    v-model="filters.search"
                                    type="text"
                                    id="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Company name..."
                                    @input="applyFilters"
                                />
                            </div>

                            <!-- Industry Filter -->
                            <div>
                                <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                                <select
                                    v-model="filters.industry_id"
                                    id="industry"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="applyFilters"
                                >
                                    <option value="">All Industries</option>
                                    <option v-for="industry in filterOptions.industries" :key="industry.id" :value="industry.id">
                                        {{ industry.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Country Filter -->
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <select
                                    v-model="filters.country_id"
                                    id="country"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="applyFilters"
                                >
                                    <option value="">All Countries</option>
                                    <option v-for="country in filterOptions.countries" :key="country.id" :value="country.id">
                                        {{ country.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Origin Filter -->
                            <div>
                                <label for="origin" class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                                <select
                                    v-model="filters.origin"
                                    id="origin"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="applyFilters"
                                >
                                    <option value="">All Types</option>
                                    <option v-for="origin in filterOptions.origins" :key="origin" :value="origin">
                                        {{ origin }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Business Cards -->
                    <div class="p-6">
                        <div v-if="businesses.data.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No businesses found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search filters.</p>
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div
                                v-for="business in businesses.data"
                                :key="business.id"
                                class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                            >
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ business.company_name }}
                                        </h3>
                                        <p class="text-sm text-gray-600">{{ business.country }}</p>
                                    </div>
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            business.attendance_mode === 'in_person'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-blue-100 text-blue-800'
                                        ]"
                                    >
                                        {{ business.attendance_mode === 'in_person' ? 'In Person' : 'Online' }}
                                    </span>
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Industries:</p>
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="industry in business.industries"
                                            :key="industry"
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                                            {{ industry }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center">
                                    <Link
                                        :href="route('matchmaking.show', business.id)"
                                        class="text-indigo-600 hover:text-indigo-500 text-sm font-medium"
                                    >
                                        View Profile
                                    </Link>

                                    <button
                                        v-if="!business.can_view_details && !business.has_pending_request"
                                        @click="requestAccess(business)"
                                        :disabled="loading"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                    >
                                        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Request Access
                                    </button>

                                    <span
                                        v-else-if="business.has_pending_request"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-orange-700 bg-orange-100 rounded-md"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Pending
                                    </span>

                                    <span
                                        v-else-if="business.can_view_details"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-md"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Connected
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="businesses.last_page > 1" class="mt-8">
                            <div class="flex justify-center">
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <Link
                                        v-for="link in businesses.links"
                                        :key="link.label"
                                        :href="link.url"
                                        v-html="link.label"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            link.active
                                                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                            link.url ? 'cursor-pointer' : 'cursor-default'
                                        ]"
                                        :preserve-scroll="true"
                                    />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    businesses: Object,
    filters: Object,
    currentFilters: Object,
});

const loading = ref(false);
const filterOptions = reactive({
    industries: props.filters.industries || [],
    countries: props.filters.countries || [],
    origins: props.filters.origins || [],
});

const filters = reactive({
    search: props.currentFilters.search || '',
    industry_id: props.currentFilters.industry_id || '',
    country_id: props.currentFilters.country_id || '',
    origin: props.currentFilters.origin || '',
});

const applyFilters = () => {
    router.get(route('matchmaking.index'), filters, {
        preserveState: true,
        replace: true,
    });
};

const requestAccess = async (business) => {
    loading.value = true;
    try {
        const response = await fetch(route('matchmaking.request', business.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });

        const data = await response.json();

        if (response.ok) {
            // Update the business status in the local data
            business.has_pending_request = true;
            $page.props.flash = { success: data.message };
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