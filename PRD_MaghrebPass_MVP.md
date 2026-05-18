# PRD — MaghrebPass MVP

**Product Requirements Document**
**Version** : 1.0 — MVP
**Projet** : MaghrebPass — Application web de planification touristique (Coupe du Monde Maroc)
**Équipe** : Nouri Zakaria · Gourmane Mostafa
**Encadrant** : M. Nadir Hamza — ISTA CFPM Sidi Moumen
**Date** : Mai 2026

---

## 1. Résumé exécutif

MaghrebPass est une application web permettant aux touristes (nationaux et étrangers) de préparer et planifier leur séjour au Maroc durant la Coupe du Monde. Elle centralise les informations sur les matchs, les hôtels, les restaurants et les attractions culturelles, le tout sans dépendre d'APIs externes payantes.

**Périmètre MVP** : Application web uniquement (React + Laravel), données gérées en base MySQL via un panneau d'administration, deux langues (FR/EN), zéro API externe.

---

## 2. Problème & Contexte

À l'approche de la Coupe du Monde au Maroc, les touristes font face à :

- Des informations dispersées sur plusieurs sites non centralisés
- L'absence d'une plateforme unique combinant sport, hébergement et culture
- Une barrière linguistique (peu de contenu disponible en anglais sur les ressources locales marocaines)

**Solution MVP** : Une plateforme web simple, en français et en anglais, où un administrateur alimente manuellement les données (matchs, hôtels, restaurants, attractions) et où les touristes peuvent consulter, filtrer et sauvegarder leurs favoris.

---

## 3. Objectifs du MVP

| Objectif                                | Critère de succès                                                                 |
| --------------------------------------- | --------------------------------------------------------------------------------- |
| Permettre la consultation des matchs    | L'utilisateur peut voir les matchs avec leurs groupes, dates, stades et scores    |
| Permettre la découverte des hôtels      | L'utilisateur peut filtrer et consulter des hôtels avec photos et prix indicatifs |
| Permettre la découverte des restaurants | L'utilisateur peut consulter des restaurants avec localisation et spécialités     |
| Permettre la découverte des attractions | L'utilisateur peut consulter les sites touristiques avec description et ville     |
| Permettre de sauvegarder des favoris    | Un utilisateur connecté peut ajouter/retirer des favoris                          |
| Panneau d'administration fonctionnel    | L'admin peut créer, modifier et supprimer tous les contenus                       |
| Interface bilingue FR/EN                | Toute l'interface est disponible en français et en anglais                        |

---

## 4. Non-inclus dans le MVP (V2+)

- Réservation d'hôtels ou de restaurants (pas d'API partenaire)
- Notifications push
- Application mobile native
- Intégration Football Data API (les matchs sont saisis manuellement)
- Carte interactive Google Maps (on affiche la ville/adresse en texte)
- Langues arabe et espagnol
- Avis / notes utilisateurs
- Système de paiement

---

## 5. Utilisateurs cibles

| Rôle                    | Description                                                                                     |
| ----------------------- | ----------------------------------------------------------------------------------------------- |
| **Touriste (visiteur)** | Peut parcourir tout le contenu sans créer de compte. Avec un compte, il peut gérer ses favoris. |
| **Touriste (inscrit)**  | Compte créé sur la plateforme. Accès aux favoris et au profil.                                  |
| **Administrateur**      | Gère tout le contenu de la plateforme via un panneau d'administration dédié.                    |

---

## 6. Fonctionnalités MVP

### 6.1 Authentification

| ID      | Fonctionnalité                                          | Priorité |
| ------- | ------------------------------------------------------- | -------- |
| AUTH-01 | Inscription (nom, email, mot de passe, langue préférée) | Must     |
| AUTH-02 | Connexion / Déconnexion                                 | Must     |
| AUTH-03 | Réinitialisation du mot de passe par email              | Should   |
| AUTH-04 | Profil utilisateur (modifier nom, langue)               | Should   |

**Notes techniques** : Laravel Breez pour l'authentification API. Tokens stockés en cookie HTTP-only.

---

### 6.2 Module Matchs

> **Approche sans API** : L'administrateur saisit les matchs manuellement dans le panneau d'administration. Les données sont stockées en MySQL. Approche réaliste : la FIFA publie les calendriers officiels bien à l'avance ; l'admin les encode une fois. Les scores sont mis à jour manuellement après chaque match.

| ID       | Fonctionnalité                                                | Priorité |
| -------- | ------------------------------------------------------------- | -------- |
| MATCH-01 | Lister tous les matchs avec filtre par groupe / phase / date  | Must     |
| MATCH-02 | Détail d'un match (équipes, date, heure, stade, ville, score) | Must     |
| MATCH-03 | Afficher le statut du match : À venir / En cours / Terminé    | Must     |
| MATCH-04 | Admin : CRUD complet sur les matchs                           | Must     |

**Modèle de données — Match** :

```
matches
  id, team_home, team_home_code, team_home_flag_url,
  team_away, team_away_code, team_away_flag_url,
  score_home, score_away,
  match_date, match_time, stadium, city, group_name,
  phase (groupe / huitième / quart / demi / finale),
  status (upcoming / live / finished),
  created_at, updated_at
```

**Règle images drapeaux** : `team_home_flag_url` et `team_away_flag_url` doivent contenir des URLs directes vers des PNG de drapeaux, par exemple `https://flagcdn.com/w320/ma.png`. Les codes équipes sont stockés sur 3 caractères (`MAR`, `POR`, `ESP`, etc.).

---

### 6.3 Module Hôtels

> **Approche sans API** : Les hôtels sont saisis manuellement par l'admin. Pour les prix, on affiche une fourchette indicative (ex : "250–600 MAD/nuit"). Pour la localisation, on affiche la ville et le quartier en texte. Pas de réservation : un bouton "Voir plus" redirige vers le site officiel de l'hôtel (URL saisie par l'admin).

| ID       | Fonctionnalité                                                                                   | Priorité |
| -------- | ------------------------------------------------------------------------------------------------ | -------- |
| HOTEL-01 | Lister les hôtels avec filtre par ville / étoiles / fourchette de prix                           | Must     |
| HOTEL-02 | Détail d'un hôtel (nom, description, ville, quartier, étoiles, prix indicatif, photos, site web) | Must     |
| HOTEL-03 | Bouton "Visiter le site" (lien externe vers site officiel)                                       | Must     |
| HOTEL-04 | Ajouter/retirer des favoris (utilisateur connecté)                                               | Must     |
| HOTEL-05 | Admin : CRUD complet sur les hôtels + upload de photos                                           | Must     |

**Modèle de données — Hôtel** :

```
hotels
  id, name, description_fr, description_en, city, district,
  stars (1–5), price_min, price_max, currency (MAD),
  website_url, phone, email, photos (JSON array of direct image URLs or uploaded storage paths),
  created_at, updated_at
```

---

### 6.4 Module Restaurants

> **Approche sans API** : Même logique que les hôtels. L'admin encode les restaurants manuellement. Pas de réservation : bouton de contact (WhatsApp ou téléphone) saisi par l'admin.

| ID      | Fonctionnalité                                                                                      | Priorité |
| ------- | --------------------------------------------------------------------------------------------------- | -------- |
| REST-01 | Lister les restaurants avec filtre par ville / cuisine / gamme de prix                              | Must     |
| REST-02 | Détail d'un restaurant (nom, description, spécialités, ville, adresse, gamme prix, photos, contact) | Must     |
| REST-03 | Bouton "Appeler" ou "WhatsApp" (numéro saisi par l'admin)                                           | Must     |
| REST-04 | Ajouter/retirer des favoris (utilisateur connecté)                                                  | Must     |
| REST-05 | Admin : CRUD complet sur les restaurants + upload de photos                                         | Must     |

**Modèle de données — Restaurant** :

```
restaurants
  id, name, description_fr, description_en, city, address,
  cuisine_type, price_range (budget / moyen / gastronomique),
  phone, whatsapp, photos (JSON array of direct image URLs or uploaded storage paths),
  created_at, updated_at
```

---

### 6.5 Module Attractions

> **Approche sans API** : Contenu culturel et touristique encodé par l'admin. Description en FR et EN. Adresse en texte. Pas de carte intégrée.

| ID      | Fonctionnalité                                                                | Priorité |
| ------- | ----------------------------------------------------------------------------- | -------- |
| ATTR-01 | Lister les attractions avec filtre par ville / catégorie                      | Must     |
| ATTR-02 | Détail d'une attraction (nom, description, ville, adresse, catégorie, photos) | Must     |
| ATTR-03 | Ajouter/retirer des favoris (utilisateur connecté)                            | Must     |
| ATTR-04 | Admin : CRUD complet sur les attractions + upload de photos                   | Must     |

**Catégories d'attractions** : Patrimoine historique / Musée / Mosquée / Médina / Plage / Montagne / Souk / Autre

**Modèle de données — Attraction** :

```
attractions
  id, name, description_fr, description_en, city, address,
  category, entry_price (nullable), opening_hours,
  photos (JSON array of direct image URLs or uploaded storage paths),
  created_at, updated_at
```

---

### 6.6 Système de Favoris

| ID     | Fonctionnalité                                              | Priorité |
| ------ | ----------------------------------------------------------- | -------- |
| FAV-01 | Ajouter un hôtel / restaurant / attraction aux favoris      | Must     |
| FAV-02 | Page "Mes Favoris" regroupant tous les éléments sauvegardés | Must     |
| FAV-03 | Retirer un élément des favoris                              | Must     |

**Modèle de données — Favoris** (relation polymorphique) :

```
favorites
  id, user_id, favoriteable_id, favoriteable_type (Hotel/Restaurant/Attraction),
  created_at
```

---

### 6.7 Panneau d'Administration

| ID       | Fonctionnalité                                                                          | Priorité |
| -------- | --------------------------------------------------------------------------------------- | -------- |
| ADMIN-01 | Tableau de bord avec compteurs (matchs, hôtels, restaurants, attractions, utilisateurs) | Must     |
| ADMIN-02 | CRUD Matchs                                                                             | Must     |
| ADMIN-03 | CRUD Hôtels + upload photos                                                             | Must     |
| ADMIN-04 | CRUD Restaurants + upload photos                                                        | Must     |
| ADMIN-05 | CRUD Attractions + upload photos                                                        | Must     |
| ADMIN-06 | Liste des utilisateurs (voir, désactiver)                                               | Should   |
| ADMIN-07 | Accès réservé (rôle admin uniquement)                                                   | Must     |

---

### 6.8 Internationalisation (i18n)

| ID      | Fonctionnalité                                                                                        | Priorité |
| ------- | ----------------------------------------------------------------------------------------------------- | -------- |
| I18N-01 | Interface disponible en Français et Anglais                                                           | Must     |
| I18N-02 | Sélecteur de langue dans le header                                                                    | Must     |
| I18N-03 | La langue préférée est sauvegardée dans le profil utilisateur                                         | Should   |
| I18N-04 | Les descriptions des contenus (hôtels, restaurants, attractions) sont saisies en FR et EN par l'admin | Must     |

**Librairie** : `react-i18next` côté frontend.

---

## 7. Architecture technique

### Stack

| Couche          | Technologie                                |
| --------------- | ------------------------------------------ |
| Backend         | Laravel 12 + Laravel Breez                 |
| Frontend        | React 19 + Vite + Tailwind CSS + Axios     |
| Base de données | MySQL 8                                    |
| Upload photos   | Laravel Storage (disque local ou `public`) |
| i18n Frontend   | react-i18next                              |
| i18n Backend    | Laravel Lang / fichiers JSON de traduction |

### Structure des rôles

```
users
  id, name, email, password, role (tourist / admin),
  preferred_language (fr / en), avatar_url, is_active,
  created_at, updated_at
```

`avatar_url` est nullable et sert à afficher une image de profil directe. `is_active` permet de désactiver un compte sans le supprimer.

### Endpoints API principaux (Laravel)

```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/me

GET    /api/matches
GET    /api/matches/{id}

GET    /api/hotels
GET    /api/hotels/{id}

GET    /api/restaurants
GET    /api/restaurants/{id}

GET    /api/attractions
GET    /api/attractions/{id}

GET    /api/favorites          [auth]
POST   /api/favorites          [auth]
DELETE /api/favorites/{id}     [auth]

-- Admin (middleware: role:admin)
GET/POST/PUT/DELETE /api/admin/matches
GET/POST/PUT/DELETE /api/admin/hotels
GET/POST/PUT/DELETE /api/admin/restaurants
GET/POST/PUT/DELETE /api/admin/attractions
GET                 /api/admin/users
PUT                 /api/admin/users/{id}/toggle
GET                 /api/admin/stats
POST                /api/admin/upload
```

---

## 8. Pages frontend

| Page                 | URL                  | Accès  |
| -------------------- | -------------------- | ------ |
| Accueil              | `/`                  | Public |
| Matchs               | `/matches`           | Public |
| Détail match         | `/matches/:id`       | Public |
| Hôtels               | `/hotels`            | Public |
| Détail hôtel         | `/hotels/:id`        | Public |
| Restaurants          | `/restaurants`       | Public |
| Détail restaurant    | `/restaurants/:id`   | Public |
| Attractions          | `/attractions`       | Public |
| Détail attraction    | `/attractions/:id`   | Public |
| Connexion            | `/login`             | Guest  |
| Inscription          | `/register`          | Guest  |
| Mon profil           | `/profile`           | Auth   |
| Mes favoris          | `/favorites`         | Auth   |
| Admin — Dashboard    | `/admin`             | Admin  |
| Admin — Matchs       | `/admin/matches`     | Admin  |
| Admin — Hôtels       | `/admin/hotels`      | Admin  |
| Admin — Restaurants  | `/admin/restaurants` | Admin  |
| Admin — Attractions  | `/admin/attractions` | Admin  |
| Admin — Utilisateurs | `/admin/users`       | Admin  |

---

## 9. Plan de développement (7 jours)

| Jour   | Tâches                                                                                                                         |
| ------ | ------------------------------------------------------------------------------------------------------------------------------ |
| **J1** | Setup Laravel + React + Tailwind. Migration DB (users, matches, hotels, restaurants, attractions, favorites). Seeders de base. |
| **J2** | Auth Laravel Breez (register, login, logout, me). Middleware rôles. Tests Postman.                                             |
| **J3** | API REST : CRUD Matchs + Hôtels (admin). Upload photos.                                                                        |
| **J4** | API REST : CRUD Restaurants + Attractions (admin). API Favoris (auth).                                                         |
| **J5** | Frontend React : Pages publiques (Accueil, Matchs, Hôtels, Restaurants, Attractions). Filtres.                                 |
| **J6** | Frontend React : Auth (login/register). Favoris. Profil. Pages admin (dashboard + tables CRUD).                                |
| **J7** | i18n (react-i18next FR/EN). Tests end-to-end. Correction bugs. Données de démo.                                                |

---

## 10. Données de démonstration (seed)

Pour la démo, seeder les données suivantes :

- **Matchs** : 8 matchs de la phase de groupes (inventés ou basés sur le tirage FIFA officiel publié)
- **Hôtels** : 10 hôtels réels marocains (Casablanca, Marrakech, Rabat, Agadir, Tanger) avec vraies infos publiques
- **Restaurants** : 10 restaurants réels avec cuisine (marocaine, internationale)
- **Attractions** : 10 sites touristiques réels (Mosquée Hassan II, Médina de Fès, Jardin Majorelle, etc.)
- **Utilisateurs** : 1 admin + 2 touristes de test

**Règle seed images** :

- `photos`, `avatar_url`, `team_home_flag_url` et `team_away_flag_url` utilisent uniquement des URLs directes qui ouvrent un fichier image.
- URLs acceptées pour les seeds externes : `https://upload.wikimedia.org/...` et `https://flagcdn.com/...`.
- URLs refusées : `https://commons.wikimedia.org/wiki/File:...`, `https://commons.wikimedia.org/wiki/Special:FilePath/...` et les URLs de pages web d'hôtels/restaurants utilisées comme photos.
- Les champs `photos` restent des tableaux JSON.

---

## 11. Critères d'acceptation du MVP

- [ ] Un visiteur peut parcourir les matchs, hôtels, restaurants et attractions sans créer de compte
- [ ] Un utilisateur peut créer un compte, se connecter et gérer ses favoris
- [ ] Un administrateur peut ajouter / modifier / supprimer tout contenu
- [ ] L'interface est disponible en français et en anglais avec un sélecteur
- [ ] Les photos uploadées s'affichent correctement dans les pages de détail
- [ ] Les URLs seedées pour photos, avatars et drapeaux ouvrent directement une image
- [ ] Les matchs exposent les codes équipes et les drapeaux dans l'API publique et admin
- [ ] Les filtres fonctionnent sur chaque module (ville minimum)
- [ ] Aucune API externe n'est requise pour faire fonctionner l'application

---

## 12. Risques & mitigations

| Risque                                  | Mitigation                                                                          |
| --------------------------------------- | ----------------------------------------------------------------------------------- |
| Données incomplètes pour la démo        | Seeder avec données réelles marocaines facilement trouvables publiquement           |
| Upload photos volumineux                | Limiter à 2MB par image, compresser côté Laravel                                    |
| Gestion des rôles oubliée sur une route | Middleware `role:admin` appliqué globalement sur le groupe `/api/admin`             |
| i18n partiel (traductions manquantes)   | Définir la langue de fallback en FR, les clés manquantes affichent le FR par défaut |

---

_Document rédigé pour le projet de synthèse — Bac+2 Développement Digital Full Stack — ISTA CFPM Sidi Moumen 2025–2026_
