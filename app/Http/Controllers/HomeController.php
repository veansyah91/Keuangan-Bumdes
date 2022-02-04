<?php

namespace App\Http\Controllers;

use App\Models\BusinessUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('ADMIN|DEV')) {
            return redirect('/admin');
        }

        // cek unit usaha yang dimiliki user
        $businessUser = BusinessUser::where('user_id', $user['id'])->first();
        if ($businessUser) {
            return redirect('/' . $businessUser['business_id'] .'/dashboard');
        }

        return view('home');
    }
}
