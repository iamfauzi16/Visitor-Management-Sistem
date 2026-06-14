<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Employee
    {
        return new Employee([
            'nik'        => $row['nik'] ?? null,
            'name'       => $row['nama'],
            'department' => $row['departemen'],
            'position'   => $row['jabatan'],
            'email'      => $row['email'] ?? null,
            'phone'      => $row['no_hp'] ?? null,
            'is_active'  => true,
        ]);
    }
}
