<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $nip = fake()->unique()->numerify('19##########0###01');
        return [
            'nip'                  => $nip,
            'name'                 => fake()->name(),
            'email'                => fake()->unique()->safeEmail(),
            'password'             => static::$password ??= Hash::make('password'),
            'role'                 => 'pegawai',
            'is_active'            => true,
            'must_change_password' => false,
            'remember_token'       => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'                 => 'admin',
            'must_change_password' => false,
        ]);
    }
}
