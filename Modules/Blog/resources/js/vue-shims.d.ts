declare module '*.vue' {
  import type { DefineComponent } from 'vue';
  const component: DefineComponent<{}, {}, any>;
  export default component;
}

// Add declaration for vue/tsx
declare module 'vue/tsx' {
  export * from 'vue';
}
