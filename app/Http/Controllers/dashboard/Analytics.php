<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Absen;
use App\Models\ShiftKerja;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class Analytics extends Controller
{
  public function index()
  {
    // ✅ Jalankan command sebelum load dashboard
    // Artisan::call('shift:cek');

    // Ambil user login
    $user = Auth::user();

    // Ambil data pegawai terkait user
    $pegawai = $user->pegawai;

    $statusAbsen = 'Check In';
    $shiftAktif = '';
    $waktuShiftAktif = '';
    $listShift = '';

    try {
      $absen = Absen::where('no_pegawai', $pegawai->no_pegawai)
        ->whereNotNull('check_in')
        ->whereNull('check_out')
        ->where('keterangan', '!=', 'tco')
        ->latest()
        ->first();

      if ($absen) {
        // 🔹 Pegawai sedang aktif shift dan belum check-out
        $shiftAktif = $absen['shift'];
        $waktuShiftAktif = ' ( ' . Carbon::parse($absen->shift_masuk)->format('H:i') . ' - ' . Carbon::parse($absen->shift_pulang)->format('H:i') . ' )';
        $statusAbsen = 'Check Out';
        Log::channel('shift')->info('Shift Aktif : ' . $shiftAktif); // Untuk melihat semua atribut
        Log::channel('shift')->info('Status Absen : Ready Check Out'); // Untuk melihat semua atribut
      } else {
        $listShift = ShiftKerja::where('no_pegawai', $pegawai->no_pegawai)
          ->where('flag', '!=', 'f')
          ->get();
      }

    } catch (\Exception $e) {
      // Bisa tambahkan log jika dibutuhkan
      Log::error($e->getMessage());
    }

    Log::channel('shift')->info('========================================================================================'); // Untuk melihat semua atribut

    return view('content.dashboard.dashboards-analytics', compact('user', 'pegawai', 'listShift', 'shiftAktif', 'waktuShiftAktif', 'statusAbsen'));
  }
}
