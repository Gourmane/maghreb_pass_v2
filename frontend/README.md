# MaghrebPass Advanced V2.5 - Frontend React

Interface React + Vite pour consommer l'API Laravel MaghrebPass.

## Fonctionnalites couvertes

- Pages publiques: matchs, hotels, restaurants, attractions, packages et carte.
- Pages detail: `/matches/:id`, `/hotels/:id`, `/restaurants/:id`, `/attractions/:id`, `/packages/:id`.
- Mini-map OpenStreetMap sur les details hotels/restaurants/attractions geolocalises.
- Filtres par ville et filtres specifiques par module.
- Authentification: connexion, inscription, deconnexion.
- Cookie HTTP-only via Sanctum; le token n'est pas stocke dans `localStorage`.
- Connexion rapide avec les comptes demo locaux uniquement.
- Favoris utilisateur: ajout, consultation, suppression.
- CTA favoris vers le Trip Planner.
- Trip Planner disponible via `/trip-planner`, `/trips` et `/my-trips`.
- Reservations utilisateur et administration des reservations.
- Profil utilisateur modifiable.
- Panneau admin: compteurs, utilisateurs, CRUD contenus principaux, packages et reservations.
- Interface bilingue FR/EN avec `react-i18next`.
- Tailwind CSS branche via `@tailwindcss/vite`.
- Design responsive desktop/mobile.

## Installation

```bash
npm install
copy .env.example .env
npm run dev
```

URL locale: `http://127.0.0.1:5173`

Le backend doit tourner sur `http://localhost:8000`.

## Validation

```bash
npm run build
```

## Configuration

Variable disponible:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```
