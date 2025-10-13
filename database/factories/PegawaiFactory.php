<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pegawai>
 */
class PegawaiFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      //
      'no_pegawai' => 'SB001',
      'nama_pegawai' => fake()->name(),
      'no_hp' => '082234567890',
      'alamat' => 'fake()->address()',
      'email' => 'test@example.com',
      'tempat_lahir' => 'Palu',
      'tgl_lahir' => fake()->date(),
      'tgl_join' => fake()->date(),
      'jenis_kelamin' => random_int(1, 2) == 1 ? 'L' : 'P',
      'jabatan' => random_int(1, 2) == 1 ? 'HR' : 'Staff',
      'status' => 'aktif',
    ];
  }
}