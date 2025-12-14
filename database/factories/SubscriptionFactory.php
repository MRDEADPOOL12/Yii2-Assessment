<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'status' => Subscription::STATUS_ACTIVE,
            'type' => Subscription::TYPE_PAID,
            'started_at' => now(),
        ];
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Subscription::TYPE_TRIAL,
            'trial_end_at' => now()->addDays(7),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Subscription::TYPE_TRIAL,
            'started_at' => now()->subDays(10),
            'trial_end_at' => now()->subDays(2),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Subscription::STATUS_CANCELLED,
            'ended_at' => now(),
        ]);
    }
}
