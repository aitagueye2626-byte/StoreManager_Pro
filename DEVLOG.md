 📓 Journal de Développement (DEVLOG)

Nom & Prénom : AÏTA GUEYE  
Projet : StoreManager Pro (ERP PHP/POO)  
Période concernée : Phase 1 — Vendredi soir

---

 1. 🗓️ Suivi Chronologique des Phases

 🌃 Vendredi — Phase 1 : Analyse, Conception et Base de données

---

📌 Étape 1.1 — Analyse et conception UML

**Heure de réalisation : 19h00 – 20h30

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

J'ai également créé le fichier `DEVLOG.md` afin de suivre les différentes étapes de mon travail.

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

 📌 Étape 1.2 — Création de la base de données

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

PRAGMA foreign_keys = ON;