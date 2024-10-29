<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceInteraction;

class LogServiceInteraction {
    public function handle(Request $request, Closure $next) {
        
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        
        $ipAddress = $request->ip();
        
        $response = $next($request);

        ServiceInteraction::create([
            'user_id' => $userId,
            'service' => $request->fullUrl(),
            'request_body' => json_encode($request->all()),
            'response_code' => $response->getStatusCode(),
            'response_body' => $response->getContent(),
            'ip_address' => $ipAddress,
        ]);

        return $response;
    }
}
