import type { BreadcrumbItem } from '@/types';

export interface Post {
  id: number;
  title: string;
  content: string;
  excerpt: string;
  image?: string;
  created_at: string;
  updated_at: string;
}

export { BreadcrumbItem };
