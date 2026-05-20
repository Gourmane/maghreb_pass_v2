# MaghrebPass Advanced V2.5 - Backend Laravel

Backend API REST pour MaghrebPass Advanced V2.5: matchs, hotels, restaurants, attractions, carte, reservations, packages, favoris, trip planner et administration.

## Prerequis

- PHP 8.2+
- Composer
- Extension PHP `pdo_mysql` activee
- Serveur MySQL 8 compatible avec une base `maghreb_pass`

L'application fonctionne sans API externe. La base applicative cible est MySQL, comme demande dans le PRD.

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

Ces comptes sont reserves au demo local apres seed. Ne pas les deployer comme identifiants publics.

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
- `GET /api/map-items`
- `GET /api/packages`
- `GET /api/packages/{id}`

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

L'authentification de l'application est basee sur Laravel Sanctum pour une SPA React. Breeze n'est pas utilise comme systeme d'authentification applicatif.

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

L'API expose les favoris avec `type` + `id`; en base, l'implementation Laravel utilise `favoriteable_type` + `favoriteable_id` pour conserver une relation polymorphique equivalente au `item_type` + `item_id` du PRD.

### Reservations et trips

- `GET /api/my-reservations`
- `POST /api/hotel-reservations`
- `POST /api/restaurant-reservations`
- `PUT /api/my-hotel-reservations/{reservation}/cancel`
- `PUT /api/my-restaurant-reservations/{reservation}/cancel`
- `POST /api/my-hotel-reservations/{reservation}/pay`
- `POST /api/my-restaurant-reservations/{reservation}/pay`
- `apiResource /api/trips`
- `POST /api/trips/{trip}/items`
- `PUT /api/trips/{trip}/items/{item}`
- `DELETE /api/trips/{trip}/items/{item}`

Les reservations hotels/restaurants exigent un utilisateur authentifie. Le statut initial est `pending` et le paiement initial est `unpaid`. L'admin peut passer une demande a `approved` ou `rejected`; il ne confirme pas directement. Le touriste confirme definitivement avec le paiement simule, ce qui passe la reservation a `confirmed` avec `payment_status=paid`, `paid_at` et `payment_reference`.

Le paiement est uniquement simule pour la demonstration academique. Aucune transaction reelle, aucun service Stripe/PayPal et aucun numero de carte ne sont utilises.

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
- `apiResource /api/admin/packages`
- `GET /api/admin/reservations`
- `PUT /api/admin/hotel-reservations/{reservation}/status`
- `PUT /api/admin/restaurant-reservations/{reservation}/status`

Les endpoints admin de statut acceptent uniquement `approved` ou `rejected`.

Le toggle utilisateur refuse maintenant la desactivation de son propre compte admin et du dernier administrateur actif.

Les endpoints admin hotels/restaurants/attractions acceptent:

- `photos[]`: URLs publiques valides
- `photo_files[]`: fichiers image, maximum 2 MB par fichier

## Donnees de demo

`php artisan migrate:fresh --seed` cree:

- 8 matchs
- 10 hotels
- 10 restaurants
- 10 attractions
- packages touristiques
- reservations et trips de demo selon les seeders disponibles
- 1 admin
- 2 touristes

`database_export/maghrebpass_data_export.json` documente les donnees catalogue exportees. Les packages, reservations et trips de demo sont recrees par seeders, pas par cet export JSON.

## Securite configuration

Ne pas livrer `.env`. Pour un demo public ou production, garder `APP_DEBUG=false`, `SESSION_ENCRYPT=true`, `COOKIE_SECURE=true` sous HTTPS, et generer un `APP_KEY` propre a l'environnement. `.env.example` contient des valeurs sures par defaut, que le developpement local peut surcharger dans `.env`.

Pour les notifications email en local, utiliser `MAIL_MAILER=log` si aucun SMTP n'est configure. Les echecs d'envoi sont journalises sans bloquer la requete principale.

## Validation

```bash
php artisan test
```

Etat courant: 51 tests, 444 assertions.

## Demo

Depuis la racine du workspace, consulter:

- `docs/DEMO_CHECKLIST.md`
- `docs/API_EXAMPLES.md`
