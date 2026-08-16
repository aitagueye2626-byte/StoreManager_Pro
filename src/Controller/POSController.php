<?php

use App\Core\Database;


class POSController
{
    private PDO $pdo;
    private ClientRepository $clientRepository;
    private ProduitRepository $produitRepository;
    private VenteService $venteService;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnexion();
        $this->clientRepository = new ClientRepository();
        $this->produitRepository = new ProduitRepository();
        $this->venteService = new VenteService();
    }

    public function afficherFormulaire(): void
    {
        $clients = $this->clientRepository->findAll();
        $produitsAvecStock = $this->produitRepository->findAllAvecStock();
        $ventesRecentes = $this->listerVentesRecentes();
        $messageSucces = $_SESSION['pos_succes'] ?? null;
        $messageErreur = $_SESSION['pos_erreur'] ?? null;
        unset($_SESSION['pos_succes'], $_SESSION['pos_erreur']);

        $racineProjet = dirname(__DIR__, 2);
        require $racineProjet . '/views/pos/index.php';
    }

    
    public function traiterVente(): void
    {
        $utilisateurId = (int) ($_SESSION['utilisateur_id'] ?? 2);

        try {
            $clientId = (int) ($_POST['client_id'] ?? 0);
            $montantVerse = (float) ($_POST['montant_verse'] ?? 0);
            $modePaiement = (string) ($_POST['mode_paiement'] ?? 'Especes');

            $panier = $this->extrairePanierDuFormulaire();

            if ($clientId <= 0) {
                throw new \InvalidArgumentException('Veuillez sélectionner un client.');
            }

            $resultat = $this->venteService->validerVente(
                $clientId,
                $utilisateurId,
                $panier,
                $montantVerse,
                $modePaiement
            );

            $_SESSION['pos_succes'] = sprintf(
                'Vente #%d enregistrée — Total : %s F%s',
                $resultat['vente_id'],
                number_format($resultat['montant_total'], 0, ',', ' '),
                $resultat['reste_du'] > 0
                    ? ' — Reste dû : ' . number_format($resultat['reste_du'], 0, ',', ' ') . ' F'
                    : ' (soldée)'
            );

        } catch (StockInsuffisantException $e) {
            $_SESSION['pos_erreur'] = 'Stock insuffisant : ' . $e->getMessage();
        } catch (LimiteCreditDepasseeException $e) {
            $_SESSION['pos_erreur'] = 'Limite de crédit dépassée : ' . $e->getMessage();
        } catch (\InvalidArgumentException $e) {
            $_SESSION['pos_erreur'] = $e->getMessage();
        } catch (\Throwable $e) {
            $_SESSION['pos_erreur'] = 'Erreur inattendue : ' . $e->getMessage();
        }

        header('Location: /pos');
        exit;
    }

    
    private function extrairePanierDuFormulaire(): array
    {
        $produitIds = $_POST['produit_id'] ?? [];
        $quantites = $_POST['quantite'] ?? [];

        $panier = [];

        foreach ($produitIds as $index => $produitId) {
            $quantite = (int) ($quantites[$index] ?? 0);

            if ((int) $produitId > 0 && $quantite > 0) {
                $panier[] = [
                    'produit_id' => (int) $produitId,
                    'quantite'   => $quantite,
                ];
            }
        }

        return $panier;
    }

    private function listerVentesRecentes(int $limite = 10): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                v.id,
                v.montant_total,
                v.statut,
                c.nom AS client_nom,
                c.prenom AS client_prenom,
                c.telephone AS client_telephone,
                COALESCE((SELECT SUM(p.montant) FROM paiement p WHERE p.vente_id = v.id), 0) AS montant_paye,
                (SELECT d.statut FROM dette d WHERE d.vente_id = v.id) AS statut_dette
             FROM vente v
             JOIN client c ON c.id = v.client_id
             ORDER BY v.id DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        $ventes = $stmt->fetchAll();

        $stmtLignes = $this->pdo->prepare(
            'SELECT lv.quantite, lv.prix_unitaire, lv.sous_total, p.nom AS produit_nom
             FROM ligne_vente lv
             JOIN produit p ON p.id = lv.produit_id
             WHERE lv.vente_id = :vente_id'
        );

        foreach ($ventes as &$vente) {
            $stmtLignes->execute(['vente_id' => $vente['id']]);
            $vente['lignes'] = $stmtLignes->fetchAll();
        }
        unset($vente);

        return $ventes;
    }
}