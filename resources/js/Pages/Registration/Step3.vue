<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import RegistrationLayout from '@/Layouts/RegistrationLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { PhoneInput } from "@lbgm/phone-number-input";

const props = defineProps({
  registration: Object,
  availableTicketTypes: Array,
  countries: Array,
})

const form = useForm({
  ticket_type: props.registration?.business?.ticket_type || '',
  attendees: props.registration?.business?.attendees?.length > 0
    ? props.registration.business.attendees.map(a => ({
        first_name: a.first_name || '',
        middle_name: a.middle_name || '',
        last_name: a.last_name || '',
        email: a.email || '',
        phone: a.phone ? a.phone.replace(/^\+/, '') : '',
        id_number: a.id_number || '',
        nationality: a.nationality || '',
        designation: a.additional_details?.designation || '',
      }))
    : [],
})

const maxAttendees = computed(() => form.ticket_type === '1*' ? 1 : 3)

const isChinaOrigin = computed(() => props.registration?.business?.origin === 'China')

watch(() => form.ticket_type, (newType) => {
  if (newType) {
    const count = newType === '1*' ? 1 : 3
    
    // Only reset if the number of attendees doesn't match the ticket type
    if (form.attendees.length !== count) {
      form.attendees = Array.from({ length: count }, (_, i) => {
        // Try to preserve existing data if available
        const existing = form.attendees[i] || {}
        return {
          first_name: existing.first_name || '',
          middle_name: existing.middle_name || '',
          last_name: existing.last_name || '',
          email: existing.email || '',
          phone: existing.phone || '',
          id_number: existing.id_number || '',
          nationality: existing.nationality || '',
          designation: existing.designation || '',
        }
      })
    }
  }
})

const validatePhone = (index, data) => {
  if (data.isValid) {
    form.attendees[index].phone = data.number;
    form.clearErrors(`attendees.${index}.phone`);
  } else if (data.isValid === false) {
    form.setError(`attendees.${index}.phone`, 'Invalid phone number');
    form.attendees[index].phone = null;
  }
}

const handleSubmit = () => {
  form.post(route('registration.storeAttendees'))
}
</script>

<template>
  <RegistrationLayout>
    <Head title="Attendee Information - Registration" />
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
              <InputLabel value="Select Ticket Type *" class="mb-3" />
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
              <InputError :message="form.errors.ticket_type" />
            </div>

            <!-- Attendee Forms -->
            <div v-if="form.ticket_type" class="space-y-6">
              <div
                v-for="(attendee, index) in form.attendees"
                :key="index"
                class="border border-gray-200 rounded-lg p-4"
              >
                <h4 class="text-lg font-medium text-gray-900 mb-4">
                  Attendee {{ index + 1 }}
                </h4>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <InputLabel :for="`first_name_${index}`" value="First Name" :required="true" />
                    <TextInput
                      :id="`first_name_${index}`"
                      v-model="attendee.first_name"
                      type="text"                      
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attendees.${index}.first_name`] }"
                    />
                    <InputError :message="form.errors[`attendees.${index}.first_name`]" />
                  </div>

                  <div>
                    <InputLabel :for="`middle_name_${index}`" value="Middle Name" />
                    <TextInput
                      :id="`middle_name_${index}`"
                      v-model="attendee.middle_name"
                      type="text"                      
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attendees.${index}.middle_name`] }"
                    />
                    <InputError :message="form.errors[`attendees.${index}.middle_name`]" />
                  </div>

                  <div>
                    <InputLabel :for="`last_name_${index}`" value="Last Name" :required="true" />
                    <TextInput
                      :id="`last_name_${index}`"
                      v-model="attendee.last_name"
                      type="text"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attendees.${index}.last_name`] }"
                    />
                    <InputError :message="form.errors[`attendees.${index}.last_name`]" />
                  </div>

                  <div>
                    <InputLabel :for="`nationality_${index}`" value="Nationality" :required="true" />
                    <SelectInput
                      :id="`nationality_${index}`"
                      v-model="attendee.nationality"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attendees.${index}.nationality`] }"
                    >
                      <option value="" disabled selected>-- Select nationality --</option>
                      <option
                        v-for="country in countries"
                        :key="country.id"
                        :value="country.id"
                      >
                        {{ country.name }}
                      </option>
                    </SelectInput>
                    <InputError :message="form.errors[`attendees.${index}.nationality`]" />
                  </div>

                  <div>
                    <InputLabel :for="`id_number_${index}`" :value="isChinaOrigin ? 'ID Number' : 'Passport ID'" :required="true" />
                    <TextInput
                      :id="`id_number_${index}`"
                      v-model="attendee.id_number"
                      type="text"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attendees.${index}.id_number`] }"
                    />
                    <InputError :message="form.errors[`attendees.${index}.id_number`]" />
                  </div>

                  <div>
                    <InputLabel :for="`email_${index}`" value="Email" :required="true" />
                    <TextInput
                      :id="`email_${index}`"
                      v-model="attendee.email"
                      type="email"                      
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attendees.${index}.email`] }"
                    />
                    <InputError :message="form.errors[`attendees.${index}.email`]" />
                  </div>

                  <div class="relative z-10">
                    <InputLabel :for="`phone_${index}`" value="Phone Number" :required="true" />
                    <div class="relative overflow-visible">
                      <PhoneInput
                        :id="`phone_${index}`"
                        v-model="attendee.phone"
                        :value="attendee.phone"
                        @phoneData="(data) => validatePhone(index, data)"
                        :listHeight="200"
                        class="mt-1 block w-full"
                        :allowed="[]"
                        :class="{ 'border-red-500': form.errors[`attendees.${index}.phone`] }"
                      />
                    </div>
                    <InputError :message="form.errors[`attendees.${index}.phone`]" />
                  </div>

                  <div>
                    <InputLabel :for="`designation_${index}`" value="Designation/Title" />
                    <TextInput
                      :id="`designation_${index}`"
                      v-model="attendee.designation"
                      type="text"
                      class="mt-1 block w-full"
                      :class="{ 'border-red-500': form.errors[`attendees.${index}.designation`] }"
                    />
                    <InputError :message="form.errors[`attendees.${index}.designation`]" />
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-between">
              <SecondaryButton type="button" @click="$inertia.visit(route('registration.step2'))">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
              </SecondaryButton>
              <PrimaryButton type="submit" :disabled="form.processing || !form.ticket_type">
                <span v-if="form.processing">Saving...</span>
                <span v-else>Continue to Add-ons</span>
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