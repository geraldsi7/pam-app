<template>
  <RegistrationLayout>
  <Head title="Registration Pending" />
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Registration Pending
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Your registration is being processed
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
            {{ registration.payment_method === 'bank_deposit' ? 'Payment Verification Pending' : 'Payment Required' }}
          </h3>

          <div class="mt-4 text-sm text-gray-500">
            <p>Reference Code: <span class="font-mono font-medium">{{ registration.reference_code }}</span></p>

            <div v-if="registration.payment_method === 'bank_deposit'" class="mt-2">
              <p>We will verify your bank deposit within 72 hours. You will receive an email confirmation once verified.</p>
              <p class="mt-2">Your login credentials will be sent after payment verification.</p>
            </div>

            <div v-else-if="registration.status === 'payment_pending'" class="mt-2">
              <p>Your registration is complete. Please proceed with payment to finalize your registration.</p>
              <p class="mt-2">Choose your preferred payment method to continue.</p>
            </div>
          </div>

          <div class="mt-6 flex gap-4 justify-center">
            <Link :as="PrimaryButton"
              v-if="registration.status === 'payment_pending'"
              :href="route('payment.index')"
            >
              Proceed to Payment
            </Link>

            <component
              :is="'a'"
              href="/"
              class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 disabled:cursor-not-allowed"
            >
              Return to Home
            </component>
          </div>
        </div>
      </div>
    </div>
  </div>
  </RegistrationLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3'
import RegistrationLayout from '@/Layouts/RegistrationLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
  registration: Object,
  pricing: Object,
})
</script>