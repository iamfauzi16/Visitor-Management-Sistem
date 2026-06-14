<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use App\Models\PositionEmployee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Employee::query()
            ->when($request->search, fn ($q, $v) =>
                $q->where('name', 'like', "%{$v}%")
                  ->orWhere('nik', 'like', "%{$v}%")
                  ->orWhere('email', 'like', "%{$v}%"))
            ->when($request->department, fn ($q, $v) => $q->where('department', $v))
            ->when($request->status, fn ($q, $v) => $q->where('is_active', $v === 'active'))
            ->latest();

        return Inertia::render('employees/Index', [
            'employees'   => $query->paginate(15)->withQueryString(),
            'filters'     => $request->only(['search', 'department', 'status']),
            'departments' => Employee::distinct()->orderBy('department')->pluck('department'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('employees/Create', [
            'positions' => PositionEmployee::where('status', 'active')
                ->orderBy('name_position')
                ->pluck('name_position'),
        ]);
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        Employee::create($request->validated());

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    // public function show(Employee $employee): Response
    // {
    //     return Inertia::render('employees/Show', [
    //         'employee' => $employee,
    //     ]);
    // }

    public function edit(Employee $employee): Response
    {
        return Inertia::render('employees/Edit', [
            'employee'  => $employee,
            'positions' => PositionEmployee::where('status', 'active')
                ->orderBy('name_position')
                ->pluck('name_position'),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan berhasil dihapus.');
    }

    public function export(Request $request)
    {
        return Excel::download(new EmployeeExport($request->all()), 'karyawan.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:2048']);

        Excel::import(new EmployeeImport, $request->file('file'));

        return redirect()->route('employees.index')
            ->with('success', 'Import data karyawan berhasil.');
    }
}
