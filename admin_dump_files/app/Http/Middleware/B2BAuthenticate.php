<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class B2BAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
    
        $guard = Auth::guard('master')->check() ? 'master' : (Auth::guard('zone')->check() ? 'zone' : null);

        if (!$guard) {
            return redirect('/b2b/login');
        }

        $user = Auth::guard($guard)->user();

    
        if ($user->status !== 1) {
            Auth::guard($guard)->logout();
            return redirect('/b2b/login')->withErrors(['account' => 'Your account is inactive.']);
        }

        return $next($request);
    }
}
