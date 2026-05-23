# Manifeste de livraison - MaghrebPass Advanced V2.5

Date de mise a jour: 2026-05-23

## Contenu livre

- `backend/`: API REST Laravel 12.
- `frontend/`: interface React 19 + Vite 7.
- `docs/`: guide developpeur, checklist demo, exemples API et rapport QA.
- `database_export/maghrebpass_data_export.json`: donnees catalogue utilisees par le seeder.
- `README.md`: documentation principale de demarrage.
- `PRD_MaghrebPass_Advanced_V2_5_Final.md`: PRD avec note d'alignement implementation courante.

## Fonctionnalites backend

- Consultation publique: matchs, hotels, restaurants, attractions, packages, carte et suggestions nearby.
- Authentification Laravel Sanctum avec token Bearer et cookie HTTP-only `maghrebpass_token`.
- Roles `tourist` et `admin`.
- Favoris pour utilisateurs connectes.
- Reservations hotels/restaurants authentifiees, approbation/refus admin, paiement simule et confirmation.
- Trip Planner authentifie avec items mono-ville.
- Administration protegee: statistiques, utilisateurs, CRUD catalogue, reservations, packages, items de package et upload images.
- Donnees de demo: 8 matchs, 10 hotels, 10 restaurants, 10 attractions, packages, reservations, 1 admin et 2 touristes.
- Upload optionnel de photos admin avec limite de 2 MB par image.

## Fonctionnalites frontend

- Pages publiques: accueil, matchs, hotels, restaurants, attractions, packages, carte.
- Pages detail pour tous les modules publics.
- Carte globale et mini-map sur les details geolocalises.
- Filtres par module.
- Connexion, inscription, deconnexion et profil.
- Favoris, reservations utilisateur, paiement simule et Trip Planner.
- Panneau admin avec statistiques, utilisateurs, reservations, CRUD contenus, packages et items.
- Interface bilingue FR/EN avec `react-i18next`.
- Styles projet avec Tailwind CSS via `@tailwindcss/vite`.

## Comptes de demonstration

Ces comptes sont reserves au demo local apres seed.

| Role | Email | Mot de passe |
| --- | --- | --- |
| Admin | `admin@maghrebpass.test` | `password` |
| Touriste FR | `tourist@maghrebpass.test` | `password` |
| Touriste EN | `emily.carter@maghrebpass.test` | `password` |

## Installation locale

Creer la base MySQL:

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

Frontend:

```bash
cd frontend
npm.cmd install
copy .env.example .env
npm.cmd run dev
```

## Validation effectuee

Confirme le 2026-05-23:

```bash
cd backend
php artisan route:list --path=api --no-ansi
php artisan migrate:status --no-ansi
php artisan test --no-ansi
```

Resultats:

- 74 routes API.
- Migrations courantes executees.
- 54 tests passes, 713 assertions.

Le build frontend se lance avec:

```bash
cd frontend
npm.cmd run build
```

Attention: `frontend/dist` est suivi dans le depot et peut etre reecrit par le build.

## Limites connues

- Le paiement est simule uniquement; aucun paiement reel n'est branche.
- Les reservations exigent un compte `tourist`.
- Les suggestions nearby sont basees sur la meme ville, pas sur une distance GPS reelle.
- La compression image n'est pas implementee; la limite backend de 2 MB est appliquee.
- MySQL doit etre disponible sur `127.0.0.1:3306` ou adapte dans `.env`.
- `database_export/maghrebpass_data_export.json` contient les donnees catalogue; packages et reservations sont recrees par seeders.
