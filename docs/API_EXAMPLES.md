# Exemples API - MaghrebPass MVP

Remplacer `BASE_URL` par `http://localhost:8000/api`.

## Health

```bash
curl http://localhost:8000/api/health
```

## Connexion touriste

```bash
curl -X POST http://localhost:8000/api/auth/login ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"tourist@maghrebpass.test\",\"password\":\"password\"}"
```

## Connexion admin

```bash
curl -X POST http://localhost:8000/api/auth/login ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@maghrebpass.test\",\"password\":\"password\"}"
```

## Catalogue public

```bash
curl "http://localhost:8000/api/matches?city=Casablanca"
curl "http://localhost:8000/api/hotels?city=Rabat"
curl "http://localhost:8000/api/restaurants?city=Tanger"
curl "http://localhost:8000/api/attractions?city=Fes"
```

## Favoris

```bash
curl -X POST http://localhost:8000/api/favorites ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -H "Authorization: Bearer TOKEN_TOURISTE" ^
  -d "{\"type\":\"hotel\",\"id\":1}"
```

```bash
curl http://localhost:8000/api/favorites ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer TOKEN_TOURISTE"
```

## Stats admin

```bash
curl http://localhost:8000/api/admin/stats ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer TOKEN_ADMIN"
```

## Creation hotel admin avec URL photo

```bash
curl -X POST http://localhost:8000/api/admin/hotels ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -H "Authorization: Bearer TOKEN_ADMIN" ^
  -d "{\"name\":\"Hotel Demo\",\"description_fr\":\"Description FR\",\"description_en\":\"Description EN\",\"city\":\"Casablanca\",\"district\":\"Centre\",\"stars\":4,\"price_min\":900,\"price_max\":1300,\"currency\":\"MAD\",\"photos\":[\"https://example.test/hotel.jpg\"]}"
```
