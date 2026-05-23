# Risques et mitigations - MaghrebPass Advanced V2.5

Statut mis a jour le 2026-05-23.

## Mitigations appliquees

- Donnees demo incompletes: les seeders generent les volumes attendus pour la demo locale.
- Upload photos volumineux: les endpoints admin acceptent `photo_files[]` et refusent les images de plus de 2 MB.
- Photos externes invalides: les champs `photos[]` doivent contenir des URLs valides.
- Acces admin: le middleware `role:admin` protege le groupe `/api/admin`.
- Auth SPA: Laravel Sanctum utilise un token Bearer et un cookie HTTP-only `maghrebpass_token`.
- Reservations: le workflow courant se limite aux touristes connectes et utilise un paiement simule sans prestataire externe.
- API externe: aucune API payante n'est requise pour demarrer, migrer, seeder ou tester.

## Limites connues

- La compression image n'est pas implementee; la limite stricte de 2 MB couvre le risque principal.
- Les suggestions nearby sont basees sur la meme ville, pas sur une distance GPS reelle.
- Le paiement simule doit etre presente comme une validation demo, jamais comme une transaction reelle.

## Validation

```bash
cd backend
php artisan test --no-ansi
```

Etat confirme le 2026-05-23: 54 tests passes, 713 assertions.
