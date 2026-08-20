<?php

namespace Database\Factories;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Pengguna>
 */
class PenggunaFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'kata_sandi' => static::$password ??= Hash::make('password'),
            'no_telepon' => fake()->phoneNumber(),
            'peran' => 'kapten_tim',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'admin',
        ]);
    }

    public function penyelenggara(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'penyelenggara',
        ]);
    }

    public function kaptenTim(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'kapten_tim',
        ]);
    }
}
