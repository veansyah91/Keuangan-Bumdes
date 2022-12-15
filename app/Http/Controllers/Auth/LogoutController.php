<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // dd(Auth::user()->tokens());
        // Auth::user()->tokens()->where('id', Auth::user()->currentAccessToken()->id)->delete();

        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();

        
    
        return redirect('/');
    }
}
