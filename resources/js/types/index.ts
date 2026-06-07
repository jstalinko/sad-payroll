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
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: {
        location: string;
        url: string;
        port: null | number;
        defaults: Record<string, unknown>;
        routes: Record<string, string>;
    };
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Karyawan {
    id: number;
    staff_id: string;
    name: string;
    position: string;
    salary: number;
    bank_name: string;
    bank_account: string;
    bank_account_name: string;
    npwp: string | null;
    phone: string | null;
    created_at: string;
    updated_at: string;
}

export interface Slip {
    id: number;
    karyawan_id: number;
    karyawan?: Karyawan;
    periode_start: string;
    periode_end: string;
    total_salary: number;
    total_deduction: number;
    basic_salary: number;
    overtime_salary: number;
    meal_allowance: number;
    transportation_allowance: number;
    bonus_salary: number;
    bonus_notes: string | null;
    late_deduction: number;
    absence_deduction: number;
    damaged_cost: number;
    other_deduction: number;
    other_deduction_notes: string | null;
    status: 'PAID' | 'DRAFT' | 'OVERDUE' | 'UNPAID' | 'PARTIALLY_PAID';
    paid_at: string | null;
    paid_by: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
