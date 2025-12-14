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
use Illuminate\Support\Facades\Mail;
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

        try {
            Mail::raw($this->body, function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject($this->subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Subscription email sent successfully', [
                'user_id' => $this->userId,
                'subscription_id' => $this->subscriptionId,
                'email' => $user->email,
                'subject' => $this->subject,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to send subscription email', [
                'user_id' => $this->userId,
                'subscription_id' => $this->subscriptionId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Subscription email job failed permanently', [
            'user_id' => $this->userId,
            'subscription_id' => $this->subscriptionId,
            'error' => $exception?->getMessage(),
        ]);
    }
}