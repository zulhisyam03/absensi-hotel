<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pegawai;

class PegawaiUserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    //
    Pegawai::factory()->create([]);

    User::factory()->create([
      'name' => 'Test User',
      'email' => 'test@example.com',
    ]);
  }
}