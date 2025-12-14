<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

final class SubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Subscription $subscription): bool
    {
        return $user->isAdmin() || $subscription->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function cancel(User $user, Subscription $subscription): bool
    {
        return $subscription->user_id === $user->id && $subscription->canBeCancelled();
    }

    public function convert(User $user, Subscription $subscription): bool
    {
        return $subscription->user_id === $user->id && $subscription->canBeConvertedToPaid();
    }
}
