<template>
  <div class="min-h-screen bg-gray-50 py-12 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900">
          Attendee Information
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Step 3 of 5
        </p>
      </div>

      <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Ticket Type Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Select Ticket Type *
              </label>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div
                  v-for="ticketType in availableTicketTypes"
                  :key="ticketType"
                  class="relative"
                >
                  <input
                    :id="`ticket-${ticketType}`"
                    v-model="form.ticket_type"
                    :value="ticketType"
                    type="radio"
                    name="ticket_type"
                    required
                    class="sr-only peer"
                  />
                  <label
                    :for="`ticket-${ticketType}`"
                    class="flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:border-transparent hover:bg-gray-50"
                  >
                    <div class="flex-1">
                      <div class="text-sm">
                        <p class="font-medium text-gray-900">
                          {{ ticketType }} Ticket
                        </p>
                        <p class="text-gray-500">
                          {{ ticketType === '1*' ? '1 attendee' : '3 attendees' }}
                        </p>
                      </div>
                    </div>
                  </label>
                </div>
              </div>
            </div>

            <!-- Attendee Forms -->
            <div v-if="form.ticket_type" class="space-y-6">
              <div
                v-for="(attendee, index) in attendees"
                :key="index"
                class="border border-gray-200 rounded-lg p-4"
              >
                <h4 class="text-lg font-medium text-gray-900 mb-4">
                  Attendee {{ index + 1 }}
                </h4>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <label :for="`first_name_${index}`" class="block text-sm font-medium text-gray-700">
                      First Name *
                    </label>
                    <input
                      :id="`first_name_${index}`"
                      v-model="attendee.first_name"
                      type="text"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label :for="`last_name_${index}`" class="block text-sm font-medium text-gray-700">
                      Last Name *
                    </label>
                    <input
                      :id="`last_name_${index}`"
                      v-model="attendee.last_name"
                      type="text"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label :for="`email_${index}`" class="block text-sm font-medium text-gray-700">
                      Email *
                    </label>
                    <input
                      :id="`email_${index}`"
                      v-model="attendee.email"
                      type="email"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label :for="`phone_${index}`" class="block text-sm font-medium text-gray-700">
                      Phone
                    </label>
                    <input
                      :id="`phone_${index}`"
                      v-model="attendee.phone"
                      type="tel"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label :for="`designation_${index}`" class="block text-sm font-medium text-gray-700">
                      Designation/Title
                    </label>
                    <input
                      :id="`designation_${index}`"
                      v-model="attendee.designation"
                      type="text"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="loading || !form.ticket_type"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                <span v-if="loading">Saving...</span>
                <span v-else>Continue to Add-ons</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  registration: Object,
  availableTicketTypes: Array,
})

const loading = ref(false)
const form = reactive({
  ref: props.registration?.reference_code || '',
  ticket_type: '',
  attendees: [],
})

const maxAttendees = computed(() => form.ticket_type === '1*' ? 1 : 3)

watch(() => form.ticket_type, (newType) => {
  if (newType) {
    const count = newType === '1*' ? 1 : 3
    form.attendees = Array.from({ length: count }, () => ({
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      designation: '',
    }))
  }
})

const handleSubmit = async () => {
  loading.value = true

  try {
    await router.post(route('registration.storeAttendees'), {
      ref: form.ref,
      ticket_type: form.ticket_type,
      attendees: form.attendees,
    })
  } catch (error) {
    loading.value = false
  }
}
</script>