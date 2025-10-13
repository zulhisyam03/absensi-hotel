<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ParamFactory extends Factory
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
      'value' => 'shift',
      'svalue' => json_encode([
        [
          'val' => 'Pagi',
          'waktu_masuk' => '06:00',
          'waktu_pulang' => '14:00',
          'updated_at' => '2025-10-30 00:00:00',
        ],
        [
          'val' => 'Siang',
          'waktu_masuk' => '14:00',
          'waktu_pulang' => '22:00',
          'updated_at' => '2025-10-30 00:00:00',
        ],
        [
          'val' => 'Malam',
          'waktu_masuk' => '22:00',
          'waktu_pulang' => '06:00',
          'updated_at' => '2025-10-30 00:00:00',
        ],
      ], JSON_UNESCAPED_UNICODE),
      'flag' => '1',
    ];
  }

  public function lokasi(): static
  {
    return $this->state(fn(array $attributes) => [
      'value' => 'lokasi',
      'svalue' => json_encode([
        'latitude' => '-0.900270',
        'longitude' => '119.889012',
        'radius' => '100',
      ], JSON_UNESCAPED_UNICODE),
      'flag' => '1',
    ]);
  }
}