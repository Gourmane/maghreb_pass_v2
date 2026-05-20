# Manifeste de livraison - MaghrebPass Advanced V2.5

Date de preparation: 2026-05-20

## Contenu livre

- `backend/`: API REST Laravel Advanced V2.5.
- `frontend/`: interface React + Vite Advanced V2.5.
- `docs/`: checklist de demonstration et exemples d'appels API.
- `PRD_MaghrebPass_Advanced_V2_5_Final.md`: cahier produit du projet.
- `ACCEPTANCE_PHASE_11.md`: validation des criteres d'acceptation.
- `PHASE_12_RISK_MITIGATIONS.md`: risques traites et mitigations appliquees.
- `README.md`: demarrage rapide et liens projet.

## Fonctionnalites backend

- Consultation publique: matchs, hotels, restaurants, attractions, packages et carte.
- Authentification Laravel Sanctum avec cookie HTTP-only `maghrebpass_token`.
- Laravel Breeze installe cote backend (`laravel/breeze`) pour respecter la stack auth du PRD.
- Gestion des favoris, reservations et trips pour utilisateurs connectes.
- Profil utilisateur et demande de reinitialisation du mot de passe.
- Administration protegee par role `admin`.
- CRUD admin sur matchs, hotels, restaurants, attractions et packages.
- Administration des reservations.
- Endpoint dedie `POST /api/admin/upload`.
- Donnees de demo avec 8 matchs, 10 hotels, 10 restaurants, 10 attractions, packages, reservations, 1 admin et 2 touristes.
- Upload optionnel de photos admin avec limite de 2 MB par image.

## Fonctionnalites frontend

- Pages publiques et pages detail selon le PRD: `/matches`, `/hotels/:id`, `/restaurants/:id`, `/attractions/:id`, `/packages/:id`.
- Carte globale et mini-map sur les details geolocalises.
- Filtres par ville et filtres specifiques par module.
- Connexion, inscription, deconnexion et profil.
- Gestion des favoris avec lien vers le Trip Planner.
- Trip Planner via `/trip-planner`, `/trips` et `/my-trips`.
- Panneau admin avec statistiques, utilisateurs, reservations et CRUD contenus.
- Interface bilingue FR/EN avec `react-i18next`.
- Tailwind CSS installe et branche via `@tailwindcss/vite`.
- Build de production Vite.

## Comptes de demonstration

Ces comptes sont reserves au demo local apres seed.

| Role | Email | Mot de passe |
| --- | --- | --- |
| Admin | `admin@maghrebpass.test` | `password` |
| Touriste FR | `tourist@maghrebpass.test` | `password` |
| Touriste EN | `emily.carter@maghrebpass.test` | `password` |

## Validation effectuee

Commande:

```bash
cd backend
php artisan test
```

Resultat attendu: 51 tests passes, 444 assertions.

```bash
cd frontend
npm run build
```

Resultat attendu: build Vite reussi.

## Notes d'installation

Le projet est configure pour MySQL dans `backend/.env.example`, comme le PRD.
Avant les migrations, creer une base locale `maghreb_pass` et verifier les identifiants `DB_USERNAME` / `DB_PASSWORD`.
Pour un demo public ou production, garder `APP_DEBUG=false`, `SESSION_ENCRYPT=true`, generer un `APP_KEY` propre a l'environnement et ne jamais livrer `.env`.

Commandes principales:

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
npm install
copy .env.example .env
npm run dev
```

## Limites connues

- La compression image n'est pas implementee; la limite backend de 2 MB est appliquee.
- MySQL doit etre disponible sur `127.0.0.1:3306` ou adapte dans `.env`.
- Les favoris sont exposes en API avec `type` + `id`; en base, Laravel utilise `favoriteable_type` + `favoriteable_id`.
- `database_export/maghrebpass_data_export.json` contient les donnees catalogue exportees; packages, reservations et trips de demo sont recrees par seeders.
