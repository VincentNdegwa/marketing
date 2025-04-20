<template>
  <Password
    v-model="modelValue"
    v-bind="props"
    :class="customClass"
  />
</template>

<script setup lang="ts">
import { defineProps, defineEmits, computed,watch } from 'vue'
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

const customClass = computed(() => ({
  'p-invalid': props.invalid
}))

watch(
  () => props.modelValue,
  (value) => emit('update:modelValue', value)
)
</script>
