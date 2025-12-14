<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Mail\SubscriptionConverted;
use App\Mail\SubscriptionCreated;
use App\Mail\SubscriptionCancelled;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class SubscriptionTrialTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SubscriptionService();
    }

    public function test_trial_subscription_auto_sets_7_day_expiry(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'type' => Subscription::TYPE_TRIAL,
        ]);

        $this->assertNotNull($subscription->trial_end_at);
        $this->assertEquals(7, $subscription->started_at->diffInDays($subscription->trial_end_at));
    }

    public function test_expired_trial_converts_to_paid(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'type' => Subscription::TYPE_TRIAL,
            'started_at' => now()->subDays(10),
            'trial_end_at' => now()->subDays(2),
        ]);

        $this->assertTrue($subscription->isExpired());

        $result = $this->service->convertToPaid($subscription);

        $this->assertTrue($result);
        $this->assertEquals(Subscription::TYPE_PAID, $subscription->fresh()->type);
        $this->assertNull($subscription->fresh()->trial_end_at);
    }

    public function test_trial_conversion_queues_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'type' => Subscription::TYPE_TRIAL,
            'started_at' => now()->subDays(10),
            'trial_end_at' => now()->subDays(2),
        ]);

        $this->service->convertToPaid($subscription);

        Mail::assertQueued(SubscriptionConverted::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_subscription_creation_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = $this->service->createSubscription([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'type' => Subscription::TYPE_TRIAL,
            'started_at' => now(),
        ]);

        Mail::assertQueued(SubscriptionCreated::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_subscription_cancellation_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'type' => Subscription::TYPE_PAID,
        ]);

        $this->service->cancelSubscription($subscription);

        Mail::assertQueued(SubscriptionCancelled::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_convert_expired_trials_command_converts_all_expired(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        // Create 3 expired trials
        for ($i = 0; $i < 3; $i++) {
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_ACTIVE,
                'type' => Subscription::TYPE_TRIAL,
                'started_at' => now()->subDays(10),
                'trial_end_at' => now()->subDays(2),
            ]);
        }

        // Create 1 non-expired trial
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'type' => Subscription::TYPE_TRIAL,
        ]);

        $result = $this->service->convertExpiredTrials();

        $this->assertEquals(3, $result['converted']);
        $this->assertEquals(0, $result['failed']);
        $this->assertEquals(3, Subscription::where('type', Subscription::TYPE_PAID)->count());
        $this->assertEquals(1, Subscription::where('type', Subscription::TYPE_TRIAL)->count());

        // Verify emails were sent for all conversions
        Mail::assertQueued(SubscriptionConverted::class, 3);
    }

    public function test_cannot_convert_paid_subscription_to_paid(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'type' => Subscription::TYPE_PAID,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only trial subscriptions can be converted');

        $subscription->convertToPaid();
    }

    public function test_cannot_convert_cancelled_trial(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_CANCELLED,
            'type' => Subscription::TYPE_TRIAL,
            'started_at' => now()->subDays(10),
            'trial_end_at' => now()->subDays(2),
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only active subscriptions can be converted');

        $subscription->convertToPaid();
    }

    public function test_email_contains_correct_data(): void
    {
        Mail::fake();

        $user = User::factory()->create(['name' => 'John Doe']);
        $plan = Plan::factory()->create(['name' => 'Premium', 'price' => 29.99]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'type' => Subscription::TYPE_TRIAL,
            'started_at' => now()->subDays(10),
            'trial_end_at' => now()->subDays(2),
        ]);

        $this->service->convertToPaid($subscription);

        Mail::assertQueued(SubscriptionConverted::class, function ($mail) use ($subscription) {
            return $mail->subscription->id === $subscription->id
                && $mail->subscription->plan->name === 'Premium'
                && $mail->subscription->user->name === 'John Doe';
        });
    }
}