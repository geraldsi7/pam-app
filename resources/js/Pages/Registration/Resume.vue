<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Registration Resumed
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Continue your registration from where you left off
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div v-if="registration" class="space-y-6">
          <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
              <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>

            <h3 class="mt-2 text-sm font-medium text-gray-900">
              Registration Found
            </h3>

            <div class="mt-4 text-sm text-gray-500">
              <p>Reference Code: <span class="font-mono font-medium">{{ registration.reference_code }}</span></p>
              <p>Current Step: Step {{ registration.current_step }}</p>
              <p>Email: {{ registration.email }}</p>
            </div>
          </div>

          <div class="flex space-x-4">
            <Link
              :href="getNextStepUrl()"
              class="flex-1 bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Continue Registration
            </Link>

            <Link
              href="{{ route('registration.create') }}"
              class="flex-1 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Start New Registration
            </Link>
          </div>
        </div>

        <div v-else class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>

          <h3 class="mt-2 text-sm font-medium text-gray-900">
            Registration Not Found
          </h3>

          <p class="mt-2 text-sm text-gray-500">
            No registration found with that reference code. Please check and try again.
          </p>

          <div class="mt-6">
            <Link
              href="{{ route('registration.create') }}"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Start New Registration
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

const getNextStepUrl = () => {
  if (!props.registration) return ''

  const step = props.registration.current_step
  const ref = props.registration.reference_code

  switch (step) {
    case 0:
      return route('registration.step2', { ref })
    case 1:
      return route('registration.step2', { ref })
    case 2:
      return route('registration.step3', { ref })
    case 3:
      return route('registration.step4', { ref })
    case 4:
      return route('registration.step5', { ref })
    default:
      return route('registration.index')
  }
}
</script>