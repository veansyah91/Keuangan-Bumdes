<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class RolesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('DEV')) {
            $roles = Role::all();
            return view('admin.roles', [
                'roles' => $roles
            ]);
        }

        abort(403);

        
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required',
        ]);

        $role = Role::create(['name' => strtoupper($request->role)]);
        
        return redirect('/roles')->with('success', 'Role Berhasil Ditambahkan');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect('/roles')->with('success', 'Role Berhasil Dihapus');
    }
}
