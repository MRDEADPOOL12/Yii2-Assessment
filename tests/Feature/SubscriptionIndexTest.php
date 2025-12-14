<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class SubscriptionIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_index_has_constant_query_count_with_eager_loading(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => bcrypt('password')]);
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        // Create 50 subscriptions
        Subscription::factory()->count(50)->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        // Clear any setup queries
        DB::flushQueryLog();
        
        // Now measure actual page load queries
        DB::enableQueryLog();
        $response = $this->actingAs($admin)->get('/subscriptions');
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        $response->assertStatus(200);

        // With eager loading, should be exactly 4 queries for any number of rows
        $this->assertEquals(4, $queryCount, 
            "Expected 4 queries with eager loading. Got {$queryCount}."
        );
        
        // Verify the queries are correct
        $this->assertStringContainsString('select * from "subscriptions"', $queries[0]['query']);
        $this->assertStringContainsString('select * from "users" where "users"."id" in', $queries[1]['query']);
        $this->assertStringContainsString('select * from "plans" where "plans"."id" in', $queries[2]['query']);
        $this->assertStringContainsString('select count(*)', $queries[3]['query']);
    }

    public function test_without_eager_loading_query_count_grows_with_data(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()->count(5)->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        DB::enableQueryLog();
        $subs = Subscription::limit(5)->get();
        foreach ($subs as $sub) {
            $sub->user->name;
            $sub->plan->name;
        }
        $queriesFor5 = count(DB::getQueryLog());

        DB::flushQueryLog();

        Subscription::factory()->count(5)->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        DB::enableQueryLog();
        $subs = Subscription::limit(10)->get();
        foreach ($subs as $sub) {
            $sub->user->name;
            $sub->plan->name;
        }
        $queriesFor10 = count(DB::getQueryLog());

        $this->assertGreaterThan($queriesFor5, $queriesFor10,
            "Without eager loading, queries should grow with data"
        );
    }

    public function test_subscription_service_uses_eager_loading(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $plan = Plan::factory()->create();

        Subscription::factory()->count(20)->create([
            'user_id' => $admin->id,
            'plan_id' => $plan->id,
        ]);

        $service = app(\App\Services\SubscriptionService::class);

        DB::enableQueryLog();
        $subscriptions = $service->getAllSubscriptions();
        $queryCount = count(DB::getQueryLog());

        $this->assertLessThanOrEqual(5, $queryCount,
            "Service should use eager loading. Got {$queryCount} queries for 20 subscriptions."
        );

        foreach ($subscriptions->take(5) as $subscription) {
            $this->assertTrue($subscription->relationLoaded('user'));
            $this->assertTrue($subscription->relationLoaded('plan'));
        }
    }

    public function test_query_count_proof_with_detailed_logging(): void
    {
        $user = User::factory()->create(['is_admin' => true, 'password' => bcrypt('password')]);
        $plan = Plan::factory()->create();

        Subscription::factory()->count(50)->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();
        
        $response = $this->actingAs($user)->get('/subscriptions');
        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        foreach ($queries as $index => $query) {
            $this->addToAssertionCount(1);
            fwrite(STDERR, sprintf(
                "\nQuery %d: %s\n",
                $index + 1,
                $query['query']
            ));
        }

        $response->assertStatus(200);

        $this->assertEquals(4, $queryCount,
            "Expected exactly 4 queries with eager loading for 50 subscriptions. Got {$queryCount}."
        );
    }
}