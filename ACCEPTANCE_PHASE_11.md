# Phase 11 - Criteres d'acceptation MVP

Statut execute le 2026-05-18.

## Criteres valides

- Visiteur: consultation publique des matchs, hotels, restaurants et attractions sans compte.
- Utilisateur: inscription, connexion, deconnexion, reset password, profil et gestion des favoris.
- Administrateur: acces protege par role admin et CRUD sur les contenus principaux.
- FR/EN: interface React avec selecteur, contenus `description_fr`/`description_en` et `preferred_language`.
- Photos: URLs et fichiers images admin acceptes, stockes sur disque `public` et retournes par les pages de detail API.
- Filtres: ville minimum sur les quatre modules, plus groupe/phase/date, etoiles/prix, cuisine/gamme et categorie.
- Frontend: routes PRD `/matches`, `/hotels/:id`, `/restaurants/:id`, `/attractions/:id`, `/login`, `/register`, `/profile`, `/favorites` et `/admin/*`.
- Donnees demo: 8 matchs, 10 hotels, 10 restaurants, 10 attractions, 1 admin et 2 touristes.
- API externe: aucune API externe n'est necessaire pour executer le backend MVP.

## Limites restantes

- MySQL doit etre demarre localement avec la base `maghreb_pass` avant `php artisan migrate:fresh --seed`.
- La compression image n'est pas ajoutee; la limite stricte de 2 MB reste appliquee.

## Commandes de validation

```bash
cd backend
php artisan test

cd ../frontend
npm run build
```
