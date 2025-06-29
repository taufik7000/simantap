<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
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

        // Jika pengguna memiliki peran yang diizinkan, lanjutkan.
        if ($user && $user->hasAnyRole($roles)) {
            return $next($request);
        }

        // --- Logika Pengalihan (Redirect) Dimulai di Sini ---

        // Jika tidak diizinkan, periksa perannya dan arahkan.
        if ($user && $user->hasRole('admin')) {
            // Jika dia admin, arahkan ke panel admin.
            return redirect(Filament::getPanel('admin')->getUrl());
        }
        
        if ($user && $user->hasRole('petugas')) {
            // Jika dia petugas, arahkan ke panel petugas.
            return redirect(Filament::getPanel('petugas')->getUrl());
        }

        // Untuk kasus lain (misal: warga mencoba akses panel petugas),
        // tampilkan error 403 Forbidden.
        abort(403, 'Anda tidak memiliki hak akses untuk panel ini.');
    }
}