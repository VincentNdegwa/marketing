<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { Boxes, LayoutGrid, Users, FileText, FolderTree, Settings } from 'lucide-vue-next';
import type { NavItem, SharedData } from '../types';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const menuItems = page.props.menu || [];

// Map icon strings to actual icon components
const iconMap = {
    'LayoutGrid': LayoutGrid,
    'Users': Users,
    'Boxes': Boxes,
    'FileText': FileText,
    'FolderTree': FolderTree,
    'Settings': Settings,
    'User': Users // Use Users icon for User until we import the specific User icon
};

const mainNavItems: NavItem[] = menuItems.map(item => ({
    title: item.title,
    href: item.href,
    icon: item.icon && iconMap[item.icon as keyof typeof iconMap] ? iconMap[item.icon as keyof typeof iconMap] : LayoutGrid,
}));

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
