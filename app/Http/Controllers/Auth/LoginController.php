<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $token = Auth::user()->createToken('web-token')->plainTextToken;
            

            if (Auth::user()->hasRole('DEV') || Auth::user()->hasRole('ADMIN')) {
                return redirect()->route('admin.dashboard', [
                    'token' => $token
                ])->with('login', 'Berhasil Login');
            }
 
            
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);       
        
    }
}
