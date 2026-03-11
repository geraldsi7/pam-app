<template>
  <RegistrationLayout>
    <Head title="Add-ons & Preferences - Registration" />
    <div class="max-w-3xl mx-auto">
      <!-- Progress Tracker -->
      <ProgressTracker
        title="Add-ons & Preferences"
        :current-step="4"
      />

      <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Expo Showcase Add-on -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="addon_expo"
                    v-model="form.addon_expo"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="addon_expo" class="font-medium text-gray-700">
                    Expo Showcase ($500)
                  </label>
                  <p class="text-gray-500">
                    Showcase your products at the expo. This includes a dedicated booth space and marketing materials.
                  </p>
                </div>
              </div>
            </div>

            <div class="flex justify-between">
              <SecondaryButton type="button" @click="$inertia.visit(route('registration.step3'))">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
              </SecondaryButton>
              <PrimaryButton type="submit" :disabled="form.processing">
                <span v-if="form.processing">Saving...</span>
                <span v-else>Continue to Review & Checkout</span>
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </RegistrationLayout>
</template>

<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import RegistrationLayout from '@/Layouts/RegistrationLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import ProgressTracker from '@/Components/ProgressTracker.vue';

const props = defineProps({
  registration: Object,
})

const form = useForm({
  addon_expo: props.registration?.addon_expo || false,
})

const handleSubmit = () => {
  form.post(route('registration.storeAddons'))
}
</script>