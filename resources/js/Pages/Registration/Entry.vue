<script setup>
import { ref, reactive } from 'vue'
import { router, useForm, Head } from '@inertiajs/vue3'
import RegistrationLayout from '@/Layouts/RegistrationLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  error: String,
})

const loading = ref(false)
const form = useForm({
  reference_code: '',
})

const submit = () => {
  try {
    if (form.reference_code.trim()) {
      form.post(route('registration.index'), {
        onFinish: () => form.reset('reference_code'),
      })
    } else {
      router.visit(route('registration.step1', { new: 1 }))
    }
  } catch (error) {
    console.error(error)
  }
}
</script>

<template>
  <RegistrationLayout>
    <Head title="Registration" />
    <div class="mx-auto w-full max-w-md">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          {{ $page.props.site.title }}
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Registration Portal
        </p>
      </div>
    </div>

    <div class="mt-8 mx-auto w-full max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Start or Resume Registration
            </h3>

            <div class="space-y-4">
              <div>
                <InputLabel for="reference_code" value="Reference Code (Optional)" />
                <div class="mt-1">
                  <TextInput id="reference_code" v-model="form.reference_code" type="text"
                    placeholder="Enter your reference code to resume" class="block w-full"
                    :class="{ 'border-red-500': form.errors.reference_code }" />
                </div>

                <InputError :message="form.errors.reference_code" />
              </div>

              <div class="flex justify-between space-x-4">
                <SecondaryButton type="button" @click="router.visit(route('registration.step1', { new: 1 }))" :disabled="form.processing">
                  New Registration
                </SecondaryButton>

                <PrimaryButton type="submit" :disabled="form.processing">
                  <span v-if="form.processing">Please wait...</span>
                  <span v-else>Continue</span>
                </PrimaryButton>
              </div>
            </div>
          </div>
        </form>

        <InputError :message="error" class="mt-4" />
      </div>
    </div>
  </RegistrationLayout>
</template>