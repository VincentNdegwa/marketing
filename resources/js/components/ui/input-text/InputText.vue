<script setup lang="ts">
import InputText from 'primevue/inputtext'
import { ref, computed } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core'

const props = defineProps<{
  defaultValue?: string | number,
  modelValue?: string | number,
  class?: HTMLAttributes['class']
}>()
const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})

const stringModel = computed({
  get: () => modelValue.value?.toString() ?? '',
  set: (val: string) => {
    modelValue.value = val
  }
})
</script>

<template>
  <InputText type="text" v-model="stringModel" />
</template>
