# AGENTS.md

## Project

Tour Management — Laravel 13 REST API for managing tournaments (turnamen), teams (tim), players (pemain), matches (pertandingan), and registrations (pendaftaran). Backend-only, no frontend build step.

## Setup

```bash
composer setup
```

Copies `.env.example` to `.env`, generates app key, runs migrations, installs npm deps, and builds assets.

## Dev server

```bash
composer dev
```

Runs concurrently: `php artisan serve`, queue worker, pail (log viewer), and vite.

## Commands

```bash
# Test (clears config cache first, then runs PHPUnit)
composer test

# Single test file
php artisan test --filter=Feature/Auth

# Single test method
php artisan test --filter="test_login_returns_token"

# Lint / format
./vendor/bin/pint

# Static analysis (if configured)
./vendor/bin/phpstan
```

## Architecture

### Domain model (Indonesian naming)

- **Pengguna** (User) — uses `kata_sandi` (not `password`), `id_pengguna` PK, `peran` enum: `admin | penyelenggara | kapten_tim`. Soft deletes.
- **Turnamen** — organized by a `penyelenggara`, has `status_turnamen` (`buka | berlangsung | selesai`) and `kuota_maksimal`.
- **Tim** — team with `id_kapten` FK to Pengguna.
- **Pemain** — player records linked to Tim.
- **Pendaftaran** — registration of a Tim to a Turnamen, with `status_pendaftaran` (`pending | disetujui | ditolak`).
- **RosterTurnamen** — roster per tournament.
- **Pertandingan** — match between two teams with `skor_1`, `skor_2`, `babak`, `status_pertandingan`.

### Migration order

`pengguna` → `turnamen` → `tim` → `pemain` → `pendaftaran` → `roster_turnamen` → `pertandingan` → SQL View/Trigger/Procedure. Foreign key dependencies are strict — do not reorder.

### SQL objects (migration `000008`)

- **View** `v_jadwal_publik` — denormalized match + tournament + team data for public schedule endpoint.
- **Trigger** `after_pendaftaran_update` — auto-transitions tournament to `berlangsung` when enough registrations are approved.
- **Procedure** `generate_bracket` — shuffles approved teams into first-round matches.

These are MySQL-specific (stored procedures, triggers). Tests use SQLite in-memory (`phpunit.xml`) so these SQL objects will NOT be present during tests.

### Auth

- **Guard config** (`config/auth.php`): provider points to `App\Models\Pengguna`, not `User`. Do not change this.
- **Password field**: `kata_sandi` everywhere — model fillable, auth check, registration. Model overrides `getAuthPassword()` to return `kata_sandi`.
- **API auth**: Laravel Sanctum with `auth:sanctum` middleware.
- **Role middleware**: `App\Http\Middleware\CheckRole` accepts variadic allowed roles. **Not yet registered as an alias** in `bootstrap/app.php` — register it if routes need `role:` middleware.
- **Breeze auth routes** exist in `routes/auth.php` for web-based session auth (login, register, password reset). API auth lives in `routes/api.php` via `AuthController`.

### Routes (`routes/api.php`)

- **Public**: `POST /register`, `POST /login`, `GET /turnamen`, `GET /jadwal`, `GET /jadwal/{id}`
- **Protected** (`auth:sanctum`): profile, tournament creation, registration approval/rejection, score updates, CRUD for tim/pemain/pengguna

### API response format

All controllers should use the `App\Traits\ApiResponser` trait:

```php
// Success: { "status": "success", "message": "...", "data": ... }
$this->successResponse($resource, 'Message.', 200);

// Error: { "status": "error", "message": "...", "errors": ... }
$this->errorResponse('Message.', 400, $errors);
```

### Form Requests & Resources

- `StoreTurnamenRequest`, `StorePendaftaranRequest`, `UpdateSkorRequest` — validate before controller logic.
- `TurnamenResource`, `PertandinganResource` — wrap model output.

## Conventions

- **Language**: Code comments, route group labels, variable names, and form validation messages are in Indonesian.
- **Naming**: Tables and columns use snake_case Indonesian (`id_pengguna`, `kata_sandi`, `no_telepon`, `status_pertandingan`). Models are PascalCase Indonesian (`Pengguna`, `Turnamen`).
- **PKs**: Custom primary keys like `id_pengguna`, `id_turnamen`, etc. (not default `id`).
- **Soft deletes**: Pengguna, Tim, and other models use `SoftDeletes`.
- **PHP 8.3+** required.
- **No frontend assets** currently — `package.json` does not exist. npm scripts in `composer.json` are for potential future use.

## Testing

- PHPUnit 12 with SQLite in-memory DB (configured in `phpunit.xml`).
- Test suites: `Unit` and `Feature`.
- Stored procedures, triggers, and views from migration `000008` are MySQL-only — they will not work in test SQLite. Write integration tests that don't depend on these SQL objects, or mock at the application layer.

## Key gotchas

- **`kata_sandi` not `password`**: The Pengguna model uses `kata_sandi` as the password column. Never use `password` as a key when creating/updating Pengguna records.
- **No `role` middleware alias registered**: `bootstrap/app.php` does not register the `role` alias for `CheckRole`. Add it if you create role-protected routes. See `CATATAN_INTEGRASI.md` for the exact registration snippet.
- **Auth config points to `Pengguna`**: `config/auth.php` providers use `App\Models\Pengguna::class`. Do not revert to `User`.
- **`User.php` model exists but is unused**: The default Laravel `User` model is still in `app/Models/User.php` but is not referenced by auth config. Ignore it.
- **Stored Procedures need MySQL**: `generate_bracket` and triggers won't work on SQLite. Tests must not rely on them.
