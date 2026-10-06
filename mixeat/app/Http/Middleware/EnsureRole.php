<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        $isLegacyMarketingAdmin = $user
            && $user->is_admin
            && in_array('marketing', $roles, true);
        if (! $user || (! in_array($user->role, $roles, true) && ! $isLegacyMarketingAdmin)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}