# 📢 Charte du Projet ERP (PHP / POO) & Consignes aux Étudiants

Bienvenue dans le projet de réalisation de l'application **StoreManager Pro** ! Ce projet est à réaliser chez vous en autonomie. 

L'objectif principal n'est pas seulement d'avoir une application qui fonctionne, mais de **démontrer votre capacité à concevoir, modéliser (UML) et coder en PHP POO From Scratch** sans dépendre aveuglément des générateurs d'IA.

---

## 🎯 Règle du Jeu : "L'IA comme Tuteur, Pas comme Remplaçant"

> 💡 **Le principe d'évaluation** :
> Vous êtes autorisés à utiliser l'IA comme un **professeur particulier** (pour vous expliquer un concept, une erreur ou une syntaxe). En revanche, **vous serez évalués individuellement en classe sur votre capacité à expliquer et modifier votre code en direct**. 
> Si une IA code à votre place et que vous ne maîtrisez pas le code rendu, vous obtiendrez la note zéro à l'évaluation en classe.

---

## 📌 Les 4 Conditions de Rendu Obligatoires

### 1. 📜 Historique Git Régulier (Obligatoire)
Le projet doit être hébergé sur un dépôt Git (GitHub ou GitLab) dès le premier jour.
- **Minimum 15 commits** étalés sur toute la durée du projet.
- **Interdiction du commit unique** : Un dépôt rendu avec un seul ou deux commits globaux à la fin sera refusé (**Note = 0/20**).
- **Messages explicites** : Chaque commit doit décrire la fonctionnalité ajoutée (ex: `feat(client): ajout du calcul de la limite de crédit`).

### 2. 📐 Dossier de Conception UML (À rendre avec le code)
Dans un dossier `/docs`, vous devez fournir :
- Le **Diagramme de Cas d'Utilisation** (Use Cases) identifiant les 4 profils (*Admin*, *Vente*, *Stock*, *Inventaire*).
- Le **Diagramme de Classes UML** modélisant vos entités (`Produit`, `Client`, `Fournisseur`, `Commande`, `Dette`, `Paiement`, `Approvisionnement`) et leurs associations.

### 3. 📝 Le Journal de Bord de Développement (`DEVLOG.md`) — Principe & Structure
Le `DEVLOG.md` (Development Log) est le **journal d'apprentissage et de suivi de votre projet**. Il s'agit d'un document rédigé **au fur et à mesure de votre avancement (pas au dernier moment)** à la racine de votre projet.

#### 💡 Quel est l'objectif du `DEVLOG.md` ?
- **Preuve de travail personnel** : Il atteste que vous avez réfléchi, testé, rencontré des bugs et compris votre propre code.
- **Support de révision pour la soutenance** : Il vous sert d'aide-mémoire le jour de l'épreuve orale en classe.

#### 📋 Structure obligatoire à respecter dans votre `DEVLOG.md` :

```markdown
# 📓 Journal de Développement (DEVLOG)
**Nom & Prénom** : [Votre Nom & Prénom]  
**Projet** : StoreManager Pro (ERP PHP/POO)  

---

## 1. Suivi Chronologique des Phases

### 🌃 [Vendredi - Phase 1] : Conception & BDD Fallback
- **Heure de réalisation** : 
- **Ce qui a été fait** : 
- **Difficultés / Obstacles** : 

### ☀️ [Samedi - Phase 2] : POO, Repositories & Ventes POS
- **Heure de réalisation** : 
- **Ce qui a été fait** : 
- **Difficultés / Obstacles** : 

### 🚀 [Dimanche - Phase 3] : Dettes, Approvisionnements & Rôles
- **Heure de réalisation** : 
- **Ce qui a été fait** : 
- **Difficultés / Obstacles** : 

---

## 2. Autopsie de 3 Méthodes Clés (Indispensable pour l'oral)

### Méthode 1 : `Database::getInstance()`
- **Fichier** : `src/Core/Database.php`
- **Rôle** : 
- **Explication ligne par ligne** : 

### Méthode 2 : `VenteService::validerVente()`
- **Fichier** : `src/Service/VenteService.php`
- **Rôle** : 
- **Explication ligne par ligne** : 

### Méthode 3 : `DetteService::enregistrerPaiement()` (ou `AuthManager::checkAccess()`)
- **Fichier** : `src/Service/...`
- **Rôle** : 
- **Explication ligne par ligne** : 
```

### 4. ⚙️ Architecture POO Clean & PDO Strict
- **Pas de Framework** (Laravel/Symfony) ni d'ORM automatique.
- Utilisation du pattern **MVC / Layered Architecture** (`Core`, `Model`, `Service`, `Controller`, `Views`).
- Toutes les requêtes SQL doivent utiliser des **requêtes préparées PDO** (Protection contre les injections SQL).
- Utilisation des **transactions SQL** (`beginTransaction` / `commit`) pour les opérations de vente et d'approvisionnement.

---



---

## 🏫 Déroulement de l'Évaluation en Classe (Jour du Rendu)

Le jour de la remise du projet, chaque étudiant passera une **épreuve individuelle de 10 minutes** décomposée comme suit :

1. 💻 **Test de Modification en Live (5 min)** : Le formateur vous demandera d'ajouter une petite fonctionnalité inédite ou d'ajuster une règle métier en direct sur votre code.
2. 🎤 **Soutenance & Questions de Code (5 min)** : Vous expliquerez le fonctionnement d'une classe ou d'une requête SQL choisie au hasard par le formateur.


