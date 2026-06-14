<?php

namespace App\Http\Controllers;

use App\Exports\VisitorExport;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['date_from', 'date_to', 'status']);

        $visitors = Visitor::with('employee:id,name,department')
            ->when($filters['date_from'] ?? null, fn ($q, $v) =>
                $q->whereDate('created_at', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) =>
                $q->whereDate('created_at', '<=', $v))
            ->when($filters['status'] ?? null, function ($q, $v) {
                match ($v) {
                    'registered'  => $q->whereNull('checkin_at'),
                    'checked_in'  => $q->whereNotNull('checkin_at')->whereNull('checkout_at'),
                    'checked_out' => $q->whereNotNull('checkout_at'),
                    default       => null,
                };
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('reports/Index', [
            'visitors' => $visitors,
            'filters'  => $filters,
        ]);
    }

    public function export(Request $request)
    {
        $filters   = $request->only(['date_from', 'date_to', 'status']);
        $from      = $filters['date_from'] ?? 'all';
        $to        = $filters['date_to'] ?? 'all';
        $filename  = "laporan-tamu_{$from}_sd_{$to}.xlsx";

        return Excel::download(new VisitorExport($filters), $filename);
    }
}
