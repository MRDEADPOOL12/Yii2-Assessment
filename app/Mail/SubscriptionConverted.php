<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class SubscriptionConverted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Trial Has Been Converted to Paid Subscription',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-converted',
            with: [
                'subscription' => $this->subscription,
                'userName' => $this->subscription->user->name,
                'planName' => $this->subscription->plan->name,
                'planPrice' => $this->subscription->plan->price,
            ],
        );
    }
}