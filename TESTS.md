# Plan de tests manuels - Projet Gestion des Salles

## Utilisateur (Visiteur)

- [X] Je peux m'inscrire et me connecter.
- [X] Je peux voir la liste des salles et leurs disponibilités.
- [X] Je peux réserver une salle.
- [X] Je peux modifier ou annuler ma réservation.
- [ ] Je peux filtrer les salles par capacité, équipements, ergonomie.
- [X] Je peux voir mes réservations et leur statut (en attente, validée).
- [X] Je peux poser une pré-réservation (statut "en attente").

## Administrateur

- [ ] Je peux accéder au dashboard admin.
- [X] Je peux valider/annuler les pré-réservations.
- [ ] Je peux gérer les salles, équipements, utilisateurs (CRUD).
- [ ] Je reçois une notification pour les pré-réservations non traitées 5 jours avant.
- [ ] Je vois un code couleur pour différencier les statuts (en attente, validée).
- [ ] Je peux gérer les équipements et critères d'ergonomie des salles.

## Recherche et Filtres

- [ ] Je peux rechercher une salle par capacité.
- [ ] Je peux filtrer par équipements (matériel, logiciel).
- [ ] Je peux filtrer par ergonomie (luminosité, PMR, etc.).

## Exigences techniques

- [ ] La base de données est conforme à la modélisation attendue.
- [ ] Les équipements sont bien associés aux salles.
- [ ] L'interface permet de réserver en moins de 5 clics.
- [ ] Le dashboard admin affiche les notifications et le code couleur.
- [ ] Le projet est versionné sur Git avec une documentation claire.
- [ ] Le diagramme UML est fourni et à jour.

---

**Remarques / TODO pour l'équipe :**

- Ajouter des tests automatiques PHPUnit pour les fonctionnalités critiques.
- Documenter les scénarios de test dans ce fichier à chaque évolution.
- Améliorer la gestion des notifications si besoin.
- Vérifiera
- S'assurer que le diagramme UML est conforme au code.
