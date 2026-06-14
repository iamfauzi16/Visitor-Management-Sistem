<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitorRequest;
use App\Models\Employee;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VisitorController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Visitor::with('employee:id,name,department')
            ->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%")
                ->orWhere('company', 'like', "%{$v}%"))
            ->when($request->employee_id, fn ($q, $v) => $q->where('employee_id', $v))
            ->when($request->date, fn ($q, $v) => $q->whereDate('created_at', $v))
            ->when($request->status, function ($q, $v) {
                match ($v) {
                    'registered'  => $q->whereNull('checkin_at'),
                    'checked_in'  => $q->whereNotNull('checkin_at')->whereNull('checkout_at'),
                    'checked_out' => $q->whereNotNull('checkout_at'),
                    default       => null,
                };
            })
            ->latest();

        return Inertia::render('visitors/Index', [
            'visitors'  => $query->paginate(15)->withQueryString(),
            'filters'   => $request->only(['search', 'employee_id', 'date', 'status']),
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('visitors/Create', [
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department', 'position']),
        ]);
    }

    public function store(StoreVisitorRequest $request): RedirectResponse
    {
        Visitor::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('visitors.index')
            ->with('success', 'Tamu berhasil didaftarkan.');
    }

    public function show(Visitor $visitor): Response
    {
        $visitor->load('employee:id,name,department,position', 'creator:id,name');

        return Inertia::render('visitors/Show', [
            'visitor' => $visitor,
        ]);
    }

    public function checkIn(Visitor $visitor): RedirectResponse
    {
        abort_if(!is_null($visitor->checkin_at), 422, 'Tamu sudah check in.');

        $visitor->update(['checkin_at' => now()]);

        return back()->with('success', 'Check in berhasil.');
    }

    public function checkOut(Visitor $visitor): RedirectResponse
    {
        abort_if(is_null($visitor->checkin_at), 422, 'Tamu belum check in.');
        abort_if(!is_null($visitor->checkout_at), 422, 'Tamu sudah check out.');

        $visitor->update(['checkout_at' => now()]);

        return back()->with('success', 'Check out berhasil.');
    }
}
