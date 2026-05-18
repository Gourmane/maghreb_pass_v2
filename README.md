# MaghrebPass MVP

Application MVP pour accompagner les supporters et touristes pendant la Coupe du Monde 2030 au Maroc.

Le workspace contient le backend Laravel et le frontend React du MVP:

- API publique: matchs, hotels, restaurants, attractions
- Authentification Sanctum avec cookie HTTP-only
- Gestion des favoris
- Administration protegee par role
- Donnees de demonstration
- Tests d'acceptation MVP
- Interface React publique, favoris, profil, admin et i18n FR/EN
- MySQL, Laravel Breeze et Tailwind CSS selon le PRD

## Demarrage rapide

```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

API: `http://localhost:8000/api`

La base MySQL `maghreb_pass` doit exister avant les migrations. Par defaut:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maghreb_pass
DB_USERNAME=root
DB_PASSWORD=
```

Dans un deuxieme terminal:

```bash
cd frontend
npm install
copy .env.example .env
npm run dev
```

Frontend: `http://127.0.0.1:5173`

## Comptes de demo

- Admin: `admin@maghrebpass.test` / `password`
- Touriste FR: `tourist@maghrebpass.test` / `password`
- Touriste EN: `emily.carter@maghrebpass.test` / `password`

## Documents projet

- [PRD_MaghrebPass_MVP.md](PRD_MaghrebPass_MVP.md)
- [ACCEPTANCE_PHASE_11.md](ACCEPTANCE_PHASE_11.md)
- [PHASE_12_RISK_MITIGATIONS.md](PHASE_12_RISK_MITIGATIONS.md)
- [Checklist demo](docs/DEMO_CHECKLIST.md)
- [Exemples API](docs/API_EXAMPLES.md)
- [Backend README](backend/README.md)
- [Frontend README](frontend/README.md)

## Validation

```bash
cd backend
php artisan test
```

```bash
cd frontend
npm run build
```

Derniere validation backend: 26 tests passes, 155 assertions.
Derniere validation frontend: build Vite reussi.
