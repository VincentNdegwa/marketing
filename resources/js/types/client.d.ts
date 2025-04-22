interface Pivot {
    user_id: number;
    business_id: number;
    is_default: boolean;
    created_at: string;
    updated_at: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    mobile: string | null;
    avatar: string | null;
    job_title: string | null;
    bio: string | null;
    address_line1: string | null;
    address_line2: string | null;
    city: string | null;
    state: string | null;
    postal_code: string | null;
    country: string | null;
    date_of_birth: string | null;
    gender: string | null;
    website: string | null;
    social_links: string | null;
    email_verified_at: string | null;
    is_active: boolean;
    last_login_at: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    pivot?: Pivot;
    admin_businesses?: Business[];
    default_business_id?: number | null;
    user_type?: string;
    roles?: Role[];
    businesses?: Business[];
}

interface Role {
    id: number;
    name: string;
    display_name: string;
    description: string;
    business_id: number;
    created_at: string;
    updated_at: string;
    pivot: {
        user_type: string;
        user_id: number;
        role_id: number;
    };
}

interface Business {
    id: number;
    name: string;
    slug: string;
    description: string;
    logo: string | null;
    email: string;
    phone: string | null;
    address: string | null;
    city: string | null;
    state: string | null;
    country: string | null;
    zip_code: string | null;
    website: string | null;
    is_active: number;
    created_at: string;
    updated_at: string;
    pivot?: Pivot;
    users?: User[];
}