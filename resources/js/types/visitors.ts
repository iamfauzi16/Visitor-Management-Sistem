import type { Employee, Visitor } from '@/types';

export interface VisitorRow extends Visitor {
    employee: Pick<Employee, 'id' | 'name' | 'department' | 'position'>;
}

export interface VisitorFilters {
    search?: string;
    employee_id?: string;
    date?: string;
    status?: string;
}
