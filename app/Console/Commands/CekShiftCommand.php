<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class CekShiftCommand extends Command
{
  protected $signature = 'shift:cek';
  protected $description = 'Cek absen yang sudah melewati waktu shift_pulang + 4 jam tanpa check out dan ubah ke tco';

  public function handle()
  {
    $absenAll = Absen::whereNotNull('check_in')
      ->whereNull('check_out')
      ->where('keterangan', '!=', 'tco')
      ->get();

    $updated = 0;

    foreach ($absenAll as $absen) {
      // Asumsikan field 'tanggal' ada di model Absen (misal '2023-10-01')
      // Jika tidak ada, ganti dengan field lain seperti $absen->check_in->toDateString()
      $tanggalAbsen = Carbon::parse($absen->check_in); // Parse tanggal absen

      // Gabung tanggal absen dengan waktu shift
      $shiftPulang = $tanggalAbsen->copy()->setTimeFromTimeString($absen->shift_pulang);
      $shiftMasuk = $tanggalAbsen->copy()->setTimeFromTimeString($absen->shift_masuk);

      $now = now();

      // Cek apakah shift lintas hari
      $isCrossDay = $shiftPulang->lessThan($shiftMasuk);

      // Jika lintas hari dan waktu sekarang < shift_masuk, tambah 1 hari ke shift_pulang
      if ($isCrossDay && $now->format('H:i:s') < $shiftMasuk->format('H:i:s')) {
        $shiftPulang = $shiftPulang->addDay();
      }

      // Hitung batas waktu: shift_pulang + 5 jam
      $batasWaktu = $shiftPulang->copy()->addHours(5);

      // Logging untuk debug (lihat di console saat jalankan command)
      Log::channel('shift')->info("Absen ID: {$absen->id}, Tanggal: {$tanggalAbsen->toDateString()}, Shift Pulang: {$shiftPulang}, Batas: {$batasWaktu}, Now: {$now}, Cross Day : {$isCrossDay}");
      // $this->info("Absen ID: {$absen->id}, Tanggal: {$tanggalAbsen->toDateString()}, Shift Pulang: {$shiftPulang}, Batas: {$batasWaktu}, Now: {$now}");

      // Jika waktu sekarang > batas waktu, update ke 'tco'
      if ($now->greaterThan($batasWaktu)) {
        $absen->update(['keterangan' => 'tco']);
        $updated++;
        $this->info("Updated absen ID {$absen->id} to 'tco'");
      }
    }

    $this->info("Selesai! Total {$updated} absen diupdate menjadi 'tco'.");
  }
}