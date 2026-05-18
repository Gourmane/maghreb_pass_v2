# Phase 12 - Risques et mitigations

Statut execute le 2026-05-18.

## Mitigations appliquees

- Donnees demo incompletes: les seeders generent les volumes prevus par le PRD.
- Upload photos volumineux: les endpoints admin acceptent `photo_files[]` et refusent les images de plus de 2 MB.
- Photos externes invalides: les champs `photos[]` doivent maintenant contenir des URLs valides.
- Acces admin: le middleware `role:admin` reste applique au groupe `/api/admin`.
- API externe: le backend ne depend d'aucune API externe pour demarrer, migrer, seeder ou tester.

## Notes techniques

- Les URLs existantes `photos[]` restent supportees pour les donnees de demo.
- Les fichiers uploades sont stockes sur le disque Laravel `public`, sous `uploads/hotels`, `uploads/restaurants` ou `uploads/attractions`.
- La compression d'image n'est pas ajoutee faute de dependance image dediee dans le projet; la limite stricte de 2 MB couvre le risque principal du MVP.

## Validation

```bash
cd backend
php artisan test
```
