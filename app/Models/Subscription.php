<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

final class Subscription extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';
    public const TYPE_TRIAL = 'trial';
    public const TYPE_PAID = 'paid';
    public const TRIAL_DAYS = 7;

    protected $fillable = ['user_id', 'plan_id', 'status', 'type', 'trial_end_at', 'started_at', 'ended_at'];

    protected function casts(): array
    {
        return [
            'trial_end_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $subscription): void {
            if ($subscription->isTrial() && !$subscription->trial_end_at) {
                $subscription->started_at = now();
                $subscription->trial_end_at = now()->addDays(self::TRIAL_DAYS);
            }
            if (!$subscription->started_at) {
                $subscription->started_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeTrial(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_TRIAL);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_PAID);
    }

    public function scopeExpiredTrials(Builder $query): Builder
    {
        return $query->trial()->active()->where('trial_end_at', '<=', now());
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function isTrial(): bool
    {
        return $this->type === self::TYPE_TRIAL;
    }

    public function isPaid(): bool
    {
        return $this->type === self::TYPE_PAID;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isExpired(): bool
    {
        if (!$this->isTrial()) {
            return false;
        }
        return $this->trial_end_at !== null && $this->trial_end_at->isPast();
    }

    public function convertToPaid(): bool
    {
        if (!$this->isTrial()) {
            throw new InvalidArgumentException('Only trial subscriptions can be converted');
        }
        if (!$this->isActive()) {
            throw new InvalidArgumentException('Only active subscriptions can be converted');
        }

        $this->type = self::TYPE_PAID;
        $this->trial_end_at = null;

        return $this->save();
    }

    public function cancel(): bool
    {
        if (!$this->isActive()) {
            throw new InvalidArgumentException('Only active subscriptions can be cancelled');
        }

        $this->status = self::STATUS_CANCELLED;
        $this->ended_at = now();

        return $this->save();
    }

    public function canBeCancelled(): bool
    {
        return $this->isActive();
    }

    public function canBeConvertedToPaid(): bool
    {
        return $this->isTrial() && $this->isActive();
    }

    public function daysRemainingInTrial(): ?int
    {
        if (!$this->isTrial() || !$this->trial_end_at) {
            return null;
        }
        $days = now()->diffInDays($this->trial_end_at, false);
        return max(0, (int) ceil($days));
    }
}
