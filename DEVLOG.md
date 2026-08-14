# 📓 Journal de Développement (DEVLOG)

**Nom & Prénom** : AÏTA GUEYE  
**Projet** : StoreManager Pro (ERP PHP/POO)

---

## 1. 🗓️ Suivi Chronologique des Phases

### 🌃 Vendredi — Phase 1 : Conception & BDD Fallback

#### 📌 Étape 1.1 — Conception UML

- **Heure de réalisation** : 19h00 – 20h30

### Ce qui a été fait

J'ai commencé par analyser le template HTML/CSS/JS afin de comprendre les fonctionnalités principales de l'application.

J'ai identifié les différents modules :

- Tableau de bord
- Vente / POS
- Gestion des dettes
- Approvisionnement
- Produits et tiers

J'ai également identifié les quatre profils utilisateurs :

- Admin Boutique
- Chargé de Vente
- Chargé de Stock
- Inventaire

J'ai ensuite réfléchi aux fonctionnalités accessibles à chaque profil.

Après cette analyse, j'ai commencé la conception UML :

- création du diagramme de cas d'utilisation dans `docs/usecase.puml` ;
- création du diagramme de classes dans `docs/classe.puml`.

J'ai également commencé la préparation du fichier `DEVLOG.md` pour suivre les différentes étapes du projet.

### ⚠️ Difficultés rencontrées

Pendant cette étape, j'ai rencontré quelques difficultés :

- comprendre la différence entre `<<include>>` et `<<extend>>` ;
- déterminer les fonctionnalités accessibles à chaque rôle ;
- comprendre la différence entre une commande et une dette ;
- déterminer comment représenter les lignes d'une commande ;
- déterminer comment représenter les lignes d'un approvisionnement ;
- comprendre comment une dette est créée lorsqu'une vente n'est pas entièrement payée ;
- réfléchir à la gestion de la limite de crédit d'un client ;
- vérifier que les diagrammes UML correspondent bien aux fonctionnalités présentes dans l'interface.

### 💡 Ce que j'ai appris

Cette première étape m'a permis de comprendre qu'il est important de bien analyser le fonctionnement de l'application avant de commencer à coder.

J'ai également mieux compris le rôle des diagrammes UML dans la conception d'une application et la différence entre les principales relations utilisées dans un diagramme de cas d'utilisation.

---

### 📌 Étape 1.2 — Schéma de la base de données

- **Heure de réalisation** : À compléter
- **Ce qui a été fait** : À compléter
- **Difficultés / Obstacles** : À compléter

---

### 📌 Étape 1.3 — Singleton Database et Fallback

- **Heure de réalisation** : À compléter
- **Ce qui a été fait** : À compléter
- **Difficultés / Obstacles** : À compléter

---

## 2. 📝 Résumé de la première étape

Pour cette première partie du projet, j'ai principalement travaillé sur la conception de l'application.

J'ai analysé l'interface existante, identifié les différents profils utilisateurs et leurs fonctionnalités, puis commencé la réalisation des diagrammes UML.

Cette étape m'a permis de mieux comprendre l'organisation générale de **StoreManager Pro** avant de commencer l'implémentation en PHP/POO.