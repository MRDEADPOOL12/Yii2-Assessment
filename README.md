## Features

- Trial subscriptions (7-day auto-conversion)
- RBAC authorization (owner/admin)
- N+1 query optimization
- Queue-based email system
- Automatic scheduling
- Clean code with strict types
- Service layer architecture
- Form request validation
- Policy-based authorization
- smtp email service

## Requirements

- PHP 8.1+
- PostgreSQL 12+
- Composer

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure PostgreSQL in `.env`:

```env
DB_DATABASE=subscriptions
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Create database:

```bash
psql -U postgres -c "CREATE DATABASE subscriptions;"
```

Run migrations:

```bash
php artisan migrate
php artisan db:seed
```

Start application:

```bash
php artisan serve
```

Start queue worker (separate terminal):

```bash
php artisan queue:work
```

## Test Accounts

- alice@example.com / password (User with active trial)
- bob@example.com / password (Admin - sees all subscriptions)
- charlie@example.com / password (User with expired trial)

## Console Commands

Convert expired trials:

```bash
php artisan subscriptions:convert-trials
```

Dry run (preview only):

```bash
php artisan subscriptions:convert-trials --dry-run
```

## Production Setup

Add to crontab for automatic scheduling:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

The command runs automatically at 2 AM daily.

## Architecture

### Models

- `User` - User accounts with admin flag
- `Plan` - Subscription plans with pricing
- `Subscription` - Main subscription model with business logic

### Services

- `SubscriptionService` - Business logic layer with transaction safety

### Policies

- `SubscriptionPolicy` - Authorization rules (owner/admin access)

### Jobs

- `SendSubscriptionEmailJob` - Queued email notifications

### Commands

- `ConvertExpiredTrials` - Daily trial conversion

## Database Migrations

1. `0001_01_01_000000_create_users_table` - Users, password resets, sessions
2. `0001_01_01_000001_create_jobs_table` - Queue tables
3. `2024_12_14_000001_create_plans_table` - Plans
4. `2024_12_14_000002_create_subscriptions_table` - Subscriptions with indexes

## Performance

N+1 Query Fix:

- Before: 101 queries for 50 subscriptions
- After: 3 queries (constant)
- Method: Eager loading with `->with(['user', 'plan'])`

## Security

- Policy-based authorization
- Form request validation
- SQL injection protection (Eloquent ORM)
- CSRF protection
- Password hashing (bcrypt)

## Code Standards

- PSR-12 compliant
- Strict type declarations
- Service layer pattern
- Dependency injection
- Transaction safety
- Comprehensive error handling
- Extensive logging

## I have done the application in laravel as discussed

<img width="812" height="727" alt="tsetrun" src="https://github.com/user-attachments/assets/48575aac-9f77-43d4-a4d9-e28cdb56980d" />
<img width="836" height="220" alt="converting trials" src="https://github.com/user-attachments/assets/321a54b3-7e4b-45a5-a489-011b1bdc92e6" />
<img width="1190" height="7<img width="1097" height="630" alt="email" src="https://github.com/user-attachments/assets/017382bd-b008-4bc0-bf0c-946d0b068ecd" />
<img width="1097" height="630" alt="email" src="https://github.com/user-attachments/assets/45a41b69-a51b-44d2-9f12-5a482f8cd733" />


