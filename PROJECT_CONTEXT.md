# PROJECT_CONTEXT

## Project Overview
An Arabic-language **elections / voting management** web application. It handles the full election lifecycle: creating elections (single-candidate or list/group based), generating randomized voter codes, assigning voters to leaders & groups, launching multi-round voting, capturing votes, and publishing results. The UI is entirely Right-To-Left (Arabic) and oriented around three roles: **admin**, **leader**, and **voter** (with an additional **superadmin** and read-only **guest** access).

Built on **Laravel 11**, served via **Laravel Octane + RoadRunner**, talking to a **MySQL** database named `election`. The app currently runs under WAMP (`c:\wamp64\www\elections\elections`).

## Tech Stack
- **Language / Runtime**: PHP ^8.2
- **Framework**: Laravel ^11.0
- **App server**: Laravel Octane ^2.4 driven by Spiral RoadRunner (`rr` binary + `.rr.yaml`); also runs under classic FPM/Apache.
- **Database**: MySQL (schema/data shipped as SQL dumps — see [Dump20260426.sql](Dump20260426.sql))
- **Auth tokens**: Laravel Sanctum (only used for one API route; web auth is custom & session-based)
- **Excel I/O**: `maatwebsite/excel` ^3.1 (bulk profile / card imports)
- **Frontend templating**: Blade (`resources/views/*.blade.php`), RTL Arabic
- **CSS/JS**: Bootstrap 4 + Bootstrap 5 + MDBootstrap + jQuery 3 + Font Awesome + DataTables, mostly loaded from CDN in [layouts/app.blade.php](resources/views/layouts/app.blade.php); some assets cached under [public/css/](public/css/) and [public/js/](public/js/). Vite is configured but mostly unused.
- **Build**: Vite 5 (`npm run dev` / `npm run build`)
- **Dev tooling**: Pint, PHPUnit ^10.5, Mockery, Faker, Spatie Ignition

## Folder Structure
```
elections/                       # project root (Laravel skeleton)
├── app/
│   ├── Console/                 # Console kernel (no custom commands defined)
│   ├── Http/
│   │   ├── Controllers/         # All business logic lives here (see "API overview")
│   │   ├── Middleware/          # Custom: CheckSessionTimeout, CheckSessionExpiry
│   │   └── Kernel.php           # Legacy L10 kernel — Laravel 11 actually uses bootstrap/app.php
│   ├── Models/                  # Eloquent models (mixed naming — see "Coding conventions")
│   └── Providers/               # App, Auth, Broadcast, Event, Route service providers
├── bootstrap/app.php            # Laravel 11 application config (middleware aliases live here)
├── config/                      # Standard Laravel config (app, auth, database, session, …)
├── database/
│   ├── migrations/              # ONLY the default Laravel ones — production schema is in the SQL dumps
│   ├── seeders/DatabaseSeeder.php
│   └── factories/UserFactory.php
├── resources/
│   ├── views/                   # ~35 Blade pages (admin, candidate, election, voter, leader managers, guest, results, etc.)
│   ├── views/layouts/app.blade.php   # Master layout with Arabic RTL navbar
│   ├── views/auth/              # login, register, verify (mostly unused — login flow is custom)
│   ├── css/, sass/, scss/, js/  # Vite source assets (largely empty/minimal)
│   ├── fonts/, images/          # Static assets used at build time
├── routes/
│   ├── web.php                  # All app routes (loginController + 8 resource controllers)
│   ├── api.php                  # Single Sanctum-protected `/user` route
│   ├── channels.php, console.php
├── public/                      # Web root — index.php, .htaccess, css/js/fonts/images, profile_picture/, uploads/
├── storage/, vendor/, tests/
├── .env                         # MySQL conn config (see "Important files")
├── .rr.yaml                     # RoadRunner config (Octane worker on :8000)
├── rr                           # RoadRunner binary (~57 MB, committed)
├── composer.json, package.json, vite.config.js, phpunit.xml
├── Dump20240514 (2).sql, Dump20240519.sql, Dump20260426.sql, bb.sql   # DB snapshots
└── PROJECT_CONTEXT.md           # This file
```

## Important Files
- [routes/web.php](routes/web.php) — central route map for the entire app
- [bootstrap/app.php](bootstrap/app.php) — Laravel 11 bootstrap; registers the `checkSessionExpiry` middleware alias
- [app/Http/Kernel.php](app/Http/Kernel.php) — leftover L10 kernel; still defines `CheckSessionTimeout` in the global stack and route middleware aliases. **Both files exist**; verify which one is actually authoritative before editing middleware.
- [app/Http/Controllers/loginController.php](app/Http/Controllers/loginController.php) — bespoke login flow (superadmin/admin/voter branching, session bookkeeping, event logging)
- [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php) — master layout, Arabic navbar, reset/logout JS
- [resources/views/welcome.blade.php](resources/views/welcome.blade.php) — landing/login page
- [.env](.env) — MySQL config (`DB_DATABASE=election`, user `root`, no password); session driver is `database`, lifetime `1`
- [.rr.yaml](.rr.yaml) — Octane/RoadRunner worker config (`0.0.0.0:8000`, 4 workers, static from `public/`)
- [Dump20260426.sql](Dump20260426.sql) — newest schema + seed data; authoritative DB structure
- [composer.json](composer.json), [package.json](package.json), [vite.config.js](vite.config.js)

## Coding Conventions
- **Model naming is inconsistent** — both PascalCase (`Election`, `Voter`, `Leader`, `CandidatesGroup`, `Setting`) and lowercase (`candidates`, `users`, `elections`) class names coexist in [app/Models](app/Models/). The lowercase classes are duplicates with a thinner schema (e.g. `users` vs `User`, `elections` vs `Election`) — prefer the PascalCase ones; the lowercase ones are referenced from controllers and cannot be removed without refactoring.
- **Tables are explicit** on every model (`protected $table = ...`) — table names don't follow Laravel's pluralization rules (e.g. `voters_group`, `leader_voter_rel`, `vote_detail`, `vote_master`, `event_table`, `profiles_infos`).
- **Primary keys are explicit and custom** (`idelection`, `idleaders`, `idvoters`, `idgroups`, …).
- **Mass-assignment via `$fillable`** is the rule.
- **Timestamps**: most models have `public $timestamps = true`; tables like `candidates`, `users`, `leaders`, `vote_detail`, `event_table`, `profiles_infos` have it `false`.
- **Controllers do everything** — there are no Form Requests, Resources, Services, Repositories, Policies, or Events. Validation is inline (mostly absent). Business logic, queries, and view rendering all live in the controller methods.
- **Heavy use of raw SQL** via `DB::select(...)` / `DB::statement(...)` / `DB::table(...)`. Many queries interpolate `$variable` directly into SQL strings — exercise care: most inputs come from authenticated session state, but **assume injection risk** when refactoring (do not introduce new interpolated raw SQL).
- **Per-request MySQL views** — several controllers (e.g. `VoterController::getvotersbyelection`) `CREATE OR REPLACE VIEW voters_{session_id}` to scope queries. Don't be alarmed by transient view names.
- **`DB::statement("SET sql_mode=''")`** is sprinkled before queries to relax `ONLY_FULL_GROUP_BY`. Many `GROUP BY` queries depend on this.
- **Table locking** — saves use `DB::unprepared('LOCK TABLES ... WRITE')` + `beginTransaction` + `UNLOCK TABLES`. Mirror this pattern when adding write paths in the same areas.
- **Arabic/Persian digit normalization** — `loginController::faTOen()` converts ٠-٩ / ۰-۹ → 0-9. Apply when accepting numeric input from Arabic keyboards.
- **Session bookkeeping** — virtually every controller method gates on `session('full_name', '')` and returns the `welcome` view when empty. Auth is **not** via Laravel's `Auth` facade.
- **Status integer codes** (no enums) — examples:
  - `elections.election_type`: 1 = single-candidate, 2 = list/group
  - `elections.election_status`: 0 = inactive, 1 = active
  - `election_rounds.round_status`: 0 = pending, 1 = active, 2 = finished
  - `candidates.candidate_status`: 1 = nominated, 2 = winner
  - `users.admin / isleader / isvoter`: 0/1 role flags
- **UI strings are Arabic literals** baked into Blade. There is no i18n layer.
- **Comments and dead code** — controllers contain large commented-out blocks; treat them as historical context rather than active code.

## Database Notes
- Connection: MySQL, database name `election`, charset `utf8mb3`/`utf8mb4`, collations `utf8mb3_unicode_ci` (most) and `utf8mb4_0900_ai_ci` (`profiles_infos`).
- **The Laravel `database/migrations/` folder only contains the three default Laravel scaffolding migrations** (users / cache / jobs). It does **not** describe the real schema. The production schema is defined in the SQL dumps at the project root — load `Dump20260426.sql` (or `bb.sql`) into MySQL to get a working database.
- **Core domain tables** (from Dump20260426.sql):
  - `profiles` — master person record (`profile_code` PK, `full_name`, `sex`, `age`, `mobile`, `address`, `picture`, `attachment`, `isconnected`, `session_handle`)
  - `profiles_infos` — extended bio (residence, joiningdate, category, education, work history JSON, identifiers, etc.)
  - `users` — login credential rows (`user_code`, role flags `admin`/`isleader`/`isvoter`, FK to `election_code` + `profile_code`); unique `(user_code, election_code)`
  - `admins` — separate admin role table (`user_code`, `profile_code`, `status`)
  - `elections` — election header (`election_code`, `election_name`, `election_type`, `election_status`, `election_date`, `logo`, `win_number`)
  - `election_rounds` — per-election round config (`round_number`, `win_percentage`, `min_win_percentage`, `win_sign`, `round_status`)
  - `candidates_groups` — lists/factions inside an election (`group_code`, `group_name`, `win_number` per list)
  - `candidates` — candidate slots per (`profile_code`, `elections_code`, `round_number`, `group_code`) with `candidate_status`
  - `voters` — eligible voters per election (`profile_code`, `election_code`, `voter_status`, `voter_group_code`)
  - `voters_group` — voter cohorts (`voter_group_code`, `voter_group_name`, `election_code`)
  - `leaders` — leader assignments per election (`profile_code`, `voter_group_code`, `election_code`)
  - `leader_voter_rel` — leader↔voter mapping
  - `vote_master` — one row per cast vote (`vote_code`, `election_code`, `round_number`, `user_code`, `vote_date`)
  - `vote_detail` — chosen candidates per vote (`election_list_code`, `round_number`, `candidate`)
  - `vote_status` — secondary vote tracking
  - `event_table` — audit log (`prf_code`, `user_code`, `connected`, `loggedin_datetime`, `loggedout_datetime`, `event_description`, `vote_description`, `session_handle`). PK is `(loggedin_datetime, event_description, session_handle)`.
  - `subjects` — generic content topics (`title`, `description`, `picture`)
  - `settings` — key/value config (`settings_name`, `settings_value`)
  - `list_master`, `profile_connection`, `groups_master` — present in some dumps, unused in others
- **Laravel framework tables** also exist: `sessions` (used because `SESSION_DRIVER=database`), `cache`, `jobs`, `users` (the default Laravel `users` table coexists with the domain `users` table — both are mapped to the same physical table).
- **Several models reference classes that are not in `app/Models/`**: `Profiles`, `Votemaster`, `ListMasters`. These cause runtime `Class not found` errors in code paths that hit them — see "Known TODOs / Issues" below.

## API Overview
All app endpoints are **web routes** in [routes/web.php](routes/web.php) (cookie-session + CSRF). The only API route is `/api/user` (Sanctum-protected, returns the authenticated user) in [routes/api.php](routes/api.php).

Web routes grouped by controller:

| Area | Routes | Controller |
|---|---|---|
| Auth & session | `/`, `POST /home`, `GET /guesthome`, `GET /dashboard`, `GET /logout/{profile_code}`, `GET /resetdata`, `GET /resetlogin`, `GET /activeelection`, fallback → `/` | [loginController](app/Http/Controllers/loginController.php) |
| Settings | `GET /settings`, `POST /savesettings` | [SettingsController](app/Http/Controllers/SettingsController.php) |
| Users / Profiles | `GET /usermanager{/prfcode?}`, `GET /userslist`, `POST /saveuserinfo`, `POST /deleteuser`, `POST /importexcel`, `POST /uploadusersfolder`, `PUT /resetusercode/{prfcode}`, `GET /idcard`, `GET /getidcard/{prfcode}/{election_code}`, `GET /getevents/{prfcode}`, `POST /saveprofileextrainfo`, `POST /importcardinfo`, `GET /adminresults` | [UsersController](app/Http/Controllers/UsersController.php) |
| Subjects | `GET /subjectmanager`, `GET /subjectslist`, `POST /savesubjectinfo` | [SubjectController](app/Http/Controllers/SubjectController.php) |
| Elections | `GET /electionmanager{/electioncode?}`, `GET /electionwithlistmanager`, `GET /electionlauncher`, `GET /electionslist`, `POST /saveelectioninfo`, `GET /electioninfo/{eleccode}/{roundnumber}`, `GET /getProfiles/{eleccode}`, `PUT /updatestatus/{election_code}/{election_status}/{codingvar}`, `PUT /updateLaunchstatus/{election_code}/{round_number}/{round_status}`, `POST /deleteelection` | [ElectionController](app/Http/Controllers/ElectionController.php) |
| Candidates | `GET /candidatemanager`, `GET /candidatemanager/{election_code}/{group_code}/{round_number}`, `POST /savecandidateinfo`, `POST /savecandidatelist`, `DELETE /deletecandidatelist/{group_code}`, `POST /resetcandidate` | [CandidateController](app/Http/Controllers/CandidateController.php) |
| Voters & voting | `GET /votermanager`, `POST /savevoterinfo`, `POST /savevotergroup`, `POST /savevote`, `GET /votersperc/{eleccode}`, `GET /loggedinperc/{eleccode}`, `GET /voterprofiles{forgroups?}/{eleccode}`, `GET /getvoterstatus/{usercode}/{eleccode}/{roundcount}`, `GET /getvotersforleaderinfo/{datevar}/{eleccode}`, `PUT /genearteresults/{eleccode}`, `GET /guestresults/{eleccode}`, `GET /getelectionresults/{eleccode}/{roundnumber}`, `GET /getvotersbyelection/{eleccode}/{votestatus}/{clickvar}/{datetosend}`, `GET /getvoterschoosen/{hashMapJson_str}` | [VoterController](app/Http/Controllers/VoterController.php) |
| Voter groups | `GET /groupmanager`, `GET /groupslist`, `GET /getvotergroups/{electioncode}`, `PUT /updatevotergroup/{voter_group_code}`, `DELETE /deletevotergroup/{voter_group_code}` | [GroupController](app/Http/Controllers/GroupController.php) |
| Leaders | `GET /leadermanager`, `GET /leaderslist`, `GET /leaderdash`, `GET /getvotersbyleader/{eleccode}/{leader_code}`, `GET /getvotersbyelectioncode/{eleccode}`, `POST /saveleaderinfo` | [LeaderController](app/Http/Controllers/LeaderController.php) |

Endpoints variously return HTML (Blade views), `json_encode($data)` strings, raw scalars, or `response()->json()` — the response shape is not uniform per route; check the controller method when integrating.

## Core Business Logic
- **Login flow** ([loginController::gotohomepage](app/Http/Controllers/loginController.php)):
  1. Normalize Arabic digits in `user_code`.
  2. If `user_code` + `super_pass` match `SUPERADMIN_USER`/`SUPERADMIN_PASS` env vars → superadmin session, `dashboard` view.
  3. Else if `user_code === APP_DEFAULTUSER` env → default admin session, `dashboard` view.
  4. Else if found in `admins` table → admin session (joins `profiles` for full name), `admin` view with rounds map.
  5. Else lookup in `users` → set `guest_usercode` session and redirect to `guesthome` (the voter UI), gated on `elections.election_status = 1` and `profiles.isconnected = 0`.
  6. Every login appends an `event_table` row with `session_handle = Session::getId()`.
- **Election creation** ([ElectionController::saveElectionInfo](app/Http/Controllers/ElectionController.php)) — locks `elections` table, computes next `election_code` as `ele_{max+1}`, persists rounds, optionally saves a logo. After activation, `GenerateUsersForElections` auto-issues 4-digit random `user_code` to every profile that doesn't yet have a non-admin user row.
- **Voter assignment** is two-level: voters → `voter_group_code` → leader (via `leader_voter_rel`).
- **Voting** ([VoterController::saveVote](app/Http/Controllers/VoterController.php)) — writes one `vote_master` row plus N `vote_detail` rows; result tallying via `genearteresults` mutates `candidates.candidate_status` to 2 for winners.
- **Session timeouts** — `CheckSessionTimeout` middleware runs globally; if it triggers, it flips `profiles.isconnected = 0` and redirects.

## Common Commands

### Local serving
| Goal | Command |
|---|---|
| Bring up the DB | Load `Dump20260426.sql` into MySQL: `mysql -u root election < "Dump20260426.sql"` (the dump itself contains `CREATE DATABASE IF NOT EXISTS election`). |
| Generate app key | `php artisan key:generate` (the shipped `.env` has an empty `APP_KEY`). |
| Run via classic dev server | `php artisan serve` |
| Run via Octane + RoadRunner | `php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000` (or run the bundled `rr` binary against `.rr.yaml`). |
| Frontend dev | `npm install && npm run dev` |
| Frontend build | `npm run build` |
| Tinker REPL | `php artisan tinker` |
| Tests | `vendor/bin/phpunit` (or `php artisan test`) — config in [phpunit.xml](phpunit.xml) |
| Lint / format | `vendor/bin/pint` |
| Cache clears | `php artisan config:clear`, `route:clear`, `view:clear`, `cache:clear` |
| Octane reload after code change | `php artisan octane:reload` (Octane keeps PHP state warm — always reload after editing controllers/models). |

### Maintenance endpoints (UI-driven, not CLI)
- `GET /resetdata` — **DESTRUCTIVE**: truncates every table in the database. Triggered from the navbar gear menu.
- `GET /resetlogin` — resets `profiles.isconnected` and clears `session_handle` for all profiles.

## Architecture Notes
- **Monolithic Laravel MVC** with custom session-based auth on top — Laravel's auth/guards/middleware are largely bypassed in favor of `session('full_name')` / `session('user_code')` / `session('profile_code')` checks at the top of nearly every controller method.
- **Octane workers keep app state in memory**, so global statics, container singletons, and request-scoped state need extra care. Avoid stashing per-request data on services. `DB::statement("SET sql_mode=''")` is intentionally repeated per method because the worker may have any prior sql_mode setting.
- **Server-rendered Blade + jQuery/AJAX** sprinkled on top — there is no SPA, no API layer beyond a single Sanctum endpoint, and no frontend framework. Endpoints return HTML or JSON depending on the use case.
- **CSRF is enforced** by the standard `VerifyCsrfToken` middleware. All POSTs from Blade forms include `{{ csrf_field() }}`.
- **`bootstrap/app.php` (Laravel 11 style) and `app/Http/Kernel.php` (Laravel 10 style) BOTH exist.** Laravel 11 uses `bootstrap/app.php` as the entry point; the legacy `Kernel.php` is dead unless re-wired. Treat `bootstrap/app.php` as authoritative for routing/middleware bootstrap, but be aware that the legacy `Kernel.php` lists the full middleware stack the previous developers expected (e.g. CSRF, encrypt cookies, session, CheckSessionTimeout) — `bootstrap/app.php` only registers the `checkSessionExpiry` alias, relying on Laravel 11 defaults for everything else.
- **Two parallel "user" concepts**: framework `User` model (Sanctum, default users table) vs domain `users` model (voter login codes). They share the physical `users` table but read/write different column subsets.

## Known TODOs / Issues
- **Missing model files referenced by controllers**: `App\Models\Profiles`, `App\Models\Votemaster`, `App\Models\ListMasters` are imported and called across [loginController](app/Http/Controllers/loginController.php), [ElectionController](app/Http/Controllers/ElectionController.php), [CandidateController](app/Http/Controllers/CandidateController.php), [VoterController](app/Http/Controllers/VoterController.php), [UsersController](app/Http/Controllers/UsersController.php), etc., but no class definitions exist under `app/Models/`. Either the files were deleted in error or are loaded from a non-PSR-4 path. Searches by `Profiles::` would 500 at runtime unless restored. Restore these models (table names appear to be `profiles`, `vote_master`, `list_master` based on usage) before adding tests or refactoring those code paths.
- **`CheckSessionTimeout` bug**: in [CheckSessionTimeout.php](app/Http/Middleware/CheckSessionTimeout.php), the variable `$profile_code` used in the `where()` clause is undefined — should be `$prf_code`. The early-return condition (`! has('lastActivityTime')`) is also inverted from what the comment says.
- **`CheckForMaintenanceMode.php`** is an empty file.
- **`voterguestpage.blade.php`** is an empty file.
- **Schema drift**: actual production schema (in SQL dumps) is far richer than what's in `database/migrations/`. There is no migration history for any domain table. New developers cannot bootstrap a database from migrations alone.
- **SQL injection surface**: heavy use of string interpolation in raw queries (e.g. `where('voter_status', '!=', '')` is fine, but `DB::select("... '$electioncode' ...")` patterns are widespread). Most params come from session/path bindings, but they should be moved to bindings.
- **Inconsistent model class naming** — duplicated `Election`/`elections`, `User`/`users` classes (see "Coding conventions"). Consolidate when feasible.
- **Empty `app.css`**, near-empty Vite SCSS — frontend pipeline is set up but unused; most CSS/JS is CDN-loaded in `layouts/app.blade.php`.
- **Default Laravel auth scaffolding (`auth/login.blade.php`, `register.blade.php`)** is still present but not wired to any route.
- **Hardcoded Arabic strings** make i18n/translation a future task.
- **`Dump20240514 (2).sql`**, **`Dump20240519.sql`**, **`bb.sql`** are older snapshots; **`Dump20260426.sql`** is the latest.

## Important Dependencies
**PHP (composer.json)**
- `laravel/framework:^11.0` — core framework
- `laravel/octane:^2.4`, `spiral/roadrunner-cli:^2.6.0`, `spiral/roadrunner-http:^3.3.0` — production worker
- `laravel/tinker:^2.9` — REPL
- `laravel/ui:^4.5` — provides default auth scaffolding (unused by custom login)
- `maatwebsite/excel:^3.1` — Excel imports (used in `importexcel`, `uploadusersfolder`, `importcardinfo`)
- `symfony/console:>=7.0 <7.4` — pinned to avoid an Octane CLI incompatibility
- dev: `laravel/pint`, `phpunit/phpunit:^10.5`, `mockery/mockery`, `nunomaduro/collision`, `fakerphp/faker`, `spatie/laravel-ignition`

**JS (package.json)**
- `vite:^5.0` + `laravel-vite-plugin:^1.0`
- `bootstrap:^5.3.3`, `mdb-ui-kit:^7.3.2`, `axios:^1.6.4`, `sass:^1.77.0`

## Development Workflow
1. **Bootstrap DB**: `mysql -u root election < "Dump20260426.sql"`.
2. **Configure env**: copy/inspect `.env`. Make sure `DB_DATABASE=election`, run `php artisan key:generate`, optionally set `APP_DEFAULTUSER`, `SUPERADMIN_USER`, `SUPERADMIN_PASS` (these are read directly from env in `loginController`).
3. **Install deps**: `composer install` then `npm install`.
4. **Run the app**: `php artisan serve` for quick iteration, or `php artisan octane:start --server=roadrunner` to match production. Frontend: `npm run dev` (Vite HMR) is only useful when editing `resources/css/app.css` / `resources/js/app.js` — most Blade pages don't use it.
5. **Reload after code changes** under Octane: `php artisan octane:reload`.
6. **Edit conventions**: when adding controllers, follow the existing pattern (session gate → `DB::statement("SET sql_mode=''")` → query → return view/json). Avoid introducing Form Requests/Resources unless refactoring widely.
7. **Schema changes**: because there are no canonical migrations, add an explicit Laravel migration for any new table or column AND update one of the SQL dumps (or create a new one). Otherwise new environments won't pick up the change.
8. **Tests**: PHPUnit is configured but the suite is empty beyond defaults. Run `vendor/bin/phpunit` to verify nothing is broken; add Feature tests for new controllers.
9. **Linting**: `vendor/bin/pint` will auto-format to Laravel style.
10. **Deploy / serve**: target machine needs PHP 8.2+, MySQL 8, RoadRunner (`rr` binary committed at repo root works on Windows x64; bring your own for Linux). RoadRunner serves both PHP and static files from `public/`.

## Production Deployment (DigitalOcean)

The app is deployed on a DigitalOcean droplet at `https://elections-vote.duckdns.org`. Full step-by-step runbook lives in [deploy/DEPLOYMENT.html](deploy/DEPLOYMENT.html) (open in a browser for the nicely-formatted version).

### Phase 1 — Small test droplet (~$18/mo, 20 users)

| Component | Choice | Why |
|---|---|---|
| Provider | DigitalOcean | Cheap, predictable, easy snapshots |
| Droplet | `s-2vcpu-2gb-amd` (Premium AMD, NVMe) | ~$18/mo, fits the 20-user test |
| OS | Ubuntu 22.04 LTS | Most stable for PHP stack |
| Region | Frankfurt (`FRA1`) | Lowest latency for Middle East users |
| Domain | `elections-vote.duckdns.org` (free DuckDNS) | No need to buy a domain; HTTPS works |
| TLS | Let's Encrypt via certbot | Free, auto-renews every 60 days |
| Web server | Nginx (reverse proxy on :80/:443) | Light memory footprint vs Apache |
| App server | Laravel Octane + RoadRunner (6 workers) | Multi-process, persistent app state |
| Database | MySQL 8 (local, tuned for 2 GB) | Native fit for app's existing schema |
| Sessions | Redis | Faster than DB sessions under load |
| Auto-start | systemd service `elections-octane` | Survives reboots and crashes |

### Phase 2 — Scale up for the real event (~$84/mo, 500 users)

Same droplet, **vertical resize only** (~5 min downtime, same IP, same TLS cert, same data):
- Resize: `s-2vcpu-2gb-amd` → `g-4vcpu-16gb` (General Purpose)
- Bump RoadRunner workers: `6 → 16`
- Bump MySQL `innodb_buffer_pool_size`: `512M → 4G`
- Bump MySQL `max_connections`: `50 → 200`

### Phase 3 — Return to small after the event

Reverse Phase 2: snapshot the event-state, resize back to `s-2vcpu-2gb-amd`, bring buffer/workers back to Phase 1 values. Cost drops to $18/mo immediately.

### Bugs encountered during initial deploy (and lessons)

The deployment took longer than expected because of three stacked bugs that masked each other:

1. **Trailing `#` comments in `.env` corrupted the parser** — PHP dotenv handled trailing comments inconsistently for lines ending in `=` (base64 padding). Fix: never put comments on the same line as a value in `.env`.
2. **Stale `artisan serve` processes** — `pkill -f 'artisan serve'` did not match the running process, so every "restart" silently failed and `curl` kept hitting the old broken process. Fix: always use `fuser -k 8001/tcp` to kill by port.
3. **Shell env var pollution through sudo** — An earlier `export APP_KEY=...` persisted as an empty string through `sudo` and overrode `.env` values. Fix: always open a fresh SSH session before `config:cache`, and **never use `env()` outside `config/*.php` files** — use `config('app.foo')` instead.

Other deployment-specific findings:

- The dump file `final.sql` contains `DEFINER=root@...` clauses that need MySQL `SUPER` privilege. Load it as `sudo mysql election < final.sql` (root via socket auth), then let the app connect as `elections_app`.
- The `rr` binary committed at the repo root turned out to be Linux-compatible (not Windows-only as PROJECT_CONTEXT.md previously claimed). The `chmod +x rr` was the only step needed.
- Laravel Octane 2.x does not expose an `octane:worker` command. Use `php artisan octane:start --server=roadrunner --workers=N`; Octane internally manages RoadRunner. Custom `.rr.yaml` files referencing `octane:worker` will silently kill workers with `Network: EOF`.
- The login flow + welcome page originally called `env('SUPERADMIN_USER')` directly from the controller and the Blade view. With `config:cache` enabled (the production default), those calls return `null`. Added `config('app.default_user')`, `config('app.superadmin_user')`, `config('app.superadmin_pass')` to [config/app.php](config/app.php) and updated the call sites.
