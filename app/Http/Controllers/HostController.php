<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHostVisitorRequest;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HostController extends Controller
{
    private function resolveEmployeeId(): int
    {
        $employeeId = auth()->user()->employee_id;
        abort_if(is_null($employeeId), 403, 'Akun Anda belum ditautkan ke data karyawan. Hubungi admin.');
        return $employeeId;
    }

    public function index(Request $request): Response
    {
        $employeeId = auth()->user()->employee_id;

        if (is_null($employeeId)) {
            return Inertia::render('host/Index', [
                'visitors'   => null,
                'filters'    => [],
                'noEmployee' => true,
            ]);
        }

        $query = Visitor::where('employee_id', $employeeId)
            ->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%")
                ->orWhere('company', 'like', "%{$v}%"))
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

        return Inertia::render('host/Index', [
            'visitors'   => $query->paginate(15)->withQueryString(),
            'filters'    => $request->only(['search', 'date', 'status']),
            'noEmployee' => false,
        ]);
    }

    public function create(): Response
    {
        $this->resolveEmployeeId();

        return Inertia::render('host/Create');
    }

    public function store(StoreHostVisitorRequest $request): RedirectResponse
    {
        $employeeId = $this->resolveEmployeeId();

        Visitor::create([
            ...$request->validated(),
            'employee_id' => $employeeId,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('host.index')
            ->with('success', 'Tamu berhasil didaftarkan.');
    }

    public function destroy(Visitor $visitor): RedirectResponse
    {
        $employeeId = $this->resolveEmployeeId();

        abort_if($visitor->employee_id !== $employeeId, 403, 'Anda tidak berhak membatalkan undangan ini.');
        abort_if(!is_null($visitor->checkin_at), 422, 'Tidak dapat membatalkan tamu yang sudah check in.');

        $visitor->delete();

        return redirect()->route('host.index')
            ->with('success', 'Undangan tamu dibatalkan.');
    }
}
