# Documentation Utilisateur

## Réserver une salle

1. Connectez-vous à votre compte utilisateur.
2. Depuis la page d'accueil ou la page de recherche, cliquez sur la photo d'une salle ou sur une région dans la page d'accueil.
3. Cliquez sur "Réserver" ou remplissez le formulaire de réservation sur la fiche de la salle.
4. Renseignez les informations demandées (dates, options, équipements, etc.).
5. Validez le formulaire. Votre demande de réservation sera enregistrée avec le statut "en attente".
6. Vous recevrez une notification lorsque l'administrateur aura validé ou refusé votre réservation.

## Voir mes réservations

1. Connectez-vous à votre compte.
2. Cliquez sur l'onglet "Réservation" ou accédez à `/booking/s`.
3. Consultez la liste de vos réservations avec leur statut (en attente, confirmée, annulée).

## Modifier une réservation

1. Depuis la liste de vos réservations, cliquez sur le bouton "Modifier" à côté de la réservation concernée.
2. Modifiez les informations souhaitées puis validez.

## Annuler une réservation

1. Depuis la liste de vos réservations, cliquez sur le bouton "Annuler" à côté de la réservation concernée.
2. Confirmez l'annulation.

## Notes

-   Une réservation "en attente" doit être validée par un administrateur avant d'être confirmée.
-   Vous pouvez consulter le détail de chaque réservation en cliquant sur son nom ou sur "Détail".

---

# Documentation Git (projet Symfony)

## Initialisation du projet

```bash
# Cloner le dépôt
git clone <url-du-repo>
cd reservation

# Installer les dépendances PHP
composer install

# Installer les dépendances JS (si besoin)
npm install
```

## Commandes Git de base

```bash
# Vérifier l'état du dépôt
git status

# Ajouter des fichiers à l'index
git add <fichier>

# Valider les modifications
git commit -m "Message clair sur la modification"

# Envoyer les modifications sur le dépôt distant
git push

# Récupérer les dernières modifications
git pull
```

## Branches

```bash
# Créer une nouvelle branche
git checkout -b nom-de-branche

# Changer de branche
git checkout nom-de-branche
```

## Bonnes pratiques

-   Faire des commits réguliers et explicites.
-   Toujours tirer (`git pull`) avant de pousser (`git push`).
-   Utiliser des branches pour les nouvelles fonctionnalités ou corrections.
-   Ne pas modifier directement la branche principale sans validation.

---

Pour toute question, contactez l'administrateur du projet ou consultez la documentation Symfony officielle : https://symfony.com/doc/current/index.html

```bash
composer update easycorp/easyadmin-bundle
```
