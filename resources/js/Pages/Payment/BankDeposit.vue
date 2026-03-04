<template>
  <div class="min-h-screen bg-gray-50 py-12 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900">
          Bank Deposit Payment
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Complete your registration with bank transfer
        </p>
      </div>

      <div class="space-y-6">
        <!-- Bank Details -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bank Transfer Details</h3>

            <div class="bg-gray-50 rounded-lg p-4">
              <dl class="space-y-3">
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">Bank Name:</dt>
                  <dd class="text-sm text-gray-900">{{ bankDetails.bank_name }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">Account Name:</dt>
                  <dd class="text-sm text-gray-900">{{ bankDetails.account_name }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">Account Number:</dt>
                  <dd class="text-sm text-gray-900 font-mono">{{ bankDetails.account_number }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">SWIFT Code:</dt>
                  <dd class="text-sm text-gray-900 font-mono">{{ bankDetails.swift_code }}</dd>
                </div>
              </dl>
            </div>

            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <h4 class="text-sm font-medium text-blue-800">
                    Important Instructions
                  </h4>
                  <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                      <li>Transfer the exact amount: <strong>${{ (pricing.total / 100).toFixed(2) }}</strong></li>
                      <li>Use your reference code as payment description</li>
                      <li>Keep the transaction receipt for your records</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Deposit Form -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Submit Deposit Details</h3>

            <form @submit.prevent="handleSubmit" class="space-y-6">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <label for="deposit_reference" class="block text-sm font-medium text-gray-700">
                    Transaction Reference *
                  </label>
                  <input
                    id="deposit_reference"
                    v-model="form.deposit_reference"
                    type="text"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Bank transaction reference number"
                  />
                </div>

                <div>
                  <label for="amount" class="block text-sm font-medium text-gray-700">
                    Deposit Amount ($) *
                  </label>
                  <input
                    id="amount"
                    v-model.number="form.amount"
                    type="number"
                    step="0.01"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label for="deposit_date" class="block text-sm font-medium text-gray-700">
                    Deposit Date *
                  </label>
                  <input
                    id="deposit_date"
                    v-model="form.deposit_date"
                    type="date"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label for="bank_name" class="block text-sm font-medium text-gray-700">
                    Your Bank Name *
                  </label>
                  <input
                    id="bank_name"
                    v-model="form.bank_name"
                    type="text"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div class="sm:col-span-2">
                  <label for="account_holder" class="block text-sm font-medium text-gray-700">
                    Account Holder Name *
                  </label>
                  <input
                    id="account_holder"
                    v-model="form.account_holder"
                    type="text"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  :disabled="loading"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                  <span v-if="loading">Submitting...</span>
                  <span v-else>Submit Deposit Details</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  registration: Object,
  pricing: Object,
  bankDetails: Object,
})

const loading = ref(false)
const form = reactive({
  ref: props.registration?.reference_code || '',
  deposit_reference: '',
  amount: props.pricing?.total ? (props.pricing.total / 100) : 0,
  deposit_date: '',
  bank_name: '',
  account_holder: '',
})

const handleSubmit = async () => {
  loading.value = true

  try {
    const response = await router.post(route('payment.bank.process'), form, {
      onSuccess: (page) => {
        // Handle success response
      },
      onError: (errors) => {
        loading.value = false
      }
    })
  } catch (error) {
    loading.value = false
  }
}
</script>