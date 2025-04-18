<script setup lang="ts">
// Placeholder component for blog module
</script>

<template>
  <div class="absolute inset-0 flex items-center justify-center bg-gray-50 dark:bg-gray-800">
    <div class="h-full w-full bg-[linear-gradient(45deg,var(--tw-gradient-stops))] from-gray-100 via-gray-200 to-gray-100 dark:from-gray-700 dark:via-gray-800 dark:to-gray-700 bg-[length:400%_400%] animate-gradient"></div>
  </div>
</template>

<style scoped>
@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.animate-gradient {
  animation: gradient 8s ease infinite;
}
</style>
