<?php
require dirname(__DIR__, 2) . '/src/Core/database.php';
require dirname(__DIR__, 2) . '/src/Model/Entity/Fournisseur.php';
require dirname(__DIR__, 2) . '/src/Model/Entity/Produit.php';
require dirname(__DIR__, 2) . '/src/Model/Entity/Approvisionnement.php';
require dirname(__DIR__, 2) . '/src/Model/Entity/LigneApprovisionnement.php';
require dirname(__DIR__, 2) . '/src/Repository/ProduitRepository.php';
require dirname(__DIR__, 2) . '/src/Service/SupplyService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$supplyService = new SupplyService();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approvisionnement_id'])) {
    try {
        $approvisionnementId = (int) $_POST['approvisionnement_id'];
        $resultat = $supplyService->receptionner($approvisionnementId);

        $_SESSION['supplies_succes'] = sprintf(
            'Bon de livraison #%d réceptionné — stock mis à jour pour %d article(s).',
            $resultat['approvisionnement_id'],
            $resultat['nombre_lignes']
        );

    } catch (\InvalidArgumentException $e) {
        $_SESSION['supplies_erreur'] = $e->getMessage();
    } catch (\Throwable $e) {
        $_SESSION['supplies_erreur'] = 'Erreur inattendue : ' . $e->getMessage();
    }

    header('Location: /approvisionnements');
    exit;
}

$bonsEnAttente = $supplyService->listerEnAttente();
$bonsReceptionnes = $supplyService->listerReceptionnes();

$montantTotalEntrees = 0.0;
foreach (array_merge($bonsEnAttente, $bonsReceptionnes) as $item) {
    $montantTotalEntrees += $item['approvisionnement']->getTotal();
}

$fournisseursActifs = count(array_unique(array_map(
    fn (array $item) => $item['approvisionnement']->getFournisseurId(),
    array_merge($bonsEnAttente, $bonsReceptionnes)
)));

$messageSucces = $_SESSION['supplies_succes'] ?? null;
$messageErreur = $_SESSION['supplies_erreur'] ?? null;
unset($_SESSION['supplies_succes'], $_SESSION['supplies_erreur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreManager Pro — Approvisionnements</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --panel-bg: rgba(22, 30, 49, 0.65);
            --border-color: rgba(45, 212, 191, 0.12);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #2dd4bf;
            --accent-glow: rgba(45, 212, 191, 0.1);
            --success: #34d399;
            --danger: #f87171;
            --warning: #fbbf24;
            --font-family: 'Plus Jakarta Sans', sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: var(--font-family); min-height: 100vh; }
        .app-container { width: 100%; padding: 24px; }
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(8, 12, 24, 0.7); border: 1px solid var(--border-color);
            padding: 16px 24px; border-radius: 20px; margin-bottom: 24px;
            backdrop-filter: blur(15px); box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        .nav-logo { font-size: 20px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .nav-logo span { color: var(--accent); }
        .nav-menu { display: flex; gap: 8px; }
        .nav-item {
            background: transparent; border: 1px solid transparent; color: var(--text-muted);
            padding: 10px 18px; border-radius: 12px; cursor: pointer; font-size: 13px;
            font-weight: 700; transition: all 0.3s; text-decoration: none; display: inline-block;
        }
        .nav-item:hover, .nav-item.active { background: var(--accent-glow); color: var(--accent); border-color: var(--accent); }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge-warning { background: rgba(245, 158, 11, 0.12); color: var(--warning); }
        .badge-success { background: rgba(16, 185, 129, 0.12); color: var(--success); }
        .panel-card {
            background: var(--panel-bg); border: 1px solid var(--border-color); backdrop-filter: blur(15px);
            border-radius: 24px; padding: 28px; box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3); margin-bottom: 24px;
        }
        .panel-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; border-left: 4px solid var(--accent); padding-left: 12px; }
        .btn-submit {
            background: linear-gradient(135deg, var(--accent) 0%, #0d9488 100%); color: #0b0f19; border: none;
            border-radius: 12px; padding: 11px 24px; font-weight: 800; font-size: 12px; text-transform: uppercase;
            letter-spacing: 0.5px; cursor: pointer;
        }
        .btn-submit.btn-success { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); color: white; }
        .debt-table { width: 100%; border-collapse: collapse; text-align: left; }
        .debt-table th {
            color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase;
            padding-bottom: 12px; border-bottom: 1px solid var(--border-color);
        }
        .debt-table td { padding: 12px 10px 12px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.03); font-size: 13px; }
        .btn-quick-action {
            background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main);
            border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.3s;
        }
        .btn-quick-action:hover { background: var(--accent-glow); border-color: var(--accent); color: var(--accent); }
        .details-drawer {
            display: none; background: rgba(255,255,255,0.012); border: 1px solid rgba(255,255,255,0.03);
            border-radius: 16px; padding: 20px; margin-top: 10px;
        }
        .receive-drawer {
            display: none; border: 1px solid rgba(52, 211, 153, 0.3);
            background: linear-gradient(180deg, rgba(11, 15, 25, 0.95) 0%, rgba(11, 15, 25, 0.98) 100%);
            border-radius: 14px; padding: 18px 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.4); margin: 12px 0;
        }
        .alert-box { padding: 14px 20px; border-radius: 14px; margin-bottom: 20px; font-size: 13px; font-weight: 700; border: 1px solid; }
        .alert-box.success { background: rgba(52, 211, 153, 0.08); border-color: var(--success); color: var(--success); }
        .alert-box.danger  { background: rgba(248, 113, 113, 0.08); border-color: var(--danger); color: var(--danger); }
    </style>
</head>
<body>
<div class="app-container">
    <nav class="navbar">
        <div class="nav-logo">StoreManager <span>Pro</span></div>
        <div class="nav-menu">
            <a href="/pos" class="nav-item">Ventes / POS</a>
            <a href="/dettes" class="nav-item">Gestion Dettes</a>
            <a href="/approvisionnements" class="nav-item active">Approvisionnements</a>
        </div>
    </nav>

    <?php if ($messageSucces): ?>
        <div class="alert-box success">✓ <?= htmlspecialchars($messageSucces) ?></div>
    <?php endif; ?>
    <?php if ($messageErreur): ?>
        <div class="alert-box danger">✕ <?= htmlspecialchars($messageErreur) ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
        <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--accent);">
            <div>
                <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Coût Total des Entrées</span>
                <div style="font-size: 18px; font-weight: 800; color: white; margin-top: 4px;"><?= number_format($montantTotalEntrees, 0, ',', ' ') ?> F</div>
            </div>
            <span style="font-size: 24px;">📥</span>
        </div>
        <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--warning);">
            <div>
                <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Bons en Attente</span>
                <div style="font-size: 18px; font-weight: 800; color: white; margin-top: 4px;"><?= count($bonsEnAttente) ?> BL</div>
            </div>
            <span style="font-size: 24px;">📄</span>
        </div>
        <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--success);">
            <div>
                <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Fournisseurs Actifs</span>
                <div style="font-size: 18px; font-weight: 800; color: white; margin-top: 4px;"><?= $fournisseursActifs ?> entreprises</div>
            </div>
            <span style="font-size: 24px;">🤝</span>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-title">Bordereaux de Livraison (Réceptions)</div>
        <table class="debt-table">
            <thead>
                <tr><th>Réf BL</th><th>Fournisseur</th><th>Valeur Lot</th><th>Statut</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (array_merge($bonsEnAttente, $bonsReceptionnes) as $item): ?>
                    <?php
                        $appro = $item['approvisionnement'];
                        $estEnAttente = $appro->getStatut() === 'EN ATTENTE';
                    ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-muted);">BL-<?= $appro->getId() ?></td>
                        <td>
                            <?= htmlspecialchars($item['fournisseur_nom']) ?>
                            <div style="font-size:10px; color:var(--text-muted);">Tél : <?= htmlspecialchars($item['fournisseur_telephone']) ?></div>
                        </td>
                        <td style="font-weight: 800; color: var(--accent);"><?= number_format($appro->getTotal(), 0, ',', ' ') ?> F</td>
                        <td>
                            <?php if ($estEnAttente): ?>
                                <span class="badge badge-warning">EN ATTENTE</span>
                            <?php else: ?>
                                <span class="badge badge-success">RECEPTIONNE</span>
                            <?php endif; ?>
                        </td>
                        <td style="display: flex; gap: 6px;">
                            <button type="button" class="btn-quick-action" onclick="toggleDetails('supply-lines-<?= $appro->getId() ?>')">Lignes</button>
                            <?php if ($estEnAttente): ?>
                                <button type="button" class="btn-quick-action" style="border-color: var(--success); color: var(--success);" onclick="toggleDetails('supply-receive-<?= $appro->getId() ?>')">Réceptionner</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding: 0; border: none;">
                            <div class="details-drawer" id="supply-lines-<?= $appro->getId() ?>">
                                <div style="font-weight: 700; font-size: 12px; color: var(--accent); margin-bottom: 8px;">Détails Réception :</div>
                                <table class="debt-table" style="font-size: 11px;">
                                    <thead><tr><th>Produit</th><th>Qté</th><th>Coût Unitaire</th><th>Total</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($item['lignes'] as $ligneItem): ?>
                                            <?php $ligne = $ligneItem['ligne']; ?>
                                            <tr>
                                                <td><?= htmlspecialchars($ligneItem['produit_nom']) ?></td>
                                                <td><?= $ligne->getQuantite() ?></td>
                                                <td><?= number_format($ligne->getPrixAchat(), 0, ',', ' ') ?> F</td>
                                                <td style="font-weight: 700; color: var(--accent);"><?= number_format($ligne->getSousTotal(), 0, ',', ' ') ?> F</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($estEnAttente): ?>
                                <div class="receive-drawer" id="supply-receive-<?= $appro->getId() ?>">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px;">
                                        <span style="font-weight: 800; font-size: 13px;">📦 Réceptionner le BL — <span style="color: var(--accent);">BL-<?= $appro->getId() ?></span></span>
                                        <span style="background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; color: var(--warning);">
                                            Fournisseur : <?= htmlspecialchars($item['fournisseur_nom']) ?>
                                        </span>
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
                                        <?php foreach ($item['lignes'] as $ligneItem): ?>
                                            <?php $ligne = $ligneItem['ligne']; ?>
                                            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 10px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;">
                                                <div style="font-weight: 700; font-size: 13px;"><?= htmlspecialchars($ligneItem['produit_nom']) ?></div>
                                                <div style="font-size: 11px; color: var(--text-muted);">Quantité à réceptionner : <strong style="color: var(--text-main);"><?= $ligne->getQuantite() ?></strong></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <form method="post" action="" style="display: flex; justify-content: flex-end;">
                                        <input type="hidden" name="approvisionnement_id" value="<?= $appro->getId() ?>">
                                        <button type="submit" class="btn-submit btn-success">✓ Valider la Réception en Stock</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($bonsEnAttente) && empty($bonsReceptionnes)): ?>
                    <tr><td colspan="5" style="color: var(--text-muted);">Aucun bon de livraison enregistré.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleDetails(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;
        const isVisible = window.getComputedStyle(panel).display !== 'none';
        panel.style.display = isVisible ? 'none' : 'block';
    }
</script>
</body>
</html>
