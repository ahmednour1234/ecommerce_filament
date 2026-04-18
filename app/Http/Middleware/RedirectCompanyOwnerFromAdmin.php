<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectCompanyOwnerFromAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only redirect when hitting exactly /admin or /admin/ (no sub-pages)
        $path = rtrim($request->path(), '/');
        if (
            $user &&
            $user->type === \App\Models\User::TYPE_COMPANY_OWNER &&
            $path === 'admin'
        ) {
            return redirect()->route('owner.dashboard');
        }

        return $next($request);
    }
}
