# MaghrebPass MVP - Backend Laravel

Backend API REST pour le MVP MaghrebPass: matchs, hotels, restaurants, attractions, favoris et administration.

## Prerequis

- PHP 8.2+
- Composer
- Extension PHP `pdo_mysql` activee
- Serveur MySQL 8 compatible avec une base `maghreb_pass`

Le MVP fonctionne sans API externe. La base applicative cible est MySQL, comme demande dans le PRD.

## Installation locale

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

## Comptes de demo

| Role | Email | Mot de passe |
| --- | --- | --- |
| Admin | `admin@maghrebpass.test` | `password` |
| Touriste FR | `tourist@maghrebpass.test` | `password` |
| Touriste EN | `emily.carter@maghrebpass.test` | `password` |

## Endpoints principaux

### Public

- `GET /api/health`
- `GET /api/matches`
- `GET /api/matches/{id}`
- `GET /api/hotels`
- `GET /api/hotels/{id}`
- `GET /api/restaurants`
- `GET /api/restaurants/{id}`
- `GET /api/attractions`
- `GET /api/attractions/{id}`

Filtres publics utiles:

- Matchs: `city`, `phase`, `status`, `date`
- Hotels: `city`, `stars`, `price_min`, `price_max`, `search`
- Restaurants: `city`, `cuisine_type`, `price_range`, `search`
- Attractions: `city`, `category`, `search`

### Auth

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`
- `PUT /api/auth/profile`
- `POST /api/auth/forgot-password`

Le frontend utilise le cookie HTTP-only `maghrebpass_token`. Les clients API peuvent aussi utiliser le token retourne par login/register avec l'en-tete:

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

### Admin

Routes protegees par `auth:sanctum` et `role:admin`.

- `GET /api/admin/stats`
- `POST /api/admin/upload`
- `GET /api/admin/users`
- `PUT /api/admin/users/{user}/toggle`
- `apiResource /api/admin/matches`
- `apiResource /api/admin/hotels`
- `apiResource /api/admin/restaurants`
- `apiResource /api/admin/attractions`

Les endpoints admin hotels/restaurants/attractions acceptent:

- `photos[]`: URLs publiques valides
- `photo_files[]`: fichiers image, maximum 2 MB par fichier

## Donnees de demo

`php artisan migrate:fresh --seed` cree:

- 8 matchs
- 10 hotels
- 10 restaurants
- 10 attractions
- 1 admin
- 2 touristes

## Validation

```bash
php artisan test
```

Etat courant: 26 tests, 155 assertions.

## Demo

Depuis la racine du workspace, consulter:

- `docs/DEMO_CHECKLIST.md`
- `docs/API_EXAMPLES.md`
