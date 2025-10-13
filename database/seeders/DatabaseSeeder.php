<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\Absen;
use App\Models\ShiftKerja;
use App\Models\Param;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->create();
    Absen::factory()->create([
      'no_pegawai' => 'SB001',
      'shift' => 'pagi',
    ]);
    Absen::factory()->create([
      'no_pegawai' => 'SB001',
      'shift' => 'pagi',
    ]);

    ShiftKerja::factory()->create([]);

    // param
    Param::factory()->create([]);
    Param::factory()->lokasi()->create([]);
  }
}