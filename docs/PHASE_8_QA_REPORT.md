# Rapport QA - MaghrebPass Advanced V2.5

Date de mise a jour: 2026-05-23

## Synthese

Verification backend courante:

- Routes API: 74 routes exposees par `php artisan route:list --path=api --no-ansi`.
- Migrations: les migrations courantes sont executees dans l'environnement local verifie.
- Tests backend: 54 tests passes, 713 assertions.

Le build frontend n'a pas ete relance pendant cette passe documentaire, car `frontend/dist` est suivi dans le depot et peut etre reecrit par `npm.cmd run build`.

## Couverture principale

| Controle | Couverture |
| --- | --- |
| Auth et profil | `AuthApiTest`, `AcceptanceCriteriaTest` |
| Middleware admin | `AdminApiTest` |
| Admin catalogue/utilisateurs | `AdminApiTest`, `PackageApiTest`, `ReservationApiTest` |
| Favoris | `FavoriteApiTest`, `AcceptanceCriteriaTest` |
| Reservations | `ReservationApiTest` |
| Paiement simule | `ReservationApiTest` |
| Packages | `PackageApiTest` |
| Suppression element utilise | `PackageApiTest` |
| Map et nearby | `PublicCatalogApiTest` |
| Trip planner | `TripPlannerApiTest` |
| Donnees demo | `AcceptanceCriteriaTest` |

## Commandes executees

```bash
cd backend
php artisan route:list --path=api --no-ansi
php artisan migrate:status --no-ansi
php artisan test --no-ansi
```

## Notes QA

- Les reservations sont volontairement authentifiees: un visiteur non connecte ne peut pas creer une reservation.
- Le paiement est une simulation de confirmation apres approbation admin.
- Les favoris sont exposes par l'API avec `type` + `id`, tandis que la base utilise une relation polymorphique Laravel.
- Pour une validation frontend, executer `npm.cmd run build` puis verifier les changements generes dans `frontend/dist`.
