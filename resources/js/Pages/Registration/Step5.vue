<template>
  <div class="min-h-screen bg-gray-50 py-12 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900">
          Review & Checkout
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Step 5 of 5
        </p>
      </div>

      <div class="space-y-6">
        <!-- Registration Summary -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Registration Summary</h3>

            <div class="space-y-4">
              <!-- Personal Info -->
              <div>
                <h4 class="font-medium text-gray-900">Personal Information</h4>
                <div class="mt-2 text-sm text-gray-600">
                  <p>{{ registration.personal_info.first_name }} {{ registration.personal_info.last_name }}</p>
                  <p>{{ registration.personal_info.email }}</p>
                  <p v-if="registration.personal_info.phone">{{ registration.personal_info.phone }}</p>
                </div>
              </div>

              <!-- Business Info -->
              <div>
                <h4 class="font-medium text-gray-900">Business Information</h4>
                <div class="mt-2 text-sm text-gray-600">
                  <p>{{ registration.business.company_name }}</p>
                  <p>Origin: {{ registration.business.origin }}</p>
                  <p>Ticket: {{ registration.business.ticket_type }}</p>
                </div>
              </div>

              <!-- Attendees -->
              <div>
                <h4 class="font-medium text-gray-900">Attendees</h4>
                <div class="mt-2 space-y-2">
                  <div
                    v-for="attendee in registration.business.attendees"
                    :key="attendee.id"
                    class="text-sm text-gray-600 p-2 bg-gray-50 rounded"
                  >
                    <p class="font-medium">{{ attendee.first_name }} {{ attendee.last_name }}</p>
                    <p>{{ attendee.email }}</p>
                    <p v-if="attendee.additional_details?.designation">{{ attendee.additional_details.designation }}</p>
                  </div>
                </div>
              </div>

              <!-- Add-ons -->
              <div>
                <h4 class="font-medium text-gray-900">Add-ons</h4>
                <div class="mt-2 text-sm text-gray-600">
                  <p v-if="registration.addon_expo">✓ Expo Showcase ($500)</p>
                  <p v-else>No add-ons selected</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pricing Summary -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing Summary</h3>

            <div class="space-y-3">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Ticket Price</span>
                <span class="text-gray-900">${{ (pricing.ticket_price / 100).toFixed(2) }}</span>
              </div>

              <div v-if="pricing.addon_price > 0" class="flex justify-between text-sm">
                <span class="text-gray-600">Expo Showcase</span>
                <span class="text-gray-900">${{ (pricing.addon_price / 100).toFixed(2) }}</span>
              </div>

              <div v-if="pricing.agent_commission > 0" class="flex justify-between text-sm text-green-600">
                <span>Referral Discount</span>
                <span>-${{ (pricing.agent_commission / 100).toFixed(2) }}</span>
              </div>

              <div class="border-t border-gray-200 pt-3">
                <div class="flex justify-between text-lg font-medium">
                  <span class="text-gray-900">Total</span>
                  <span class="text-gray-900">${{ (pricing.total / 100).toFixed(2) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Referral Code -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Referral Code (Optional)</h3>

            <div class="space-y-4">
              <div>
                <label for="referral_code" class="block text-sm font-medium text-gray-700">
                  Agent Referral Code
                </label>
                <input
                  id="referral_code"
                  v-model="form.referral_code"
                  type="text"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Enter referral code for discount"
                />
                <p class="mt-1 text-sm text-gray-500">
                  If you have a referral code from an agent, enter it here for a discounted rate.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Method</h3>

            <div class="space-y-4">
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <input
                    id="paystack"
                    v-model="form.payment_method"
                    value="paystack"
                    type="radio"
                    name="payment_method"
                    class="sr-only peer"
                  />
                  <label
                    for="paystack"
                    class="flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:border-transparent hover:bg-gray-50"
                  >
                    <div class="flex-1">
                      <div class="text-sm">
                        <p class="font-medium text-gray-900">Paystack</p>
                        <p class="text-gray-500">Pay securely with card or bank transfer</p>
                      </div>
                    </div>
                  </label>
                </div>

                <div>
                  <input
                    id="bank_deposit"
                    v-model="form.payment_method"
                    value="bank_deposit"
                    type="radio"
                    name="payment_method"
                    class="sr-only peer"
                  />
                  <label
                    for="bank_deposit"
                    class="flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:border-transparent hover:bg-gray-50"
                  >
                    <div class="flex-1">
                      <div class="text-sm">
                        <p class="font-medium text-gray-900">Bank Deposit</p>
                        <p class="text-gray-500">Manual bank transfer</p>
                      </div>
                    </div>
                  </label>
                </div>
              </div>
            </div>

            <div class="mt-6">
              <button
                @click="handleSubmit"
                :disabled="loading || !form.payment_method"
                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                <span v-if="loading">Processing...</span>
                <span v-else>Complete Registration & Proceed to Payment</span>
              </button>
            </div>
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
})

const loading = ref(false)
const form = reactive({
  ref: props.registration?.reference_code || '',
  referral_code: '',
  payment_method: '',
})

const handleSubmit = async () => {
  loading.value = true

  try {
    await router.post(route('registration.storeCheckout'), form)
  } catch (error) {
    loading.value = false
  }
}
</script>