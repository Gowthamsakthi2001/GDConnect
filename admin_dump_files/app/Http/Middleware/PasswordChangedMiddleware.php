<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PasswordChangedMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Cast to Carbon manually if not already datetime
            $lastChanged = $user->password_changed_at
            ? Carbon::parse($user->password_changed_at)
            : null;

            $lastLogin   = session('password_checked_at')
                ? Carbon::parse(session('password_checked_at'))
                : null;
                
            // Log::info("Global web route called");
            // Log::info("Global web route called User ".json_encode($user)); 
            // Log::info("User Database Update Date ".$user->password_checked_at);
            // Log::info("User Login Update Date ".$lastChanged);
            // dd($user,$lastChanged,$lastLogin);

            if ($lastChanged && (!$lastLogin || $lastChanged->gt($lastLogin))) {
                Auth::logout();

               return redirect()->route('login')
               ->with('success', 'Logout Successfully!');

            }
        }

        return $next($request);
    }
}
