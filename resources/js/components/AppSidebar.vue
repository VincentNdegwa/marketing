<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Boxes, LayoutGrid, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const user_type = page.props.auth.user.user_type;

const mainNavItems: NavItem[] = [];
if (user_type == 'Super Admin') {
    mainNavItems.push(
        {
            title: 'Dashboard',
            href: 'dashboard.admin',
            icon: LayoutGrid,
        },
        {
            title: 'Clients',
            href: 'clients.index',
            icon: Users,
        },
        {
            title: 'Modules',
            href: 'dashboard.admin',
            icon: Boxes,
        },
    );
} else {
    mainNavItems.push({
        title: 'Dashboard',
        href: 'dashboard.admin',
        icon: LayoutGrid,
    });
}

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
