<?php

namespace Database\Factories\Users\Models;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Users\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = \Users\Models\User::class;

    public function definition(): array
    {
        $first = fake()->firstName();
        $second = fake()->firstName();
        $last = fake()->lastName();

        return [
            'name' => [
                'en' => $first.' '.$second.' '.$last,
                'ar' => $first.' '.$second.' '.$last,
            ],
            // Note: users table schema here only includes 'name' JSON; do not set per-part columns.
            'user_name' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => '05'.fake()->numerify('########'),
            'code' => fake()->unique()->bothify('user-####'),
            'password' => bcrypt('password'),
            'approve' => 1,
            'status' => 1,
        ];
    }
}
