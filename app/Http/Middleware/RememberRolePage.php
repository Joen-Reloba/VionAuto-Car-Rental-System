<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RememberRolePage
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->user() || ! $request->isMethod('GET') || ! $response->isSuccessful()) {
            return $response;
        }

        $role = $request->user()->role;

        if ($role === 'admin' && $request->is('admin/*')) {
            $request->session()->put('last_admin_url', $request->fullUrl());
        }

        if ($role === 'staff' && $request->is('staff/*')) {
            $request->session()->put('last_staff_url', $request->fullUrl());
        }

        return $response;
    }
}
