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
LigneApprovisionnement.....

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