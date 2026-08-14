
PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS ligne_inventaire;
DROP TABLE IF EXISTS inventaire;
DROP TABLE IF EXISTS ligne_approvisionnement;
DROP TABLE IF EXISTS approvisionnement;
DROP TABLE IF EXISTS remboursement;
DROP TABLE IF EXISTS dette;
DROP TABLE IF EXISTS paiement;
DROP TABLE IF EXISTS ligne_vente;
DROP TABLE IF EXISTS vente;
DROP TABLE IF EXISTS stock;
DROP TABLE IF EXISTS produit;
DROP TABLE IF EXISTS fournisseur;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS utilisateur;


CREATE TABLE utilisateur (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    nom            TEXT NOT NULL,
    prenom         TEXT NOT NULL,
    email          TEXT NOT NULL UNIQUE,
    mot_de_passe   TEXT NOT NULL,
    role           TEXT NOT NULL CHECK (role IN ('admin', 'vente', 'stock', 'inventaire'))
);

CREATE TABLE client (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    nom         TEXT NOT NULL,
    prenom      TEXT,
    telephone   TEXT NOT NULL,
    email       TEXT,
    adresse     TEXT
);

CREATE TABLE fournisseur (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    nom         TEXT NOT NULL,
    telephone   TEXT,
    email       TEXT,
    adresse     TEXT
);

CREATE TABLE produit (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    nom            TEXT NOT NULL,
    description    TEXT,
    categorie      TEXT,
    prix           NUMERIC NOT NULL CHECK (prix >= 0),
    seuil_alerte   INTEGER NOT NULL DEFAULT 5
);
CREATE TABLE stock (
    id                   INTEGER PRIMARY KEY AUTOINCREMENT,
    produit_id           INTEGER NOT NULL UNIQUE REFERENCES produit(id) ON DELETE CASCADE,
    quantite_disponible  INTEGER NOT NULL DEFAULT 0 CHECK (quantite_disponible >= 0),
    date_mise_a_jour     TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE vente (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id       INTEGER NOT NULL REFERENCES client(id),
    utilisateur_id  INTEGER NOT NULL REFERENCES utilisateur(id),
    date            TEXT NOT NULL DEFAULT (datetime('now')),
    montant_total   NUMERIC NOT NULL DEFAULT 0,
    statut          TEXT NOT NULL DEFAULT 'VALIDEE' CHECK (statut IN ('VALIDEE', 'ANNULEE'))
);

CREATE TABLE ligne_vente (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    vente_id        INTEGER NOT NULL REFERENCES vente(id) ON DELETE CASCADE,
    produit_id      INTEGER NOT NULL REFERENCES produit(id),
    quantite        INTEGER NOT NULL CHECK (quantite > 0),
    prix_unitaire   NUMERIC NOT NULL,
    sous_total      NUMERIC GENERATED ALWAYS AS (quantite * prix_unitaire) STORED
);

CREATE TABLE paiement (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    vente_id        INTEGER NOT NULL REFERENCES vente(id) ON DELETE CASCADE,
    date            TEXT NOT NULL DEFAULT (datetime('now')),
    montant         NUMERIC NOT NULL CHECK (montant > 0),
    mode_paiement   TEXT NOT NULL CHECK (mode_paiement IN ('Especes', 'Orange Money', 'Wave', 'Virement')),
    statut          TEXT NOT NULL DEFAULT 'VALIDE' CHECK (statut IN ('VALIDE', 'ANNULE'))
);

CREATE TABLE dette (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    vente_id         INTEGER NOT NULL UNIQUE REFERENCES vente(id),
    client_id        INTEGER NOT NULL REFERENCES client(id),
    montant          NUMERIC NOT NULL,
    montant_restant  NUMERIC NOT NULL,
    date             TEXT NOT NULL DEFAULT (datetime('now')),
    statut           TEXT NOT NULL DEFAULT 'NON SOLDEE' CHECK (statut IN ('NON SOLDEE', 'SOLDEE'))
);

CREATE TABLE remboursement (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    dette_id    INTEGER NOT NULL REFERENCES dette(id) ON DELETE CASCADE,
    montant     NUMERIC NOT NULL CHECK (montant > 0),
    date        TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE approvisionnement (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    fournisseur_id  INTEGER NOT NULL REFERENCES fournisseur(id),
    utilisateur_id  INTEGER NOT NULL REFERENCES utilisateur(id),
    date            TEXT NOT NULL DEFAULT (datetime('now')),
    montant_total   NUMERIC NOT NULL DEFAULT 0,
    statut          TEXT NOT NULL DEFAULT 'EN ATTENTE' CHECK (statut IN ('EN ATTENTE', 'RECEPTIONNE'))
);

CREATE TABLE ligne_approvisionnement (
    id                     INTEGER PRIMARY KEY AUTOINCREMENT,
    approvisionnement_id   INTEGER NOT NULL REFERENCES approvisionnement(id) ON DELETE CASCADE,
    produit_id             INTEGER NOT NULL REFERENCES produit(id),
    quantite               INTEGER NOT NULL CHECK (quantite > 0),
    prix_unitaire          NUMERIC NOT NULL,
    sous_total             NUMERIC GENERATED ALWAYS AS (quantite * prix_unitaire) STORED
);

CREATE TABLE inventaire (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    utilisateur_id  INTEGER NOT NULL REFERENCES utilisateur(id),
    date            TEXT NOT NULL DEFAULT (datetime('now')),
    statut          TEXT NOT NULL DEFAULT 'EN COURS' CHECK (statut IN ('EN COURS', 'CLOTURE'))
);

CREATE TABLE ligne_inventaire (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    inventaire_id       INTEGER NOT NULL REFERENCES inventaire(id) ON DELETE CASCADE,
    produit_id          INTEGER NOT NULL REFERENCES produit(id),
    quantite_theorique  INTEGER NOT NULL,
    quantite_reelle     INTEGER NOT NULL,
    ecart               INTEGER GENERATED ALWAYS AS (quantite_reelle - quantite_theorique) STORED
);


CREATE INDEX idx_vente_client ON vente(client_id);
CREATE INDEX idx_ligne_vente_vente ON ligne_vente(vente_id);
CREATE INDEX idx_dette_client ON dette(client_id);
CREATE INDEX idx_remboursement_dette ON remboursement(dette_id);
CREATE INDEX idx_ligne_appro_appro ON ligne_approvisionnement(approvisionnement_id);
CREATE INDEX idx_ligne_inv_inventaire ON ligne_inventaire(inventaire_id);



INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Gueye', 'Aita', 'aita.admin@storemanager.sn', 'hash_admin_123', 'admin'),
('Ba', 'Ibrahima', 'ibrahima.vente@storemanager.sn', 'hash_vente_123', 'vente'),
('Diop', 'Khady', 'khady.stock@storemanager.sn', 'hash_stock_123', 'stock'),
('Sow', 'Modou', 'modou.inv@storemanager.sn', 'hash_inv_123', 'inventaire');

INSERT INTO client (nom, prenom, telephone, email, adresse) VALUES
('Diallo', 'Maimouna', '701122334', NULL, 'Touba'),
('Sarr', 'Moussa', '769876543', NULL, 'Touba'),
('Diouf', 'Fama', '781234567', NULL, 'Touba'),
('Ndiaye', 'Abdou', '776543210', NULL, 'Touba'),
('Awa', 'Cisse', '783332211', NULL, 'Touba');

INSERT INTO fournisseur (nom, telephone, email, adresse) VALUES
('Grossiste Sénégal SARL', '338001122', 'contact@grossiste-sn.com', 'Dakar'),
('Import Riz Plus', '338223344', 'contact@importrizplus.com', 'Diourbel');

INSERT INTO produit (nom, description, categorie, prix, seuil_alerte) VALUES
('Bidon d''huile 5L', 'Huile végétale bidon 5 litres', 'Alimentation', 8000, 5),
('Carton de lait', 'Carton de lait en poudre', 'Alimentation', 15000, 10),
('Carton de savon', 'Carton de savon de ménage', 'Hygiène', 12000, 5),
('Huile de palme 1L', 'Huile de palme rouge 1 litre', 'Alimentation', 2000, 5),
('Paquet de sucre 1kg', 'Sucre en poudre 1kg', 'Alimentation', 1500, 20),
('Sac de riz 50kg', 'Riz brisé sac de 50kg', 'Alimentation', 25000, 15);

INSERT INTO stock (produit_id, quantite_disponible) VALUES
((SELECT id FROM produit WHERE nom = 'Bidon d''huile 5L'), 5),
((SELECT id FROM produit WHERE nom = 'Carton de lait'), 40),
((SELECT id FROM produit WHERE nom = 'Carton de savon'), 3),
((SELECT id FROM produit WHERE nom = 'Huile de palme 1L'), 0),
((SELECT id FROM produit WHERE nom = 'Paquet de sucre 1kg'), 200),
((SELECT id FROM produit WHERE nom = 'Sac de riz 50kg'), 100);

INSERT INTO vente (client_id, utilisateur_id, montant_total, statut) VALUES
((SELECT id FROM client WHERE nom = 'Ndiaye'), (SELECT id FROM utilisateur WHERE role = 'vente'), 58000, 'VALIDEE');

INSERT INTO vente (client_id, utilisateur_id, montant_total, statut) VALUES
((SELECT id FROM client WHERE nom = 'Diouf'), (SELECT id FROM utilisateur WHERE role = 'vente'), 44000, 'VALIDEE');

INSERT INTO vente (client_id, utilisateur_id, montant_total, statut) VALUES
((SELECT id FROM client WHERE nom = 'Sarr'), (SELECT id FROM utilisateur WHERE role = 'vente'), 74000, 'VALIDEE');

INSERT INTO vente (client_id, utilisateur_id, montant_total, statut) VALUES
((SELECT id FROM client WHERE nom = 'Diallo'), (SELECT id FROM utilisateur WHERE role = 'vente'), 15000, 'VALIDEE');

INSERT INTO ligne_vente (vente_id, produit_id, quantite, prix_unitaire) VALUES
((SELECT id FROM vente WHERE montant_total = 58000), (SELECT id FROM produit WHERE nom = 'Sac de riz 50kg'), 1, 25000),
((SELECT id FROM vente WHERE montant_total = 58000), (SELECT id FROM produit WHERE nom = 'Carton de lait'), 2, 15000),
((SELECT id FROM vente WHERE montant_total = 44000), (SELECT id FROM produit WHERE nom = 'Carton de savon'), 2, 12000),
((SELECT id FROM vente WHERE montant_total = 44000), (SELECT id FROM produit WHERE nom = 'Bidon d''huile 5L'), 2, 8000),
((SELECT id FROM vente WHERE montant_total = 74000), (SELECT id FROM produit WHERE nom = 'Sac de riz 50kg'), 2, 25000),
((SELECT id FROM vente WHERE montant_total = 74000), (SELECT id FROM produit WHERE nom = 'Paquet de sucre 1kg'), 16, 1500),
((SELECT id FROM vente WHERE montant_total = 15000), (SELECT id FROM produit WHERE nom = 'Carton de savon'), 1, 12000),
((SELECT id FROM vente WHERE montant_total = 15000), (SELECT id FROM produit WHERE nom = 'Bidon d''huile 5L'), 1, 3000);

INSERT INTO paiement (vente_id, montant, mode_paiement, statut) VALUES
((SELECT id FROM vente WHERE montant_total = 58000), 58000, 'Wave', 'VALIDE');

INSERT INTO paiement (vente_id, montant, mode_paiement, statut) VALUES
((SELECT id FROM vente WHERE montant_total = 44000), 10000, 'Especes', 'VALIDE');

INSERT INTO paiement (vente_id, montant, mode_paiement, statut) VALUES
((SELECT id FROM vente WHERE montant_total = 74000), 24000, 'Wave', 'VALIDE');


INSERT INTO dette (vente_id, client_id, montant, montant_restant, statut) VALUES
((SELECT id FROM vente WHERE montant_total = 44000), (SELECT id FROM client WHERE nom = 'Diouf'), 44000, 34000, 'NON SOLDEE'),
((SELECT id FROM vente WHERE montant_total = 74000), (SELECT id FROM client WHERE nom = 'Sarr'), 74000, 50000, 'NON SOLDEE'),
((SELECT id FROM vente WHERE montant_total = 15000), (SELECT id FROM client WHERE nom = 'Diallo'), 15000, 15000, 'NON SOLDEE');

INSERT INTO remboursement (dette_id, montant, date) VALUES
((SELECT id FROM dette WHERE client_id = (SELECT id FROM client WHERE nom = 'Sarr')), 24000, '2026-08-07 22:48:53'),
((SELECT id FROM dette WHERE client_id = (SELECT id FROM client WHERE nom = 'Diouf')), 10000, '2026-08-07 21:48:00');

INSERT INTO approvisionnement (fournisseur_id, utilisateur_id, montant_total, statut) VALUES
((SELECT id FROM fournisseur WHERE nom = 'Grossiste Sénégal SARL'), (SELECT id FROM utilisateur WHERE role = 'stock'), 500000, 'RECEPTIONNE'),
((SELECT id FROM fournisseur WHERE nom = 'Import Riz Plus'), (SELECT id FROM utilisateur WHERE role = 'stock'), 150000, 'EN ATTENTE');

INSERT INTO ligne_approvisionnement (approvisionnement_id, produit_id, quantite, prix_unitaire) VALUES
((SELECT id FROM approvisionnement WHERE montant_total = 500000), (SELECT id FROM produit WHERE nom = 'Paquet de sucre 1kg'), 200, 1200),
((SELECT id FROM approvisionnement WHERE montant_total = 500000), (SELECT id FROM produit WHERE nom = 'Carton de lait'), 20, 13000),
((SELECT id FROM approvisionnement WHERE montant_total = 150000), (SELECT id FROM produit WHERE nom = 'Sac de riz 50kg'), 6, 22000);

INSERT INTO inventaire (utilisateur_id, statut) VALUES
((SELECT id FROM utilisateur WHERE role = 'inventaire'), 'CLOTURE');

INSERT INTO ligne_inventaire (inventaire_id, produit_id, quantite_theorique, quantite_reelle) VALUES
((SELECT id FROM inventaire LIMIT 1), (SELECT id FROM produit WHERE nom = 'Bidon d''huile 5L'), 5, 5),
((SELECT id FROM inventaire LIMIT 1), (SELECT id FROM produit WHERE nom = 'Carton de savon'), 3, 2),
((SELECT id FROM inventaire LIMIT 1), (SELECT id FROM produit WHERE nom = 'Huile de palme 1L'), 0, 0);