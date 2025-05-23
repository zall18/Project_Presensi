<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Device ' . $this->faker->word(),
            'device_id' => strtoupper($this->faker->bothify('SN###')),
            'lokasi' => $this->faker->address(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
