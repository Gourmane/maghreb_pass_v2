# MaghrebPass Developer Guide

This guide is for developers who need to work on MaghrebPass together in the same repository. It explains how to run the project, where the main code lives, how the database and images are handled, and how to make changes without breaking another developer's work.

## 1. Project Summary

MaghrebPass Advanced V2.5 is a web application for tourists and football supporters visiting Morocco for the 2030 World Cup.

The project contains:

- A Laravel 12 REST API backend.
- A React 19 + Vite frontend.
- MySQL persistence.
- Laravel Sanctum authentication for the SPA, using an HTTP-only `maghrebpass_token` cookie.
- Public catalog features for matches, hotels, restaurants, attractions, packages, and map items.
- Tourist features for favorites, reservations, profile management, and trip planning.
- Admin features for catalog management, users, reservations, packages, statistics, and photo uploads.

## 2. Repository Map

The application code is under the `maghreb_pass` directory.

```text
maghreb_pass/
|-- backend/                 Laravel API, database migrations, seeders, tests
|-- frontend/                React SPA
|-- database_export/         Catalog JSON used by the catalog seeder
|-- docs/                    Project documentation
|-- images/                  Source/project images kept in the repository
|-- README.md                Main quick-start documentation
|-- PRODUCT.md               Product notes
|-- PRD_*.md                 Product requirements
```

Important backend areas:

```text
backend/
|-- app/Http/Controllers/Api/        API controllers
|-- app/Http/Requests/Api/           Request validation
|-- app/Http/Resources/              API response shaping
|-- app/Models/                      Eloquent models
|-- database/migrations/             Database schema history
|-- database/seeders/                Demo and catalog data generation
|-- routes/api.php                   Main API route map
|-- tests/Feature/                  Main behavior coverage
```

Important frontend areas:

```text
frontend/src/
|-- App.jsx                         Route and application composition
|-- components/                     UI screens and shared feature components
|-- i18n/                           FR/EN text resources
|-- lib/api.js                      Axios API client
|-- lib/catalog.js                  Frontend fallback catalog helpers
|-- styles.css                      Main styling
```

## 3. Technology Stack

| Area | Technology |
| --- | --- |
| Backend | PHP 8.2+, Laravel 12 |
| Auth | Laravel Sanctum |
| Database | MySQL |
| Frontend | React 19, Vite |
| HTTP client | Axios |
| Maps | Leaflet, React Leaflet, OpenStreetMap links |
| Styling | Tailwind CSS Vite integration plus project CSS |
| Localization | `react-i18next`, French and English |
| Backend tests | Laravel test suite / PHPUnit |
| Frontend validation | Vite production build |

## 4. Local Prerequisites

Install these before running the project:

- PHP 8.2 or newer.
- Composer.
- MySQL with the `pdo_mysql` PHP extension enabled.
- Node.js and npm compatible with the current Vite toolchain.
- Git.

The backend and frontend run separately during development.

## 5. First Local Setup

### 5.1 Create the MySQL Database

Create the database before running Laravel migrations.

The expected local database name in `backend/.env.example` is:

```env
DB_DATABASE=advenced_maghrebpass_v2
```

### 5.2 Backend Setup

From `maghreb_pass/backend`:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Backend API base URL:

```text
http://localhost:8000/api
```

`php artisan migrate:fresh --seed` drops existing tables in the configured local database, rebuilds the schema, and loads demo data. Do not run it against a database that contains data you need to keep.

### 5.3 Frontend Setup

From `maghreb_pass/frontend` in a second terminal:

```bash
npm.cmd install
copy .env.example .env
npm.cmd run dev
```

Frontend URL:

```text
http://127.0.0.1:5173
```

The frontend expects the Laravel API at:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

## 6. Environment Files

Do not commit local `.env` files.

### 6.1 Backend Environment Notes

Key backend values:

```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=advenced_maghrebpass_v2
DB_USERNAME=root
DB_PASSWORD=
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
FRONTEND_URL=http://localhost:5173
FRONTEND_ALT_URL=http://127.0.0.1:5173
FILESYSTEM_DISK=public
MAIL_MAILER=log
```

For public demos or production-like environments:

- Keep `APP_DEBUG=false`.
- Use a unique `APP_KEY`.
- Keep cookies encrypted.
- Enable secure cookies under HTTPS.
- Do not expose real SMTP or service secrets in the repository.

### 6.2 Frontend Environment Notes

Current frontend environment variable:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

The Axios client in `frontend/src/lib/api.js` uses `withCredentials: true` for the SPA authentication flow.

## 7. Demo Accounts

After seeding the local database:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@maghrebpass.test` | `password` |
| Tourist FR | `tourist@maghrebpass.test` | `password` |
| Tourist EN | `emily.carter@maghrebpass.test` | `password` |

These accounts are for local demo data only.

## 8. How the Application Is Organized

### 8.1 Backend Flow

The API generally follows this path:

1. `routes/api.php` maps the endpoint.
2. A controller receives the request.
3. A Form Request validates input for create/update actions.
4. Eloquent models read or write the database.
5. API Resources normalize response output.

Examples:

- `HotelController` and `HotelResource` expose public hotel data.
- Admin controllers handle protected CRUD actions.
- Reservation controllers enforce tourist reservation behavior.
- `RoleMiddleware` protects admin-only routes.

### 8.2 Frontend Flow

The frontend:

1. Uses the API client from `frontend/src/lib/api.js`.
2. Builds screens in `frontend/src/components`.
3. Reads localized labels from `frontend/src/i18n`.
4. Uses `image_url` first and `photos[0]` as a fallback for catalog cards.

When adding a new screen or feature, check whether it needs:

- A backend route.
- Request validation.
- A Resource response field.
- Frontend API handling.
- FR and EN UI text.
- Tests or build validation.

## 9. Main Feature Areas

### 9.1 Public Features

- Matches.
- Hotels.
- Restaurants.
- Attractions.
- Map items.
- Packages.

### 9.2 Authenticated Tourist Features

- Register, login, logout, and profile updates.
- Favorites for hotels, restaurants, and attractions.
- Hotel and restaurant reservations.
- Payment simulation after admin approval.
- Trip planner and trip items.

### 9.3 Admin Features

- Statistics.
- User activation toggle.
- CRUD for matches, hotels, restaurants, attractions, and packages.
- Package item management and ordering.
- Reservation approval or rejection.
- Photo upload endpoint.

## 10. API Orientation

The source of truth is `backend/routes/api.php`.

Common endpoint groups:

| Group | Examples |
| --- | --- |
| Health | `GET /api/health` |
| Auth | `POST /api/auth/login`, `GET /api/auth/me` |
| Public catalog | `GET /api/hotels`, `GET /api/matches` |
| Favorites | `GET /api/favorites`, `POST /api/favorites` |
| Reservations | `POST /api/hotel-reservations`, `GET /api/my-reservations` |
| Trips | `apiResource /api/trips`, trip item endpoints |
| Admin | `/api/admin/...` protected by authentication and admin role |

Useful supporting documentation:

- `docs/API_EXAMPLES.md`
- `backend/README.md`

## 11. Database Guide

### 11.1 Database Source of Truth

Database structure is defined by Laravel migrations in:

```text
backend/database/migrations
```

Demo and catalog data is generated by seeders in:

```text
backend/database/seeders
```

When the schema changes, create or edit migrations. Do not rely on a teammate manually editing a local MySQL schema to make application code work.

### 11.2 Main Tables

| Table | Purpose |
| --- | --- |
| `users` | Tourists and admins |
| `matches` | Football match catalog |
| `hotels` | Hotel catalog |
| `restaurants` | Restaurant catalog |
| `attractions` | Attraction catalog |
| `favorites` | Polymorphic favorites owned by users |
| `hotel_reservations` | Hotel reservation lifecycle |
| `restaurant_reservations` | Restaurant reservation lifecycle |
| `packages` | Travel package headers |
| `package_items` | Ordered items inside a package |
| `trips` | User trip plans |
| `trip_items` | Ordered items inside a trip |
| `personal_access_tokens` | Sanctum tokens |
| `sessions`, `cache`, `jobs` tables | Laravel application infrastructure |

### 11.3 Key Relationships

- A user can own favorites, reservations, and trips.
- A hotel can have many hotel reservations.
- A restaurant can have many restaurant reservations.
- A favorite stores a polymorphic target through `favoriteable_type` and `favoriteable_id`.
- A package has many ordered `package_items`.
- A trip has many ordered `trip_items`.
- Package and trip items can point to catalog records or use custom text content.

### 11.4 Catalog Tables and Images

Hotels, restaurants, and attractions store:

- `image_url` for a main image URL.
- `photos` as a JSON array for additional image URLs or uploaded public file URLs.

The frontend commonly displays:

1. `image_url` when available.
2. Otherwise the first value from `photos`.

### 11.5 Reservations

Hotel and restaurant reservations use a status lifecycle:

```text
pending -> approved -> confirmed
pending -> rejected
pending/approved -> cancelled
```

The application uses simulated payment:

- New reservation payment state starts as `unpaid`.
- Admin approval does not equal final confirmation.
- The tourist simulation flow sets a confirmed reservation to paid and stores payment metadata.

No real payment provider is part of this project.

### 11.6 Data Seeding

`DatabaseSeeder` currently calls:

- `AdminSeeder`
- `TouristSeeder`
- `CatalogJsonSeeder`
- `PackageSeeder`
- `ReservationSeeder`

`CatalogJsonSeeder` loads catalog data from:

```text
database_export/maghrebpass_data_export.json
```

That JSON export is used for:

- Matches.
- Hotels.
- Restaurants.
- Attractions.

Packages, reservations, and other demo records are produced by their seeders.

### 11.7 Database Change Rules for Team Work

Use these rules when two developers work together:

1. Put schema changes in migrations.
2. Put reusable demo data changes in seeders or the catalog JSON export when appropriate.
3. Mention destructive commands such as `migrate:fresh` in your message or pull request notes.
4. Run migrations after pulling backend schema changes.
5. Keep local ad hoc database experiments out of the shared expected setup unless they are converted into migrations or seed data.

## 12. Images and Manual Content Workflow

Images are important in this project because catalog cards and detail pages depend on them.

### 12.1 Image Options Already Supported

The current backend supports these catalog image paths:

1. Store public HTTPS image URLs in `image_url` or `photos`.
2. Upload local image files through admin create/update flows using `photo_files[]`.
3. Upload one image through the admin upload endpoint and use the returned public URL.

Supported uploaded catalog directories are:

- `hotels`
- `restaurants`
- `attractions`

Uploaded files are stored on Laravel's public disk. Run this once locally after setup:

```bash
php artisan storage:link
```

Without the storage symlink, locally uploaded images may exist on disk but not load from the browser.

### 12.2 Manual Image Entry Rules

If you add images manually:

- Prefer valid direct HTTPS image URLs when data should survive a clean database seed.
- For uploaded local images, use the application/admin upload flow instead of typing guessed storage paths.
- Keep the main image intentional. `image_url` should represent the default image for cards and detail headers.
- Keep `photos` as a valid array of image URLs.
- Verify the image appears in the frontend after the backend data change.
- Do not store binary image files directly inside MySQL rows.

### 12.3 Manual Database Content Rules

Manual local database edits are acceptable for temporary testing, but they are not enough for shared development.

If a manual change must be available to the other developer:

- Convert schema changes into migrations.
- Convert reusable demo data into seeders or the export JSON flow.
- Document any required one-time migration or data command.
- Avoid requiring the other developer to inspect your local database manually.

## 13. Collaboration Workflow

For a two-developer project, keep the workflow explicit.

### 13.1 Before Starting Work

1. Pull the latest shared branch.
2. Read open changes or task notes before editing the same feature area.
3. Run affected setup commands if migrations or dependencies changed.
4. Create a focused branch when using Git branches for collaboration.

Example branch names:

```text
feature/admin-hotel-photos
fix/reservation-status-flow
docs/developer-guide
```

### 13.2 While Implementing

- Keep one change focused on one feature or one bug.
- Avoid mixing backend schema changes, unrelated UI redesigns, and documentation rewrites in the same change.
- If you change an API response field, update the frontend consumer and documentation that depends on it.
- If you change visible UI text, update localization where needed.
- If you change a protected workflow, test with the correct user role.

### 13.3 Before Sharing Work

Run the relevant validation:

```bash
cd backend
php artisan route:list --path=api --no-ansi
php artisan migrate:status --no-ansi
php artisan test --no-ansi
```

```bash
cd frontend
npm.cmd run build
```

Current backend validation confirmed on 2026-05-23:

- 74 API routes.
- 54 tests passed.
- 713 assertions.

Do not run `npm.cmd run build` when you want a docs-only change unless you are ready to review generated changes in `frontend/dist`.

In the change note or pull request, state:

- What changed.
- Why it changed.
- Whether migrations changed.
- Whether seed data changed.
- Whether `.env` values need attention.
- What was tested.
- Screenshots for visible UI changes when useful.

## 14. Recommended Development Conventions

### 14.1 Backend

- Add validation through Form Requests for write endpoints.
- Prefer Eloquent models and Resources over controller-only ad hoc structures.
- Keep route authorization visible through middleware and controller checks.
- Add or update Feature tests for API behavior changes.
- Keep admin and tourist permissions separate.

### 14.2 Frontend

- Use the shared Axios API client.
- Keep components aligned with existing screen structure.
- Add localized labels instead of hardcoding one language where the existing UI is bilingual.
- Check desktop and mobile behavior for changed screens.
- Verify image fallback behavior for catalog items.

### 14.3 Documentation

Update documentation when a change affects:

- Setup commands.
- Environment variables.
- Database behavior.
- API workflows.
- Demo accounts.
- Team handoff instructions.

## 15. Testing Checklist

Use this checklist before handing work to another developer:

| Change Type | Minimum Check |
| --- | --- |
| Backend route or business rule | `php artisan test` |
| Database migration | migrate on a local database and run backend tests |
| Frontend screen | `npm.cmd run build` |
| Auth/admin flow | test with tourist and admin roles as relevant |
| Reservation flow | check pending, approval, and simulated payment behavior |
| Image upload/data change | verify image URL renders in frontend |
| Localization change | inspect FR and EN behavior |

## 16. Troubleshooting

### Backend Cannot Connect to MySQL

Check:

- MySQL is running.
- The database exists.
- `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` match the local MySQL setup.
- PHP has the MySQL PDO extension enabled.

### Frontend Calls Fail

Check:

- Laravel is running on the expected port.
- `VITE_API_BASE_URL` points to `/api`.
- Backend CORS and Sanctum stateful domain values match the frontend URL.

### Authentication Behaves Differently Between Tools

The SPA uses Sanctum-compatible cookie behavior and Axios credentials. API tools can also use the returned Bearer token when supported by the endpoint flow.

### Uploaded Images Do Not Load

Check:

- `php artisan storage:link` has been run.
- The image exists on the public disk.
- The stored URL is the URL returned by the backend upload flow.
- The frontend item has a valid `image_url` or a usable first `photos` entry.

### Seed Fails Because Catalog JSON Is Missing

`CatalogJsonSeeder` expects:

```text
database_export/maghrebpass_data_export.json
```

Do not remove or move that file without updating the seeder path.

## 17. Documents to Read Next

- `README.md`
- `backend/README.md`
- `frontend/README.md`
- `docs/API_EXAMPLES.md`
- `docs/DEMO_CHECKLIST.md`
- `PRD_MaghrebPass_Advanced_V2_5_Final.md`
- `ACCEPTANCE_PHASE_11.md`
- `PHASE_12_RISK_MITIGATIONS.md`

## 18. Maintenance Rule

This guide should stay synchronized with the repository. When setup, schema, image handling, API behavior, or team workflow changes, update this file in the same change set.

## 19. GitHub Push Notes

Before pushing:

- Treat `maghreb_pass` as the canonical project root.
- Do not stage `.env`, `node_modules`, `vendor`, cache files, zips, PDFs, DOCX reports, or screenshots unless the delivery explicitly requires them.
- Review `frontend/dist` carefully because a Vite build can rewrite tracked generated files.
- Prefer the current README and this guide over old top-level audit reports in the parent folder.
