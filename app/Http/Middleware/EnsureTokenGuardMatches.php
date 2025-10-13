<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenGuardMatches
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard): Response
{
    
    $authHeader = $request->header('Authorization');
   
    if (!$authHeader) {
        return response()->json(['message' => 'Unauthenticated. No Authorization header.'], 401);
    }

    // ✅ Extract token string
    if (stripos($authHeader, 'Bearer ') === 0) {
        $token = trim(substr($authHeader, 7));
    } else {
        $token = trim($authHeader);
    }

    if (!$token) {
        return response()->json(['message' => 'Unauthenticated. Token missing.'], 401);
    }

    // ✅ Decode JWT payload
    $parts = explode('.', $token);
    if (count($parts) < 2) {
        return response()->json(['message' => 'Invalid token format.'], 401);
    }

    $payloadBase64 = $parts[1];
    $payloadJson   = base64_decode(strtr($payloadBase64, '-_', '+/'));
    $payload       = json_decode($payloadJson, true);

    if (!is_array($payload) || empty($payload['jti'])) {
        return response()->json(['message' => 'Invalid token payload (no jti).'], 401);
    }

    $jti = $payload['jti'];

    // ✅ Find token row in DB
    $tokenRow = DB::table('oauth_access_tokens')->where('id', $jti)->first();

    if (!$tokenRow) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // ✅ Check guard column
    if (empty($tokenRow->guard) || $tokenRow->guard !== $guard) {
        return response()->json(['message' => "Unauthenticated"], 401);
    }

    // ✅ Resolve user model dynamically from provider
    $provider = config("auth.guards.{$guard}.provider");
    $model    = config("auth.providers.{$provider}.model");

    $user = (new $model)::find($tokenRow->user_id);

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // ✅ Attach user to the guard
    Auth::guard($guard)->setUser($user);
    Auth::shouldUse($guard);

    // ✅ Attach token instance to user (so $user->token() works)
    $tokenModel = \Laravel\Passport\Token::find($jti);
    if ($tokenModel) {
        $user->withAccessToken($tokenModel);
    }

    return $next($request);
}

}
