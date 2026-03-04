<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          FDI B2B Government Summit
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Registration Portal
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Start or Resume Registration
            </h3>

            <div class="space-y-4">
              <div>
                <label for="reference_code" class="block text-sm font-medium text-gray-700">
                  Reference Code (Optional)
                </label>
                <div class="mt-1">
                  <input
                    id="reference_code"
                    v-model="form.reference_code"
                    type="text"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Enter your reference code to resume"
                  />
                </div>
              </div>

              <div class="flex space-x-4">
                <button
                  type="submit"
                  :disabled="loading"
                  class="flex-1 bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                  <span v-if="loading">Please wait...</span>
                  <span v-else>Continue</span>
                </button>

                <Link
                  :href="route('registration.create')"
                  class="flex-1 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Start New Registration
                </Link>
              </div>
            </div>
          </div>
        </form>

        <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
          <p class="text-sm text-red-600">{{ error }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  error: String,
})

const loading = ref(false)
const form = reactive({
  reference_code: '',
})

const handleSubmit = async () => {
  loading.value = true

  try {
    if (form.reference_code.trim()) {
      router.visit(route('registration.index', { ref: form.reference_code.trim() }))
    } else {
      router.visit(route('registration.create'))
    }
  } catch (error) {
    loading.value = false
  }
}
</script>