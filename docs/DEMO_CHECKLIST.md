# Checklist de demonstration - MaghrebPass Advanced V2.5

Objectif: presenter les fonctionnalites reelles de la version courante avec les donnees locales seeders.

## 1. Preparation

Sur le PC de demo:

```bash
git clone https://github.com/Gourmane/maghreb_pass_v2.git
cd maghreb_pass_v2
```

Creer la base MySQL locale avant de lancer Laravel:

```sql
CREATE DATABASE advenced_maghrebpass_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Backend:

```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
php artisan config:clear
php artisan migrate:fresh --seed
php artisan storage:link
php artisan route:list --path=api --no-ansi
php artisan test --no-ansi
php artisan serve
```

API locale: `http://localhost:8000/api`

Frontend, dans un deuxieme terminal:

```bash
cd frontend
npm.cmd install
copy .env.example .env
npm.cmd run dev
```

Frontend local: `http://127.0.0.1:5173`

Ne pas lancer `npm.cmd run build` avant une demo si vous ne voulez pas modifier les fichiers suivis dans `frontend/dist`.

## 2. Verifications rapides API

- `GET http://localhost:8000/api/health`
- `GET /api/matches`
- `GET /api/matches/1/nearby`
- `GET /api/hotels?city=Rabat`
- `GET /api/restaurants?city=Tanger`
- `GET /api/attractions`
- `GET /api/packages?city=Casablanca`
- `GET /api/map-items?city=Casablanca&type=all`

Etat confirme le 2026-05-23:

- 74 routes API
- 54 tests backend passes
- 713 assertions

## 3. Parcours navigateur - visiteur

1. Ouvrir `http://127.0.0.1:5173`.
2. Presenter l'accueil et la navigation principale.
3. Ouvrir les catalogues publics: matchs, hotels, restaurants, attractions, packages.
4. Dans `/matches`, ouvrir le filtre Groupe, choisir `Groupe A`, charger la recherche et confirmer que seuls les matchs du groupe choisi sont affiches.
5. Ouvrir une fiche detail geolocalisee et montrer la mini-map.
6. Ouvrir `/map` et filtrer par ville/type.
7. Ouvrir un match et presenter les suggestions nearby basees sur la meme ville.

## 4. Parcours navigateur - touriste

1. Se connecter avec `tourist@maghrebpass.test` / `password`.
2. Modifier le profil ou la langue.
3. Ajouter un hotel, restaurant ou attraction aux favoris.
4. Ouvrir `/favorites` et supprimer un favori si besoin.
5. Creer une reservation hotel ou restaurant.
6. Ouvrir `/my-reservations`.
7. Apres approbation admin, montrer le paiement simule qui confirme la reservation.
8. Ouvrir `/trip-planner`, creer un trip et ajouter un item de la meme ville.

## 5. Parcours navigateur - admin

1. Se connecter avec `admin@maghrebpass.test` / `password`.
2. Ouvrir `/admin`.
3. Presenter les statistiques et la liste utilisateurs.
4. Creer ou modifier un contenu dans matchs, hotels, restaurants, attractions ou packages.
5. Presenter les items de package sur un package existant.
6. Ouvrir `/admin/reservations`.
7. Approuver ou refuser une reservation pending.
8. Montrer que l'admin ne peut pas desactiver son propre compte ou le dernier admin actif.

## 6. Parcours API optionnel

Pour les appels manuels, utiliser `docs/API_EXAMPLES.md`.

Le login retourne un token Bearer et pose aussi le cookie HTTP-only `maghrebpass_token`. Le navigateur utilise le cookie; les outils API peuvent utiliser l'en-tete:

```http
Authorization: Bearer TOKEN
```

## 7. Points a annoncer clairement

- Les visiteurs peuvent consulter le contenu public sans compte.
- Les reservations exigent un compte `tourist`.
- Le workflow reservation est: demande `pending`, approbation/refus admin, paiement simule par le touriste, confirmation finale.
- Le paiement est une simulation academique; aucun service de paiement reel n'est connecte.
- L'authentification utilise Laravel Sanctum et un cookie HTTP-only.
- Les roles reels sont `tourist` et `admin`.
- Les favoris sont exposes en API avec `type` + `id`; la base utilise `favoriteable_type` + `favoriteable_id`.
- Les packages et trips utilisent une logique mono-ville.
- Aucune API externe payante n'est necessaire.

## 8. Limites a annoncer

- La compression image n'est pas implementee; la limite backend de 2 MB est appliquee.
- Les comptes `password` sont uniquement des comptes seed locaux.
- Les suggestions nearby sont basees sur la meme ville, pas sur un calcul GPS.
- `database_export/maghrebpass_data_export.json` contient les donnees catalogue; packages et reservations sont generes par seeders.
