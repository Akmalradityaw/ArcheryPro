<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Support\DataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => DataTable::paginate(
                User::query(),
                ['username', 'email'],
                ['username' => 'username', 'email' => 'email', 'role' => 'role', 'dibuat' => 'created_at']
            ),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());
        $user->assignRole($request->role);

        return redirect('/admin/users')->with('success', "User {$user->username} dibuat.");
    }

    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect('/admin/users')->with('success', "User {$user->username} diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 403, 'Tidak bisa menghapus akun sendiri.');
        $user->delete();

        return redirect('/admin/users')->with('success', "User {$user->username} dihapus.");
    }

    public function resetPassword(User $user): RedirectResponse
    {
        // ponytail: password acak sekali-tampil via flash; tanpa kolom tambahan / email
        $baru = Str::random(8);
        $user->update(['password' => $baru]);

        return redirect('/admin/users')->with('success', "Password {$user->username} direset menjadi: {$baru}");
    }

    public function resetToken(User $user): RedirectResponse
    {
        $user->update(['token' => Str::random(20)]);

        return redirect('/admin/users')->with('success', "Token {$user->username} diperbarui.");
    }
}
