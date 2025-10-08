<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Absen;
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

    User::factory()->create([
      'name' => 'Test User',
      'email' => 'test@example.com',
    ]);

    Absen::factory()->create([
      'user_id' => 'sb001',
      'nama_pegawai' => 'Hendi',
      'shift' => 'pagi',
    ]);
    Absen::factory()->create([
      'user_id' => 'sb002',
      'nama_pegawai' => 'Hendi',
      'shift' => 'pagi',
    ]);
    Absen::factory()->create([
      'user_id' => 'sb001',
      'nama_pegawai' => 'Hendi',
      'shift' => 'pagi',
    ]);
  }
}
