<template>
  <Head title="Bank Deposit Payment" />
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

            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <InputLabel for="deposit_reference" value="Transaction Reference" required="true" />
                  <TextInput
                    id="deposit_reference"
                    v-model="form.deposit_reference"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Bank transaction reference number"
                    :class="{ 'border-red-500': form.errors.deposit_reference }"
                  />
                  <InputError :message="form.errors.deposit_reference" />
                </div>

                <div>
                  <InputLabel for="amount" value="Deposit Amount ($)" required="true" />
                  <TextInput
                    id="amount"
                    v-model.number="form.amount"
                    type="number"
                    step="0.01"
                    class="mt-1 block w-full"
                    :class="{ 'border-red-500': form.errors.amount }"
                  />
                  <InputError :message="form.errors.amount" />
                </div>

                <div>
                  <InputLabel for="deposit_date" value="Deposit Date" required="true" />
                  <TextInput
                    id="deposit_date"
                    v-model="form.deposit_date"
                    type="date"
                    class="mt-1 block w-full"
                    :class="{ 'border-red-500': form.errors.deposit_date }"
                  />
                  <InputError :message="form.errors.deposit_date" />
                </div>

                <div>
                  <InputLabel for="bank_name" value="Your Bank Name" required="true" />
                  <TextInput
                    id="bank_name"
                    v-model="form.bank_name"
                    type="text"
                    class="mt-1 block w-full"
                    :class="{ 'border-red-500': form.errors.bank_name }"
                  />
                  <InputError :message="form.errors.bank_name" />
                </div>

                <div class="sm:col-span-2">
                  <InputLabel for="account_holder" value="Account Holder Name" required="true" />
                  <TextInput
                    id="account_holder"
                    v-model="form.account_holder"
                    type="text"
                    class="mt-1 block w-full"
                    :class="{ 'border-red-500': form.errors.account_holder }"
                  />
                  <InputError :message="form.errors.account_holder" />
                </div>
              </div>

              <div class="mt-6 flex justify-between">
              <SecondaryButton type="button" @click="$inertia.visit(route('registration.step4'))">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
              </SecondaryButton>
              <PrimaryButton type="submit" :disabled="form.processing">
                <span v-if="form.processing">Submitting...</span>
                <span v-else>Submit</span>
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </PrimaryButton>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router, useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  registration: Object,
  pricing: Object,
  bankDetails: Object,
})

const form = useForm({
  deposit_reference: '',
  amount: props.pricing?.total ? (props.pricing.total / 100) : 0,
  deposit_date: '',
  bank_name: '',
  account_holder: '',
})

const submit = () => {
  form.post(route('payment.bank.process'), {
    onSuccess: (page) => {
      // Handle success response
    }
  })
}
</script>