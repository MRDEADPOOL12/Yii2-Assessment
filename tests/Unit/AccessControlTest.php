<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Policies\SubscriptionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new SubscriptionPolicy();
    }

    public function test_owner_can_view_own_subscription(): void
    {
        $owner = User::factory()->create(['is_admin' => false]);
        $plan = Plan::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'user_id' => $owner->id,
            'plan_id' => $plan->id,
        ]);

        $this->assertTrue($this->policy->view($owner, $subscription));
    }

    public function test_owner_cannot_view_others_subscription(): void
    {
        $owner = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create(['is_admin' => false]);
        $plan = Plan::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'user_id' => $otherUser->id,
            'plan_id' => $plan->id,
        ]);

        $this->assertFalse($this->policy->view($owner, $subscription));
    }

    public function test_admin_can_view_all_subscriptions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $regularUser = User::factory()->create(['is_admin' => false]);
        $plan = Plan::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'user_id' => $regularUser->id,
            'plan_id' => $plan->id,
        ]);

        $this->assertTrue($this->policy->view($admin, $subscription));
    }

    public function test_owner_can_cancel_own_active_subscription(): void
    {
        $owner = User::factory()->create(['is_admin' => false]);
        $plan = Plan::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'user_id' => $owner->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->assertTrue($this->policy->cancel($owner, $subscription));
    }

    public function test_owner_cannot_cancel_others_subscription(): void
    {
        $owner = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create(['is_admin' => false]);
        $plan = Plan::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'user_id' => $otherUser->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->assertFalse($this->policy->cancel($owner, $subscription));
    }

    public function test_cannot_cancel_already_cancelled_subscription(): void
    {
        $owner = User::factory()->create(['is_admin' => false]);
        $plan = Plan::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'user_id' => $owner->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_CANCELLED,
        ]);

        $this->assertFalse($this->policy->cancel($owner, $subscription));
    }

    public function test_subscription_has_user_id_attribute_check(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        // Verify attribute-based check works
        $this->assertEquals($user->id, $subscription->user_id);
        $this->assertTrue($subscription->user_id === $user->id);
    }

    public function test_admin_flag_determines_admin_privileges(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $regularUser = User::factory()->create(['is_admin' => false]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($regularUser->isAdmin());
    }
}
