<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->hasRole('DEV')) {
            return $next($request);
        }
        
        $subscribe = Subscribe::first();
        $now = Date('Y-m-d');
        if (!$subscribe || $now > $subscribe['due_date']) {
            return redirect('over-due');
        }
        return $next($request);
    }
}
