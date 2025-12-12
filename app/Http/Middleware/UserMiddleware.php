<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\facades\Auth;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     if (Auth::check()) {
    //         if (Auth::user()->role_as == '0') //1=admin & 0 is to user
    //         {
    //             return $next($request);
    //         } else {
    //             return redirect('/home')->with('status', 'Access Denied! U r not user');
    //         }
    //     } else {
    //         return redirect('/login')->with('status', 'please log in first');
    //     }
    // }

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->role_as == '0') //1=admin & 0 is to user
            {
                return $next($request);
            } else {
                return redirect('/admin/dashboard')->with('status', 'Access Denied! U r not user');
            }
        } else {
            return redirect('/welcome')->with('status', 'please log in first');
        }
    }
}
