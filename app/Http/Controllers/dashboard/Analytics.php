<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Absen;
use App\Models\ShiftKerja;
use Illuminate\Support\Facades\Artisan;

class Analytics extends Controller
{
  public function index()
  {
    // ✅ Jalankan command sebelum load dashboard
    Artisan::call('shift:cek');

    // Ambil user login
    $user = Auth::user();

    // Ambil data pegawai terkait user
    $pegawai = $user->pegawai;

    $statusAbsen = 'Check In';
    $shiftAktif = '';

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
      } else {
        // 🔹 Jika tidak ada absen aktif, cari shift berikutnya berdasarkan waktu sekarang
        $now = now()->format('H:i:s');
        $nowCarbon = now(); // Untuk perhitungan waktu yang lebih akurat

        // Ambil semua shift milik pegawai (urut berdasarkan waktu_masuk)
        $shifts = $pegawai->shift->sortBy('waktu_masuk');

        // Cek apakah sekarang ada shift aktif (jika lintas hari misal 22:00–05:00), TAPI pastikan belum check_out hari ini
        $shiftAktif = $shifts->first(function ($shift) use ($now, $pegawai) {
          // 🔹 Cek apakah shift ini sudah di-check_out hari ini (gunakan tanggal check_in untuk akurasi, karena check_in biasanya hari yang sama)
          $hasCheckedOut = Absen::where('no_pegawai', $pegawai->no_pegawai)
            ->where('shift', $shift->shift)
            ->whereNotNull('check_out')
            ->whereDate('check_in', today()) // Asumsi check_in adalah hari shift dimulai
            ->exists();

          if ($hasCheckedOut) {
            return false; // Lewati shift ini jika sudah check_out
          }

          // Jika belum check_out, cek apakah waktu sekarang dalam rentang shift
          $start = $shift->waktu_masuk;
          $end = $shift->waktu_pulang;

          // Jika lintas hari (contoh 22:00 - 05:00)
          if ($end < $start) {
            return ($now >= $start || $now <= $end);
          }

          return ($now >= $start && $now <= $end);
        });

        // Jika tidak ada shift aktif (atau sudah check_out), cari shift berikutnya
        if (!$shiftAktif) {
          $shiftBerikutnya = $shifts->first(function ($shift) use ($now) {
            return $shift->waktu_masuk > $now;
          });

          // Jika semua shift sudah lewat, ambil shift pertama (besok)
          if (!$shiftBerikutnya) {
            $shiftBerikutnya = $shifts->first();
          }

          // Update info status
          $shiftAktif = $shiftBerikutnya->shift;
          $waktuShiftAktif = ' ( ' . Carbon::parse($shiftBerikutnya->waktu_masuk)->format('H:i') . ' - ' . Carbon::parse($shiftBerikutnya->waktu_pulang)->format('H:i') . ' )';
        } else {
          // Jika ada shift aktif, periksa apakah sudah ada checkin dalam 2 jam dari waktu_masuk
          $waktuMasukCarbon = Carbon::parse($shiftAktif->waktu_masuk);
          $batasWaktuCheckin = $waktuMasukCarbon->copy()->addHours(2);

          // Periksa apakah ada absen (checkin) untuk shift ini hari ini dalam batas 2 jam
          $hasCheckin = Absen::where('no_pegawai', $pegawai->no_pegawai)
            ->where('shift', $shiftAktif->shift)
            ->whereDate('check_in', today())
            ->where('check_in', '<=', $batasWaktuCheckin)
            ->exists();

          // Jika sudah lebih dari 2 jam dari waktu_masuk dan belum ada checkin, anggap shift tidak aktif, lanjut ke berikutnya
          if ($nowCarbon->greaterThan($batasWaktuCheckin) && !$hasCheckin) {
            $shiftBerikutnya = $shifts->first(function ($shift) use ($now) {
              return $shift->waktu_masuk > $now;
            });

            // Jika semua shift sudah lewat, ambil shift pertama (besok)
            if (!$shiftBerikutnya) {
              $shiftBerikutnya = $shifts->first();
            }

            // Update info status
            $shiftAktif = $shiftBerikutnya->shift;
            $waktuShiftAktif = ' ( ' . Carbon::parse($shiftBerikutnya->waktu_masuk)->format('H:i') . ' - ' . Carbon::parse($shiftBerikutnya->waktu_pulang)->format('H:i') . ' )';
          } else {
            // Jika ada shift aktif dan kondisi checkin terpenuhi
            $shiftAktif = $shiftAktif->shift;
            $waktuShiftAktif = ' ( ' . Carbon::parse($shiftAktif->waktu_masuk)->format('H:i') . ' - ' . Carbon::parse($shiftAktif->waktu_pulang)->format('H:i') . ' )';
          }
        }
      }
    } catch (\Exception $e) {
      // Bisa tambahkan log jika dibutuhkan
      // Log::error($e->getMessage());
    }

    // Cek Pengaturan Shift Pegawai
    $shiftPegawai = ShiftKerja::where('no_pegawai', $user->pegawai->no_pegawai)->first();
    if (empty($shiftPegawai)) {
      $shiftAktif = '( Belum Memiliki Shift Kerja )';
      $waktuShiftAktif = '';
    }
    return view('content.dashboard.dashboards-analytics', compact('user', 'pegawai', 'shiftAktif', 'waktuShiftAktif', 'statusAbsen'));
  }
}
