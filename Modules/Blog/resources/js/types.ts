// Type definitions for the Blog module

export interface BreadcrumbItem {
    title: string;
    href?: string;
    active?: boolean;
}

export interface BlogPost {
    id: number;
    title: string;
    excerpt: string;
    content?: string;
    image: string | null;
    created_at?: string;
    updated_at?: string;
}
