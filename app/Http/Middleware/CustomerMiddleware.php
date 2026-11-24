<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('user_id')) {
            return redirect()->route('login');
        }

        if ((int) session('user_role') !== 0) {
            abort(403, 'Chỉ khách hàng mới được thao tác');
        }

        return $next($request);
    }
}
