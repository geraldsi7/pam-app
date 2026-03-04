<template>
  <div class="min-h-screen bg-gray-50 py-12 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900">
          Add-ons & Preferences
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Step 4 of 5
        </p>
      </div>

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
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
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

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="loading"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                <span v-if="loading">Saving...</span>
                <span v-else>Continue to Review & Checkout</span>
              </button>
            </div>
          </form>
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
})

const loading = ref(false)
const form = reactive({
  ref: props.registration?.reference_code || '',
  addon_expo: false,
})

const handleSubmit = async () => {
  loading.value = true

  try {
    await router.post(route('registration.storeAddons'), form)
  } catch (error) {
    loading.value = false
  }
}
</script>