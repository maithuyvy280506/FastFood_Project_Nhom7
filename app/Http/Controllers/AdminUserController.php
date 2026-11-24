<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->whereIn('role', [1, 2])
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $nextUserCode = 'U'.str_pad((string) ((User::max('id_user') ?? 0) + 1), 3, '0', STR_PAD_LEFT);

        return view('admin.users.create', compact('nextUserCode'));
    }

    public function edit(int $id): View
    {
        $user = User::query()
            ->where('role', 2)
            ->findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => 2,
            'status' => 1,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Thêm nhân viên thành công!');
    }

    public function toggleStatus($id)
    {
        $user = User::query()
            ->where('role', 2)
            ->findOrFail($id);

        $user->status = (int) $user->status === 1 ? 0 : 1;
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Cập nhật trạng thái thành công!');
    }
}
