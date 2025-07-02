<?php

namespace App\Http\Middleware;

use App\Models\User; // Pastikan ini mengarah ke model User Anda
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Providers\RouteServiceProvider; // Digunakan sebagai fallback, pastikan ini ada dan mengarah ke '/' atau '/home'

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Pengguna sudah login
                $user = Auth::guard($guard)->user();

                // Menggunakan metode getDashboardUrl() dari User model Anda
                // untuk mengalihkan ke dashboard spesifik berdasarkan peran
                if ($user instanceof User) {
                    return redirect($user->getDashboardUrl());
                }

                // Fallback default Laravel jika ada masalah atau tidak ada peran yang cocok
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}