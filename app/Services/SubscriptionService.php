<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Jobs\SendSubscriptionEmailJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class SubscriptionService
{
    public function createSubscription(array $data): Subscription
    {
        try {
            DB::beginTransaction();
            
            $subscription = Subscription::create($data);
            
            Log::info('Subscription created', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'type' => $subscription->type,
            ]);
            
            DB::commit();
            
            return $subscription;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Subscription creation failed', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function cancelSubscription(Subscription $subscription): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $subscription->cancel();
            
            Log::info('Subscription cancelled', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);
            
            DB::commit();
            
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Cancellation failed', ['subscription_id' => $subscription->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function convertToPaid(Subscription $subscription): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $subscription->convertToPaid();
            
            $subscription->load(['user', 'plan']);
            
            SendSubscriptionEmailJob::dispatch(
                $subscription->user_id,
                $subscription->id,
                'Trial Converted to Paid',
                sprintf(
                    'Your trial for %s plan has been converted to paid subscription.',
                    $subscription->plan->name
                )
            );
            
            Log::info('Trial converted to paid', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);
            
            DB::commit();
            
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Conversion failed', ['subscription_id' => $subscription->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function convertExpiredTrials(): array
    {
        $expiredTrials = Subscription::expiredTrials()->get();
        
        $converted = 0;
        $failed = 0;

        foreach ($expiredTrials as $subscription) {
            try {
                $this->convertToPaid($subscription);
                $converted++;
            } catch (Throwable $e) {
                $failed++;
                Log::error('Trial conversion failed', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('converted', 'failed');
    }

    public function getUserSubscriptions(User $user)
    {
        return Subscription::with(['user', 'plan'])
            ->forUser($user->id)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getAllSubscriptions(int $limit = 50)
    {
        return Subscription::with(['user', 'plan'])
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }
}
