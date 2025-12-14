# Installation Guide

## Step-by-Step Setup

### 1. System Requirements

- PHP 8.1 or higher
- PostgreSQL 12+
- Composer

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=subscriptions
DB_USERNAME=postgres
DB_PASSWORD=your_password_here
```

### 5. Create Database

```bash
psql -U postgres -c "CREATE DATABASE subscriptions;"
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed Test Data

```bash
php artisan db:seed
```

### 8. Start Application

Terminal 1:
```bash
php artisan serve
```

Terminal 2:
```bash
php artisan queue:work
```

### 9. Access Application

Visit: http://localhost:8000

Login with: alice@example.com / password

## Production Deployment

### Cron Setup

```bash
crontab -e
```

Add:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker

Use Supervisor:
```ini
[program:subscription-worker]
command=php /path-to-project/artisan queue:work
autostart=true
autorestart=true
```

### Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Done!
