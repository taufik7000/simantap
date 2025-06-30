<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Menangani proses otentikasi untuk NIK atau Email.
     */
// ...
public function store(Request $request)
{
    // 1. Validasi untuk email dan password
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. Lakukan otentikasi dengan email dan password
    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        $user = Auth::user();
        $user->update([
             'last_login_at' => Carbon::now(),
             'last_login_ip' => $request->ip(),
        ]);
        return redirect()->to($user->getDashboardUrl());
    }

    // 3. Jika gagal, kembalikan dengan pesan error
    return back()->withErrors([
        'email' => 'Kombinasi email dan password tidak cocok.',
    ])->onlyInput('email');
}

    /**
     * Menangani proses logout.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}