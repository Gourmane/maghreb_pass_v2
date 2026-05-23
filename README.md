# MaghrebPass Advanced V2.5

MaghrebPass Advanced V2.5 est une application web de planification touristique pour les visiteurs de la Coupe du Monde 2030 au Maroc.

Le projet contient:

- Backend API Laravel 12 avec Laravel Sanctum.
- Frontend React 19 + Vite 7.
- Base MySQL locale `advenced_maghrebpass_v2`.
- Interface publique: matchs, hotels, restaurants, attractions, packages, carte et details.
- Authentification Sanctum avec cookie HTTP-only `maghrebpass_token`.
- Roles `tourist` et `admin`.
- Espace touriste: favoris, profil, reservations hotels/restaurants, paiement simule, trip planner.
- Espace admin: statistiques, utilisateurs, CRUD catalogue, reservations, packages, items de package et upload images.
- Donnees de demonstration via seeders.
- Interface bilingue FR/EN.

## Prerequis

- PHP 8.2+
- Composer
- MySQL avec extension PHP `pdo_mysql`
- Node.js et npm compatibles avec Vite 7
- Git

## Demarrage rapide

Creer d'abord la base MySQL locale:

```sql
CREATE DATABASE advenced_maghrebpass_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Backend:

```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

API locale: `http://localhost:8000/api`

Configuration MySQL attendue dans `backend/.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=advenced_maghrebpass_v2
DB_USERNAME=root
DB_PASSWORD=
```

Frontend, dans un deuxieme terminal:

```bash
cd frontend
npm.cmd install
copy .env.example .env
npm.cmd run dev
```

Frontend local: `http://127.0.0.1:5173`

## Comptes de demo

Ces comptes sont crees par `php artisan migrate:fresh --seed` et sont reserves au developpement local.

| Role | Email | Mot de passe |
| --- | --- | --- |
| Admin | `admin@maghrebpass.test` | `password` |
| Touriste FR | `tourist@maghrebpass.test` | `password` |
| Touriste EN | `emily.carter@maghrebpass.test` | `password` |

## Fonctionnement principal

- Les visiteurs non connectes peuvent consulter les contenus publics.
- Les reservations hotels/restaurants exigent un compte `tourist`.
- Les reservations commencent en `pending`, peuvent etre `approved` ou `rejected` par l'admin, puis deviennent `confirmed` apres paiement simule par le touriste.
- Le paiement est uniquement une simulation de validation demo. Aucun paiement reel, Stripe, PayPal ou numero de carte n'est utilise.
- Les favoris concernent les hotels, restaurants et attractions.
- Les packages et trips imposent une logique mono-ville pour les items de catalogue.
- Les suggestions nearby sont basees sur la meme ville, pas sur une distance GPS reelle.

## Securite

- Ne jamais commit `backend/.env` ou `frontend/.env`.
- Pour une demo publique ou production, garder `APP_DEBUG=false`, `SESSION_ENCRYPT=true`, activer `COOKIE_SECURE=true` sous HTTPS et generer un `APP_KEY` propre a l'environnement.
- `MAIL_MAILER=log` suffit en local; aucun secret SMTP reel n'est requis.

## Validation

Commandes backend utiles:

```bash
cd backend
php artisan route:list --path=api --no-ansi
php artisan migrate:status --no-ansi
php artisan test --no-ansi
```

Etat confirme le 2026-05-23:

- Routes API: 74
- Tests backend: 54 tests passes, 713 assertions

Frontend:

```bash
cd frontend
npm.cmd run build
```

Attention: `frontend/dist` est present dans le depot et peut etre modifie par un build. Verifier `git status` avant et apres si vous ne voulez pas inclure des artefacts generes.

## Documentation projet

- [Developer guide](docs/DEVELOPER_GUIDE.md)
- [Backend README](backend/README.md)
- [Frontend README](frontend/README.md)
- [Checklist demo](docs/DEMO_CHECKLIST.md)
- [Exemples API](docs/API_EXAMPLES.md)
- [PRD](PRD_MaghrebPass_Advanced_V2_5_Final.md)
- [Manifeste de livraison](DELIVERY_MANIFEST.md)

## Notes avant GitHub push

- Verifier que `.env`, `node_modules`, `vendor`, caches, zips, PDF/DOCX et fichiers personnels ne sont pas stages.
- Verifier les changements generes dans `frontend/dist` avant de les inclure.
- La racine canonique du projet applicatif est `maghreb_pass`.
- Les anciens rapports Markdown a la racine du dossier parent sont des artefacts historiques et ne doivent pas remplacer cette documentation canonique.
