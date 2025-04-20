<script setup lang="ts">
import InputNumber from 'primevue/inputnumber'
import type { HTMLAttributes } from 'vue'
import { useVModel } from '@vueuse/core'

// Define props and emits
const props = defineProps<{
  modelValue?: number | null
  defaultValue?: number | null
  class?: HTMLAttributes['class']
  min?: number
  max?: number
  minFractionDigits?: number
  maxFractionDigits?: number
  useGrouping?: boolean
  mode?: 'decimal' | 'currency'
  currency?: string
  locale?: string
  prefix?: string
  suffix?: string
  placeholder?: string
  disabled?: boolean
  invalid?: boolean
  variant?: 'filled' | 'outlined'
  size?: 'small' | 'normal' | 'large'
  showButtons?: boolean
  buttonLayout?: 'stacked' | 'horizontal' | 'vertical'
  step?: number
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void
}>()

const model = useVModel(props, 'modelValue', emit, {
  passive: true,
  defaultValue: props.defaultValue ?? null
})
</script>

<template>
  <InputNumber
    v-model="model"
    :class="props.class"
    :min="props.min"
    :max="props.max"
    :minFractionDigits="props.minFractionDigits"
    :maxFractionDigits="props.maxFractionDigits"
    :useGrouping="props.useGrouping"
    :mode="props.mode"
    :currency="props.currency"
    :locale="props.locale"
    :prefix="props.prefix"
    :suffix="props.suffix"
    :placeholder="props.placeholder"
    :disabled="props.disabled"
    :invalid="props.invalid"
    :variant="props.variant"
    :size="props.size"
    :showButtons="props.showButtons"
    :buttonLayout="props.buttonLayout"
    :step="props.step"
  >
    <template #incrementbuttonicon>
      <span class="pi pi-plus" />
    </template>
    <template #decrementbuttonicon>
      <span class="pi pi-minus" />
    </template>
  </InputNumber>
</template>
