<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::query()
            ->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%")
                ->orWhere('email', 'like', "%{$v}%"))
            ->when($request->role, fn ($q, $v) => $q->where('role', $v))
            ->latest();

        return Inertia::render('users/Index', [
            'users'   => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/Create', [
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department']),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('password_confirmation');

        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('users/Edit', [
            'user'      => $user,
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department']),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->except('password_confirmation');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
