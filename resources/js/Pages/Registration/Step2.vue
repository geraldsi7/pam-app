<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import RegistrationLayout from '@/Layouts/RegistrationLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import MultiSelectInput from '@/Components/MultiSelectInput.vue'
import { computed, ref } from 'vue'
import SelectInput from '@/Components/SelectInput.vue'
import { PhoneInput } from "@lbgm/phone-number-input";

const props = defineProps({
  registration: Object,
  industries: Array,
  countries: Array,
})

const industryOptions = computed(() =>
  props.industries.map(industry => ({
    value: industry.id,
    label: industry.name
  }))
)

const countryOptions = computed(() =>
  props.countries.map(country => ({
    value: country.id,
    label: country.name
  }))
)

const phoneError = ref(false)

const normalizeMultiSelectValues = (value, key = "id") => {
  if (!value) {
    return [];
  }

  if (Array.isArray(value)) {
    return value
      .map((item) => (typeof item === "string" ? item : item?.[key]))
      .filter(Boolean);
  }

  return [];
};

const validatePhone = (data) => {
  if (data.isValid) {
    form.phone = data.number;
    phoneError.value = false;
    form.errors.phone = null; // Clear any existing error
  } else if (data.isValid === false) {
    form.errors.phone = 'Invalid phone number';
    form.phone = null;
    phoneError.value = true;
  }
}

const form = useForm({
  company_name: props.registration?.business?.company_name || '',
  country_id: props.registration?.business?.country_id || '',
  email: props.registration?.business?.email || '',
  phone: props.registration?.business?.phone.replace(/^\+/, '') || '',
  attendance_mode: props.registration?.business?.attendance_mode || '',
  industries: normalizeMultiSelectValues(props.registration?.business?.industries, 'id'),
  company_size: props.registration?.business?.business_details?.company_size || '',
  website: props.registration?.business?.business_details?.website || '',
  street_address: props.registration?.business?.business_details?.street_address || '',
})


const handleSubmit = () => {
  form.post(route('registration.storeBusinessInfo'))
}
</script>

<template>
  <RegistrationLayout>
    <Head title="Business Information - Registration" />
    <div class="mx-auto">
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
            <InputLabel for="company_name" value="Company Name" :required="true" />
            <TextInput
              id="company_name"
              v-model="form.company_name"
              type="text"
              required
              class="mt-1 block w-full"
              :class="{ 'border-red-500': form.errors.company_name }"
            />
            <InputError :message="form.errors.company_name" />
          </div>

          <div>
            <InputLabel for="country_id" value="Country" :required="true" />
            <SelectInput id="country_id" v-model="form.country_id" class="mt-1 block w-full" :class="{ 'border-red-500': form.errors.country_id }">
              <option value="" disabled selected>-- Select country --</option>
              <option
                v-for="country in props.countries"
                :key="country.id"
                :value="country.id"
              >
                {{ country.name }}
              </option>
            </SelectInput>
            <InputError :message="form.errors.country_id" />
          </div>

          <div>
            <InputLabel for="email" value="Business Email" :required="true" />
            <TextInput
              id="email"
              v-model="form.email"
              type="email"
              required
              class="mt-1 block w-full"
              :class="{ 'border-red-500': form.errors.email }"
              placeholder="Enter business email address"
            />
            <InputError :message="form.errors.email" />
          </div>

          <div class="relative z-10">
            <InputLabel for="phone" value="Business Phone" :required="true" />
            <div class="relative overflow-visible">
              <PhoneInput
                :id="'phone'"
                :name="'phone'"
                v-model="form.phone"
                :value="form.phone"
                :placeholder="'Enter business phone number'"
                @phoneData="validatePhone"
                :listHeight="200"
                :allowed="[]"
                :arrow=true
                class="mt-1 block w-full"
                :class="{
                  'border-red-600': form.errors.phone,
                }"
              />
            </div>
            <InputError :message="form.errors.phone" />
          </div>

          <div>
            <InputLabel for="attendance_mode" value="Mode of Attendance" :required="true" />
            <SelectInput id="attendance_mode" v-model="form.attendance_mode" class="mt-1 block w-full" :class="{ 'border-red-500': form.errors.attendance_mode }">
              <option value="" disabled selected>-- Select attendance mode --</option>
              <option value="in_person">In-person</option>
              <option value="online">Online</option>
            </SelectInput>
            <InputError :message="form.errors.attendance_mode" />
          </div>

          <!-- <div>
            <InputLabel for="industries" value="Industries" />
            <select
              id="industries"
              v-model="form.industries"
              multiple
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              :class="{ 'border-red-500': form.errors.industries }"
            >
              <option
                v-for="industry in industries"
                :key="industry.id"
                :value="industry.id"
              >
                {{ industry.name }}
              </option>
            </select>
            <InputError :message="form.errors.industries" />
          </div> -->

          <div>
          <InputLabel for="industries" value="Industries" :required="true" />
          <MultiSelectInput id="industries" v-model="form.industries" class="w-full" :options="industryOptions"
            placeholder="Select industries..." />
          <InputError :message="form.errors.industries" />
        </div>

          <div>
            <InputLabel for="company_size" value="Company Size" />
            <SelectInput
              id="company_size"
              v-model="form.company_size"
              class="mt-1 block w-full rounded-sm border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              :class="{ 'border-red-500': form.errors.company_size }"
            >
              <option value="" disabled selected>-- Select company size --</option>
              <option value="1-10">1-10 employees</option>
              <option value="11-50">11-50 employees</option>
              <option value="51-200">51-200 employees</option>
              <option value="201-1000">201-1000 employees</option>
              <option value="1000+">1000+ employees</option>
            </SelectInput>
            <InputError :message="form.errors.company_size" />
          </div>

          <div>
            <InputLabel for="website" value="Website" />
            <TextInput
              id="website"
              v-model="form.website"
              type="url"
              class="mt-1 block w-full"
              :class="{ 'border-red-500': form.errors.website }"
            />
            <InputError :message="form.errors.website" />
          </div>

          <div>
            <InputLabel for="street_address" value="Street Address" />
            <textarea
              id="street_address"
              v-model="form.street_address"
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              :class="{ 'border-red-500': form.errors.street_address }"
            ></textarea>
            <InputError :message="form.errors.street_address" />
          </div>

          <div class="flex justify-between">
            <SecondaryButton type="button" @click="$inertia.visit(route('registration.step1'))">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
              Back
            </SecondaryButton>
            <PrimaryButton type="submit" :disabled="form.processing">
              <span v-if="form.processing">Saving...</span>
              <span v-else>Continue to Attendees</span>
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </RegistrationLayout>
</template>