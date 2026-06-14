<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionEmployeeRequest;
use App\Http\Requests\UpdatePositionEmployeeRequest;
use App\Models\PositionEmployee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PositionEmployeeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = PositionEmployee::query()
            ->when($request->search, fn ($q, $v) => $q->where('name_position', 'like', "%{$v}%"))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest();

        return Inertia::render('position-employees/Index', [
            'positions' => $query->paginate(15)->withQueryString(),
            'filters'   => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('position-employees/Create');
    }

    public function store(StorePositionEmployeeRequest $request): RedirectResponse
    {
        PositionEmployee::create($request->validated());

        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(PositionEmployee $position): Response
    {
        return Inertia::render('position-employees/Edit', [
            'position' => $position,
        ]);
    }

    public function update(UpdatePositionEmployeeRequest $request, PositionEmployee $position): RedirectResponse
    {
        $position->update($request->validated());

        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(PositionEmployee $position): RedirectResponse
    {
        $position->delete();

        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
