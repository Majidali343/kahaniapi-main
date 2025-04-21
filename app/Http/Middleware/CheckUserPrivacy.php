<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPrivacy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $currentUserAgent = $request->userAgent();
        
        // Retrieve the user's session from the database
        $session = DB::table('user_sessions')
            ->where('user_id', $user->id)
            ->where('user_agent', $currentUserAgent)
            ->first();
    
        // Check if the session exists and matches the current session ID
        if (!$session || $session->user_agent !== $currentUserAgent) {
          
            return response()->json(['message' => 'You have logged in on another device.'], 401);
        }
    
        return $next($request);
    }
}
