<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Business Information
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Step 2 of 5
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700">
              Company Name *
            </label>
            <input
              id="company_name"
              v-model="form.company_name"
              type="text"
              required
              class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label for="origin" class="block text-sm font-medium text-gray-700">
              Origin *
            </label>
            <select
              id="origin"
              v-model="form.origin"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">Select origin</option>
              <option value="China">China</option>
              <option value="Outside">Outside China</option>
              <option value="Online">Online</option>
            </select>
          </div>

          <div>
            <label for="industry" class="block text-sm font-medium text-gray-700">
              Industry
            </label>
            <input
              id="industry"
              v-model="form.industry"
              type="text"
              class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label for="company_size" class="block text-sm font-medium text-gray-700">
              Company Size
            </label>
            <select
              id="company_size"
              v-model="form.company_size"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">Select company size</option>
              <option value="1-10">1-10 employees</option>
              <option value="11-50">11-50 employees</option>
              <option value="51-200">51-200 employees</option>
              <option value="201-1000">201-1000 employees</option>
              <option value="1000+">1000+ employees</option>
            </select>
          </div>

          <div>
            <label for="website" class="block text-sm font-medium text-gray-700">
              Website
            </label>
            <input
              id="website"
              v-model="form.website"
              type="url"
              class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label for="address" class="block text-sm font-medium text-gray-700">
              Address
            </label>
            <textarea
              id="address"
              v-model="form.address"
              rows="3"
              class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            ></textarea>
          </div>

          <div>
            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
            >
              <span v-if="loading">Saving...</span>
              <span v-else>Continue to Attendees</span>
            </button>
          </div>
        </form>
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
  company_name: '',
  origin: '',
  industry: '',
  company_size: '',
  website: '',
  address: '',
})

const handleSubmit = async () => {
  loading.value = true

  try {
    await router.post(route('registration.storeBusinessInfo'), form)
  } catch (error) {
    loading.value = false
  }
}
</script>