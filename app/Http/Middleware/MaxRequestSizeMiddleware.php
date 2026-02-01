<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaxRequestSizeMiddleware
{
    protected int $maxSize = 5242880;

    public function handle(Request $request, Closure $next): Response
    {
        $contentLength = $request->header('Content-Length');

        if ($contentLength && (int) $contentLength > $this->maxSize) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request entity too large',
            ], 413);
        }

        return $next($request);
    }
}
