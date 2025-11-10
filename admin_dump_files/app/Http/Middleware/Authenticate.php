<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    // protected function redirectTo($request)
    // {
        
    //     if (! $request->expectsJson()) {
            
    //         if ($request->is('customer/ticket-portal/*')) {
    //             return url('/ticket-portal/login');
    //         }
            
    //         return route('login');
    //     }
    // }
    
    protected function redirectTo($request) //updated by Gowtham.S
    {
        if (! $request->expectsJson()) {
            
            if ($request->is('customer/ticket-portal/*')) {
                return url('/ticket-portal/login');
            }
            
            $guard = Auth::guard('web')->check();
            
            if(!$guard){
                Auth::guard('web')->logout();
                return route('login');

            }
            return route('login');
        }

    }
 
}
