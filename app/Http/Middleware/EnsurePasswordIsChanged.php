<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->role === 'student' && ! $user->is_password_changed) {
            if (! $request->routeIs('student.password.*') && ! $request->routeIs('student.logout')) {
                return redirect()->route('student.password.change')->with('error', 'Please change your default password before continuing.');
            }
        }

        return $next($request);
    }
}
