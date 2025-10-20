<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Absen>
 */
class AbsenFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $shiftoptions = ['pagi', 'siang', 'malam'];
    return [
      //
      'no_pegawai' => fake()->unique(),
      'shift' => $this->faker->randomElement($shiftoptions),
      'pict' => $this->faker->imageUrl(640, 480, 'people', true),
      'shift_masuk' => '08:00:00',
      'shift_pulang' => '12:00:00',
      'check_in' => now(),
      'status' => 'masuk',
      'keterangan' => '',
    ];
  }
}
