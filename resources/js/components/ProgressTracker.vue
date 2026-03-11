<template>
  <div class="card-premium p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-enterprise-heading">{{ title }}</h2>
      <span class="text-sm text-enterprise-muted">Step {{ currentStep }} of {{ totalSteps }}</span>
    </div>

    <!-- Desktop Progress Bar -->
    <div class="hidden md:block">
      <div class="flex items-center justify-between">
        <div
          v-for="(step, index) in steps"
          :key="step.key"
          class="flex flex-col items-center flex-1"
        >
          <div class="flex items-center w-full">
            <div class="flex flex-col items-center">
              <!-- Step Circle -->
              <div
                :class="[
                  'h-10 w-10 rounded-full flex items-center justify-center shadow-sm transition-all duration-300',
                  index + 1 < currentStep
                    ? 'bg-[hsl(42,80%,50%)] text-accent-foreground' // Completed step
                    : index + 1 === currentStep
                    ? 'bg-primary text-primary-foreground' // Current step
                    : 'bg-muted border-2 border-border text-muted-foreground' // Future step
                ]"
              >
                <!-- Show checkmark for completed steps -->
                <svg
                  v-if="index + 1 < currentStep"
                  xmlns="http://www.w3.org/2000/svg"
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="lucide lucide-check text-white flex-shrink-0 mt-0.5"
                >
                  <path d="M20 6 9 17l-5-5"></path>
                </svg>
                <!-- Show step number for current and future steps -->
                <span
                  v-else
                  :class="[
                    'text-sm font-semibold',
                    index + 1 === currentStep ? 'text-[hsl(42,80%,50%)]' : 'text-muted-foreground'
                  ]"
                >
                  {{ index + 1 }}
                </span>
              </div>
              <!-- Step Label -->
              <span
                :class="[
                  'text-xs font-medium mt-2 text-center',
                  index + 1 <= currentStep ? 'text-primary' : 'text-muted-foreground'
                ]"
              >
                {{ step.label }}
              </span>
            </div>
            <!-- Connecting Line (except for last step) -->
            <div
              v-if="index < steps.length - 1"
              :class="[
                'flex-1 h-px mx-2 transition-colors duration-300',
                index + 1 < currentStep ? 'bg-[hsl(42,80%,50%)]' : 'bg-gray-200'
              ]"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Progress Steps -->
    <div class="block md:hidden">
      <div class="flex items-center justify-center space-x-2 mb-4">
        <div
          v-for="(step, index) in steps"
          :key="step.key"
          class="flex items-center"
        >
          <div
            :class="[
              'h-8 w-8 rounded-full flex items-center justify-center transition-all duration-300',
              index + 1 < currentStep
                ? 'bg-[hsl(42,80%,50%)] text-accent-foreground' // Completed step
                : index + 1 === currentStep
                ? 'bg-primary text-[hsl(42,80%,50%)]' // Current step
                : 'bg-muted border-2 border-border text-muted-foreground' // Future step
            ]"
          >
            <!-- Show checkmark for completed steps -->
            <svg
              v-if="index + 1 < currentStep"
              xmlns="http://www.w3.org/2000/svg"
              width="12"
              height="12"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="lucide lucide-check text-white"
            >
              <path d="M20 6 9 17l-5-5"></path>
            </svg>
            <!-- Show step number for current and future steps -->
            <span
              v-else
              :class="[
                'text-xs font-semibold',
                index + 1 === currentStep ? 'text-primary-foreground' : 'text-muted-foreground'
              ]"
            >
              {{ index + 1 }}
            </span>
          </div>
          <!-- Connecting Line (except for last step) -->
          <div
            v-if="index < steps.length - 1"
            :class="[
              'h-1 w-6 transition-colors duration-300',
              index + 1 < currentStep ? 'bg-accent' : 'bg-border'
            ]"
          ></div>
        </div>
      </div>

      <div class="text-center">
        <p class="text-sm font-medium text-foreground">
          Step {{ currentStep }}: {{ currentStepLabel }}
        </p>
        <p class="text-xs text-muted-foreground mt-1">{{ currentStepDescription }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Registration Progress'
  },
  currentStep: {
    type: Number,
    required: true,
    validator: (value) => value >= 1
  }
})

// Define the steps internally
const steps = [
  { key: 'personal', label: 'Personal Info', description: 'Complete your personal details' },
  { key: 'business', label: 'Business Details', description: 'Provide your business information' },
  { key: 'delegates', label: 'Delegate Registration', description: 'Register attendees and ticket selection' },
  { key: 'addons', label: 'Add-ons', description: 'Select additional packages and services' },
  { key: 'payment', label: 'Payment', description: 'Review details and complete payment' }
]

// Computed properties for current step info
const totalSteps = computed(() => steps.length)

const currentStepLabel = computed(() => {
  const step = steps[props.currentStep - 1]
  return step ? step.label : ''
})

const currentStepDescription = computed(() => {
  const step = steps[props.currentStep - 1]
  return step ? step.description : ''
})
</script>