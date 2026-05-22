# MaghrebPass Advanced V2.5

Application Advanced V2.5 pour accompagner les supporters et touristes pendant la Coupe du Monde 2030 au Maroc.

Le workspace contient le backend Laravel et le frontend React:

- API publique: matchs, hotels, restaurants, attractions, packages et carte
- Authentification Sanctum avec cookie HTTP-only
- Gestion des favoris, reservations authentifiees, paiement simule et trip planner
- Administration protegee par role: CRUD catalogue, utilisateurs, reservations et packages
- Donnees de demonstration
- Tests d'acceptation V2.5
- Interface React publique, favoris, profil, reservations, trip planner, admin et i18n FR/EN
- MySQL, Laravel Sanctum et React SPA selon le PRD

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

Ces identifiants sont reserves au demo local apres seed. Ne pas les utiliser pour un demo public ou un environnement production.

- Admin: `admin@maghrebpass.test` / `password`
- Touriste FR: `tourist@maghrebpass.test` / `password`
- Touriste EN: `emily.carter@maghrebpass.test` / `password`

## Securite demo public

- Ne jamais livrer le fichier `backend/.env`; il est ignore par git et doit rester local.
- Pour un demo public ou production, garder `APP_DEBUG=false`, `SESSION_ENCRYPT=true`, `COOKIE_SECURE=true` sous HTTPS, et generer un `APP_KEY` propre a l'environnement.
- Le fichier `backend/.env.example` documente ces valeurs sures; les developpeurs peuvent les surcharger localement dans `.env`.
- Pour les emails en demo locale, `MAIL_MAILER=log` est suffisant; aucun secret SMTP reel n'est requis.

## Documents projet

- [PRD_MaghrebPass_Advanced_V2_5_Final.md](PRD_MaghrebPass_Advanced_V2_5_Final.md)
- [ACCEPTANCE_PHASE_11.md](ACCEPTANCE_PHASE_11.md)
- [PHASE_12_RISK_MITIGATIONS.md](PHASE_12_RISK_MITIGATIONS.md)
- [Checklist demo](docs/DEMO_CHECKLIST.md)
- [Exemples API](docs/API_EXAMPLES.md)
- [Developer guide](docs/DEVELOPER_GUIDE.md)
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

Derniere validation backend: 51 tests passes, 444 assertions.
Derniere validation frontend: build Vite reussi.

## Notes de livraison

- Les favoris sont exposes par l'API avec `type` + `id`; en base, Laravel utilise `favoriteable_type` + `favoriteable_id`, implementation polymorphique equivalente au `item_type` + `item_id` du PRD.
- Les visiteurs peuvent consulter les contenus publics, mais les reservations hotels/restaurants exigent un compte touriste connecte.
- Une reservation devient confirmee uniquement apres approbation admin puis paiement simule. Aucun paiement reel, Stripe, PayPal ou numero de carte n'est utilise.
- Les suggestions nearby sont basees sur la meme ville, pas sur une distance GPS reelle.
- `database_export/maghrebpass_data_export.json` contient les donnees catalogue exportees. Les packages, reservations et trips de demonstration sont generes par les seeders.
