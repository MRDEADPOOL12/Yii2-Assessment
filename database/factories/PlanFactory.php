<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Basic', 'Pro', 'Enterprise', 'Starter', 'Premium']),
            'price' => fake()->randomFloat(2, 9.99, 99.99),
        ];
    }
}
