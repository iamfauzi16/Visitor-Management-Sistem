<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function query()
    {
        return Employee::query()
            ->when($this->filters['search'] ?? null, fn ($q, $v) =>
                $q->where('name', 'like', "%{$v}%")
                  ->orWhere('email', 'like', "%{$v}%"))
            ->when($this->filters['department'] ?? null, fn ($q, $v) =>
                $q->where('department', $v))
            ->when(isset($this->filters['status']), fn ($q) =>
                $q->where('is_active', $this->filters['status'] === 'active'));
    }

    public function headings(): array
    {
        return ['ID', 'NIK', 'Nama', 'Departemen', 'Jabatan', 'Email', 'No. HP', 'Status'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nik,
            $row->name,
            $row->department,
            $row->position,
            $row->email,
            $row->phone,
            $row->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}
