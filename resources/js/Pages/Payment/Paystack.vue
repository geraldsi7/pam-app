<template>
  <div class="min-h-screen bg-gray-50 py-12 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900">
          Secure Payment
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Complete your registration with Paystack
        </p>
      </div>

      <div class="space-y-6">
        <!-- Pricing Summary -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h3>

            <div class="space-y-3">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Amount to Pay</span>
                <span class="text-lg font-medium text-gray-900">${{ (pricing.total / 100).toFixed(2) }}</span>
              </div>

              <div class="border-t border-gray-200 pt-3">
                <div class="text-sm text-gray-500">
                  Reference Code: <span class="font-mono">{{ registration.reference_code }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Paystack Payment Form -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>

            <div id="paystack-form" class="space-y-4">
              <!-- Paystack will be initialized here -->
              <div class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                <p class="mt-2 text-sm text-gray-500">Loading payment form...</p>
              </div>
            </div>

            <div class="mt-6 text-center">
              <button
                @click="initializePaystack"
                :disabled="loading"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                <span v-if="loading">Processing Payment...</span>
                <span v-else>Pay with Paystack</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Security Notice -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-7-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h4 class="text-sm font-medium text-blue-800">
                Secure Payment
              </h4>
              <div class="mt-2 text-sm text-blue-700">
                <p>Your payment information is encrypted and secure. Paystack uses industry-standard security measures.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  registration: Object,
  pricing: Object,
})

const loading = ref(false)

const initializePaystack = () => {
  loading.value = true

  // Paystack configuration
  const paystackConfig = {
    key: 'pk_test_your_paystack_public_key', // Replace with actual key
    email: props.registration.personal_info.email,
    amount: props.pricing.total, // Amount in kobo (Paystack uses kobo, which is 1/100 of Naira)
    currency: 'USD',
    ref: `REG-${props.registration.reference_code}-${Date.now()}`,
    callback: (response) => {
      // Handle successful payment
      handlePaymentSuccess(response)
    },
    onClose: () => {
      loading.value = false
    }
  }

  // Initialize Paystack popup
  const paystack = new PaystackPop()
  paystack.newTransaction(paystackConfig)
}

const handlePaymentSuccess = async (response) => {
  try {
    await router.post(route('payment.paystack.process'), {
      ref: props.registration.reference_code,
      paystack_reference: response.reference,
    })
  } catch (error) {
    loading.value = false
  }
}

onMounted(() => {
  // Load Paystack script if not already loaded
  if (!window.PaystackPop) {
    const script = document.createElement('script')
    script.src = 'https://js.paystack.co/v1/inline.js'
    script.async = true
    document.head.appendChild(script)
  }
})
</script>