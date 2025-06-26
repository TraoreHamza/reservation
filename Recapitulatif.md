# user stories pour une application de reservation de salle .

En tant qu’utilisateur anonyme , je souhaite pouvoir consulter l’application afin de d'cider de m’inscrire.
En tant qu’utilisateur, je souhaite reserver une salle, afin d'y organiser un evenement.
En tant qu’utilisateur, je souhaite avoir un filtre de recherche , afin de faciliter ma recherche de salle.
En tant qu’utilisateur, je souhaite pouvoir voir les disponnibilités des salle, afin de choisir celle qui correspond a mes dates d evenement.
En tant qu’utilisateur, je souhaite pouvoir choisir l' equipements afin de selectionner ce dont j'ai besoin.
En tant qu’utilisateur, je souhaite etre notifié de la validité de ma reservation, afin de m assurer de sa prise en compte.
En tant qu’utilisateur, je souhaite etre notifié de l'acceptation de ma réservation, afin de lancer l organisation de l'evenement.
En tant qu’utilisateur, je souhaite pouvoir modifier ou annuler ma reservation, afin de gerer l organisation de l'evenement.
En tant qu’utilisateur, je souhaite pouvoir choisir des prestations supplementaire , afin facilité l organisation de l'evenement.
En tant qu’utilisateur, je souhaite pouvoir laisser un avis, afin de partager mon experience.
En tant qu’utilisateur, je souhaite pouvoir noter la salle , afin de l evaluer.
En tant qu’administrateur, je souhaite pouvoir gerer les salles , afin de maintenir le catalogue a jour.
En tant qu’administrateur, je souhaite pouvoir gerer les utilisateur, afin d' assurer le respect des cgu.
En tant qu’administrateur, je souhaite pouvoir gerer les reservation, afin d'assurer la bonne disponibilité des salles.
En tant qu’administrateur, je souhaite etre allérté 5 jours avant la date d'une reservation non traitées, afin de la prendre en charge.
En tant qu’administrateur, je souhaite etre notifié lors d'une nouvelle resérvation , afin de la prendre en charge.
En tant qu’administrateur, je souhaite pouvoir valider ou annuler une reservation , afin de de gérer les planning de reservation.
En tant qu’administrateur, je souhaite avoir un tableau de bord complet pour la gestion des données , afin d avoir une vision clair et simple des réservation.
En tant qu’administrateur, je souhaite pouvoir gerer les equipements disponibles dans les salles (logiciels,materiels et critéres d ergonomie), afin de maintenir a jour le catalogue.




---



## Liste priorisée de user stories

| Priorité | Rôle                 | Action                                                                | Bénéfice                                                           |
| :------: | -------------------- | --------------------------------------------------------------------- | ------------------------------------------------------------------ |
|     1    | Visiteur             | Consulter la liste des annonces                                       | Découvrir rapidement le contenu disponible                         |
|     2    | Visiteur             | Lire une annonce détaillé                                              | Approfondir la recherche d’intérêt                                     |
|     3    | Utilisateur          | S’inscrire / se connecter                                             |  Accéder aux fonctionnalités protégées |
|     4    | Utilisateur connecté | réserver ou pre-reserver une salle                                                 | s'assurer de la validité de la reservation                                      |
|     5    | Utilisateur connecté |  modifier ou annuler sa reservation                           | Gérer des imprevu ou corriger une erreur                                     |
|     6    | Utilisateur connecté       | possibilié de faire des recherches ciblées par filtre (capacitée,lieu,equipement,ergonomie,date)                | faciliter la recherche selon ses critéres                    |
|     7    | Administrateur       | Gérer les notification des reservation non traitées                 |  attirer l attention sur les reservation non traitée dont la date approche             |
|     8    | Administrateur       | Gérer les utilisateurs (rôles, blocage)                               | Contrôler l’accès et la sécurité du site                           |
|     9    | Administrateur       | Créer / éditer / supprimer une annonce via EasyAdmin      | Gérer le contenu  de manière structurée                         |
|    10    | Administrateur       | Consulter des statistiques basiques (nombre reservation, équipement, disponibilité, code couleur...) | Suivre l’activité du site  

---


## deroulement de la conception

- Création du projet symfony
- Publier le projet sur github
- Ajouter les membres de l'équipe
- Définir une convention de nomage (variable, fonction, class)
- répartir les taches 

#### Asset (image logo ...) 
- trouver un nom pour l application
- definir le code couleur
- definir les polices d 'ecriture typographie
- selectionner un lot d image pour les salle (50)
- recadrer les images a l' aide de caneva
  
  
#### entité et fixtures
"Hamza"
- création de la BDD
- création de l entité User
- création de l entité Client (fiche client/info)
- création de l entité Room
- création de l entité Equipment
- création de l entité Option
- création de l entité Booking
- création de l entité Favorite
- création de l entité Review
- création de l entité Location
- création de l entité Quotation (prix/devis uniquement)


#### commande terminal

- `d:d:c`
- `make:migration`
- `d:m:m`


- création des fixtures
- lancer les fixtures
- `d:f:l`
 "hamza" 

#### creation Controller (uniquement pour le user)
"yasmina"
- UserController
  - profile()    // route permetant la modif des données utilisateur et affichage des favoris   GET POST
  - fiche()      //route de modification des données clients       GET POST
  - favorite()   // ajout ou retrait du favoris toggle       POST
  
- RoomController
  - index()      //toutes les salles        GET POST
  - view()       //une salle detaillées        GET POST


  
- BookingController
  - index        // les reservation de l utilisateur        GET POST
  - bookRoom     // traitement de la reservation       POST
  - edit()       // modifier de la reservation        GET POST
  - cancel()     // annulation de la reservation       POST


- ReviewController
  - new()        GET POST
  - delete()        POST
  - edit()        GET POST
  


#### formulaire fait 


"yasmina"
//controller de l admin dans easyAdmin   *"lawrence"*


#### Service

"lawrence"
- QuotationService()   // permet de creer un devis a partir d'une résarvation 
*"hamza si pas fait"* 


#### Eventlistener

- NotificationListener()    //envoi des differente notification des reservations(user, admin)
- VerificationListener()    // envoi un message flash si la fiche client n est pas complete
  
"lawrence" commence par Notification et verification et fini par Quatation                           *"yasmina si pas fait"*
c'est pas parceque une class n est pas encore codé qu on peut pas coder nos fonctionnalité qui sont liée.

  
---
 Tous ensemble  securité

#### EasyAdmin  

#### Securité

- validation de donnees (assert dans les entités)
- blocage des routes admin (dans security.ymal)


#### twig 
partage entre nous

#### finition déploiement



sallevenue

bookmysalle

salleservice




