<template>
  <Password
    v-model="internalValue"
    v-bind="props"
    inputClass="!w-full !flex-1"
  />
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Password from 'primevue/password'

const props = defineProps({
  modelValue: String,
  feedback: {
    type: Boolean,
    default: true
  },
  toggleMask: Boolean,
  promptLabel: String,
  weakLabel: String,
  mediumLabel: String,
  strongLabel: String,
  placeholder: String,
  size: String,
  variant: String,
  disabled: Boolean,
  invalid: Boolean,
  name: String,
  'aria-labelledby': String,
  'aria-label': String
})

const emit = defineEmits(['update:modelValue'])

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const customClass = computed(() => ({
  'p-invalid': props.invalid
}))
</script>
