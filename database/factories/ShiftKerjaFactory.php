<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShiftKerja>
 */
class ShiftKerjaFactory extends Factory
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
      'shift' => 'pagi',
      'waktu_masuk' => '08:00:00',
      'waktu_pulang' => '12:00:00',
      'flag' => 'a',
    ];
  }
}
