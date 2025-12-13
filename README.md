# Senior Lead Developer — Yii2 Assessment
**Title:** Legacy Module Refactor & Feature Extension

This package contains a deliberately *messy* Yii2 module slice meant to be refactored and extended.
It is not a full standalone app; integrate it into a Yii2 Basic/Advanced project or use it as a drop-in
module. The code intentionally violates best practices.

---

## What You Must Do

### 1) Refactor the Subscription Module
- Move all SQL/DB logic out of views/controllers into models or services.
- Add Yii2 behaviors for `created_at` and `updated_at` (currently set manually or inconsistently).
- Replace inline/raw queries with parameterized `ActiveQuery` while preserving the output shape
  so views can render without template changes.

### 2) Add a New Feature — Trial Subscription
- Support a **"trial"** type with start/end auto-set to 7 days from creation.
- Trials auto-convert to **paid** on expiry unless user cancels.

### 3) Automations (Console + Queue)
- Implement a **console command** that runs daily to:
  - Detect expired trials.
  - Convert them to paid.
  - Queue an **email notification** (use `yii2-queue`), *do not* call `mail()` directly.
- Provide minimal queue config and a job class to send the email.

### 4) Security Fix (RBAC + Attribute Checks)
- Integrate Yii2 RBAC:
  - Only the **owner** can view/cancel their own subscriptions.
  - **Admins** can view all.
- Attribute-based check on `subscription.user_id === Yii::$app->user->id`.

### 5) Zero-Downtime Migration
- Fix the broken migration by adding missing columns and indexes *idempotently*.
- Must run safely **even if** partial schema already exists (no fatal errors; use guards).

### 6) Performance Optimization
- The list page currently has **N+1** queries on `User` and `Plan` relations.
- Fix with eager loading so DB query count remains constant regardless of number of rows.
- Provide a **Yii Debug Toolbar** screenshot before/after as proof.

---

## Provided (Intentionally Flawed) Files
- `modules/subscription/models/Subscription.php` — AR mixing business logic and static SQL.
- `modules/subscription/controllers/SubscriptionController.php` — bypasses RBAC, inline queries.
- `modules/subscription/views/subscription/index.php` — embeds SQL in the view (!!!).
- `modules/subscription/views/subscription/view.php` — more view-side logic + manual timestamps.
- `migrations/m230101_123456_create_subscription_table.php` — failed mid-run; leaves partial schema.
- `console/controllers/TrialController.php` — scaffold to implement daily trial conversion.
- `jobs/SendSubscriptionEmailJob.php` — queue job scaffold.
- `config/web.php`, `config/console.php` — snippet configs to merge.
- `data/seed.sql` — minimal seed data to simulate states.
- `tests/` — placeholders for PHPUnit/Codeception tests you will fill in.

> NOTE: Models `User` and `Plan` are *very* minimal placeholders for relation wiring.
> In your refactor you may replace them with your project’s actual models.

---

## Setup
1. Drop this folder into your Yii2 project root (or copy module contents).
2. Merge configs from `config/web.php` and `config/console.php` into your app configs.
3. Create database and import `data/seed.sql` (adjust table prefix if any).
4. Run the broken migration (expect partial success), then write a **new** migration to fix it
   **idempotently**.

```bash
php yii migrate/up --migrationPath=@app/migrations
```

5. Visit the list route (controller assumes `/subscription/index`) to see the N+1 behavior.

---

## Deliverables (What to Return)
- **Refactored module** (drop-in replacement).
- **New migration(s)** with idempotent checks.
- **Console command** for trial conversion.
- **Queue config** + **job class** for email sending.
- **README** with:
  - Architecture decisions.
  - Performance before/after.
  - Security changes and RBAC roles/permissions.
- **Tests** covering:
  - Trial conversion.
  - Owner access check.
  - N+1 fix (assert query count using Yii profiler or functional test).

---

## Rubric (What We Evaluate)
- Code quality and Yii2 idioms (behaviors, AR, DI/services).
- Safety of migrations; backward compatibility.
- Security correctness (RBAC + attribute checks).
- Performance and query strategy.
- Test quality and coverage.
- Clarity of reasoning in README.

Good luck — and have fun cleaning up legacy code!
