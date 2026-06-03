<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login (ganti 'user_id' sesuai session milikmu)
        if (!$request->session()->has('c_employee_id')) {

            // Simpan URL yang ingin diakses (kecuali halaman login agar tidak loop)
            if (!$request->is('login')) {
                Session::put('redirect_after_login', $request->fullUrl());
            }

            // Redirect ke halaman login
            return redirect()->route('auth.index')->with([
                'status' => 'info',
                'message' => 'Please Login to proceed'
            ]);
        }

        return $next($request);
    }
}
