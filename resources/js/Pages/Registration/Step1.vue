<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import RegistrationLayout from '@/Layouts/RegistrationLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { PhoneInput } from "@lbgm/phone-number-input";
import { ref } from 'vue';

const props = defineProps({
  registration: Object,
})

const form = useForm({
  first_name: props.registration?.personal_info?.first_name || '',
  middle_name: props.registration?.personal_info?.middle_name || '',
  last_name: props.registration?.personal_info?.last_name || '',
  email: props.registration?.email || '',
  phone: props.registration?.personal_info?.phone?.replace(/^\+/, '') || '',
  designation: props.registration?.personal_info?.designation || '',
})

const phoneError = ref(false);

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

const handleSubmit = () => {
  form.post(route('registration.storePersonalInfo'))
}
</script>

<template>
  <RegistrationLayout>

    <Head title="Personal Information - Registration" />
    <div class="mx-auto">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Personal Information
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Step 1 of 5
        </p>
      </div>
    </div>

    <div class="mt-8 mx-auto w-full max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid gap-4">
            <div>
              <InputLabel for="first_name" value="First Name" required="true" />
              <TextInput id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full"
                :class="{ 'border-red-500': form.errors.first_name }"
                placeholder="Enter your first name" />
              <InputError :message="form.errors.first_name" />
            </div>

            <div>
              <InputLabel for="last_name" value="Last Name" required="true" />
              <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full"
                :class="{ 'border-red-500': form.errors.last_name }"
                placeholder="Enter your last name" />
              <InputError :message="form.errors.last_name" />
            </div>
          </div>

          <div>
            <InputLabel for="email" value="Email Address" required="true" />
            <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full"
              :class="{ 'border-red-500': form.errors.email }"
              placeholder="Enter your email address" />
            <InputError :message="form.errors.email" />
          </div>

          <div class="relative z-10">
            <InputLabel for="phone" value="Phone Number" :required="true" />
            <div class="relative overflow-visible">
              <PhoneInput
                :id="'phone'"
                :name="'phone'"
                v-model="form.phone"
                :value="form.phone"
                :placeholder="'Enter your phone number'"
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
            <InputLabel for="designation" value="Designation/Title" />
            <TextInput id="designation" v-model="form.designation" type="text" class="mt-1 block w-full"
              :class="{ 'border-red-500': form.errors.designation }"
              placeholder="Enter your designation/title" />
            <InputError :message="form.errors.designation" />
          </div>

          <div class="flex justify-end">
            <PrimaryButton type="submit" :disabled="form.processing">
              <span v-if="form.processing">Saving...</span>
              <span v-else>Continue</span>
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