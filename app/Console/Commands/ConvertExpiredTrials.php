<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ConvertExpiredTrials extends Command
{
    protected $signature = 'subscriptions:convert-trials {--dry-run : Preview without making changes}';
    protected $description = 'Convert expired trial subscriptions to paid subscriptions';

    public function handle(SubscriptionService $subscriptionService): int
    {
        if ($this->option('dry-run')) {
            $count = \App\Models\Subscription::expiredTrials()->count();
            $this->info("Found {$count} expired trials (dry-run mode)");
            return self::SUCCESS;
        }

        $this->info('Converting expired trials...');

        try {
            $result = $subscriptionService->convertExpiredTrials();
            
            $this->newLine();
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Converted', $result['converted']],
                    ['Failed', $result['failed']],
                ]
            );

            if ($result['converted'] > 0) {
                $this->info("✓ Successfully converted {$result['converted']} subscription(s)");
            } else {
                $this->info('No expired trials found');
            }

            if ($result['failed'] > 0) {
                $this->error("✗ {$result['failed']} conversion(s) failed - check logs");
            }

            Log::info('Trial conversion completed', $result);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Conversion process failed: ' . $e->getMessage());
            
            Log::error('Trial conversion command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
