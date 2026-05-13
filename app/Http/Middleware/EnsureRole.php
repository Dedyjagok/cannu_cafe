<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Memastikan user yang login memiliki role yang diizinkan.
     *
     * Penggunaan di route:
     *   ->middleware('role:owner')
     *   ->middleware('role:kasir')
     *   ->middleware('role:owner,kasir')   // salah satu role
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  Satu atau lebih role yang diizinkan
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Pastikan user aktif
        if (! $request->user()->is_active) {
            abort(403, 'Akun Anda dinonaktifkan. Hubungi owner.');
        }

        // Cek apakah role user termasuk dalam daftar role yang diizinkan
        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
