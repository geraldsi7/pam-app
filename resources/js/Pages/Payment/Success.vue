<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Payment Successful
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Your registration has been completed successfully!
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>

          <h3 class="mt-2 text-sm font-medium text-gray-900">
            Registration Complete
          </h3>

          <div class="mt-4 text-sm text-gray-500">
            <p>Reference Code: <span class="font-mono font-medium">{{ registration.reference_code }}</span></p>
            <p class="mt-2">
              A confirmation email has been sent to {{ registration.personal_info.email }}
            </p>
            <p class="mt-2">
              Your login credentials will be sent to your email shortly.
            </p>
          </div>

          <div class="mt-6">
            <Link
              :href="route('dashboard')"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Go to Dashboard
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  registration: Object,
})
</script>