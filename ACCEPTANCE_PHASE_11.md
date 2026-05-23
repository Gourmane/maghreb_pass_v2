# Criteres d'acceptation - MaghrebPass Advanced V2.5

Statut mis a jour le 2026-05-23.

Ce fichier est une note d'acceptation du projet courant. La source de verite reste le code dans `backend`, `frontend` et les tests.

## Criteres valides

- Visiteur: consultation publique des matchs, hotels, restaurants, attractions, packages, carte et details sans compte.
- Auth: inscription, connexion, deconnexion, reset password, profil et cookie HTTP-only `maghrebpass_token` via Laravel Sanctum.
- Roles: `tourist` et `admin`.
- Touriste: favoris, reservations hotels/restaurants, annulation autorisee selon etat, paiement simule apres approbation admin, Trip Planner.
- Administrateur: acces protege par role admin, statistiques, utilisateurs, reservations, CRUD catalogue, packages et items.
- FR/EN: interface React bilingue avec `react-i18next` et donnees bilingues.
- Photos: URLs et fichiers images admin acceptes avec limite stricte de 2 MB.
- Filtres: ville et filtres specifiques par module selon les endpoints publics.
- Donnees demo: 8 matchs, 10 hotels, 10 restaurants, 10 attractions, packages, reservations, 1 admin et 2 touristes.
- API externe: aucune API payante n'est necessaire pour executer le projet.

## Limites restantes

- La base MySQL locale `advenced_maghrebpass_v2` doit exister avant `php artisan migrate:fresh --seed`.
- Les reservations exigent un compte `tourist`; les visiteurs non connectes sont invites a se connecter.
- Le paiement est une simulation academique, pas une integration de paiement reel.
- La compression image n'est pas ajoutee; la limite backend de 2 MB reste appliquee.

## Commandes de validation

```bash
cd backend
php artisan route:list --path=api --no-ansi
php artisan migrate:status --no-ansi
php artisan test --no-ansi
```

Etat confirme:

- 74 routes API.
- 54 tests passes.
- 713 assertions.

Validation frontend disponible:

```bash
cd frontend
npm.cmd run build
```

Verifier `frontend/dist` avant/apres build car ce dossier peut etre modifie.
