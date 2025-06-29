<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PanelAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Jika pengguna memiliki peran yang diizinkan untuk panel ini, lanjutkan.
        if ($user && $user->hasAnyRole($roles)) {
            return $next($request);
        }

        // Jika tidak diizinkan, arahkan pengguna ke dasbornya sendiri.
        // Cukup panggil fungsi yang sudah kita buat di model User.
        return redirect($user->getDashboardUrl());
    }
}