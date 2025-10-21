<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Analytics extends Controller
{
  public function index()
  {
    // Ambil user login
    $user = Auth::user();

    // Ambil data pegawai terkait user
    $pegawai = $user->pegawai;

    // Inisialisasi variabel shift kosong
    $shiftAktif = collect();

    if ($pegawai) {
      // Ambil waktu sekarang (format H:i)
      $now = Carbon::now()->format('H:i:s');

      // Ambil shift berdasarkan no_pegawai dan waktu aktif
      $shiftAktif = $pegawai->shift()
        ->where('waktu_masuk', '<=', $now)
        ->where('waktu_pulang', '>=', $now)
        ->firstOrFail();
    }

    // Kirim ke view
    return view('content.dashboard.dashboards-analytics', compact('user', 'pegawai', 'shiftAktif'));
  }
}