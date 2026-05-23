## Product Context

MaghrebPass Advanced V2.5 aide les visiteurs de la Coupe du Monde 2030 au Maroc a consulter des contenus touristiques, organiser un sejour simple et presenter un back-office academique complet.

### Users

- Visiteur: consulte les matchs, hotels, restaurants, attractions, packages, carte et details publics.
- Touriste connecte: gere profil, favoris, reservations hotels/restaurants et trips.
- Administrateur: gere les utilisateurs, contenus catalogue, reservations, packages, items de package et images.

### Current Product Behavior

- L'authentification utilise Laravel Sanctum avec cookie HTTP-only `maghrebpass_token`.
- Les roles applicatifs sont `tourist` et `admin`.
- Les reservations exigent un compte touriste.
- Le paiement est simule apres approbation admin et sert uniquement a confirmer une reservation dans le cadre demo.
- Les cartes utilisent Leaflet/OpenStreetMap.
- Les donnees sont bilingues FR/EN quand le module le requiert.
- Aucune API externe payante ni paiement reel n'est requis.

### Brand Personality

La personnalite visee est accueillante, fiable et marocaine. L'interface doit inspirer confiance et rester claire pour un utilisateur qui compare rapidement des villes, prix, dates, statuts et options de sejour.

### Aesthetic Direction

Le produit utilise une direction chaude et culturelle: fond clair, surfaces creme, rouge marocain, vert, touches dorees, iconographie lucide, photographie du Maroc et motifs subtils.

Les interfaces publiques peuvent etre plus visuelles. Les interfaces admin doivent rester plus denses et operationnelles: tableaux, formulaires, statuts, actions rapides et validation visible.

### Design Principles

- Prioriser la clarte de voyage: ville, date, prix, statut, contact et action principale doivent etre visibles rapidement.
- Garder une identite marocaine sobre: rouge, vert, or et photos doivent soutenir le contenu.
- Presenter le paiement comme une simulation demo, pas comme un paiement reel.
- Concevoir bilingue par defaut avec textes courts et robustes aux longueurs FR/EN.
- Differencier public et admin: experience publique accueillante, back-office compact et fiable.

### Open Questions

- Niveau d'accessibilite cible explicite, par exemple WCAG AA.
- Preference officielle pour mode clair uniquement ou ajout futur d'un mode sombre.
- Strategie de deploiement public si le projet est publie hors environnement local.
