<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccessRole
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next, ...$roles)
  {
    $user = Auth::user();

    // Jika belum login atau tidak punya data pegawai
    if (!$user || !$user->pegawai) {
      abort(403, 'Access Denied');
    }

    $jabatan = strtolower($user->pegawai->jabatan);
    $allowedRoles = array_map('strtolower', $roles);

    if (!in_array($jabatan, $allowedRoles)) {
      abort(403, 'Access Denied');
    }

    return $next($request);
  }
}