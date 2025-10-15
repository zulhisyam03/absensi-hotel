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