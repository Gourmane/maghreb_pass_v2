# Manifeste de livraison - MaghrebPass MVP

Date de preparation: 2026-05-18

## Contenu livre

- `backend/`: API REST Laravel du MVP.
- `frontend/`: interface React + Vite du MVP.
- `docs/`: checklist de demonstration et exemples d'appels API.
- `PRD_MaghrebPass_MVP.md`: cahier produit du projet.
- `ACCEPTANCE_PHASE_11.md`: validation des criteres d'acceptation.
- `PHASE_12_RISK_MITIGATIONS.md`: risques traites et mitigations appliquees.
- `README.md`: demarrage rapide et liens projet.

## Fonctionnalites backend

- Consultation publique: matchs, hotels, restaurants, attractions.
- Authentification Laravel Sanctum avec cookie HTTP-only `maghrebpass_token`.
- Laravel Breeze installe cote backend (`laravel/breeze`) pour respecter la stack auth du PRD.
- Gestion des favoris pour utilisateurs connectes.
- Profil utilisateur et demande de reinitialisation du mot de passe.
- Administration protegee par role `admin`.
- CRUD admin sur matchs, hotels, restaurants et attractions.
- Endpoint dedie `POST /api/admin/upload`.
- Donnees de demo avec 8 matchs, 10 hotels, 10 restaurants, 10 attractions, 1 admin et 2 touristes.
- Upload optionnel de photos admin avec limite de 2 MB par image.

## Fonctionnalites frontend

- Pages publiques et pages detail selon le PRD: `/matches`, `/hotels/:id`, `/restaurants/:id`, `/attractions/:id`.
- Filtres par ville et filtres specifiques par module.
- Connexion, inscription, deconnexion et profil.
- Gestion des favoris.
- Panneau admin avec statistiques, utilisateurs et CRUD contenus.
- Interface bilingue FR/EN avec `react-i18next`.
- Tailwind CSS installe et branche via `@tailwindcss/vite`.
- Build de production Vite.

## Comptes de demonstration

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

Resultat attendu: 26 tests passes, 155 assertions.

```bash
cd frontend
npm run build
```

Resultat attendu: build Vite reussi.

## Notes d'installation

Le projet est configure pour MySQL dans `backend/.env.example`, comme le PRD.
Avant les migrations, creer une base locale `maghreb_pass` et verifier les identifiants `DB_USERNAME` / `DB_PASSWORD`.

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
