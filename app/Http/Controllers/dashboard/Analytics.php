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
        $now = now(); // Ubah ke objek Carbon (bukan format string)
        $nowCarbon = now(); // Untuk perhitungan waktu yang lebih akurat

        // Ambil semua shift milik pegawai (urut berdasarkan waktu_masuk)
        $shifts = $pegawai->shift->sortBy('waktu_masuk');

        // mapping shift into Carbon
        $shiftData = $shifts->map(function($shift) use ($now) {
            $start = Carbon::parse($shift->waktu_masuk);
            $end   = Carbon::parse($shift->waktu_pulang);

            // shift lintas hari (contoh 23:00–07:00)
            if ($start->gt($end)) {
                $end->addDay();
            }

            return (object)[
                'model' => $shift,
                'start' => $start,
                'end'   => $end,
            ];
        });

        // tentukan shift yang waktu_masuk-nya TERDEKAT tetapi tidak melebihi NOW
        $shiftAktifData = $shiftData
          ->filter(fn($s) => now()->gte($s->start))
          ->sortByDesc('start')
          ->first();

      // kembalikan model asli (SHIFT)
      $shiftAktif = $shiftAktifData->model;
      Log::channel('shift')->info('Shift Aktif Baru : '.$shiftAktif);
        // Jika tidak ada shift aktif (atau sudah check_out), cari shift berikutnya
        if (!$shiftAktif) {
          $shiftBerikutnya = $shifts->first(function ($shift) use ($now) {
            // Pastikan $now adalah objek Carbon
            $nowCarbon = $now instanceof Carbon ? $now : Carbon::parse($now);

            // Parse waktu_masuk dengan tanggal yang tepat
            $waktuMasukCarbon = Carbon::parse($shift->waktu_masuk); // Default hari ini

            // Jika shift lintas hari dan sekarang dalam rentang hari berikutnya, kurangi hari
            if ($shift->waktu_masuk > $shift->waktu_pulang && $nowCarbon->format('H:i:s') <= $shift->waktu_pulang) {
              $waktuMasukCarbon = $waktuMasukCarbon->copy()->subDay();
            }

            // Bandingkan dengan Carbon
            $isGreater = $waktuMasukCarbon->greaterThan($nowCarbon);

            Log::channel('shift')->info('Waktu Masuk : ' . $waktuMasukCarbon . ' NOW : ' . $nowCarbon . ' > : ' . ($isGreater ? 'true' : 'false'));

            return $isGreater;
          });

          // Jika semua shift sudah lewat, ambil shift pertama (besok)
          if (!$shiftBerikutnya) {
            $shiftBerikutnya = $shifts->first();
            Log::channel('shift')->info('Ambil Shift Pertama (Besok) :' . $shiftAktif); // Untuk melihat semua atribut
          }

          // Update info status
          $shiftAktif = $shiftBerikutnya->shift;
          Log::channel('shift')->info('Shift Aktif 1 :' . $shiftAktif); // Untuk melihat semua atribut
          $waktuShiftAktif = ' ( ' . Carbon::parse($shiftBerikutnya->waktu_masuk)->format('H:i') . ' - ' . Carbon::parse($shiftBerikutnya->waktu_pulang)->format('H:i') . ' )';
          Log::channel('shift')->info('Waktu Shift 1 :' . $waktuShiftAktif); // Untuk melihat semua atribut
        } else {
          // Jika ada shift aktif, periksa apakah sudah ada checkin dalam 3 jam dari waktu_masuk

          $now = Carbon::now(); // Sudah objek Carbon, tapi pastikan konsisten
          $tanggalSekarang = $now->copy()->startOfDay(); // Awal hari dari waktu sekarang
          // Tentukan tanggal start berdasarkan kondisi
          $tanggalStart = $tanggalSekarang; // Default: hari sekarang
          $isLintasHari = $shiftAktif->waktu_masuk > $shiftAktif->waktu_pulang;
          if ($isLintasHari) {
            // Shift lintas hari
            if ($now->format('H:i:s') <= $shiftAktif->waktu_pulang) {
              // Jika sekarang <= waktu_pulang (dalam rentang hari berikutnya), start di hari kemarin
              $tanggalStart = $tanggalSekarang->copy()->subDay();
            }
            // Jika sekarang >= waktu_masuk, start tetap hari sekarang
          } else {
            // Shift normal: jika diperlukan, sesuaikan untuk shift yang belum dimulai
            // Contoh: if ($now->format('H:i:s') < $shiftAktif->waktu_masuk) { $tanggalStart = $tanggalSekarang->copy()->subDay(); }
          }
          // Hitung start dan end berdasarkan tanggalStart
          $start = $tanggalStart->copy()->setTimeFromTimeString($shiftAktif->waktu_masuk);
          if ($isLintasHari) {
            $end = $tanggalStart->copy()->addDay()->setTimeFromTimeString($shiftAktif->waktu_pulang);
          } else {
            $end = $tanggalStart->copy()->setTimeFromTimeString($shiftAktif->waktu_pulang);
          }
          Log::channel('shift')->info('Start :' . $start); // Untuk melihat waktu start
          Log::channel('shift')->info('End :' . $end); // Untuk melihat waktu end

          $waktuMasukCarbon = Carbon::parse($start);
          $batasWaktuCheckin = $waktuMasukCarbon->copy()->addHours(3);
          Log::channel('shift')->info('Batas Waktu Check In : ' . $batasWaktuCheckin);

          // Periksa apakah ada absen (checkin) untuk shift ini hari ini dalam batas 2 jam
          $hasCheckin = Absen::where('no_pegawai', $pegawai->no_pegawai)
            ->where('shift', $shiftAktif->shift)
            ->whereDate('check_in', today())
            ->where('check_in', '<=', $batasWaktuCheckin)
            ->exists();

          Log::channel('shift')->info('Data Check In :' . $hasCheckin);
          Log::channel('shift')->info('Data :', $shiftAktif->toArray());

          // Jika sudah lebih dari 2 jam dari waktu_masuk dan belum ada checkin, anggap shift tidak aktif, lanjut ke berikutnya
          if ($nowCarbon->greaterThan($batasWaktuCheckin) && !$hasCheckin) {
            $shiftBerikutnya = $shifts->first(function ($shift) use ($now) {
              // Pastikan $now adalah objek Carbon
              $nowCarbon = $now instanceof Carbon ? $now : Carbon::parse($now);

              // Parse waktu_masuk dengan tanggal yang tepat
              $waktuMasukCarbon = Carbon::parse($shift->waktu_masuk); // Default hari ini

              // Jika shift lintas hari dan sekarang dalam rentang hari berikutnya, kurangi hari
              if ($shift->waktu_masuk > $shift->waktu_pulang && $nowCarbon->format('H:i:s') <= $shift->waktu_pulang) {
                $waktuMasukCarbon = $waktuMasukCarbon->copy()->subDay();
              }

              // Bandingkan dengan Carbon
              $isGreater = $waktuMasukCarbon->greaterThan($nowCarbon);

              Log::channel('shift')->info('Waktu Masuk Shift : ' . $shift->shift . ' - ' . $waktuMasukCarbon . ' NOW : ' . $nowCarbon . ' > : ' . ($isGreater ? 'true' : 'false'));

              return $isGreater;
            });

            // Jika semua shift sudah lewat, ambil shift pertama (besok)
            if (!$shiftBerikutnya) {
              $shiftBerikutnya = $shifts->first();
              Log::channel('shift')->info('Ambil Shift Pertama (Besok) :' . $shiftAktif); // Untuk melihat semua atribut
            }

            // Update info status
            $shiftAktif = $shiftBerikutnya->shift;
            Log::channel('shift')->info('Shift Aktif 2 :' . $shiftAktif); // Untuk melihat semua atribut
            $waktuShiftAktif = ' ( ' . Carbon::parse($shiftBerikutnya->waktu_masuk)->format('H:i') . ' - ' . Carbon::parse($shiftBerikutnya->waktu_pulang)->format('H:i') . ' )';
            Log::channel('shift')->info('Waktu Shift 2 :' . $waktuShiftAktif); // Untuk melihat semua atribut
          } else {
            // Jika ada shift aktif dan kondisi checkin terpenuhi
            $shiftAktifObj = $shiftAktif; // simpan objek
            $shiftAktif = $shiftAktifObj->shift; // ambil nama shift-nya
            Log::channel('shift')->info('Shift Aktif 3 :', $shiftAktifObj->toArray());

            $waktuShiftAktif = ' ( ' .
              Carbon::parse($shiftAktifObj->waktu_masuk)->format('H:i') .
              ' - ' .
              Carbon::parse($shiftAktifObj->waktu_pulang)->format('H:i') .
              ' )';
          }
        }
      }
    } catch (\Exception $e) {
      // Bisa tambahkan log jika dibutuhkan
      // Log::error($e->getMessage());
    }

    Log::channel('shift')->info('========================================================================================'); // Untuk melihat semua atribut

    // Cek Pengaturan Shift Pegawai
    $shiftPegawai = ShiftKerja::where('no_pegawai', $user->pegawai->no_pegawai)->first();
    if (empty($shiftPegawai)) {
      $shiftAktif = '( Belum Memiliki Shift Kerja )';
      $waktuShiftAktif = '';
    }
    return view('content.dashboard.dashboards-analytics', compact('user', 'pegawai', 'shiftAktif', 'waktuShiftAktif', 'statusAbsen'));
  }
}
