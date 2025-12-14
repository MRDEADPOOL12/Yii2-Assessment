<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

final class SendSubscriptionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    public function __construct(
        public readonly int $userId,
        public readonly int $subscriptionId,
        public readonly string $subject,
        public readonly string $body
    ) {
    }

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::warning('User not found for subscription email', [
                'user_id' => $this->userId,
                'subscription_id' => $this->subscriptionId,
            ]);
            return;
        }

        Log::info('Subscription email queued', [
            'user_id' => $this->userId,
            'subscription_id' => $this->subscriptionId,
            'email' => $user->email,
            'subject' => $this->subject,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Subscription email job failed', [
            'user_id' => $this->userId,
            'subscription_id' => $this->subscriptionId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
