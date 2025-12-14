<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $basic = Plan::create(['name' => 'Basic', 'price' => 9.99]);
        $pro = Plan::create(['name' => 'Pro', 'price' => 19.99]);
        $enterprise = Plan::create(['name' => 'Enterprise', 'price' => 99.00]);

        $alice = User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        $bob = User::create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $charlie = User::create([
            'name' => 'Charlie',
            'email' => 'charlie@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        Subscription::create([
            'user_id' => $alice->id,
            'plan_id' => $basic->id,
            'status' => 'active',
            'type' => 'trial',
        ]);

        Subscription::create([
            'user_id' => $charlie->id,
            'plan_id' => $pro->id,
            'status' => 'active',
            'type' => 'trial',
            'started_at' => now()->subDays(10),
            'trial_end_at' => now()->subDays(3),
        ]);

        Subscription::create([
            'user_id' => $bob->id,
            'plan_id' => $enterprise->id,
            'status' => 'active',
            'type' => 'paid',
            'started_at' => now()->subMonths(2),
        ]);

        Subscription::create([
            'user_id' => $alice->id,
            'plan_id' => $pro->id,
            'status' => 'cancelled',
            'type' => 'paid',
            'started_at' => now()->subMonths(3),
            'ended_at' => now()->subWeeks(2),
        ]);
    }
}
