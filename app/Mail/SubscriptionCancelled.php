<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class SubscriptionCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Subscription Has Been Cancelled',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-cancelled',
            with: [
                'subscription' => $this->subscription,
                'userName' => $this->subscription->user->name,
                'planName' => $this->subscription->plan->name,
            ],
        );
    }
}