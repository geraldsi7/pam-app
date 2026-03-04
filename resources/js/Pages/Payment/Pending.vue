<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Payment Submitted
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Your bank deposit details have been received
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>

          <h3 class="mt-2 text-sm font-medium text-gray-900">
            Verification Pending
          </h3>

          <div class="mt-4 text-sm text-gray-500">
            <p>Reference Code: <span class="font-mono font-medium">{{ registration.reference_code }}</span></p>
            <p class="mt-2">
              We will verify your bank deposit within 24 hours. You will receive an email confirmation once verified.
            </p>
            <p class="mt-2">
              Your login credentials will be sent after payment verification.
            </p>
          </div>

          <div class="mt-6">
            <Link
              :href="route('home')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Return to Home
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