<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class SubscriptionCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Your New Subscription!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-created',
            with: [
                'subscription' => $this->subscription,
                'userName' => $this->subscription->user->name,
                'planName' => $this->subscription->plan->name,
                'planPrice' => $this->subscription->plan->price,
                'isTrial' => $this->subscription->isTrial(),
                'trialDays' => $this->subscription->daysRemainingInTrial(),
            ],
        );
    }
}