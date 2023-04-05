<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $token = Auth::user()->tokens()->where('id', $request->token)->first();
        
        if ($token) {
            $token->delete();
        }

        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();

        
    
        return redirect('/');
    }
}
