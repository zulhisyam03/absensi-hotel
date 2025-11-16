<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class ForgotPasswordBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-forgot-password-basic');
  }
  public function showResetForm($token)
  {
    return view('content.authentications.auth-reset-password', ['token' => $token]);
  }

  // Mengirim email reset password (via AJAX)
  public function sendResetLinkEmail(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
    ]);
    // Mengirim link reset menggunakan Laravel's Password facade
    $status = Password::sendResetLink(
      $request->only('email')
    );
    Log::channel('shift')->info('Status Kirim email: ' . $status . ' Kepada Email ' . $request->email);

    // Jika berhasil, kembalikan response JSON untuk AJAX
    if ($status === Password::RESET_LINK_SENT) {
      return response()->json([
        'success' => true,
        'message' => 'Link reset password telah dikirim ke email Anda. Periksa Inbox atau Spam pada Email'
      ]);
    }
    // Jika gagal, kembalikan error JSON (instead of throwing exception for easier JS handling)
    return response()->json([
      'success' => false,
      'message' => trans($status), // e.g., "We can't find a user with that email address."
    ], 422);
  }

  public function reset(Request $request)
  {
    $request->validate([
      'token' => 'required',
      'email' => 'required|email',
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);
    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function ($user, $password) {
        $user->forceFill([
          'password' => bcrypt($password)
        ])->setRememberToken(\Str::random(60));
        $user->save();
      }
    );
    if ($status === Password::PASSWORD_RESET) {
      return response()->json(['success' => true, 'message' => 'Password berhasil direset.']);
    }
    return response()->json(['success' => false, 'message' => trans($status)], 422);
  }
}