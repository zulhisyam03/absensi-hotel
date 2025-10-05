<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
      // Authentication passed...
      return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
      'email' => 'Email atau password salah.',
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
