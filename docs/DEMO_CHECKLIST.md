# Checklist de demonstration - MaghrebPass Advanced V2.5

Objectif: presenter rapidement les fonctionnalites V2.5 avec des donnees locales.

## 1. Preparation

```bash
cd backend
php artisan config:clear
php artisan migrate:fresh --seed
php artisan storage:link
php artisan test
php artisan serve
```

API locale: `http://localhost:8000/api`

Dans un deuxieme terminal:

```bash
cd frontend
npm install
copy .env.example .env
npm run dev
```

Frontend local: `http://127.0.0.1:5173`

## 2. Verifications rapides

- Ouvrir `GET http://localhost:8000/api/health`
- Verifier les listes publiques:
  - `GET /api/matches`
  - `GET /api/hotels`
  - `GET /api/restaurants`
  - `GET /api/attractions`
  - `GET /api/packages`
  - `GET /api/map-items?city=Casablanca`
- Verifier un filtre par ville:
  - `GET /api/hotels?city=Rabat`
  - `GET /api/restaurants?city=Tanger`

## 3. Parcours touriste

1. Se connecter avec `tourist@maghrebpass.test` / `password` en demo local uniquement.
2. Copier le token Bearer.
3. Appeler `GET /api/auth/me`.
4. Ajouter un favori:

```json
{
  "type": "hotel",
  "id": 1
}
```

5. Lister `GET /api/favorites`.
6. Creer un trip via `POST /api/trips`, puis ajouter un element de la meme ville via `POST /api/trips/{id}/items`.
7. Verifier `GET /api/my-reservations`, annuler une reservation `pending` ou `approved` non payee, puis payer une reservation `approved` via le paiement simule.
8. Supprimer le favori avec `DELETE /api/favorites/{id}`.

## 4. Parcours admin

1. Se connecter avec `admin@maghrebpass.test` / `password` en demo local uniquement.
2. Copier le token Bearer.
3. Afficher `GET /api/admin/stats`.
4. Afficher `GET /api/admin/users`.
5. Creer, modifier et supprimer un contenu via:
  - `/api/admin/matches`
  - `/api/admin/hotels`
  - `/api/admin/restaurants`
  - `/api/admin/attractions`
  - `/api/admin/packages`
6. Approuver/refuser une reservation pending via `/api/admin/hotel-reservations/{reservation}/status` ou `/api/admin/restaurant-reservations/{reservation}/status`.
7. Verifier que l'admin ne peut pas desactiver son propre compte ou le dernier administrateur actif.

## 5. Points a presenter

- Acces public sans compte.
- Authentification Sanctum par cookie HTTP-only ou token API.
- Role admin protege par middleware.
- Favoris lies a l'utilisateur connecte.
- Favoris exposes en API avec `type` + `id`; en base, l'implementation Laravel utilise `favoriteable_type` + `favoriteable_id` pour garder une relation polymorphique equivalente au `item_type` + `item_id` du PRD.
- Carte globale et mini-map sur les fiches geolocalisees.
- Reservations hotels/restaurants reservees aux touristes connectes.
- Workflow reservation: demande pending, approbation admin, paiement simule, confirmation finale.
- Paiement simule uniquement; aucune transaction reelle et aucun numero de carte.
- Packages mono-ville et trips mono-ville.
- Trip Planner accessible via `/trip-planner`, `/trips` et `/my-trips`.
- Donnees bilingues FR/EN.
- Photos sous forme d'URLs ou fichiers images admin limites a 2 MB.
- Aucune API externe requise.
- Nearby base sur des recommandations dans la meme ville, pas sur une distance GPS reelle.
- Derniere validation backend reference: 51 tests passes, 444 assertions.
- Derniere validation frontend reference: build Vite reussi.

## 6. Limites a annoncer

- La compression image n'est pas implementee; la limite backend de 2 MB est appliquee.
- Les comptes `password` sont des comptes seed locaux uniquement.
- `database_export/maghrebpass_data_export.json` contient les donnees catalogue exportees; les packages, reservations et trips de demo sont recrees par seeders.
