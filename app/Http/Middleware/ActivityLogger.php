<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogger
{
    /**
     * Middleware ini berjalan setelah response — log aksi penting saja.
     * Logging detail dilakukan di Service Layer untuk kontrol penuh.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        // Bisa digunakan untuk global logging jika diperlukan
        // Namun untuk project ini, logging dilakukan di Service Layer
    }
}