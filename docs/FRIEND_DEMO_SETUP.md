# Demarrage demo sur un autre ordinateur

Ce fichier sert pour lancer MaghrebPass depuis un ZIP ou un clone GitHub sur un autre PC.

## Prerequis

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL local
- Extension PHP `pdo_mysql`

## 1. Base de donnees

Creer une base MySQL vide:

```sql
CREATE DATABASE advenced_maghrebpass_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 2. Backend

Dans un terminal:

```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

API attendue:

```text
http://127.0.0.1:8000/api/health
```

## 3. Frontend

Dans un deuxieme terminal:

```bash
cd frontend
npm.cmd install
copy .env.example .env
npm.cmd run dev
```

Site attendu:

```text
http://127.0.0.1:5173
```

## Comptes de demo

```text
Admin: admin@maghrebpass.test / password
Touriste: tourist@maghrebpass.test / password
Touriste EN: emily.carter@maghrebpass.test / password
```

## Verification rapide

Avant la presentation:

```bash
cd backend
php artisan test --no-ansi
```

```bash
cd frontend
npm.cmd run build
```

Si les deux commandes passent, le projet est pret pour la demo.
