<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { Boxes, LayoutGrid, Users, FileText, FolderTree, Settings, KeyRound, UserCheck } from 'lucide-vue-next';
import type { NavItem, SharedData } from '../types';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const menuItems = page.props.menu || [];
const user_type = page.props.user_type || '';

const iconMap = {
    'LayoutGrid': LayoutGrid,
    'Users': Users,
    'Boxes': Boxes,
    'FileText': FileText,
    'FolderTree': FolderTree,
    'Settings': Settings,
    'User': UserCheck,
    'KeyRound':KeyRound
};

const transformedMenuItems: NavItem[] = menuItems.map(item => ({
    title: item.title,
    href: item.href,
    icon: item.icon && iconMap[item.icon as keyof typeof iconMap] ? iconMap[item.icon as keyof typeof iconMap] : LayoutGrid,
    name: item.name,
    parent: item.parent,
    order: item.order,
    ignore_if: item.ignore_if,
    depend_on: item.depend_on,
    module: item.module,
    permission: item.permission,
    isActive: false,
    children: [] as NavItem[]
}));

const menuItemsMap = new Map<string, NavItem>();

transformedMenuItems.forEach(item => {
    if (item.name) {
        menuItemsMap.set(item.name, { ...item, children: [] });
    }
});

const mainNavItems: NavItem[] = [];
transformedMenuItems.forEach(item => {
    if (!item.parent) {
        mainNavItems.push(menuItemsMap.get(item.name || '') || item);
    } else {
        const parent = menuItemsMap.get(item.parent);
        if (parent && parent.children) {
            parent.children.push(item);
        } else {
            mainNavItems.push(item);
        }
    }
});

// Sort by order property
mainNavItems.sort((a, b) => (a.order || 0) - (b.order || 0));

// Sort children by order property
mainNavItems.forEach(item => {
    if (item.children && item.children.length > 0) {
        item.children.sort((a, b) => (a.order || 0) - (b.order || 0));
    }
});

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits',
    //     icon: BookOpen,
    // },
];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route(user_type=='Super Admin'?'dashboard.admin':'dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
