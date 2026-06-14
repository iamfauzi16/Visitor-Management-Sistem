<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\PositionEmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::resource('employees', EmployeeController::class);

    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    Route::resource('positions', PositionEmployeeController::class)->except(['show']);

    Route::resource('users', UserController::class)->except(['show']);
});

Route::middleware(['auth', 'role:host'])->prefix('host')->name('host.')->group(function () {
    Route::get('/', [HostController::class, 'index'])->name('index');
    Route::get('/create', [HostController::class, 'create'])->name('create');
    Route::post('/', [HostController::class, 'store'])->name('store');
    Route::delete('/{visitor}', [HostController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('visitors/create', [VisitorController::class, 'create'])->name('visitors.create');
    Route::post('visitors', [VisitorController::class, 'store'])->name('visitors.store');
    Route::get('visitors', [VisitorController::class, 'index'])->name('visitors.index');
    Route::get('visitors/{visitor}', [VisitorController::class, 'show'])->name('visitors.show');
    Route::patch('visitors/{visitor}/checkin', [VisitorController::class, 'checkIn'])->name('visitors.checkin');
    Route::patch('visitors/{visitor}/checkout', [VisitorController::class, 'checkOut'])->name('visitors.checkout');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
;