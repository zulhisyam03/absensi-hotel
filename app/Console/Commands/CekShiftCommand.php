<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absen;
use Carbon\Carbon;

class CekShiftCommand extends Command
{
  protected $signature = 'shift:cek';
  protected $description = 'Cek absen yang sudah lebih dari 15 jam tanpa check out dan ubah ke tco';

  public function handle()
  {
    $absenAll = Absen::whereNotNull('check_in')
      ->whereNull('check_out')
      ->where('keterangan', '!=', 'tco')
      ->get();

    $updated = 0;

    foreach ($absenAll as $absen) {
      $selisihJam = $absen->check_in->diffInHours(now());

      if ($selisihJam > 15) {
        $absen->update(['keterangan' => 'tco']);
        $updated++;
      }
    }

    $this->info("Selesai! Total {$updated} absen diupdate menjadi 'tco'.");
  }
}