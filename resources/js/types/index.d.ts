import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string|null;
    icon?: LucideIcon;
    isActive?: boolean;
    name?: string;
    parent?: string | null;
    order?: number;
    ignore_if?: string[];
    depend_on?: string[];
    module?: string;
    permission?: string;
    children?: NavItem[];
}
export interface ServerNavItem {
    title: string;
    href: string|null;
    icon?: string;
    isActive?: boolean;
    name?: string;
    parent?: string | null;
    order?: number;
    ignore_if?: string[];
    depend_on?: string[];
    module?: string;
    permission?: string;
}


export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    menu: ServerNavItem[];
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    user_type: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
