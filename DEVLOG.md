# 📓 Journal de Développement (DEVLOG)

Nom & Prénom : AÏTA GUEYE
Projet : StoreManager Pro (ERP PHP/POO)

---

## 1. 🗓️ Suivi Chronologique des Phases

### 🌃 Vendredi — Phase 1 : Analyse, Conception et Base de données

---

#### 📌 Étape 1.1 — Analyse et conception UML

Heure de réalisation : 19h00 – 20h30

🛠️ Ce qui a été fait

J'ai commencé par analyser le template HTML/CSS/JS qui nous a été fourni. Le but était de comprendre les différentes parties de l'application et les fonctionnalités qui devront être développées.

J'ai identifié les principaux modules de l'application :

- Tableau de bord ;
- Vente / POS ;
- Gestion des dettes ;
- Approvisionnement ;
- Produits et tiers.

J'ai ensuite identifié les différents profils utilisateurs :

- Admin Boutique : accès à toutes les fonctionnalités ;
- Chargé de Vente : gestion des ventes et des clients ;
- Chargé de Stock : gestion des stocks et des approvisionnements ;
- Inventaire : consultation et gestion des stocks.

Après cette analyse, j'ai commencé la conception des diagrammes UML.

J'ai créé :

- le diagramme de cas d'utilisation dans `docs/usecase.puml` ;
- le diagramme de classes dans `docs/classe.puml`.

J'ai également créé le fichier `DEVLOG.md` afin de suivre les différentes étapes de mon travail, ainsi qu'un dossier `docs/photosUML/` contenant des captures de mes diagrammes, à la demande du coach.

⚠️ Difficultés rencontrées

J'ai rencontré plusieurs difficultés pendant cette étape :

- déterminer les fonctionnalités accessibles à chaque rôle ;
- comprendre la différence entre une commande et une dette ;
- comprendre comment représenter les produits présents dans une commande ;
- comprendre comment représenter les produits présents dans un approvisionnement ;
- déterminer dans quel cas une dette est créée lorsqu'un client ne paie pas toute sa commande ;
- réfléchir à la limite de crédit d'un client ;
- vérifier que mes diagrammes correspondent bien aux fonctionnalités de l'application.

💡 Ce que j'ai appris

Cette étape m'a permis de comprendre qu'il est important de bien analyser le projet avant de commencer à coder.

---

#### 📌 Étape 1.2 — Création de la base de données

Heure de réalisation : 20h30 – 22h00

🛠️ Ce qui a été fait

Après la conception UML, j'ai commencé à préparer la base de données du projet.

J'ai créé deux fichiers SQL :

- `schema.sql` pour PostgreSQL ;
- `schema_sqlite.sql` pour SQLite.

PostgreSQL sera utilisé comme base principale et SQLite servira de solution de secours.

J'ai créé les différentes tables nécessaires au fonctionnement de l'application et ajouté des relations entre les tables grâce aux clés étrangères.

J'ai également ajouté des contraintes `CHECK` afin d'éviter certaines données incorrectes, par exemple pour les statuts et certains champs importants.

J'ai ajouté des données de test afin de pouvoir vérifier le fonctionnement de mes futures requêtes PHP.

J'ai également :

- installé l'outil `sqlite3` ;
- exécuté le script SQLite ;
- créé la base `storemanager.db` ;
- vérifié les tables et les données créées.

⚠️ Difficultés rencontrées

J'ai rencontré quelques problèmes pendant la création des bases de données :

- PostgreSQL et SQLite n'utilisent pas toujours la même syntaxe ;
- j'ai dû comprendre la différence entre `SERIAL` avec PostgreSQL et `AUTOINCREMENT` avec SQLite ;
- j'ai découvert qu'avec SQLite les clés étrangères doivent être activées avec :

```sql
PRAGMA foreign_keys = ON;
```

💡 Ce que j'ai appris

Cette étape m'a permis de comprendre les différences pratiques entre deux moteurs de base de données, et l'importance des contraintes (clés étrangères, `CHECK`) pour garantir la cohérence des données dès la conception.

---

#### 📌 Étape 1.3 — Singleton Database & Fallback automatique

Heure de réalisation : 22h00 – 23h00

🛠️ Ce qui a été fait

Une fois les deux bases de données prêtes, j'ai créé la classe `Database` dans `src/Core/database.php`, en suivant le patron de conception Singleton : une seule instance de connexion est créée pour toute l'application, et tout le monde y accède via `Database::getInstance()`.

Cette classe essaie d'abord de se connecter à PostgreSQL. Si la connexion échoue (serveur non lancé, base absente, etc.), elle rattrape l'erreur avec un `try/catch` et bascule automatiquement sur la base SQLite de secours (`storemanager.db`), sans faire planter l'application.

J'ai testé les deux cas manuellement :  

- avec PostgreSQL actif → la connexion utilise bien `pgsql` ;
- avec PostgreSQL arrêté (`sudo service postgresql stop`) → la connexion bascule bien sur `sqlite`.

⚠️ Difficultés rencontrées

Cette étape a été la plus longue à cause de plusieurs petits problèmes liés à l'environnement, plutôt qu'au code lui-même :

- ma base PostgreSQL `approvisionnement` contenait déjà d'anciennes tables d'un précédent exercice (gestion scolaire), ce qui m'a fait croire un moment que mes données avaient été effacées, alors qu'en réalité mon script n'avait tout simplement jamais fini de s'exécuter avec succès dessus ;
- lors de la configuration de la connexion SQLite dans l'extension VS Code, j'ai laissé un backslash (`\`) dans le chemin au lieu d'un slash (`/`), ce qui a créé un fichier avec un nom incorrect au lieu de le placer dans le bon dossier ;
- PHP affichait une erreur `could not find driver` lors du test du fallback SQLite : le module `php-sqlite3` n'était pas installé sur ma machine, alors que l'outil en ligne de commande `sqlite3` l'était déjà. J'ai compris que ce sont deux paquets totalement différents : l'un permet de manipuler SQLite depuis le terminal, l'autre permet à PHP de le faire ;
- j'ai dû vérifier que `PRAGMA foreign_keys = ON;` était bien exécuté à chaque connexion SQLite dans le code PHP, en plus du script SQL, car cette option ne se mémorise jamais dans le fichier `.db` ;
- petite erreur de casse lors du commit Git : j'avais tapé `Database.php` avec un D majuscule alors que mon fichier s'appelle `database.php`, ce qui a été refusé par Git (sensible à la casse sous Linux).

💡 Ce que j'ai appris

Cette étape m'a fait comprendre l'intérêt réel du patron Singleton : centraliser la connexion en un seul point évite d'ouvrir plusieurs connexions inutiles. J'ai aussi compris que le fallback n'est utile que si on le teste vraiment en conditions réelles (en coupant le service PostgreSQL), pas seulement en le lisant dans le code. Enfin, j'ai réalisé l'importance de vérifier les chemins de fichiers précisément (majuscules, séparateurs, emplacement réel) avant de chercher une explication plus compliquée à une erreur.

📌 Étape 2.1 — Création des entités POO

Heure de réalisation : 09h00 – 11h00

🛠️ Ce qui a été fait :

-Création du dossier src/Model/Entity/ pour regrouper les différentes entités du projet.
-Création des premières classes PHP correspondant aux éléments principaux de l'application :

Utilisateur
Produit
Client
Fournisseur
Commande
LigneCommande
Dette
Paiement
Approvisionnement
LigneApprovisionnement

-Pour chaque entité, j'ai commencé par définir ses attributs à partir des informations identifiées dans le diagramme de classes et la base de données.
-Les attributs ont été déclarés avec une visibilité adaptée afin de respecter le principe d'encapsulation.
-J'ai ajouté les constructeurs nécessaires pour initialiser les objets.
-J'ai commencé à utiliser les méthodes permettant de manipuler les données des objets.
-Cette étape permet de préparer la structure POO qui sera utilisée ensuite par les repositories et les services.


⚠️ Difficultés rencontrées :

-Comprendre comment transformer une table de la base de données en une classe PHP.
-Comprendre la différence entre un attribut et une méthode.
-Comprendre pourquoi les attributs ne doivent pas être directement accessibles depuis l'extérieur de la classe.
-Déterminer quels attributs appartiennent à chaque entité.
-Comprendre les relations entre les différentes entités, notamment entre Commande et LigneCommande, ainsi qu'entre Approvisionnement et LigneApprovisionnement.
-Comprendre comment créer un objet à partir d'une classe avec new.

💡 Ce que j'ai appris :

-Cette étape m'a permis de mieux comprendre les bases de la programmation orientée objet en PHP. J'ai compris qu'une classe représente un élément de mon application et qu'un objet est une instance de cette classe. J'ai également commencé à comprendre l'intérêt de l'encapsulation, des constructeurs et des méthodes pour organiser correctement le code.

📦 Résultat :

-Les principales entités du projet sont maintenant créées dans src/Model/Entity/. Cette base permettra de passer à l'étape suivante : la création des Repositories et l'utilisation des requêtes préparées avec PDO.

### 📌 Étape 2.2 — Repositories & SQL sécurisé

**Heure de réalisation :** 11h00 – 13h00

#### 🛠️ Ce qui a été fait

Après la création des entités POO, j'ai créé la partie **Repository**. Le rôle des repositories est de faire le lien entre les objets PHP et la base de données.

J'ai créé le dossier :

`src/Repository/`

avec les trois repositories suivants :

- `ClientRepository.php`
- `FournisseurRepository.php`
- `ProduitRepository.php`

Chaque repository utilise la connexion à la base de données grâce à `Database::getInstance()->getConnexion()`.

J'ai également utilisé **PDO** pour exécuter les requêtes SQL.

#### 👤 ClientRepository

Dans `ClientRepository.php`, j'ai créé plusieurs méthodes permettant de gérer les clients :

- `findAll()` : récupère tous les clients.
- `findById()` : recherche un client grâce à son identifiant.
- `rechercherParNom()` : recherche un client par son nom ou son numéro de téléphone.
- `creer()` : ajoute un nouveau client dans la base de données.
- `mettreAJour()` : modifie les informations d'un client.
- `delete()` : supprime un client.

J'ai aussi créé la méthode `hydrater()`. Elle permet de transformer une ligne récupérée depuis la base de données en objet `Client`.

#### 🏢 FournisseurRepository

Dans `FournisseurRepository.php`, j'ai créé les méthodes nécessaires pour gérer les fournisseurs :

- `findAll()` : récupère la liste des fournisseurs.
- `findById()` : recherche un fournisseur par son identifiant.
- `creer()` : ajoute un fournisseur.
- `mettreAJour()` : modifie les informations d'un fournisseur.
- `delete()` : supprime un fournisseur.

Comme pour les clients, la méthode `hydrater()` permet de transformer les données de la base en objet `Fournisseur`.

#### 📦 ProduitRepository

Dans `ProduitRepository.php`, j'ai créé les méthodes permettant de gérer les produits et leur stock :

- `findAll()` : récupère tous les produits.
- `findById()` : recherche un produit par son identifiant.
- `findAllAvecStock()` : récupère les produits avec leur quantité disponible en stock.
- `getQuantiteDisponible()` : récupère la quantité disponible d'un produit.
- `decrementerStock()` : diminue la quantité disponible lors d'une vente.
- `incrementerStock()` : augmente la quantité disponible lors d'un approvisionnement.
- `creer()` : ajoute un produit et crée également son stock initial à zéro.
- `mettreAJourPrix()` : permet de modifier le prix d'un produit.
- `delete()` : supprime un produit.

La méthode `hydrater()` permet ici de transformer une ligne de la base de données en objet `Produit`.

#### 🔐 Sécurisation des requêtes SQL

Pour les requêtes qui utilisent des données venant de l'utilisateur, j'ai utilisé :

- `prepare()`
- `execute()`

avec des paramètres nommés comme `:id`, `:nom`, `:prix`, etc.

Par exemple, pour rechercher un client par son identifiant, j'utilise une requête préparée au lieu de placer directement la valeur dans la requête SQL.

Cela permet de mieux sécuriser les requêtes et de limiter les risques d'injection SQL.

Pour les requêtes simples qui ne nécessitent pas de données provenant de l'utilisateur, j'ai utilisé `query()`.

#### ⚠️ Difficultés rencontrées & solutions

Une difficulté rencontrée concernait l'organisation des dossiers.

Au départ, le dossier `Model` se trouvait au mauvais endroit. J'ai donc déplacé `Model` pour obtenir une organisation plus claire :

`src/Model/Entity/`

et

`src/Repository/`

J'ai également dû comprendre la différence entre une **Entity** et un **Repository**.

Une Entity représente les données et le comportement d'un objet, par exemple un `Client` ou un `Produit`.

Le Repository, lui, s'occupe de récupérer, créer, modifier ou supprimer ces données dans la base de données.

Une autre difficulté était de comprendre la méthode `hydrater()`. J'ai compris qu'elle sert à prendre les données obtenues avec PDO et à créer un véritable objet PHP à partir de ces données.

#### 💡 Ce que j'ai appris

Cette étape m'a permis de mieux comprendre la séparation des responsabilités dans une application POO.

J'ai compris que je ne dois pas mettre toutes les requêtes SQL directement dans les contrôleurs.

Les repositories centralisent les accès à la base de données.

J'ai également appris à utiliser les requêtes préparées PDO avec `prepare()` et `execute()`, ainsi qu'à transformer les résultats SQL en objets PHP grâce à l'hydratation.

#### 📦 Résultat

À la fin de cette étape, j'ai obtenu :

```text
src/
├── Model/
│   └── Entity/
│       ├── Client.php
│       ├── Fournisseur.php
│       └── Produit.php
│
└── Repository/
    ├── ClientRepository.php
    ├── FournisseurRepository.php
    └── ProduitRepository.php

    📌 Étape 2.3 — Service Métier Vente POS & Transaction SQL
-Horaire de réalisation : 14h00 – 17h00
-Livrable principal : src/Service/VenteService.php
Fichiers concernés :

src/Service/VenteService.php
src/Core/Database.php
src/Model/Entity/Client.php
src/Model/Entity/Produit.php
src/Repository/ClientRepository.php
src/Repository/ProduitRepository.php

-Objectif de l’étape :

Mettre en place la logique métier de validation d’une vente au niveau du point de vente. Cette logique doit permettre de vérifier le panier, calculer le montant total, contrôler le stock, gérer les paiements partiels, créer une dette si nécessaire et vérifier la limite de crédit du client, tout en garantissant la cohérence des données grâce à une transaction SQL.

📝 Résumé de l’étape 2.3
-**Horaire de réalisation :** (14h00 - 17h00) 
Durant cette étape, j’ai développé le service VenteService, qui représente une partie centrale du module de vente.
Ce service permet de valider une vente en prenant en compte :

-le panier ;
-la disponibilité des produits ;
-le calcul du montant total ;
-le paiement total ou partiel ;
-la décrémentation du stock ;
-la création d’un paiement ;
-la création d’une dette si nécessaire ;
-le contrôle de la limite de crédit du client.
-J’ai utilisé une transaction SQL avec PDO afin de garantir la cohérence des données. Si une erreur survient pendant le traitement, toutes les modifications sont annulées grâce à rollBack().
-J’ai également mis en place deux exceptions personnalisées :

---StockInsuffisantException
---LimiteCreditDepasseeException

Elles permettent de gérer plus clairement les erreurs métier liées au stock et au crédit client.
#### 💡 Ce que j'ai appris

Cette deuxième phase m’a permis de mettre en place une partie essentielle du module de vente : le service métier VenteService.
J’ai pu implémenter la logique de validation d’une vente en prenant en compte le panier, le calcul du montant total, la vérification du stock, la décrémentation des produits, la gestion du paiement partiel et la création d’une dette lorsque le client ne règle pas la totalité.
J’ai également travaillé sur la sécurisation des opérations en base de données grâce aux transactions PDO. Ainsi, si une erreur survient pendant la vente, les modifications sont annulées avec rollBack(), ce qui évite les incohérences dans la base de données.
La gestion de la limite de crédit m’a permis de mieux comprendre l’importance des règles métier dans une application de gestion commerciale. Il ne suffit pas d’enregistrer une vente : il faut aussi vérifier que le client respecte les conditions définies par la boutique.
Cette étape était donc importante, car elle constitue une base solide pour la suite du projet, notamment l’intégration du service dans le contrôleur, l’affichage des résultats dans l’interface et la gestion plus complète des erreurs utilisateur.

📎 Complément — Limite de crédit par client

En vérifiant mon fichier HTML de maquette original, j'ai découvert que la limite de crédit n'est pas une valeur unique pour tous les clients, mais un champ propre à chaque client (`limite_credit`), affiché dynamiquement dans le formulaire de vente selon le client sélectionné. J'ai donc ajouté une colonne `limite_credit` à ma table `client` (PostgreSQL et SQLite), ainsi qu'un champ, un paramètre de constructeur et un getter correspondants dans mon entité `Client`. `VenteService` compare la dette active du client (somme des `montant_restant` de ses dettes non soldées) additionnée du reste à payer de la nouvelle vente à cette limite avant d'accepter une vente à crédit.

### 📌 Étape 2.4 — Controller POS & Vue Caisse

Heure de réalisation : 17h00 – 20h00

🛠️ Ce qui a été fait

J'ai créé `POSController.php`, qui prépare les données nécessaires à l'affichage (liste des clients, produits avec leur stock, ventes récentes avec le détail de leurs lignes) puis transmet l'affichage à la vue `views/pos/index.php`. Le Controller traite aussi la soumission du formulaire de vente en appelant `VenteService::validerVente()`, et transforme les exceptions métier (stock insuffisant, limite de crédit dépassée) en messages affichés à l'utilisateur après redirection.

Pour la vue, j'ai repris fidèlement le CSS et le JavaScript de ma maquette HTML originale : pavé numérique tactile pour la saisie, gestion du panier en JavaScript côté navigateur, affichage dynamique de la limite de crédit selon le client sélectionné, et tiroir dépliable affichant le détail de chaque vente dans le registre.

⚠️ Difficultés rencontrées

J'ai confondu par erreur le contenu du Controller et celui de la vue en les collant dans le mauvais fichier, ce qui provoquait une erreur de classe déclarée deux fois. J'ai aussi eu un affichage qui s'interrompait au milieu du formulaire à cause d'un appel à une méthode `getSeuilAlerte()` qui n'existe pas sur mon entité `Produit` ; je l'ai remplacée par un seuil fixe. J'ai également utilisé `dirname(__DIR__, 2)` plutôt qu'une concaténation de chemins avec `/../../` pour que le Controller retrouve correctement le fichier de la vue depuis `src/Controller/`.

💡 Ce que j'ai appris

Le modèle MVC prend tout son sens une fois qu'on le voit fonctionner concrètement : le Controller prépare des données pures (tableaux, objets), et c'est seulement au moment du `require` de la vue que ces données sont transformées en HTML — les deux responsabilités restent séparées.

📦 Résultat

Le module Vente / POS est fonctionnel de bout en bout : sélection d'un client avec affichage de sa limite de crédit, ajout d'articles au panier avec vérification du stock, calcul du total en temps réel, validation de la vente avec contrôle de crédit et transaction SQL, et registre des ventes avec détail consultable.


### 🚀 Dimanche — Phase 3 : Dettes, Approvisionnements, Rôles & Clôture

---

#### 📌 Étape 3.1 — Gestion des Dettes & Remboursements

Heure de réalisation : 09h00 – 11h30

🛠️ Ce qui a été fait

J'ai créé `DetteRepository.php`, qui centralise l'accès SQL aux tables `dette` et `remboursement` : récupération des dettes actives avec les informations du client associé, historique des remboursements déjà effectués, détail des articles de la vente à crédit d'origine, et statistiques globales (créances actives, nombre de clients débiteurs, total recouvré).

J'ai créé `DebtService.php`, qui contient la vraie logique métier du remboursement : vérifier que le montant versé est positif et ne dépasse pas le solde restant dû, enregistrer le versement, diminuer le solde de la dette, et faire automatiquement passer son statut à `SOLDEE` dès que le solde atteint zéro — le tout sous une transaction PDO (`beginTransaction`, `commit`, `rollBack`), comme pour `VenteService`.

Comme la charte ne demandait que 3 fichiers pour cette étape (pas de Controller listé, contrairement au module Vente), j'ai rendu `views/dettes/index.php` autonome : il fait lui-même ses `require`, récupère ses données via `DetteRepository`, et traite directement la soumission du formulaire de remboursement en appelant `DebtService`, avant d'afficher le HTML.

⚠️ Difficultés rencontrées

- en comparant mon entité `Remboursement` à ma vraie table `remboursement`, j'ai découvert qu'il manquait la colonne `mode_paiement`, alors que ma maquette HTML montrait bien un sélecteur "Canal de Paiement" (Orange Money, Wave...) dans le formulaire de remboursement. J'ai dû l'ajouter avec `ALTER TABLE` sur mes deux bases ;
- j'ai eu une confusion de fichiers , ce qui m'a fait perdre du temps à déboguer une erreur qui n'existait pas réellement dans le code, avant de repartir proprement de zéro sur ces 3 fichiers précis ;
- j'ai remarqué que mes deux vues (`views/pos/index.php` et `views/dettes/index.php`) portent le même nom de fichier — ce n'est pas un problème pour PHP puisque le chemin complet les différencie, mais ça demande de la vigilance dans VS Code pour ne pas confondre les onglets ouverts.

💡 Ce que j'ai appris

Cette étape m'a fait comprendre que je ne dois jamais assumer que mon entité et ma table correspondent parfaitement colonne par colonne : c'est le rôle du Repository de faire ce pont, même quand les noms diffèrent. J'ai aussi confirmé l'intérêt de vérifier ma vraie maquette HTML avant de coder une fonctionnalité, plutôt que de deviner ce qu'elle devrait contenir.

📦 Résultat

Le module Gestion des Dettes est fonctionnel : registre des dettes actives avec recherche par client, tiroirs dépliables pour consulter les articles de la vente et l'historique des paiements, et formulaire de remboursement (total ou partiel, avec raccourcis "Tout solder" / "50%") qui met à jour le solde et le statut de la dette en base.

#### 📌 Étape 3.2 — Approvisionnements & Réception BL

Heure de réalisation : 11h30 – 13h30

🛠️ Ce qui a été fait

J'ai créé `SupplyService.php`, qui contient toute la logique de l'approvisionnement : lister les bons de livraison en attente et déjà réceptionnés (avec le nom du fournisseur et le détail des lignes), et surtout traiter la réception d'un bon de livraison. La réception incrémente le stock de chaque produit concerné via `ProduitRepository::incrementerStock()`, puis fait passer le bon de livraison au statut `RECEPTIONNE` — le tout sous une transaction PDO, pour garantir que soit tout est appliqué, soit rien ne l'est.

La consigne ne demandant que 2 livrables pour cette étape (`SupplyService.php` et le volet de réception), j'ai regroupé toutes les requêtes SQL directement dans le service plutôt que de créer un Repository séparé — contrairement aux étapes précédentes (Client, Fournisseur, Produit, Dette) où la séparation Repository/Service avait été respectée.

J'ai créé `views/supplies/index.php` comme volet de réception : un registre listant tous les bons de livraison, avec un tiroir dépliable "Lignes" pour voir le détail des produits, et pour les bons encore en attente, un tiroir "Réceptionner" avec un bouton de validation qui déclenche la mise à jour du stock.

⚠️ Difficultés rencontrées

J'ai réalisé que ma table `ligne_approvisionnement` ne contient qu'une colonne `quantite` (la quantité commandée), sans colonne séparée pour la quantité réellement livrée — alors que ma maquette HTML montrait un champ modifiable "Qté Reçue". J'ai choisi de simplifier : la quantité reçue est toujours considérée égale à la quantité commandée, sans champ modifiable, plutôt que d'ajouter une nouvelle colonne à ce stade avancé du projet.

J'ai aussi eu une confusion de fichiers en testant ma vue : le contenu réellement exécuté ne correspondait pas à celui que j'avais préparé (variable `$approvisionnements` introuvable, aucun style appliqué), ce qui montrait qu'un ancien brouillon traînait encore au même endroit. J'ai dû supprimer le fichier existant avant de remettre le bon en place, pour être certaine de ne pas mélanger deux versions différentes.

💡 Ce que j'ai appris

Cette étape m'a confirmé l'intérêt des transactions SQL dès qu'une action métier touche plusieurs tables liées (ici, la table `approvisionnement` et potentiellement plusieurs lignes de `stock` en même temps). J'ai aussi appris à toujours vérifier le contenu réel d'un fichier avant de chercher une erreur compliquée — un simple `wc -l` ou `grep` permet de confirmer rapidement qu'on travaille bien sur la bonne version d'un fichier.

📦 Résultat

Le module Approvisionnement est fonctionnel : registre des bons de livraison avec leur statut, détail des produits par bon, et réception en un clic qui met à jour le stock automatiquement et fait passer le bon au statut RECEPTIONNE.