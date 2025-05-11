<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Business\App\Models\Business;
use Modules\Business\App\Models\BusinessUser;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'type' => 'client', // Add default type for tests
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create and attach a business to the user.
     */
    public function withBusiness(): static
    {
        return $this->afterCreating(function ($user) {
            $business = Business::factory()->create();
            $user->businesses()->attach($business);
        });
    }

    /**
     * Create and attach a business and authenticate the user.
     */
    public function withBusinessAndAuth(): static
    {
        return $this->afterCreating(function ($user) {
            $business = Business::factory()->create();
            $user->businesses()->attach($business);
            $this->actingAs($user);
        });
    }
}
