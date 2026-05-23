# Exemples API - MaghrebPass Advanced V2.5

Base locale: `http://localhost:8000/api`

Les exemples PowerShell utilisent `curl.exe` pour eviter l'alias PowerShell `curl`.

## Health

```powershell
curl.exe http://localhost:8000/api/health
```

## Connexion touriste

```powershell
curl.exe -X POST http://localhost:8000/api/auth/login `
  -H "Accept: application/json" `
  -H "Content-Type: application/json" `
  -d "{\"email\":\"tourist@maghrebpass.test\",\"password\":\"password\"}"
```

## Connexion admin

```powershell
curl.exe -X POST http://localhost:8000/api/auth/login `
  -H "Accept: application/json" `
  -H "Content-Type: application/json" `
  -d "{\"email\":\"admin@maghrebpass.test\",\"password\":\"password\"}"
```

Le login retourne un token Bearer et pose aussi le cookie HTTP-only `maghrebpass_token`. Dans les exemples ci-dessous, remplacer `TOKEN_TOURISTE` ou `TOKEN_ADMIN` par le token retourne.

## Catalogue public

```powershell
curl.exe "http://localhost:8000/api/matches?city=Casablanca"
curl.exe "http://localhost:8000/api/matches/1/nearby"
curl.exe "http://localhost:8000/api/hotels?city=Rabat"
curl.exe "http://localhost:8000/api/restaurants?city=Tanger"
curl.exe "http://localhost:8000/api/attractions?city=Fes"
curl.exe "http://localhost:8000/api/packages?city=Casablanca"
curl.exe "http://localhost:8000/api/map-items?city=Casablanca&type=all"
```

## Favoris

```powershell
curl.exe -X POST http://localhost:8000/api/favorites `
  -H "Accept: application/json" `
  -H "Content-Type: application/json" `
  -H "Authorization: Bearer TOKEN_TOURISTE" `
  -d "{\"type\":\"hotel\",\"id\":1}"
```

```powershell
curl.exe http://localhost:8000/api/favorites `
  -H "Accept: application/json" `
  -H "Authorization: Bearer TOKEN_TOURISTE"
```

## Reservation hotel

```powershell
curl.exe -X POST http://localhost:8000/api/hotel-reservations `
  -H "Accept: application/json" `
  -H "Content-Type: application/json" `
  -H "Authorization: Bearer TOKEN_TOURISTE" `
  -d "{\"hotel_id\":1,\"full_name\":\"Touriste Demo\",\"email\":\"demo.hotel@maghrebpass.test\",\"phone\":\"+212600000100\",\"check_in_date\":\"2030-06-15\",\"check_out_date\":\"2030-06-18\",\"guests\":2,\"number_of_rooms\":1,\"message\":\"Arrivee apres le match\"}"
```

## Mes reservations et paiement simule

```powershell
curl.exe http://localhost:8000/api/my-reservations `
  -H "Accept: application/json" `
  -H "Authorization: Bearer TOKEN_TOURISTE"
```

Apres approbation admin:

```powershell
curl.exe -X POST http://localhost:8000/api/my-hotel-reservations/1/pay `
  -H "Accept: application/json" `
  -H "Authorization: Bearer TOKEN_TOURISTE"
```

## Trip planner

```powershell
curl.exe -X POST http://localhost:8000/api/trips `
  -H "Accept: application/json" `
  -H "Content-Type: application/json" `
  -H "Authorization: Bearer TOKEN_TOURISTE" `
  -d "{\"title\":\"Casablanca match week\",\"city\":\"Casablanca\"}"
```

## Stats admin

```powershell
curl.exe http://localhost:8000/api/admin/stats `
  -H "Accept: application/json" `
  -H "Authorization: Bearer TOKEN_ADMIN"
```

## Statut reservation admin

```powershell
curl.exe -X PUT http://localhost:8000/api/admin/hotel-reservations/1/status `
  -H "Accept: application/json" `
  -H "Content-Type: application/json" `
  -H "Authorization: Bearer TOKEN_ADMIN" `
  -d "{\"status\":\"approved\"}"
```

## Creation hotel admin avec URL photo

```powershell
curl.exe -X POST http://localhost:8000/api/admin/hotels `
  -H "Accept: application/json" `
  -H "Content-Type: application/json" `
  -H "Authorization: Bearer TOKEN_ADMIN" `
  -d "{\"name\":\"Hotel Demo\",\"description_fr\":\"Description FR\",\"description_en\":\"Description EN\",\"city\":\"Casablanca\",\"district\":\"Centre\",\"stars\":4,\"price_min\":900,\"price_max\":1300,\"currency\":\"MAD\",\"photos\":[\"https://example.test/hotel.jpg\"]}"
```
