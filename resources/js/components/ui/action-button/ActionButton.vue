<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
  href: string;
  permission?: string;
  icon?: string;
}

const props = withDefaults(defineProps<Props>(), {
  permission: undefined,
  icon: undefined
});
</script>

<template>
  <template v-if="permission">
    <Link v-permission="permission" :href="href"
      class="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center rounded-sm px-3 py-2 shadow-xs">
      <i v-if="icon" :class="['pi', icon, 'mr-2']"></i>
      <slot>Action</slot>
    </Link>
  </template>
  <template v-else>
    <Link :href="href" 
      class="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center rounded-sm px-3 py-2 shadow-xs">
      <i v-if="icon" :class="['pi', icon, 'mr-2']"></i>
      <slot>Action</slot>
    </Link>
  </template>
</template>