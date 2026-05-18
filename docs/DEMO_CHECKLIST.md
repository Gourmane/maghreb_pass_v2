# Checklist de demonstration - MaghrebPass MVP

Objectif: presenter rapidement les fonctionnalites backend du MVP avec des donnees locales.

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
- Verifier un filtre par ville:
  - `GET /api/hotels?city=Rabat`
  - `GET /api/restaurants?city=Tanger`

## 3. Parcours touriste

1. Se connecter avec `tourist@maghrebpass.test` / `password`.
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
6. Supprimer le favori avec `DELETE /api/favorites/{id}`.

## 4. Parcours admin

1. Se connecter avec `admin@maghrebpass.test` / `password`.
2. Copier le token Bearer.
3. Afficher `GET /api/admin/stats`.
4. Afficher `GET /api/admin/users`.
5. Creer, modifier et supprimer un contenu via:
  - `/api/admin/matches`
  - `/api/admin/hotels`
  - `/api/admin/restaurants`
  - `/api/admin/attractions`

## 5. Points a presenter

- Acces public sans compte.
- Authentification Sanctum par token.
- Role admin protege par middleware.
- Favoris lies a l'utilisateur connecte.
- Donnees bilingues FR/EN.
- Photos sous forme d'URLs ou fichiers images admin limites a 2 MB.
- Aucune API externe requise.

## 6. Limites a annoncer

- La compression image n'est pas implementee; la limite backend de 2 MB est appliquee.
