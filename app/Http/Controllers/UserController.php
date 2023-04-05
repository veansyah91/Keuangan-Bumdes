<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Business;
use App\Models\BusinessUser;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::all(),
            'businesses' => Business::all(),
            'roles' => Role::where('name', '<>', 'DEV')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $thisUser = Auth::user();

        if ($thisUser->getRoleNames()[0] == 'DEV') {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('ADMIN');

            return redirect('/users')->with('success', 'Pengguna Berhasil Ditambahkan');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        if ($request->business != 0) {
            $business_user = BusinessUser::create([
                'business_id' => $request->business,
                'user_id' => $user['id'],
            ]);
        }

        return redirect('/users')->with('success', 'Pengguna Berhasil Ditambahkan');
        
    }

    public function resetPassword(User $user, Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'is_default' => false
        ]);

        return redirect('/users')->with('success', 'Password Berhasil Diubah');

    }

    public function resetRole(User $user, Request $request)
    {
        $user->syncRoles($request->role);

        $businessUser = BusinessUser::where('user_id', $user['id'])->first();

        if ($request->role == "ADMIN") {
            if ($businessUser) {
                $businessUser->delete();
            }
        } else {
            if ($businessUser) {
                $businessUser->update([
                    'business_id' => $request->business
                ]);
            } else {
                BusinessUser::create([
                    'user_id' => $user['id'],
                    'business_id' => $request->business
                ]);
            }
            
        }

        return redirect('/users')->with('success', 'Data User Berhasil Diubah');        
    }

    public function changePassword()
    {
        $user = Auth::user();
        if ($user->getRoleNames()[0] == "ADMIN") {
            return view('admin.users.change-password');
        }

        return view('auth.passwords.reset', ["user" => $user]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);
        
        $update = $user->update([
            'password' => Hash::make($request->password),
            'is_default' => false
        ]);

        if ($user->getRoleNames()[0] == "ADMIN") {
            return redirect('/identity')->with('success', 'Password Berhasil Diubah');
        }

        return redirect('home');

    }

    public function delete(User $user)
    {
        $user->delete();

        return redirect('/users');
    }
}
