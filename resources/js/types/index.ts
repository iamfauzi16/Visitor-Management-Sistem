import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    url: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface NavGroup {
    label: string;
    items: NavItem[];
}

export interface SiteSettings {
    site_name: string;
    primary_color: string;
    logo_path: string | null;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    flash: {
        success: string | null;
        error: string | null;
    };
    siteSettings: SiteSettings;
    ziggy: {
        location: string;
        url: string;
        port: null | number;
        defaults: Record<string, unknown>;
        routes: Record<string, string>;
    };
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'receptionist' | 'host';
    employee_id: number | null;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: { url: string | null; label: string; active: boolean }[];
}

export interface Employee {
    id: number;
    nik: string | null;
    name: string;
    department: string;
    position: string;
    email: string | null;
    phone: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export type VisitorStatus = 'registered' | 'checked_in' | 'checked_out';

export interface Visitor {
    id: number;
    name: string;
    id_type: 'ktp' | 'sim' | 'passport' | null;
    id_number: string | null;
    company: string | null;
    phone: string | null;
    email: string | null;
    employee_id: number;
    employee?: Pick<Employee, 'id' | 'name' | 'department' | 'position'>;
    purpose: string | null;
    vehicle_number: string | null;
    badge_number: string | null;
    checkin_at: string | null;
    checkout_at: string | null;
    created_by: number;
    creator?: Pick<User, 'id' | 'name'>;
    status: VisitorStatus;
    status_label: string;
    created_at: string;
    updated_at: string;
}

export type EmployeeOption = Pick<Employee, 'id' | 'name' | 'department' | 'position'>;

export interface PositionEmployee {
    id: number;
    name_position: string;
    status: 'active' | 'inactive';
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
