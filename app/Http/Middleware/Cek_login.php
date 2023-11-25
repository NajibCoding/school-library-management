<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Modul;
use Illuminate\Http\Request;
use App\Service\RoleServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Cek_login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('auth/login');
        }
        return $next($request);
    }
}
