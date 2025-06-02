<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';

const textarea = ref<HTMLTextAreaElement | null>(null);

const props = defineProps<{
  modelValue: string;
  class?: string;
  disabled?: boolean;
  id?: string;
  rows?: number;
}>();

const emit = defineEmits(['update:modelValue']);

defineExpose({ focus: () => textarea.value?.focus() });

onMounted(() => {
  if (textarea.value?.hasAttribute('autofocus')) {
    textarea.value.focus();
  }
});

const computedValue = computed({
  get() {
    return props.modelValue;
  },
  
  set(value) {
    emit('update:modelValue', value);
  }
});
</script>

<template>
  <textarea
    :id="id"
    ref="textarea"
    v-model="computedValue"
    :class="[
      'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm',
      class
    ]"
    :disabled="disabled"
    :rows="rows || 3"
  />
</template>