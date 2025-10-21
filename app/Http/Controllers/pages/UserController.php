<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Untuk mengakses user yang sedang login
use Illuminate\Support\Facades\Hash; // Untuk hashing password

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
    return view('content.config.user');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request)
  {
    //
    // Validasi input
    $request->validate([
      'password_baru' => 'required|min:8|confirmed', // 'confirmed' akan otomatis cek dengan 'ver_password_baru'
    ]);
    // Ambil user yang sedang login
    $user = Auth::user();
    // Hash dan simpan password baru
    $user->password = Hash::make($request->password_baru);
    $user->save();
    // Kembalikan respons JSON untuk AJAX
    return response()->json([
      'success' => true,
      'message' => 'Password berhasil diubah. Silahkan Login Kembali'
    ]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}