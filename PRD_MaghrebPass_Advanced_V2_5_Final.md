# PRD - MaghrebPass Advanced V2.5

Date de synchronisation documentaire: 2026-05-23

Ce document decrit le produit courant tel qu'il existe dans le code. Les sources de verite techniques restent:

- `backend/routes/api.php`
- `backend/database/migrations`
- `backend/database/seeders`
- `backend/tests/Feature`
- `frontend/src`
- `backend/.env.example`
- `frontend/package.json`

## 1. Resume produit

MaghrebPass Advanced V2.5 est une application web academique pour aider les visiteurs de la Coupe du Monde 2030 au Maroc a consulter des contenus touristiques et organiser un sejour simple.

Le projet fonctionne comme:

- catalogue public de matchs, hotels, restaurants, attractions et packages;
- carte interactive des lieux geolocalises;
- espace touriste pour favoris, reservations et trips;
- espace admin pour gestion des contenus, utilisateurs, reservations, packages et images.

## 2. Stack cible et stack implementee

| Zone | Implementation courante |
| --- | --- |
| Backend | Laravel 12, PHP 8.2+ |
| Auth | Laravel Sanctum |
| Base de donnees | MySQL locale `advenced_maghrebpass_v2` |
| Tests backend | PHPUnit / Laravel test suite |
| Frontend | React 19, Vite 7 |
| HTTP client | Axios |
| Carte | Leaflet, React Leaflet, OpenStreetMap |
| UI/Styles | CSS projet, Tailwind CSS via `@tailwindcss/vite`, lucide-react |
| i18n | `react-i18next`, FR/EN |

Breeze est present comme dependance Laravel de developpement, mais l'application courante n'utilise pas un flow UI Breeze.

## 3. Utilisateurs et roles

### Visiteur non connecte

Peut consulter:

- accueil;
- matchs;
- hotels;
- restaurants;
- attractions;
- packages;
- carte;
- fiches detail;
- suggestions nearby autour d'un match.

Ne peut pas creer de favoris, reservations ou trips.

### Touriste connecte

Role base de donnees: `tourist`.

Peut:

- gerer son profil;
- changer sa langue preferee;
- ajouter/supprimer des favoris pour hotels, restaurants et attractions;
- creer des reservations hotels/restaurants;
- voir ses reservations;
- annuler une reservation autorisee par son etat;
- confirmer une reservation approuvee via paiement simule;
- creer et gerer ses trips.

### Administrateur

Role base de donnees: `admin`.

Peut:

- consulter statistiques et utilisateurs;
- activer/desactiver des utilisateurs selon les protections existantes;
- gerer matchs, hotels, restaurants, attractions et packages;
- gerer les items de package;
- approuver ou refuser les reservations;
- uploader des images admin.

## 4. Authentification

L'authentification applicative utilise Laravel Sanctum.

Le login/register:

- retourne un token Bearer utilisable par les clients API;
- pose un cookie HTTP-only `maghrebpass_token` utilise par la SPA React.

Le frontend ne stocke pas le token Bearer dans `localStorage`. Il utilise `withCredentials: true` via Axios.

## 5. Modules implementes

### Catalogue public

Modules publics:

- matchs;
- hotels;
- restaurants;
- attractions;
- packages;
- map items.

Les fiches detail exposent les informations utiles au voyage: ville, prix, dates, description, images, contact, geolocalisation et actions disponibles selon le module.

### Carte

La carte retourne des hotels, restaurants, attractions et matchs geolocalises. Les filtres supportes sont `city` et `type`.

### Match nearby

Un match expose des suggestions de hotels, restaurants et attractions basees sur la meme ville. Ce n'est pas un calcul de distance GPS.

### Favoris

Les favoris sont disponibles pour:

- hotel;
- restaurant;
- attraction.

L'API expose `type` + `id`; la base utilise `favoriteable_type` + `favoriteable_id`.

### Reservations

Les reservations hotels/restaurants exigent un compte `tourist`.

Workflow courant:

```text
pending -> approved -> confirmed
pending -> rejected
pending/approved -> cancelled
```

L'admin approuve ou refuse une demande. Le touriste confirme une reservation approuvee via paiement simule. Aucun paiement reel n'est implemente.

### Packages

Les packages touristiques sont consultables publiquement quand ils sont actifs. L'admin peut creer et modifier les packages et leurs items.

Les items de package peuvent representer des hotels, restaurants, attractions, matchs ou contenu custom. La logique courante privilegie les items de meme ville.

### Trip Planner

Le Trip Planner est reserve aux touristes connectes.

Un utilisateur peut:

- creer un trip;
- modifier ou supprimer son trip;
- ajouter des items catalogue, favoris ou custom;
- modifier ou supprimer ses items.

Les items catalogue doivent respecter la ville du trip.

### Administration

L'admin dispose de:

- dashboard statistiques;
- gestion utilisateurs;
- CRUD catalogue;
- gestion reservations;
- gestion packages et items;
- upload images.

Les routes admin sont protegees par `auth:sanctum` et `role:admin`.

## 6. Endpoints principaux

La source de verite reste `backend/routes/api.php`.

Groupes implementes:

- `GET /api/health`
- `/api/auth/*`
- `/api/matches`
- `/api/matches/{id}/nearby`
- `/api/hotels`
- `/api/restaurants`
- `/api/attractions`
- `/api/packages`
- `/api/map-items`
- `/api/favorites`
- `/api/hotel-reservations`
- `/api/restaurant-reservations`
- `/api/my-reservations`
- `/api/trips`
- `/api/admin/*`

Etat confirme le 2026-05-23: 74 routes API.

## 7. Donnees de demonstration

Les seeders creent:

- 8 matchs;
- 10 hotels;
- 10 restaurants;
- 10 attractions;
- packages touristiques;
- reservations de demo;
- 1 admin;
- 2 touristes.

Comptes locaux:

| Role | Email | Mot de passe |
| --- | --- | --- |
| Admin | `admin@maghrebpass.test` | `password` |
| Touriste FR | `tourist@maghrebpass.test` | `password` |
| Touriste EN | `emily.carter@maghrebpass.test` | `password` |

## 8. Contraintes et limites

- Aucune API externe payante n'est requise.
- Aucun paiement reel n'est implemente.
- Les reservations visiteurs non connectes ne sont pas implementees.
- Les favoris matchs/packages ne sont pas implementes.
- Les suggestions nearby sont basees sur la ville, pas sur la distance GPS.
- La compression image n'est pas implementee; les uploads admin sont limites a 2 MB.

## 9. Idees futures optionnelles

Ces elements ne doivent pas etre presentes comme implementes:

- reservation invite sans compte;
- paiement reel;
- favoris pour matchs ou packages;
- calcul nearby par distance GPS;
- notifications email transactionnelles reelles;
- deploiement production complet;
- mode sombre;
- compression ou optimisation automatique des images.

## 10. Validation courante

Commandes backend confirmees:

```bash
cd backend
php artisan route:list --path=api --no-ansi
php artisan migrate:status --no-ansi
php artisan test --no-ansi
```

Resultats confirmes:

- 74 routes API;
- migrations courantes executees;
- 54 tests passes;
- 713 assertions.

Validation frontend disponible:

```bash
cd frontend
npm.cmd run build
```

Comme `frontend/dist` peut etre modifie par le build, verifier `git status` avant de l'inclure dans un push.
