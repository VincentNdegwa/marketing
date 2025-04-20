<script setup lang="ts">
import InputText from 'primevue/inputtext'
import { ref, computed } from 'vue'
import type { HTMLAttributes } from 'vue'
import { useVModel } from '@vueuse/core'

const props = defineProps<{
  defaultValue?: string | number,
  modelValue?: string | number,
  class?: HTMLAttributes['class'],
  type?: string,
  required?: boolean,
  autofocus?: boolean,
  tabindex?: number,
  autocomplete?: string,
  placeholder?: string,
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
  <InputText :type="props.type" v-model="stringModel" :class="props.class" :required="props.required"
    :autofocus="props.autofocus" :tabindex="props.tabindex" :autocomplete="props.autocomplete"
    :placeholder="props.placeholder" />
</template>
