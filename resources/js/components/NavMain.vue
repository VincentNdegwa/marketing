<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ChevronDown, ChevronRight } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
const expandedMenus = ref<Record<string, boolean>>({});

const toggleMenu = (menuName: string | undefined) => {
  if (!menuName) return;
  expandedMenus.value[menuName] = !expandedMenus.value[menuName];
};

const isExpanded = (menuName: string | undefined) => {
  if (!menuName) return false;
  return !!expandedMenus.value[menuName];
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <!-- Parent menu item -->
                <SidebarMenuItem>
                    <div class="flex items-center justify-between" v-if="item.children && item.children.length > 0">
                        <SidebarMenuButton 
                            as-child 
                            :is-active="item.href === page.url" 
                            :tooltip="item.title"
                            :has-submenu="true"
                            class="flex-grow"
                        >
                        <template v-if="item.href">
                            <Link :href="route(item.href)">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </template>
                        <template v-else>
                            <div class="flex items-center px-2 py-1.5 text-sm font-medium">
                                <component :is="item.icon" class="h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </div>
                        </template>
                    </SidebarMenuButton>
                        <button 
                            @click="toggleMenu(item.name)" 
                            class="p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none"
                            :aria-expanded="isExpanded(item.name)"
                            aria-label="Toggle menu"
                        >
                            <ChevronDown v-if="isExpanded(item.name)" class="h-4 w-4" />
                            <ChevronRight v-else class="h-4 w-4" />
                        </button>
                    </div>
                    <SidebarMenuButton 
                        v-else
                        as-child 
                        :is-active="item.href === page.url" 
                        :tooltip="item.title"
                    >
                        <template v-if="item.href">
                            <Link :href="route(item.href)">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </template>
                        <template v-else>
                            <div class="flex items-center px-2 py-1.5 text-sm font-medium">
                                <component :is="item.icon" class="h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </div>
                        </template>
                    </SidebarMenuButton>
                    
                    <!-- Child menu items if any -->
                    <SidebarMenu 
                        v-if="item.children && item.children.length > 0 && isExpanded(item.name)" 
                        class="ml-4 mt-1 transition-all duration-200 ease-in-out"
                    >
                        <SidebarMenuItem v-for="child in item.children" :key="child.title">
                            <SidebarMenuButton as-child :is-active="child.href === page.url" :tooltip="child.title">
                                <template v-if="child.href">
                                    <Link :href="route(child.href)">
                                        <component :is="child.icon" />
                                        <span>{{ child.title }}</span>
                                    </Link>
                                </template>
                                <template v-else>
                                    <div class="flex items-center px-2 py-1.5 text-sm font-medium">
                                        <component :is="child.icon" class="mr-2 h-4 w-4" />
                                        <span>{{ child.title }}</span>
                                    </div>
                                </template>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
