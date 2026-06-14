<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = today();
        $base  = Visitor::whereDate('created_at', $today);

        return Inertia::render('Dashboard', [
            'total_today'    => (clone $base)->count(),
            'checked_in'     => (clone $base)->whereNotNull('checkin_at')->whereNull('checkout_at')->count(),
            'checked_out'    => (clone $base)->whereNotNull('checkout_at')->count(),
            'visitors_today' => (clone $base)->with('employee:id,name,department')->latest()->get(),
        ]);
    }
}
