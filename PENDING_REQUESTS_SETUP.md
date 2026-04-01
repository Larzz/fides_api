# Pending Requests API — setup

This module adds `request_types`, `requests`, and `request_attachments`, plus authenticated routes under the same Laravel app.

## Schema note

`requests.details` is stored as `TEXT` (not `VARCHAR(255)`) so UI summaries like *"Annual · 18–20 Mar (3 days)"* are not truncated.

## Sample `.env` excerpt

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fides_api
DB_USERNAME=root
DB_PASSWORD=

# Sanctum (first-party SPA / mobile — adjust for your domain)
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SESSION_DRIVER=cookie
```

## Artisan — greenfield / local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=RequestTypeSeeder
php artisan db:seed --class=PendingRequestsUserSeeder
php artisan db:seed --class=PendingRequestDemoSeeder
```

Or run the full `DatabaseSeeder` (includes other dashboard seeders):

```bash
php artisan db:seed
```

## Auth smoke test

```bash
curl -s -X POST http://localhost:8000/api/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"pending-admin-1@example.com","password":"password"}'
```

Use the returned `data.token` as `Authorization: Bearer …` on:

- `GET /api/requests`
- `GET /api/dashboard/stats`

Employees use:

- `GET|POST /api/my-requests` with the same token model.

## Storage

Attachments are stored on the `local` disk under `storage/app/requests/{user_id}/`.

```bash
php artisan storage:link
```

(Optional if you serve files via web.)
