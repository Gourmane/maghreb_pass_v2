# PRD — MaghrebPass Advanced V2.5

**Product Requirements Document**  
**Version** : 2.5 — Advanced Travel Planner gratuit et simplifié  
**Projet** : MaghrebPass — Plateforme intelligente de planification touristique pour les visiteurs de la Coupe du Monde au Maroc  
**Équipe** : Nouri Zakaria · Gourmane Mostafa  
**Encadrant** : M. Nadir Hamza — ISTA CFPM Sidi Moumen  
**Date** : Mai 2026  
**Stack cible** : Laravel 12 · React 19 · Vite · Tailwind CSS · MySQL · Laravel Breeze · Leaflet/OpenStreetMap · react-i18next

---

## 1. Résumé exécutif

MaghrebPass Advanced V2.5 est l’évolution avancée du MVP MaghrebPass.

La première version permettait aux touristes de consulter les matchs, les hôtels, les restaurants et les attractions, de filtrer les contenus et de gérer leurs favoris.

La version avancée transforme MaghrebPass en une plateforme de planification touristique plus complète, mais toujours gratuite, réaliste et simple à développer. Le visiteur peut visualiser les lieux sur une carte interactive, découvrir des suggestions autour des matchs et consulter des packages touristiques. Le touriste authentifié peut envoyer des demandes de réservation, suivre leur statut, confirmer une demande approuvée via une étape de validation démo et organiser son séjour avec un Trip Planner simple.

L’objectif principal est de passer d’un simple catalogue touristique à un **Smart Travel Planner** adapté aux visiteurs de la Coupe du Monde au Maroc, sans API payante, sans paiement en ligne réel et sans système de réservation externe.

---

## 2. Contexte du projet actuel

Le projet actuel contient déjà une base fonctionnelle :

- Authentification utilisateur/admin
- Liste des matchs
- Liste des hôtels
- Liste des restaurants
- Liste des attractions
- Système de favoris
- Panneau d’administration
- Données stockées en MySQL
- Données éventuellement exportées ou importées en JSON

La base de données actuelle contient notamment :

```txt
users
matches
hotels
restaurants
attractions
favorites
```

Les données existantes incluent déjà plusieurs villes marocaines comme :

```txt
Casablanca
Rabat
Marrakech
Tanger
Agadir
Fès
```

---

## 3. Problème à résoudre

Le MVP actuel répond au besoin de consultation, mais il reste limité :

- L’utilisateur peut voir des hôtels, mais ne peut pas faire de demande de réservation.
- L’utilisateur peut voir des restaurants, mais ne peut pas réserver une table.
- Les attractions sont listées, mais il n’y a pas encore de carte interactive.
- Les matchs ne sont pas encore reliés à des suggestions touristiques.
- Les favoris existent, mais il n’y a pas encore de vrai planning de séjour.
- L’admin gère les contenus, mais ne gère pas encore les réservations ou les packages.
- L’expérience utilisateur reste proche d’un catalogue, pas encore d’un assistant de voyage.

Le besoin de la version avancée est de permettre au touriste de construire une expérience complète autour de son séjour au Maroc, tout en gardant un projet réalisable gratuitement.

---

## 4. Vision produit

La vision de MaghrebPass V2.5 est :

> Transformer MaghrebPass en une plateforme intelligente permettant aux visiteurs de la Coupe du Monde au Maroc de découvrir, organiser et planifier leur séjour autour des matchs, des hôtels, des restaurants et des attractions touristiques.

La plateforme doit permettre à un touriste de répondre facilement à ces questions :

```txt
Quel match puis-je voir ?
Où puis-je dormir dans cette ville ?
Où puis-je manger ?
Quelles attractions puis-je visiter ?
Quels lieux sont proches du match ?
Quel package puis-je suivre ?
Comment organiser mon séjour jour par jour ?
Comment envoyer une demande de réservation ?
```

---

## 5. Objectifs de la version avancée

| Objectif | Description | Priorité |
|---|---|---|
| Carte interactive | Afficher hôtels, restaurants, attractions et éventuellement matchs sur une carte | Must |
| Réservations simples | Permettre aux touristes d’envoyer des demandes de réservation | Must |
| Gestion admin des réservations | L’admin peut approuver ou refuser les demandes | Must |
| Packages touristiques | Créer des plans prêts à l’emploi combinant hôtel, restaurant, attraction et match | Must |
| Suggestions autour des matchs | Proposer des lieux dans la même ville que le match | Must |
| Favoris standardisés | Utiliser une structure polymorphique `item_type` + `item_id` | Must |
| Trip Planner simple | Permettre à l’utilisateur connecté d’organiser son séjour | Should |
| Expérience utilisateur premium | Interface moderne, claire et orientée voyage | Must |
| Préserver le MVP | Ne pas casser les fonctionnalités existantes | Must |

---

## 6. Contraintes principales

Cette version doit rester :

```txt
Gratuite
Simple à développer
Avancée mais réaliste
Compatible avec le MVP
Sans paiement en ligne réel
Sans API externe obligatoire
Sans système de réservation réel
Sans application mobile native
```

Le système ne propose pas de paiement en ligne réel. Une étape de confirmation/paiement simulé est incluse uniquement pour la démonstration académique, sans transaction bancaire, sans carte et sans prestataire externe comme Stripe ou PayPal.

Le projet est une démonstration académique et non un SaaS de production. Les contenus catalogue sont alimentés manuellement par l’administrateur ou par des seeders/données de démonstration. Les images sont gérées via URL ou upload admin selon les écrans existants. Les workflows sensibles, notamment la confirmation/paiement, restent simulés pour la démonstration.

---

## 7. Périmètre fonctionnel

### Inclus dans V2.5

- Amélioration des données existantes
- Normalisation des villes
- Coordonnées géographiques des lieux
- Carte interactive avec Leaflet/OpenStreetMap
- Demandes de réservation pour hôtels
- Demandes de réservation pour restaurants
- Gestion admin des demandes
- Packages touristiques mono-ville
- Suggestions autour des matchs
- Trip Planner simple mono-ville
- Pages publiques améliorées
- Pages admin avancées
- Filtrage par ville, type, budget, catégorie
- Standardisation des favoris
- Emails transactionnels optionnels via Laravel Mail et SMTP gratuit
- Images par URL externe avec fallback image
- Validation backend avec Laravel Form Requests
- Protection admin avec middleware `auth` + `admin`

### Non inclus dans V2.5

- Paiement en ligne
- Réservation réelle via Booking, Airbnb, Google ou autre API externe
- API externe obligatoire
- Application mobile native
- Notifications push
- Avis utilisateurs avancés
- Chat temps réel
- Système de stock réel des chambres
- Confirmation automatique auprès des hôtels/restaurants
- Drag & drop complexe dans l’admin
- Packages multi-villes
- Trips multi-villes
- Calcul automatique du prix des packages

---

## 8. Utilisateurs cibles

### 8.1 Visiteur non connecté

Le visiteur peut :

- Consulter les matchs
- Consulter les hôtels
- Consulter les restaurants
- Consulter les attractions
- Voir la carte interactive
- Voir les packages publics
- Voir les suggestions autour d’un match
- Accéder aux pages de connexion et d’inscription avant de réserver

Limite :

```txt
Un visiteur non connecté ne peut pas envoyer de demande de réservation.
Il doit créer un compte ou se connecter comme touriste avant de soumettre une réservation.
```

### 8.2 Touriste connecté

Le touriste connecté peut :

- Gérer son profil
- Ajouter/retirer des favoris
- Voir ses favoris
- Envoyer des demandes de réservation
- Consulter le statut de ses réservations
- Annuler une demande de réservation si elle est encore autorisée
- Effectuer une confirmation/paiement simulé après approbation admin
- Créer plusieurs trips
- Ajouter des hôtels, restaurants, attractions ou matchs dans son planning
- Modifier les éléments de son trip
- Organiser son séjour jour par jour

### 8.3 Administrateur

L’administrateur peut :

- Gérer les matchs
- Gérer les hôtels
- Gérer les restaurants
- Gérer les attractions
- Consulter les statistiques utilisateurs et activer/désactiver des comptes depuis le dashboard
- Gérer les réservations
- Approuver/refuser les réservations
- Gérer les packages
- Gérer les éléments des packages
- Visualiser les statistiques du dashboard

---

# 9. Modules fonctionnels

---

## 9.1 Module Authentification

Le module d’authentification reste basé sur Laravel Breeze.

### Fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| AUTH-01 | Connexion utilisateur | Must |
| AUTH-02 | Inscription utilisateur | Must |
| AUTH-03 | Déconnexion | Must |
| AUTH-04 | Profil utilisateur | Should |
| AUTH-05 | Rôle admin/tourist | Must |
| AUTH-06 | Désactivation compte utilisateur | Should |

### Règles

- Un utilisateur peut être `tourist` ou `admin`.
- Les routes admin sont protégées par les middlewares `auth` et `admin`.
- Le middleware `admin` vérifie que l’utilisateur connecté possède `role = admin`.
- Les favoris, trips et réservations personnelles nécessitent une authentification.
- Un visiteur non connecté peut consulter les contenus publics.
- Un visiteur non connecté doit créer un compte ou se connecter avant de soumettre une réservation.
- Les réservations appartiennent toujours à un touriste authentifié afin de permettre le suivi, les statuts, l’annulation autorisée, la sécurité de propriété et une gestion admin claire.

### Premier administrateur

Le premier compte administrateur est créé via un seeder Laravel.

```txt
AdminUserSeeder
```

Exemple de compte pour la démo :

```txt
Email : admin@maghrebpass.ma
Password : password
Role : admin
```

Commande :

```bash
php artisan db:seed --class=AdminUserSeeder
```

---

## 9.2 Module Matchs

Le module Matchs existe déjà dans le MVP. En V2.5, il est enrichi avec des suggestions touristiques.

### Fonctionnalités conservées

| ID | Fonctionnalité | Priorité |
|---|---|---|
| MATCH-01 | Liste des matchs | Must |
| MATCH-02 | Détail d’un match | Must |
| MATCH-03 | Filtre par ville, date, groupe ou phase | Must |
| MATCH-04 | CRUD admin des matchs | Must |

### Nouvelles fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| MATCH-05 | Afficher les hôtels de la même ville que le match | Must |
| MATCH-06 | Afficher les restaurants de la même ville que le match | Must |
| MATCH-07 | Afficher les attractions de la même ville que le match | Must |
| MATCH-08 | Afficher les packages associés à la ville du match | Should |
| MATCH-09 | Afficher le stade sur la carte si coordonnées disponibles | Should |

### Colonnes optionnelles à ajouter aux matchs

```sql
stadium_latitude DECIMAL(10,7) NULL
stadium_longitude DECIMAL(10,7) NULL
map_url TEXT NULL
```

Ces colonnes sont optionnelles. Un match peut être inclus dans un package même sans coordonnées GPS.

### Endpoint proposé

```txt
GET /api/matches/{id}/nearby
```

### Réponse attendue

```json
{
  "match": {},
  "hotels": [],
  "restaurants": [],
  "attractions": [],
  "packages": []
}
```

### Règle métier

Dans V2.5, la proximité est calculée simplement par ville :

```txt
same city = nearby
```

Le calcul par distance GPS n’est pas inclus dans V2.5.

---

## 9.3 Module Hôtels avancé

### Fonctionnalités conservées

| ID | Fonctionnalité | Priorité |
|---|---|---|
| HOTEL-01 | Liste des hôtels | Must |
| HOTEL-02 | Détail hôtel | Must |
| HOTEL-03 | Filtre par ville, étoiles, prix | Must |
| HOTEL-04 | Ajouter aux favoris | Must |
| HOTEL-05 | CRUD admin | Must |

### Nouvelles fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| HOTEL-06 | Coordonnées GPS de l’hôtel | Must |
| HOTEL-07 | Affichage sur la carte | Must |
| HOTEL-08 | Demande de réservation | Must |
| HOTEL-09 | Note indicative | Should |
| HOTEL-10 | Équipements sous forme JSON | Should |
| HOTEL-11 | Mise en avant `is_featured` | Should |
| HOTEL-12 | Mini-carte dans la page détail | Should |

### Colonnes à ajouter

```sql
latitude DECIMAL(10,7) NULL
longitude DECIMAL(10,7) NULL
map_url TEXT NULL
is_featured BOOLEAN DEFAULT false
rating DECIMAL(2,1) NULL
amenities JSON NULL
image_url TEXT NULL
```

### Règles

- `latitude` et `longitude` servent à afficher l’hôtel sur la carte Leaflet.
- `map_url` sert uniquement comme lien externe vers Google Maps ou OpenStreetMap.
- `rating` est une note indicative saisie manuellement par l’admin.
- `image_url` contient une URL externe.
- Une image fallback locale est affichée si l’URL est vide ou cassée.

---

## 9.4 Module Restaurants avancé

### Fonctionnalités conservées

| ID | Fonctionnalité | Priorité |
|---|---|---|
| REST-01 | Liste des restaurants | Must |
| REST-02 | Détail restaurant | Must |
| REST-03 | Filtre ville/cuisine/prix | Must |
| REST-04 | Ajouter aux favoris | Must |
| REST-05 | CRUD admin | Must |

### Nouvelles fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| REST-06 | Coordonnées GPS du restaurant | Must |
| REST-07 | Affichage sur la carte | Must |
| REST-08 | Demande de réservation de table | Must |
| REST-09 | Heures d’ouverture | Should |
| REST-10 | Note indicative | Should |
| REST-11 | Mise en avant `is_featured` | Should |
| REST-12 | Mini-carte dans la page détail | Should |

### Colonnes à ajouter

```sql
latitude DECIMAL(10,7) NULL
longitude DECIMAL(10,7) NULL
map_url TEXT NULL
is_featured BOOLEAN DEFAULT false
rating DECIMAL(2,1) NULL
opening_hours VARCHAR(255) NULL
image_url TEXT NULL
```

### Format de `opening_hours`

Le champ `opening_hours` est une chaîne libre saisie par l’admin.

Exemple :

```txt
Lun-Dim : 09:00 - 23:00
```

Il n’utilise pas de JSON structuré dans V2.5.

---

## 9.5 Module Attractions avancé

### Fonctionnalités conservées

| ID | Fonctionnalité | Priorité |
|---|---|---|
| ATTR-01 | Liste des attractions | Must |
| ATTR-02 | Détail attraction | Must |
| ATTR-03 | Filtre ville/catégorie | Must |
| ATTR-04 | Ajouter aux favoris | Must |
| ATTR-05 | CRUD admin | Must |

### Nouvelles fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| ATTR-06 | Coordonnées GPS de l’attraction | Must |
| ATTR-07 | Affichage sur la carte | Must |
| ATTR-08 | Durée recommandée de visite | Should |
| ATTR-09 | Note indicative | Should |
| ATTR-10 | Mise en avant `is_featured` | Should |
| ATTR-11 | Ajout au planning de voyage | Should |
| ATTR-12 | Mini-carte dans la page détail | Should |

### Colonnes à ajouter

```sql
latitude DECIMAL(10,7) NULL
longitude DECIMAL(10,7) NULL
map_url TEXT NULL
is_featured BOOLEAN DEFAULT false
rating DECIMAL(2,1) NULL
recommended_duration_minutes INT NULL
image_url TEXT NULL
```

---

## 10. Nouveau module — Carte interactive

### 10.1 Objectif

Permettre au touriste de visualiser les lieux touristiques et sportifs sur une carte interactive.

La carte doit afficher :

- Hôtels
- Restaurants
- Attractions
- Stades ou matchs si coordonnées disponibles

### 10.2 Technologie

Utiliser :

```txt
Leaflet
React Leaflet
OpenStreetMap
```

Aucune clé API payante n’est nécessaire.

### 10.3 Fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| MAP-01 | Page publique `/map` | Must |
| MAP-02 | Afficher les hôtels avec marker | Must |
| MAP-03 | Afficher les restaurants avec marker | Must |
| MAP-04 | Afficher les attractions avec marker | Must |
| MAP-05 | Filtrer par type de lieu | Must |
| MAP-06 | Filtrer par ville | Must |
| MAP-07 | Popup avec image, nom, ville et lien détail | Must |
| MAP-08 | Afficher les matchs/stades sur la carte si coordonnées disponibles | Should |
| MAP-09 | Mini-carte dans les pages détail | Should |

### 10.4 Comportement par défaut

Au premier chargement de `/map`, la carte affiche les lieux géolocalisés de Casablanca.

```txt
Ville par défaut : Casablanca
Type par défaut : Tous
Affichage : uniquement les lieux géolocalisés de la ville sélectionnée
```

L’utilisateur peut ensuite changer la ville et le type de lieu avec des filtres.

### 10.5 Endpoint

```txt
GET /api/map-items?city=Casablanca&type=all
```

### 10.6 Réponse attendue

```json
{
  "hotels": [
    {
      "id": 1,
      "type": "hotel",
      "name": "Four Seasons Hotel Casablanca",
      "city": "Casablanca",
      "latitude": 33.589886,
      "longitude": -7.603869,
      "image": "...",
      "detail_url": "/hotels/1"
    }
  ],
  "restaurants": [],
  "attractions": [],
  "matches": []
}
```

### 10.7 Règles

- Seuls les éléments avec `latitude` et `longitude` sont affichés.
- Si un élément n’a pas de coordonnées, il reste visible dans les listes normales.
- Si aucun élément géolocalisé n’est disponible, la carte affiche un message clair :

```txt
Aucun lieu géolocalisé disponible pour cette ville.
```

- La pagination et le lazy loading ne sont pas inclus dans V2.5.
- Cette approche est suffisante pour une démo avec un volume de données limité.

---

## 11. Nouveau module — Demandes de réservation

### 11.1 Objectif

Permettre aux touristes authentifiés d’envoyer des demandes de réservation sans intégrer un vrai système externe.

Cette approche est réaliste car le projet ne dispose pas d’API officielle d’hôtels ou de restaurants.

Le choix d’exiger une authentification est intentionnel : il permet de rattacher chaque réservation à son propriétaire, de consulter les statuts dans l’espace touriste, d’annuler une demande lorsque les règles le permettent et de simplifier le traitement côté administrateur.

### 11.2 Principe

Le système ne confirme pas automatiquement la réservation.

Workflow :

```txt
Touriste envoie une demande
↓
Admin reçoit la demande
↓
Admin approuve ou refuse
↓
Touriste connecté consulte le statut
↓
Si la demande est approuvée, le touriste effectue une confirmation/paiement simulé
↓
Email optionnel envoyé si SMTP configuré
```

### 11.3 Visiteur non connecté

Un visiteur non connecté peut consulter les contenus publics, mais il ne peut pas soumettre une demande de réservation. S’il tente de réserver un hôtel ou un restaurant, l’interface l’oriente vers la connexion ou l’inscription.

```txt
Créer un compte ou se connecter est obligatoire avant d’envoyer une demande de réservation.
```

### 11.4 Utilisateur connecté

Si l’utilisateur est connecté :

- `full_name` et `email` sont pré-remplis depuis son profil.
- L’utilisateur peut modifier ces informations avant l’envoi.
- Le champ `phone` reste obligatoire.
- Il peut consulter ses réservations dans `/my-reservations`.
- Il peut annuler une demande si son statut et son état de paiement le permettent.
- Après approbation admin, il peut effectuer une étape de validation démo appelée confirmation/paiement simulé.

### 11.4.1 Confirmation/paiement simulé

Le système ne propose aucun paiement en ligne réel. Il n’utilise ni Stripe, ni PayPal, ni carte bancaire, ni transaction bancaire, ni prestataire externe.

La confirmation/paiement simulé intervient uniquement après approbation admin. Elle marque la réservation comme confirmée dans le scénario académique et génère une référence de démonstration, sans traiter d’argent réel.

---

## 11.5 Réservations hôtels

### Table `hotel_reservations`

```sql
id
user_id
hotel_id
full_name
email
phone
check_in_date
check_out_date
guests
number_of_rooms
message nullable
status enum('pending','approved','confirmed','rejected','cancelled')
payment_status enum('unpaid','paid')
paid_at nullable
payment_reference nullable
created_at
updated_at
```

### Validation backend Laravel Form Request

```txt
hotel_id : required, exists:hotels,id
full_name : required, string, max:255
email : required, email, max:255
phone : required, string, max:30
check_in_date : required, date, after_or_equal:today
check_out_date : required, date, after:check_in_date
guests : required, integer, min:1, max:20
number_of_rooms : required, integer, min:1, max:10
message : nullable, string, max:1000
```

### Fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| HRES-01 | Formulaire demande réservation hôtel | Must |
| HRES-02 | Lier demande à un hôtel | Must |
| HRES-03 | Lier demande au touriste authentifié | Must |
| HRES-04 | Statut par défaut `pending` | Must |
| HRES-05 | Admin approuve/refuse | Must |
| HRES-06 | Touriste connecté voit ses réservations | Must |
| HRES-07 | Touriste connecté annule une demande autorisée | Should |
| HRES-08 | Champ `number_of_rooms` obligatoire | Must |
| HRES-09 | Confirmation/paiement simulé après approbation | Must |

### Route d’annulation

```txt
PUT /api/my-hotel-reservations/{id}/cancel
```

---

## 11.6 Réservations restaurants

### Table `restaurant_reservations`

```sql
id
user_id
restaurant_id
full_name
email
phone
reservation_date
reservation_time
guests
message nullable
status enum('pending','approved','confirmed','rejected','cancelled')
payment_status enum('unpaid','paid')
paid_at nullable
payment_reference nullable
created_at
updated_at
```

### Validation backend Laravel Form Request

```txt
restaurant_id : required, exists:restaurants,id
full_name : required, string, max:255
email : required, email, max:255
phone : required, string, max:30
reservation_date : required, date, after_or_equal:today
reservation_time : required, date_format:H:i
guests : required, integer, min:1, max:20
message : nullable, string, max:1000
```

### Fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| RRES-01 | Formulaire réservation restaurant | Must |
| RRES-02 | Lier demande à un restaurant | Must |
| RRES-03 | Choisir date, heure, nombre de personnes | Must |
| RRES-04 | Statut par défaut `pending` | Must |
| RRES-05 | Admin approuve/refuse | Must |
| RRES-06 | Touriste connecté voit ses demandes | Must |
| RRES-07 | Touriste connecté annule une demande autorisée | Should |
| RRES-08 | Confirmation/paiement simulé après approbation | Must |

### Route d’annulation

```txt
PUT /api/my-restaurant-reservations/{id}/cancel
```

---

## 11.7 Page `/my-reservations`

La page `/my-reservations` regroupe les réservations hôtels et restaurants.

### Endpoint

```txt
GET /api/my-reservations
```

### Réponse recommandée

```json
{
  "hotel_reservations": [],
  "restaurant_reservations": []
}
```

### Interface frontend

La page affiche des filtres ou onglets :

```txt
Toutes
Hôtels
Restaurants
Pending
Confirmed
Rejected
Cancelled
```

---

## 11.8 Statuts des réservations

```txt
pending
approved
confirmed
rejected
cancelled
```

### Règles

```txt
Le statut initial est pending.
Seul l’admin peut approuver ou refuser.
Le statut approved permet au touriste de lancer la confirmation/paiement simulé.
La confirmation simulée transforme la demande approved en confirmed et payment_status paid.
Un utilisateur connecté peut annuler uniquement ses propres demandes pending ou approved non payées.
Une réservation confirmed, rejected ou cancelled est affichée en lecture seule côté utilisateur.
```

---

## 11.9 Emails optionnels

Le système peut envoyer un email automatique quand l’admin approuve, confirme via validation simulée ou refuse une réservation.

Cette fonctionnalité utilise :

```txt
Laravel Mail
SMTP gratuit
```

Exemples de solutions gratuites :

```txt
Mailtrap pour test local
Gmail SMTP pour démo simple
SMTP gratuit de l’hébergeur si disponible
```

Si l’email n’est pas configuré, la réservation reste fonctionnelle.

---

## 11.10 Endpoints réservations

### Auth

```txt
POST /api/hotel-reservations
POST /api/restaurant-reservations
GET /api/my-reservations
PUT /api/my-hotel-reservations/{id}/cancel
PUT /api/my-restaurant-reservations/{id}/cancel
POST /api/my-hotel-reservations/{id}/pay
POST /api/my-restaurant-reservations/{id}/pay
```

### Admin

```txt
GET /api/admin/reservations
PUT /api/admin/hotel-reservations/{id}/status
PUT /api/admin/restaurant-reservations/{id}/status
```

---

## 12. Nouveau module — Packages touristiques

### 12.1 Objectif

Permettre à l’admin de créer des packages touristiques prêts à l’emploi.

Un package peut combiner :

- Hôtel
- Restaurant
- Attraction
- Match
- Activité personnalisée

### 12.2 Règle principale

Dans V2.5, un package appartient à une seule ville.

Les packages multi-villes ne sont pas inclus afin de garder le projet simple, gratuit et réaliste.

### 12.3 Table `packages`

```sql
id
title_fr
title_en
description_fr
description_en
city
duration_days
price_min decimal nullable
price_max decimal nullable
currency default 'MAD'
cover_image_url nullable
is_active boolean default true
created_at
updated_at
```

### 12.4 Prix des packages

Les champs `price_min` et `price_max` sont saisis manuellement par l’administrateur.

Ils représentent une estimation indicative du budget du package.

Ils ne sont pas calculés automatiquement à partir des hôtels, restaurants ou attractions inclus.

### 12.5 Publication des packages

Seuls les packages avec :

```txt
is_active = true
```

sont visibles publiquement.

Les packages inactifs restent visibles uniquement dans l’administration.

### 12.6 Table `package_items`

```sql
id
package_id
item_type enum('hotel','restaurant','attraction','match','custom')
item_id nullable
title_fr nullable
title_en nullable
description_fr nullable
description_en nullable
day_number integer
start_time nullable
sort_order integer default 0
created_at
updated_at
```

### 12.7 Package item `custom`

Un `package_item` de type `custom` n’a pas de `item_id`.

Il affiche uniquement :

```txt
title_fr
title_en
description_fr
description_en
day_number
start_time
sort_order
```

Il sert à ajouter une étape libre dans le programme.

### 12.8 Matchs dans les packages

Un match peut être ajouté dans un package avec :

```txt
item_type = match
item_id = match_id
```

Les coordonnées GPS du stade sont optionnelles.

### 12.9 Limite d’éléments

Un package peut contenir au maximum :

```txt
30 éléments
```

### 12.10 Suppression d’un élément utilisé

L’admin ne peut pas supprimer un hôtel, restaurant, attraction ou match s’il est utilisé dans un `package_item` ou un `trip_item`.

Message recommandé :

```txt
Impossible de supprimer cet élément, car il est utilisé dans un package ou un trip.
```

### 12.11 Fonctionnalités publiques

| ID | Fonctionnalité | Priorité |
|---|---|---|
| PACK-01 | Liste des packages actifs | Must |
| PACK-02 | Détail package | Must |
| PACK-03 | Affichage jour par jour | Must |
| PACK-04 | Filtre par ville | Must |
| PACK-05 | Affichage budget estimatif | Must |
| PACK-06 | Ajouter package aux favoris ou au trip | Could |

### 12.12 Fonctionnalités admin

| ID | Fonctionnalité | Priorité |
|---|---|---|
| APACK-01 | CRUD packages | Must |
| APACK-02 | Ajouter éléments au package | Must |
| APACK-03 | Ordonner les éléments | Must |
| APACK-04 | Activer/désactiver package | Must |
| APACK-05 | Ajouter cover image URL | Should |

### 12.13 Ordre des éléments

L’admin ordonne les éléments d’un package avec des boutons :

```txt
Monter
Descendre
```

Le drag & drop n’est pas obligatoire dans V2.5.

Le champ `sort_order` détermine l’ordre d’affichage.

---

## 13. Nouveau module — Trip Planner

### 13.1 Objectif

Permettre à un utilisateur connecté de créer son propre plan de séjour.

Le Trip Planner donne au projet une vraie valeur utilisateur, tout en restant simple.

### 13.2 Règle principale

Dans V2.5, un trip est mono-ville.

Le champ `city` définit la ville principale du séjour.

Si l’utilisateur veut organiser Casablanca + Marrakech, il crée deux trips séparés.

### 13.3 Principe

L’utilisateur crée un voyage :

```txt
Titre : Séjour Casablanca
Ville : Casablanca
Dates : 2030-06-13 → 2030-06-16
Budget : Moyen
```

Puis il ajoute des éléments :

```txt
Jour 1 :
- Hôtel
- Restaurant
- Attraction

Jour 2 :
- Match
- Restaurant

Jour 3 :
- Attraction
```

### 13.4 Plusieurs trips

Un utilisateur connecté peut créer plusieurs trips.

Chaque trip appartient à un seul utilisateur.

L’utilisateur peut consulter, modifier ou supprimer uniquement ses propres trips.

### 13.5 Table `trips`

```sql
id
user_id
title
city
start_date
end_date
budget_range enum('budget','medium','premium')
created_at
updated_at
```

### 13.6 Table `trip_items`

```sql
id
trip_id
item_type enum('hotel','restaurant','attraction','match')
item_id
day_number integer
start_time nullable
notes nullable
sort_order integer default 0
created_at
updated_at
```

### 13.7 Suppression d’un trip

La suppression d’un trip supprime automatiquement tous ses `trip_items` associés via cascade delete.

Relation attendue :

```txt
trips 1 --- * trip_items
```

Règle SQL/Laravel :

```txt
trip_items.trip_id -> cascadeOnDelete()
```

### 13.8 Limite d’éléments

Un trip peut contenir au maximum :

```txt
30 éléments
```

### 13.9 Éléments répétés

Un même hôtel, restaurant, attraction ou match peut être ajouté plusieurs fois dans un trip.

Exemple :

```txt
Même restaurant le jour 1 et le jour 3
Même hôtel pendant plusieurs jours
```

Il n’y a pas de contrainte d’unicité stricte entre :

```txt
trip_id
item_type
item_id
```

### 13.10 Modification des éléments du Trip Planner

Un utilisateur peut modifier un élément de son trip avec :

```txt
PUT /api/trip-items/{id}
```

Champs modifiables :

```txt
day_number
start_time
notes
sort_order
```

### 13.11 Fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| TRIP-01 | Créer un trip | Should |
| TRIP-02 | Voir mes trips | Should |
| TRIP-03 | Voir détail trip jour par jour | Should |
| TRIP-04 | Ajouter hôtel au trip | Should |
| TRIP-05 | Ajouter restaurant au trip | Should |
| TRIP-06 | Ajouter attraction au trip | Should |
| TRIP-07 | Ajouter match au trip | Should |
| TRIP-08 | Supprimer élément du trip | Should |
| TRIP-09 | Modifier titre, dates, budget | Could |
| TRIP-10 | Modifier un trip item | Should |

### 13.12 Endpoints

```txt
GET /api/trips [auth]
POST /api/trips [auth]
GET /api/trips/{id} [auth]
PUT /api/trips/{id} [auth]
DELETE /api/trips/{id} [auth]

POST /api/trips/{id}/items [auth]
PUT /api/trip-items/{id} [auth]
DELETE /api/trip-items/{id} [auth]
```

---

## 14. Module Favoris

### 14.1 Objectif

Permettre à l’utilisateur connecté de sauvegarder des hôtels, restaurants et attractions.

### 14.2 Structure officielle V2.5

La table `favorites` utilise une structure polymorphique simple.

```sql
id
user_id
item_type
item_id
created_at
updated_at
```

Valeurs principales :

```txt
hotel
restaurant
attraction
```

Cette structure est obligatoire pour simplifier :

- La page Mes favoris
- L’ajout au Trip Planner depuis les favoris
- L’évolution future vers les packages ou matchs

### 14.3 Migration depuis l’ancien MVP

Si le MVP utilise déjà cette structure, aucune migration n’est nécessaire.

Si le MVP utilise des colonnes séparées comme :

```txt
hotel_id
restaurant_id
attraction_id
```

une migration contrôlée sera créée pour convertir les anciens favoris vers la nouvelle structure.

Exemple :

```txt
hotel_id = 5       -> item_type = hotel, item_id = 5
restaurant_id = 3  -> item_type = restaurant, item_id = 3
attraction_id = 8  -> item_type = attraction, item_id = 8
```

Une sauvegarde de la base doit être effectuée avant cette migration.

### 14.4 Fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| FAV-01 | Ajouter hôtel aux favoris | Must |
| FAV-02 | Ajouter restaurant aux favoris | Must |
| FAV-03 | Ajouter attraction aux favoris | Must |
| FAV-04 | Supprimer favori | Must |
| FAV-05 | Page Mes favoris | Must |
| FAV-06 | Ajouter un favori au Trip Planner | Should |

### 14.5 Extension possible

| ID | Fonctionnalité | Priorité |
|---|---|---|
| FAV-07 | Ajouter package aux favoris | Could |
| FAV-08 | Ajouter match aux favoris | Could |

---

## 15. Panneau d’administration V2.5

### 15.1 Objectif

Le panneau admin doit évoluer pour gérer les nouveaux modules.

### 15.2 Protection des routes admin

Les routes admin sont protégées par :

```txt
auth
admin
```

Exemple Laravel :

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // routes admin
});
```

Le middleware `admin` vérifie :

```php
auth()->user()->role === 'admin'
```

### 15.3 Pages admin

```txt
/admin
/admin/matches
/admin/hotels
/admin/restaurants
/admin/attractions
/admin/reservations
/admin/packages
/admin/packages/:id/items
```

La gestion utilisateur n’est pas un module CRUD frontend autonome. Les statistiques utilisateurs, la liste des comptes et l’activation/désactivation simple sont accessibles depuis le dashboard `/admin`. Le chemin `/admin/users`, s’il est saisi directement, doit revenir vers une vue admin sûre au lieu d’ouvrir un module CRUD inexistant.

### 15.4 Page `/admin/reservations`

La page `/admin/reservations` affiche les réservations hôtels et restaurants dans une seule interface.

Filtres disponibles :

```txt
Toutes
Hôtels
Restaurants
Pending
Confirmed
Rejected
Cancelled
```

Colonnes recommandées :

```txt
Type
Nom client
Email
Téléphone
Élément réservé
Date
Statut
Actions
```

Chaque réservation est rattachée à un touriste authentifié. L’admin identifie le demandeur avec :

```txt
user_id
full_name
email
phone
```

### 15.5 Fonctionnalités

| ID | Fonctionnalité | Priorité |
|---|---|---|
| ADMIN-01 | Dashboard statistiques | Must |
| ADMIN-02 | CRUD matchs | Must |
| ADMIN-03 | CRUD hôtels | Must |
| ADMIN-04 | CRUD restaurants | Must |
| ADMIN-05 | CRUD attractions | Must |
| ADMIN-06 | Liste utilisateurs et activation/désactivation depuis le dashboard | Should |
| ADMIN-07 | Gestion demandes de réservation | Must |
| ADMIN-08 | Confirmation/refus réservation | Must |
| ADMIN-09 | CRUD packages | Must |
| ADMIN-10 | Gestion éléments package | Must |
| ADMIN-11 | Champs GPS dans les formulaires | Must |
| ADMIN-12 | Blocage suppression élément utilisé | Must |

### 15.6 Dashboard admin V2.5

Le dashboard admin affiche :

```txt
Nombre d’utilisateurs
Nombre de matchs
Nombre d’hôtels
Nombre de restaurants
Nombre d’attractions
Nombre de favoris
Nombre de réservations hôtels pending
Nombre de réservations restaurants pending
Nombre total de réservations pending
Nombre total de réservations confirmed
Nombre de packages actifs
Nombre de trips créés
```

---

## 16. Internationalisation

### 16.1 Objectif

Le projet garde une interface FR/EN.

### 16.2 Technologie

```txt
react-i18next
```

### 16.3 Langue par défaut

La langue par défaut est :

```txt
Français
```

Le choix de langue est sauvegardé dans :

```txt
localStorage
```

Si aucun choix n’existe, l’interface démarre en français.

### 16.4 Sélecteur de langue

Le sélecteur FR/EN est affiché dans :

```txt
Navbar publique
Interface utilisateur connectée
```

### 16.5 Données bilingues

Les packages utilisent obligatoirement :

```txt
title_fr
title_en
description_fr
description_en
```

Les hôtels, restaurants et attractions conservent les champs actuels du MVP.

Si leurs descriptions sont uniquement en français, elles restent affichées en français même lorsque l’interface est en anglais.

La traduction complète des contenus hôtels, restaurants et attractions est une amélioration future.

---

## 17. Gestion des images

### 17.1 Choix technique

Les images sont stockées sous forme d’URL externe dans la base de données.

Champs utilisés :

```txt
image_url
cover_image_url
```

L’admin colle une URL dans les formulaires.

Laravel Storage n’est pas obligatoire dans V2.5.

### 17.2 Fallback image

Si une image est vide ou cassée, l’interface affiche une image fallback locale.

Exemple :

```txt
/default-hotel.jpg
/default-restaurant.jpg
/default-attraction.jpg
/default-package.jpg
```

---

## 18. Rating

Le champ `rating` est une note indicative saisie manuellement par l’administrateur.

Elle ne provient pas d’avis utilisateurs.

Elle n’est pas calculée automatiquement.

Exemple :

```txt
Note indicative : 4.5/5
```

---

## 19. Architecture technique

### 19.1 Backend

```txt
Laravel 12
Laravel Breeze
MySQL
Eloquent ORM
API REST
Middleware auth/admin
Form Requests validation
API Resources
Laravel Mail optionnel
```

### 19.2 Frontend

```txt
React 19
Vite
Tailwind CSS
Axios
React Router DOM
react-i18next
Leaflet
React Leaflet
```

### 19.3 Base de données

Base actuelle conservée :

```txt
users
matches
hotels
restaurants
attractions
favorites
```

Nouvelles tables :

```txt
hotel_reservations
restaurant_reservations
packages
package_items
trips
trip_items
```

Colonnes ajoutées aux tables existantes :

```txt
latitude
longitude
map_url
is_featured
rating
amenities
opening_hours
recommended_duration_minutes
image_url
stadium_latitude
stadium_longitude
```

---

## 20. Endpoints API V2.5

### Public

```txt
GET /api/matches
GET /api/matches/{id}
GET /api/matches/{id}/nearby

GET /api/hotels
GET /api/hotels/{id}

GET /api/restaurants
GET /api/restaurants/{id}

GET /api/attractions
GET /api/attractions/{id}

GET /api/map-items?city=Casablanca&type=all

GET /api/packages
GET /api/packages/{id}
```

### Auth

```txt
GET /api/auth/me
POST /api/auth/logout

GET /api/favorites
POST /api/favorites
DELETE /api/favorites/{id}

GET /api/my-reservations
POST /api/hotel-reservations
POST /api/restaurant-reservations
PUT /api/my-hotel-reservations/{id}/cancel
PUT /api/my-restaurant-reservations/{id}/cancel
POST /api/my-hotel-reservations/{id}/pay
POST /api/my-restaurant-reservations/{id}/pay

GET /api/trips
POST /api/trips
GET /api/trips/{id}
PUT /api/trips/{id}
DELETE /api/trips/{id}

POST /api/trips/{id}/items
PUT /api/trip-items/{id}
DELETE /api/trip-items/{id}
```

### Admin

```txt
GET /api/admin/stats

GET/POST/PUT/DELETE /api/admin/matches
GET/POST/PUT/DELETE /api/admin/hotels
GET/POST/PUT/DELETE /api/admin/restaurants
GET/POST/PUT/DELETE /api/admin/attractions

GET /api/admin/users
PUT /api/admin/users/{id}/toggle

GET /api/admin/reservations
PUT /api/admin/hotel-reservations/{id}/status
PUT /api/admin/restaurant-reservations/{id}/status

GET/POST/PUT/DELETE /api/admin/packages
GET/POST/PUT/DELETE /api/admin/packages/{package}/items
```

---

## 21. Pages frontend V2.5

### Public

| Page | URL |
|---|---|
| Accueil | `/` |
| Matchs | `/matches` |
| Détail match | `/matches/:id` |
| Hôtels | `/hotels` |
| Détail hôtel | `/hotels/:id` |
| Restaurants | `/restaurants` |
| Détail restaurant | `/restaurants/:id` |
| Attractions | `/attractions` |
| Détail attraction | `/attractions/:id` |
| Carte interactive | `/map` |
| Packages | `/packages` |
| Détail package | `/packages/:id` |
| Connexion | `/login` |
| Inscription | `/register` |

### Auth

| Page | URL |
|---|---|
| Profil | `/profile` |
| Mes favoris | `/favorites` |
| Mes réservations | `/my-reservations` |
| Trip Planner | `/trip-planner` |
| Mes voyages | `/my-trips` |
| Détail voyage | `/trips/:id` |

### Admin

| Page | URL |
|---|---|
| Dashboard admin | `/admin` |
| Matchs admin | `/admin/matches` |
| Hôtels admin | `/admin/hotels` |
| Restaurants admin | `/admin/restaurants` |
| Attractions admin | `/admin/attractions` |
| Utilisateurs dans dashboard | `/admin` |
| Réservations admin | `/admin/reservations` |
| Packages admin | `/admin/packages` |
| Éléments package | `/admin/packages/:id/items` |

---

## 22. Règles métier

### Réservations

```txt
Une réservation est toujours une demande.
Elle n’est jamais confirmée automatiquement.
Le statut initial est pending.
Seul l’admin peut approuver ou refuser.
Un utilisateur non connecté ne peut pas envoyer de demande de réservation.
Un touriste doit être connecté pour envoyer une demande avec ses informations.
Un utilisateur connecté peut voir ses demandes.
Un utilisateur connecté peut annuler ses demandes pending ou approved non payées.
Après approbation admin, le touriste peut effectuer une confirmation/paiement simulé.
Aucune transaction réelle n’est effectuée : pas de Stripe, PayPal, carte bancaire, banque ou prestataire externe.
Toutes les demandes sont validées côté backend avec Laravel Form Requests.
```

### Packages

```txt
Un package appartient à une seule ville.
Un package peut contenir plusieurs éléments.
Un package peut contenir des éléments existants ou des éléments custom.
Un package inactif ne s’affiche pas publiquement.
Les prix sont saisis manuellement par l’admin.
Un package peut contenir au maximum 30 éléments.
```

### Trip Planner

```txt
Un trip appartient à un utilisateur.
Un trip est mono-ville.
Un utilisateur peut créer plusieurs trips.
Un utilisateur ne peut voir que ses propres trips.
Un trip peut contenir hôtels, restaurants, attractions et matchs.
Les éléments sont organisés par jour.
Un même élément peut apparaître plusieurs fois dans un trip.
Un trip peut contenir au maximum 30 éléments.
La suppression d’un trip supprime ses trip_items.
```

### Carte

```txt
Un élément apparaît sur la carte seulement s’il a latitude et longitude.
La carte affiche Casablanca par défaut.
Les filtres de la carte ne modifient pas la base de données.
La carte utilise OpenStreetMap/Leaflet.
```

### Favoris

```txt
Les favoris utilisent item_type et item_id.
Les types principaux sont hotel, restaurant et attraction.
Les favoris peuvent être ajoutés au Trip Planner.
```

### Suppression d’éléments utilisés

```txt
L’admin ne peut pas supprimer un hôtel, restaurant, attraction ou match s’il est utilisé dans un package_item ou un trip_item.
Le système affiche un message d’erreur clair.
```

---

## 23. Critères d’acceptation

### Carte

- [ ] La page `/map` existe.
- [ ] La carte affiche Casablanca par défaut.
- [ ] Les hôtels avec coordonnées s’affichent.
- [ ] Les restaurants avec coordonnées s’affichent.
- [ ] Les attractions avec coordonnées s’affichent.
- [ ] L’utilisateur peut filtrer par type.
- [ ] L’utilisateur peut filtrer par ville.
- [ ] Chaque marker affiche un popup.
- [ ] Un message s’affiche si aucun lieu géolocalisé n’est disponible.

### Réservations

- [ ] Un utilisateur peut envoyer une demande de réservation hôtel.
- [ ] Le champ `number_of_rooms` existe pour l’hôtel.
- [ ] Un utilisateur peut envoyer une demande de réservation restaurant.
- [ ] La réservation restaurant contient date, heure et nombre de personnes.
- [ ] Les validations backend sont en place.
- [ ] Le statut initial est `pending`.
- [ ] L’admin peut voir les demandes.
- [ ] L’admin peut confirmer/refuser une demande.
- [ ] Un utilisateur connecté peut voir ses demandes.
- [ ] Un utilisateur connecté peut annuler une demande `pending`.

### Packages

- [ ] L’admin peut créer un package.
- [ ] Le package appartient à une ville.
- [ ] Le package possède `is_active`.
- [ ] L’admin peut ajouter des éléments au package.
- [ ] L’admin peut ajouter un item `custom`.
- [ ] L’admin peut ordonner les éléments avec Monter/Descendre.
- [ ] Les packages actifs s’affichent publiquement.
- [ ] Le détail package affiche un planning jour par jour.
- [ ] Un package contient au maximum 30 éléments.
- [ ] Les packages sont disponibles en FR/EN.

### Trip Planner

- [ ] Un utilisateur connecté peut créer plusieurs trips.
- [ ] Un trip est mono-ville.
- [ ] Un utilisateur peut ajouter un élément au trip.
- [ ] Un utilisateur peut modifier un trip item.
- [ ] Un utilisateur peut voir son trip jour par jour.
- [ ] Un utilisateur ne peut pas voir les trips des autres.
- [ ] Un même élément peut être ajouté plusieurs fois.
- [ ] Un trip contient au maximum 30 éléments.
- [ ] Supprimer un trip supprime ses trip_items.

### Favoris

- [ ] La table `favorites` utilise `item_type` et `item_id`.
- [ ] L’utilisateur peut ajouter hôtel, restaurant et attraction aux favoris.
- [ ] L’utilisateur peut supprimer un favori.
- [ ] L’utilisateur peut ajouter un favori à un trip.
- [ ] Une migration contrôlée existe si l’ancien MVP utilise une autre structure.

### Admin

- [ ] Les routes admin sont protégées par `auth` et `admin`.
- [ ] Le middleware admin vérifie `role = admin`.
- [ ] L’admin ne peut pas supprimer un élément utilisé dans un package ou un trip.
- [ ] Le dashboard affiche les statistiques V2.5.

### Non-régression MVP

- [ ] Les listes existantes fonctionnent.
- [ ] Les détails existants fonctionnent.
- [ ] Les favoris fonctionnent.
- [ ] L’auth fonctionne.
- [ ] Le dashboard admin fonctionne.
- [ ] Les CRUD existants fonctionnent.

---

## 24. Plan de développement recommandé

### Phase 0 — Préparation et audit MVP

```txt
Créer ou vérifier le repo GitHub
Vérifier que le projet MVP fonctionne
Vérifier les tables MySQL existantes
Vérifier les données hôtels/restaurants/attractions/matchs
Vérifier et normaliser les champs city
Vérifier la structure de favorites
Migrer favorites vers item_type + item_id si nécessaire
Importer les données JSON seulement si nécessaire
Sauvegarder la base avant migration V2.5
Créer une branche v2-advanced
```

### Phase 1 — Data Enhancement

```txt
Ajouter colonnes GPS
Ajouter image_url
Ajouter rating
Ajouter is_featured
Ajouter amenities pour hôtels
Ajouter opening_hours pour restaurants
Ajouter recommended_duration_minutes pour attractions
Ajouter stadium_latitude/stadium_longitude optionnels pour matchs
Mettre à jour models/casts
Mettre à jour admin forms
Mettre à jour API responses
```

### Phase 2 — Carte interactive

```txt
Installer Leaflet et React Leaflet
Créer endpoint /api/map-items?city=Casablanca&type=all
Créer page /map
Afficher Casablanca par défaut
Ajouter filtres ville/type
Ajouter popups
Ajouter message si aucun résultat
Ajouter mini-carte dans les pages détail si possible
```

### Phase 3 — Réservations

```txt
Créer tables hotel_reservations et restaurant_reservations
Ajouter number_of_rooms pour hôtels
Ajouter reservation_date et reservation_time pour restaurants
Créer Form Requests de validation
Créer models/controllers/routes
Créer formulaires frontend
Pré-remplir nom/email si utilisateur connecté
Créer page admin réservations
Créer page my-reservations
Ajouter annulation utilisateur pending
Ajouter Laravel Mail optionnel
```

### Phase 4 — Packages

```txt
Créer packages et package_items
Ajouter is_active
Ajouter price_min et price_max manuels
Ajouter limite de 30 éléments
Créer admin CRUD
Créer gestion items package
Ajouter items custom
Ajouter boutons Monter/Descendre
Créer pages publiques packages
Créer détail package jour par jour
Bloquer suppression élément utilisé
```

### Phase 5 — Match Nearby

```txt
Créer endpoint /api/matches/{id}/nearby
Ajouter section dans détail match
Afficher hôtels/restaurants/attractions/packages de la même ville
Vérifier cohérence city
```

### Phase 6 — Trip Planner

```txt
Créer trips et trip_items
Créer cascade delete pour trip_items
Ajouter limite de 30 éléments
Créer API auth
Créer pages frontend
Permettre ajout d’éléments au planning
Permettre modification d’un trip item
Permettre suppression d’un trip item
Permettre ajout depuis favoris
```

### Phase 7 — Internationalisation

```txt
Installer/configurer react-i18next
Définir français comme langue par défaut
Sauvegarder la langue dans localStorage
Ajouter sélecteur FR/EN dans navbar
Traduire les textes principaux
```

### Phase 8 — QA finale

```txt
Tester auth
Tester middleware admin
Tester admin
Tester favoris
Tester migration favoris si nécessaire
Tester réservations
Tester validations backend
Tester annulation
Tester packages
Tester suppression élément utilisé
Tester map
Tester trip planner
Tester responsive
Tester build frontend
Tester routes API
Tester non-régression MVP
```

---

## 25. Estimation du temps de développement

Estimation réaliste :

```txt
3 à 4 semaines
```

### Estimation par phase

```txt
Phase 0 — Audit MVP + préparation : 1 jour
Phase 1 — Data Enhancement : 2 jours
Phase 2 — Carte interactive : 3 jours
Phase 3 — Réservations : 4 jours
Phase 4 — Packages : 4 jours
Phase 5 — Match Nearby : 2 jours
Phase 6 — Trip Planner simple : 4 jours
Phase 7 — i18n minimum : 1 jour
Phase 8 — QA finale : 2 jours
```

Total estimé :

```txt
23 jours environ
```

### Si le temps manque

Priorité à faire :

```txt
1. Data Enhancement
2. Carte interactive
3. Réservations
4. Packages
5. Match Nearby
```

À couper ou réduire si le temps manque :

```txt
Trip Planner
i18n complète
Emails automatiques
Mini-cartes détail
```

Le Trip Planner reste une fonctionnalité `Should`.

---

## 26. Risques et mitigations

| Risque | Impact | Mitigation |
|---|---|---|
| Données GPS manquantes | Carte incomplète | Autoriser `nullable` et afficher seulement les lieux géolocalisés |
| Villes incohérentes | Match Nearby incorrect | Normaliser les champs `city` en Phase 0 |
| Structure favoris différente | Migration risquée | Sauvegarde DB + migration contrôlée |
| Réservations confondues avec vraie réservation | Mauvaise compréhension utilisateur | Utiliser le terme “Demande de réservation” |
| Projet trop complexe | Retard développement | Implémentation par phases |
| Régression du MVP | Bugs existants | Tests après chaque phase |
| Images externes cassées | Mauvais affichage | Garder fallback image |
| Admin surchargé | UX difficile | Dashboard clair + filtres |
| Trip Planner trop long à faire | Retard | Le placer en `Should`, après les modules Must |
| Emails SMTP non configurés | Notification absente | Garder email optionnel, statut visible dans plateforme |
| Suppression d’élément utilisé | Données cassées | Bloquer la suppression avec message clair |

---

## 27. Priorités MoSCoW

### Must Have

```txt
Carte interactive
Coordonnées GPS
Normalisation des villes
Demandes de réservation hôtels
Champ number_of_rooms
Demandes de réservation restaurants avec date/heure
Validations backend
Gestion admin réservations
Packages touristiques mono-ville
Suggestions autour des matchs
Favoris polymorphiques
Protection admin auth + admin
Préservation du MVP
```

### Should Have

```txt
Trip Planner mono-ville
Mes réservations
Annulation des réservations pending
Notes indicatives
Lieux featured
Durée recommandée attractions
Équipements hôtels
Emails optionnels
Mini-cartes détail
Ajout au trip depuis favoris
```

### Could Have

```txt
Favoris pour packages
Favoris pour matchs
Distance GPS réelle
Export PDF du trip
Partage du trip par lien
Drag & drop package items
```

### Won’t Have for V2.5

```txt
Paiement
API Booking
Application mobile
Notifications push
Avis utilisateurs complets
Chat en temps réel
Packages multi-villes
Trips multi-villes
Calcul automatique du prix package
Pagination avancée de la carte
```

---

## 28. Définition de succès

MaghrebPass V2.5 est réussi si :

```txt
Le projet ne ressemble plus seulement à un catalogue.
L’utilisateur peut planifier un séjour complet ou semi-complet.
Les matchs sont reliés à l’expérience touristique.
La carte donne une visualisation claire des lieux.
Les réservations donnent une valeur business.
Les packages donnent une expérience prête à l’emploi.
L’admin peut gérer toute la plateforme.
Le projet reste gratuit.
Le MVP existant reste fonctionnel.
```

---

## 29. Formulation courte pour le rapport ou la soutenance

```txt
La version avancée de MaghrebPass transforme l’application initiale en un véritable planificateur intelligent de séjour touristique.

Elle permet aux visiteurs de la Coupe du Monde de consulter les matchs, découvrir les lieux proches, visualiser les hôtels, restaurants et attractions sur une carte interactive, envoyer des demandes de réservation et organiser leur séjour à travers des packages ou un planning personnalisé.

Cette version reste réaliste et gratuite, car elle n’utilise pas de paiement en ligne réel ni d’API externe obligatoire. L’étape de confirmation/paiement simulé existe uniquement pour la démonstration académique après approbation admin.
```

---

## 30. Nom recommandé de la version

```txt
MaghrebPass V2.5 — Smart Travel Planner
```

Ou en français :

```txt
MaghrebPass V2.5 — Planificateur intelligent de séjour touristique
```

---

## 31. Recommandation finale

Pour ce projet, le meilleur ordre de développement est :

```txt
1. Vérifier et stabiliser le MVP
2. Normaliser les villes
3. Standardiser les favoris
4. Ajouter latitude/longitude/map_url
5. Créer la carte interactive
6. Ajouter les demandes de réservation
7. Ajouter les packages
8. Ajouter les suggestions autour des matchs
9. Ajouter Trip Planner si le temps reste suffisant
10. Finaliser l’i18n et la QA
```

La version la plus réaliste et la plus forte pour ce projet est donc :

```txt
MaghrebPass Advanced V2.5 = MVP actuel + Map + Réservations simples + Packages + Suggestions autour des matchs + Trip Planner simple
```

Le projet reste avancé, mais sans devenir trop complexe ni dépendant de services payants.
