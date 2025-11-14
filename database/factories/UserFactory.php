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
           $departments = ['Engineering', 'Marketing', 'Sales', 'Design', 'Support', 'HR', 'Finance'];
             $roles = ['user', 'admin', 'manager', 'editor'];
             $statuses = ['active', 'inactive', 'pending', 'suspended'];
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'department' => $this->faker->randomElement($departments),
            'role' => $this->faker->randomElement($roles),
            'status' => $this->faker->randomElement($statuses),
            'password' => Hash::make('password123'),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            
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
}
