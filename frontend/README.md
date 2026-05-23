# MaghrebPass Advanced V2.5 - Frontend React

Interface React 19 + Vite 7 pour consommer l'API Laravel MaghrebPass.

## Fonctionnalites couvertes

- Accueil public avec contenus de demonstration.
- Pages publiques: `/matches`, `/hotels`, `/restaurants`, `/attractions`, `/packages`, `/map`.
- Pages detail: `/matches/:id`, `/hotels/:id`, `/restaurants/:id`, `/attractions/:id`, `/packages/:id`.
- Carte globale Leaflet/OpenStreetMap et mini-map sur les fiches geolocalisees.
- Filtres par module selon les parametres API disponibles.
- Connexion, inscription, deconnexion et profil.
- Auth Sanctum via cookie HTTP-only `maghrebpass_token`; le frontend ne stocke pas de token Bearer dans `localStorage`.
- Favoris pour hotels, restaurants et attractions.
- Reservations hotels/restaurants pour utilisateurs connectes.
- Mes reservations avec annulation et paiement simule apres approbation admin.
- Trip Planner via `/trip-planner`, `/trips` et `/my-trips`.
- Panneau admin: statistiques, utilisateurs, CRUD catalogue, reservations, packages et items.
- Interface bilingue FR/EN avec `react-i18next`.
- Styles projet avec Tailwind CSS via `@tailwindcss/vite`.
- Design responsive desktop/mobile.

## Installation

Depuis `maghreb_pass/frontend`:

```bash
npm.cmd install
copy .env.example .env
npm.cmd run dev
```

URL locale: `http://127.0.0.1:5173`

Le backend doit tourner sur `http://localhost:8000`.

## Configuration

Variable disponible:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

Le client Axios dans `src/lib/api.js` utilise `withCredentials: true` pour envoyer le cookie Sanctum.

## Validation

```bash
npm.cmd run build
```

Attention: `frontend/dist` est present dans le depot et peut etre modifie par un build. Verifier `git status` avant et apres si vous ne voulez pas inclure des artefacts generes.
