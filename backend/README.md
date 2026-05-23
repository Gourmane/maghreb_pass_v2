# MaghrebPass Advanced V2.5 - Backend Laravel

Backend API REST Laravel 12 pour MaghrebPass Advanced V2.5.

## Prerequis

- PHP 8.2+
- Composer
- Extension PHP `pdo_mysql`
- MySQL avec une base locale `advenced_maghrebpass_v2`

L'application ne depend d'aucune API externe payante pour demarrer, migrer, seeder ou tester.

## Installation locale

Creer la base MySQL:

```sql
CREATE DATABASE advenced_maghrebpass_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Depuis `maghreb_pass/backend`:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

API locale: `http://localhost:8000/api`

Configuration MySQL par defaut:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=advenced_maghrebpass_v2
DB_USERNAME=root
DB_PASSWORD=
```

`php artisan migrate:fresh --seed` supprime les tables existantes de la base configuree. Ne pas l'executer sur une base contenant des donnees a conserver.

## Authentification et roles

- Auth applicative: Laravel Sanctum.
- Login/register retournent un token Bearer et posent aussi un cookie HTTP-only `maghrebpass_token`.
- Un middleware API lit ce cookie et le transforme en en-tete `Authorization` pour le frontend SPA.
- Roles stockes en base: `tourist` et `admin`.
- Breeze est installe comme dependance de developpement Laravel, mais l'application n'utilise pas de flow UI Breeze.

## Comptes de demo

Crees par les seeders locaux:

| Role | Email | Mot de passe |
| --- | --- | --- |
| Admin | `admin@maghrebpass.test` | `password` |
| Touriste FR | `tourist@maghrebpass.test` | `password` |
| Touriste EN | `emily.carter@maghrebpass.test` | `password` |

Ces comptes ne doivent pas etre deployes comme identifiants publics.

## Endpoints principaux

Source de verite: `routes/api.php`.

### Public

- `GET /api/health`
- `GET /api/matches`
- `GET /api/matches/{id}`
- `GET /api/matches/{id}/nearby`
- `GET /api/hotels`
- `GET /api/hotels/{id}`
- `GET /api/restaurants`
- `GET /api/restaurants/{id}`
- `GET /api/attractions`
- `GET /api/attractions/{id}`
- `GET /api/packages`
- `GET /api/packages/{id}`
- `GET /api/map-items`

Filtres publics utiles:

- Matchs: `city`, `group_name`, `phase`, `match_date`
- Hotels: `city`, `stars`, `price_min`, `price_max`
- Restaurants: `city`, `cuisine_type`, `price_range`
- Attractions: `city`, `category`
- Packages: `city`, `search`
- Carte: `city`, `type` avec `all`, `hotel`, `restaurant`, `attraction`, `match`

### Auth

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`
- `PUT /api/auth/profile`
- `POST /api/auth/forgot-password`
- `GET /api/user`

Les clients API peuvent utiliser:

```http
Authorization: Bearer <token>
```

### Favoris

- `GET /api/favorites`
- `POST /api/favorites`
- `DELETE /api/favorites/{id}`

Payload d'ajout:

```json
{
  "type": "hotel",
  "id": 1
}
```

Types acceptes: `hotel`, `restaurant`, `attraction`.

L'API expose `type` + `id`; la base utilise une relation polymorphique Laravel avec `favoriteable_type` + `favoriteable_id`.

### Reservations et trips

- `GET /api/my-reservations`
- `POST /api/hotel-reservations`
- `POST /api/restaurant-reservations`
- `PUT /api/my-hotel-reservations/{reservation}/cancel`
- `PUT /api/my-restaurant-reservations/{reservation}/cancel`
- `POST /api/my-hotel-reservations/{reservation}/pay`
- `POST /api/my-restaurant-reservations/{reservation}/pay`
- `GET /api/trips`
- `POST /api/trips`
- `GET /api/trips/{trip}`
- `PUT/PATCH /api/trips/{trip}`
- `DELETE /api/trips/{trip}`
- `POST /api/trips/{trip}/items`
- `PUT /api/trips/{trip}/items/{item}`
- `DELETE /api/trips/{trip}/items/{item}`

Les reservations exigent un utilisateur authentifie. Le workflow courant est:

```text
pending -> approved -> confirmed
pending -> rejected
pending/approved -> cancelled
```

Le passage a `confirmed` se fait par paiement simule cote touriste apres approbation admin. Aucune transaction reelle n'est executee.

### Admin

Routes protegees par `auth:sanctum` et `role:admin`.

- `GET /api/admin/stats`
- `POST /api/admin/upload`
- `GET /api/admin/users`
- `PUT /api/admin/users/{user}/toggle`
- `GET /api/admin/reservations`
- `PUT /api/admin/hotel-reservations/{reservation}/status`
- `PUT /api/admin/restaurant-reservations/{reservation}/status`
- CRUD `/api/admin/matches`
- CRUD `/api/admin/hotels`
- CRUD `/api/admin/restaurants`
- CRUD `/api/admin/attractions`
- CRUD `/api/admin/packages`
- `POST /api/admin/packages/{package}/items`
- `PUT /api/admin/packages/{package}/items/{item}`
- `DELETE /api/admin/packages/{package}/items/{item}`
- `PUT /api/admin/packages/{package}/items/{item}/move/{direction}`

Les statuts admin acceptes pour reservations sont `approved` et `rejected`.

Les uploads image admin acceptent des images jusqu'a 2 MB et les placent sur le disque public Laravel.

## Donnees de demo

`php artisan migrate:fresh --seed` cree notamment:

- 8 matchs
- 10 hotels
- 10 restaurants
- 10 attractions
- packages touristiques
- reservations de demo
- 1 admin
- 2 touristes

`database_export/maghrebpass_data_export.json` alimente le catalogue de base. Les packages et reservations sont generes par seeders.

## Securite configuration

Ne pas livrer `.env`. Pour une demo publique ou production:

- `APP_DEBUG=false`
- `SESSION_ENCRYPT=true`
- `COOKIE_SECURE=true` sous HTTPS
- `APP_KEY` propre a l'environnement
- pas de secrets SMTP ou service tiers dans le depot

## Validation

Commandes confirmees:

```bash
php artisan route:list --path=api --no-ansi
php artisan migrate:status --no-ansi
php artisan test --no-ansi
```

Etat confirme le 2026-05-23:

- 74 routes API
- 54 tests passes
- 713 assertions

## Documentation liee

- `../README.md`
- `../docs/DEVELOPER_GUIDE.md`
- `../docs/API_EXAMPLES.md`
- `../docs/DEMO_CHECKLIST.md`
