<template>
  <div class="blog-component">
    <h1>{{ title }}</h1>
    <p>This is a sample Vue component in the Blog module</p>
    <div class="blog-content">
      <slot></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  title: {
    type: String,
    default: 'Blog Module'
  }
});
</script>

<style scoped>
.blog-component {
  padding: 20px;
  border-radius: 8px;
  background-color: #f9fafb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

h1 {
  color: #1e40af;
  margin-bottom: 16px;
}

.blog-content {
  margin-top: 20px;
}
</style>
