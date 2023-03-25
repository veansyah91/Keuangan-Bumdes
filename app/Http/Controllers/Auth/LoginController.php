<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\BusinessUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            if (Auth::user()->hasRole('ADMIN')) {
                return redirect()->route('admin.dashboard');
            }
            $businessUser = BusinessUser::where('user_id', Auth::user()->id)->first();

            return redirect()->route('business.dashboard', [
                'business' => $businessUser['business_id']
            ]);
        }
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
                if (Auth::user()->hasRole('DEV')) {
                    return redirect()->route('users.index');
                }
                return redirect()->route('admin.dashboard', [
                    'token' => $token
                ])->with('login', 'Berhasil Login');
            }

            $businessUser = BusinessUser::where('user_id', Auth::user()->id)->first();

            return redirect()->route('business.dashboard', [
                'token' => $token,
                'business' => $businessUser['business_id']
            ])->with('login', 'Berhasil Login');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);       
        
    }
}
