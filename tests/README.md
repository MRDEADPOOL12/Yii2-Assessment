# Tests Placeholders

Create tests (PHPUnit/Codeception) to verify:
- Trial conversion converts expired trials to paid and queues email job.
- Owner access: owner can view/cancel their own subscription; admin can view all.
- N+1 query fix: assert that number of DB queries does not grow with the number of rows.

Suggested locations:
- `tests/unit/SubscriptionTrialTest.php`
- `tests/unit/AccessControlTest.php`
- `tests/functional/SubscriptionIndexCest.php`
