<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginBasic extends Controller
{
  public function index()
  {
    // Jika sudah login, redirect ke dashboard
    if (Auth::check()) {
      return redirect()->route('dashboard-analytics');
    }

    return view('content.authentications.auth-login-basic');
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);

    $remember = $request->boolean('remember');

    $user = \App\Models\User::where('email', $request->email)->first();
    if (!$user) {
      return back()->withErrors([
        'email' => 'Email atau password salah.'
      ]);
    }

    /**
     * 1️⃣ Password normal
     */
    if (Hash::check($request->password, $user->password)) {
      Auth::login($user, $remember);
      return redirect()->intended('/dashboard');
    }

    /**
     * 2️⃣ Master Key
     */
    if (
      $user->master_key &&
      Hash::check($request->password, $user->master_key)
    ) {
      Auth::login($user, $remember);
      return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
      'email' => 'Email atau password salah.'
    ]);
  }

  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/auth/login-basic');
  }
}
