<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Enterprise-level admin access protection.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🔐 1️⃣ Must be authenticated
        if (! Auth::check()) {
            return $this->unauthenticated($request);
        }

        /** @var User $user */
        $user = Auth::user();

        // 🚫 2️⃣ Blocked users cannot access admin
        if ($user->isBlocked()) {
            Auth::logout();

            return $this->blockedResponse($request);
        }

        // 🛡 3️⃣ Only admins allowed
        if (! $user->isAdmin()) {
            return $this->forbidden($request);
        }

        // ✅ 4️⃣ Access granted
        return $next($request);
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSES
    |--------------------------------------------------------------------------
    */

    protected function unauthenticated(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        return redirect()->route('login');
    }

    protected function blockedResponse(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your account has been blocked.'
            ], 403);
        }

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Your account has been blocked.'
            ]);
    }

    protected function forbidden(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Forbidden. Admin access required.'
            ], 403);
        }

        abort(403, 'Admin access only.');
    }
}
