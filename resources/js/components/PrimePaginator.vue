<template>
  <Paginator
    v-if="data && (data.total > 0 || alwaysShow)"
    :first="(data.current_page - 1) * data.per_page"
    :rows="data.per_page"
    :totalRecords="data.total"
    :rowsPerPageOptions="rowsPerPageOptions"
    :template="template || defaultTemplate"
    :currentPageReportTemplate="currentPageReportTemplate"
    :alwaysShow="alwaysShow"
    :pageLinkSize="pageLinkSize"
    @update:first="onPageChange"
    @update:rows="onRowsChange"
  >
    <template v-for="(_, name) in $slots" #[name]="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
  </Paginator>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Paginator from 'primevue/paginator';

interface PaginatorData {
  data: any[];
  current_page: number;
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: { url: string | null; label: string; active: boolean }[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}

const props = defineProps({
  data: {
    type: Object as () => PaginatorData,
    required: true
  },
  template: {
    type: [String, Object],
    default: null
  },
  preserveScroll: {
    type: Boolean,
    default: false
  },
  preserveState: {
    type: Boolean,
    default: false
  },
  only: {
    type: Array as () => string[],
    default: () => []
  },
  alwaysShow: {
    type: Boolean,
    default: true
  },
  pageLinkSize: {
    type: Number,
    default: 5
  },
  rowsPerPageOptions: {
    type: Array as () => number[],
    default: () => [10, 25, 50, 100]
  },
  currentPageReportTemplate: {
    type: String,
    default: '({currentPage} of {totalPages})'
  }
});

const emit = defineEmits(['page-changed', 'per-page-changed']);

const defaultTemplate = computed(() => {
  return {
    '640px': 'PrevPageLink CurrentPageReport NextPageLink',
    '960px': 'FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink',
    '1300px': 'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink',
    'default': 'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown'
  };
});

const onPageChange = (first: number) => {
  const page = Math.floor(first / props.data.per_page) + 1;
  
  if (page === props.data.current_page) return;
  
  emit('page-changed', page);
  
  // If we're using Inertia.js, navigate to the new page
  navigateToPage(page);
};

const onRowsChange = (rows: number) => {
  if (rows === props.data.per_page) return;
  
  emit('per-page-changed', rows);
  
  // If we're using Inertia.js, navigate to page 1 with new per_page
  navigateToPage(1, rows);
};

const navigateToPage = (page: number, perPage?: number) => {
  const url = new URL(window.location.href);
  const params = new URLSearchParams(url.search);
  
  params.set('page', page.toString());
  
  if (perPage) {
    params.set('per_page', perPage.toString());
  }
  
  const path = `${url.pathname}?${params.toString()}`;
  
  router.get(path, {}, {
    preserveScroll: props.preserveScroll,
    preserveState: props.preserveState,
    only: props.only
  });
};
</script>