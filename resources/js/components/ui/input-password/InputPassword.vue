<template>

  <Password v-model="stringModel" :feedback="props.feedback" :toggleMask="props.toggleMask"
    :promptLabel="props.promptLabel" :weakLabel="props.weakLabel" :mediumLabel="props.mediumLabel"
    :strongLabel="props.strongLabel" :fluid=props.fluid inputClass="!w-full !flex-1" />

</template>

<script setup lang="ts">
import { computed } from 'vue'
import Password from 'primevue/password'
import { useVModel } from '@vueuse/core'

const props = defineProps<
  {
    defaultValue?: string | number,
    modelValue?: string | number,
    fluid?:boolean,
    feedback?: boolean,
    toggleMask?: boolean,
    promptLabel?: string,
    weakLabel?: string,
    mediumLabel?: string,
    strongLabel?: string,
    placeholder?: string,
    size?: string,
    variant?: string,
    disabled?: boolean,
    invalid?: boolean,
    name?: string,
    required?: boolean,
    autofocus?: boolean,
    tabindex?: number,
    autocomplete?: string,
    'aria-labelledby': string,
    'aria-label': string
  }


>(
)

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

const customClass = computed(() => ({
  'p-invalid': props.invalid
}))
</script>
