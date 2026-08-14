# ⚡ Planning Hackathon W-E (Vendredi, Samedi & Dimanche) : Commits & Structure DEVLOG.md

Ce document détaille **heure par heure** la feuille de route du week-end. Après chaque livrable, l'étudiant doit effectuer le **commit Git exact spécifié** et consigner les entrées dans son fichier **`DEVLOG.md`** selon la structure imposée.

---

## 🗓️ FEUILLE DE ROUTE : LIVRABLES & COMMITS GIT

---

### 🌃 PHASE 1 : VENDREDI SOIR (19h00 - 23h00) — Conception & BDD Fallback

#### 📌 Step 1.1 (19h00 - 20h30) : Conception UML
- **Livrable** : Diagrammes Use Case & Classes dans `/docs/`.
- **Commit Git à exécuter** :
  ```bash
  git commit -m "docs(uml): ajout des diagrammes de cas d'utilisation et de classes POO"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 1 (Conception UML)*.

#### 📌 Step 1.2 (20h30 - 22h00) : Schéma SQL PostgreSQL / SQLite
- **Livrable** : Scripts `schema.sql` (PostgreSQL) et `schema_sqlite.sql`.
- **Commit Git à exécuter** :
  ```bash
  git commit -m "feat(database): creation des scripts d'initialisation SQL avec contraintes FK et CHECK"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 1 (Schéma BDD)*.

#### 📌 Step 1.3 (22h00 - 23h00) : Singleton Database & Fallback Automatique
- **Livrable** : `src/Core/Database.php` (Connexion PostgreSQL avec fallback `try/catch` sur SQLite `erp.db`).
- **Commit Git à exécuter** :
  ```bash
  git commit -m "feat(core): implementation de Database Singleton avec fallback automatique PostgreSQL vers SQLite"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 1 (Database Fallback)*.

---

### ☀️ PHASE 2 : SAMEDI (09h00 - 20h00) — Cœur POO & Ventes POS

#### 📌 Step 2.1 (09h00 - 11h00) : Entités POO Pure
- **Livrable** : Classes `src/Model/Entity/` (`Produit.php`, `Client.php`, `Dette.php`, `Commande.php`, etc.).
- **Commit Git à exécuter** :
  ```bash
  git commit -m "feat(entity): creation des entites POO avec encapsulation et methodes metier"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 2 (Entités POO)*.

#### 📌 Step 2.2 (11h00 - 13h00) : Repositories & SQL Sécurisé
- **Livrable** : `ProduitRepository.php`, `ClientRepository.php`, `FournisseurRepository.php`.
- **Commit Git à exécuter** :
  ```bash
  git commit -m "feat(repository): mise en place des classes Repository avec requetes preparees PDO"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 2 (Repositories)*.

#### 📌 Step 2.3 (14h00 - 17h00) : Service Métier Vente POS & Transaction SQL
- **Livrable** : `src/Service/VenteService.php` (Panier, décrémentation stock, limite de crédit sous transaction PDO).
- **Commit Git à exécuter** :
  ```bash
  git commit -m "feat(service): implementation de VenteService avec transaction SQL et controle de limite de credit"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 2 (VenteService)*.

#### 📌 Step 2.4 (17h00 - 20h00) : Controller POS & Vue Caisse
- **Livrable** : `POSController.php` et vue `views/pos/index.php`.
- **Commit Git à exécuter** :
  ```bash
  git commit -m "feat(pos): finalisation du controleur et de l'interface de caisse tactile"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 2 (Interface POS)*.

---

### 🚀 PHASE 3 : DIMANCHE (09h00 - 18h00) — Dettes, Approvisionnements, Rôles & Clôture

#### 📌 Step 3.1 (09h00 - 11h30) : Gestion des Dettes & Remboursements
- **Livrable** : `DetteRepository.php`, `DebtService.php`, et vue `views/dettes/index.php`.
- **Commit Git à exécuter** :
  ```bash
  git commit -m "feat(dettes): ajout du service de remboursement partiel et mise a jour des statuts SOLDEE"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 3 (Gestion Dettes)*.

#### 📌 Step 3.2 (11h30 - 13h30) : Approvisionnements & Réception BL
- **Livrable** : `SupplyService.php` et volet de réception des BL.
- **Commit Git à exécuter** :
  ```bash
  git commit -m "feat(supplies): ajout de la reception de bons de livraison et incrementation automatique du stock"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 3 (Approvisionnements)*.

#### 📌 Step 3.3 (14h30 - 16h30) : AuthManager & Contrôle des Rôles
- **Livrable** : `src/Service/AuthManager.php`, `AuthController.php`, et filtrage des accès (*Admin*, *Vente*, *Stock*, *Inventaire*).
- **Commit Git à exécuter** :
  ```bash
  git commit -m "feat(auth): implementation de l'authentification multi-profils et restriction des acces par role"
  ```
- **Consigne `DEVLOG.md`** : Remplir la section *Phase 3 (Authentification)*.

#### 📌 Step 3.4 (16h30 - 18h00) : Rédaction de l'Autopsie des 3 Méthodes Clés & Push Final
- **Livrable** : Fichier `DEVLOG.md` complété avec la section *2. Autopsie de 3 Méthodes Clés* + Push final GitHub.
- **Commit Git à exécuter** :
  ```bash
  git commit -m "docs(devlog): finalisation du journal de bord DEVLOG.md et autopsie des 3 methodes cles"
  ```
- **Consigne `DEVLOG.md`** : Remplir l'explication détaillée des 3 méthodes choisies pour l'épreuve orale du Lundi.
