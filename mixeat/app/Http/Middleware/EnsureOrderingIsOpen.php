<?php

namespace App\Http\Middleware;

use App\Services\OrderingHours;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrderingIsOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app(OrderingHours::class)->isOpen()) {
            $message = (string) config('ordering.closed_message');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'errors' => ['ordering_hours' => [$message]],
                ], 422);
            }

            throw ValidationException::withMessages([
                'ordering_hours' => $message,
            ]);
        }

        return $next($request);
    }
}
