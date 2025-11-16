<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CekShiftCommand extends Command
{
  protected $signature = 'shift:cek';
  protected $description = 'Cek absen yang sudah melewati waktu shift_pulang + 5 jam tanpa check out dan ubah ke tco';

  public function handle()
  {
    $now = now();
    $updated = 0;

    // Ambil semua absen yang masih aktif (belum checkout)
    $absenAll = Absen::whereNotNull('check_in')
      ->whereNull('check_out')
      ->where('keterangan', '!=', 'tco')
      ->get();

    foreach ($absenAll as $absen) {
      try {
        // Parse shift_pulang (sudah datetime penuh di DB)
        $shiftPulang = Carbon::parse($absen->shift_pulang);

        // Hitung batas waktu (shift_pulang + 5 jam)
        $batasWaktu = $shiftPulang->copy()->addHours(5);

        Log::channel('shift')->info("CekShift - ID: {$absen->id}, Pegawai: {$absen->no_pegawai}");
        Log::channel('shift')->info("Shift Pulang: {$shiftPulang}, Batas Waktu: {$batasWaktu}, Sekarang: {$now}");

        // Jika waktu sekarang sudah lewat 5 jam dari shift_pulang
        if ($now->greaterThan($batasWaktu)) {
          $absen->update(['keterangan' => 'tco']);
          $updated++;
          Log::channel('shift')->info("✅ Update TCO untuk absen ID: {$absen->id}");
          $this->info("Updated absen ID {$absen->id} to 'tco'");
        }
      } catch (\Exception $e) {
        Log::channel('shift')->error("❌ Error CekShift pada absen ID {$absen->id}: {$e->getMessage()}");
      }
    }

    $this->info("Selesai! Total {$updated} absen diupdate menjadi 'tco'.");
    Log::channel('shift')->info("=== CekShiftCommand selesai: {$updated} absen diupdate ===");
  }
}
