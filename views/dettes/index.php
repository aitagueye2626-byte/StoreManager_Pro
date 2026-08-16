<?php


require dirname(__DIR__, 2) . '/src/Core/database.php';
require dirname(__DIR__, 2) . '/src/Model/Entity/Client.php';
require dirname(__DIR__, 2) . '/src/Model/Entity/Dette.php';
require dirname(__DIR__, 2) . '/src/Model/Entity/Remboursement.php';
require dirname(__DIR__, 2) . '/src/Repository/DetteRepository.php';
require dirname(__DIR__, 2) . '/src/Service/DebtService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$detteRepository = new DetteRepository();
$debtService = new DebtService();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dette_id'])) {
    try {
        $detteId = (int) $_POST['dette_id'];
        $montant = (float) ($_POST['montant_verse'] ?? 0);
        $modePaiement = (string) ($_POST['mode_paiement'] ?? 'Especes');

        $resultat = $debtService->rembourser($detteId, $montant, $modePaiement);

        $_SESSION['dettes_succes'] = sprintf(
            'Remboursement de %s F enregistré — Dette #%d %s.',
            number_format($montant, 0, ',', ' '),
            $resultat['dette_id'],
            $resultat['statut'] === 'SOLDEE'
                ? 'entièrement soldée'
                : '— reste dû : ' . number_format($resultat['montant_restant'], 0, ',', ' ') . ' F'
        );

    } catch (MontantRemboursementInvalideException $e) {
        $_SESSION['dettes_erreur'] = $e->getMessage();
    } catch (\InvalidArgumentException $e) {
        $_SESSION['dettes_erreur'] = $e->getMessage();
    } catch (\Throwable $e) {
        $_SESSION['dettes_erreur'] = 'Erreur inattendue : ' . $e->getMessage();
    }

    header('Location: /dettes');
    exit;
}

$stats = $detteRepository->getStatsGlobales();
$dettesActives = $detteRepository->findAllActivesAvecClient();

foreach ($dettesActives as &$item) {
    $dette = $item['dette'];
    $item['remboursements'] = $detteRepository->findRemboursementsParDette($dette->getId());
    $item['lignes_vente'] = $detteRepository->findLignesVenteParDette($dette->getVenteId());
}
unset($item);

$messageSucces = $_SESSION['dettes_succes'] ?? null;
$messageErreur = $_SESSION['dettes_erreur'] ?? null;
unset($_SESSION['dettes_succes'], $_SESSION['dettes_erreur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreManager Pro — Gestion Dettes</title>
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
        .badge-danger  { background: rgba(244, 63, 94, 0.12); color: var(--danger); }
        .badge-warning { background: rgba(245, 158, 11, 0.12); color: var(--warning); }
        .badge-success { background: rgba(16, 185, 129, 0.12); color: var(--success); }

        .panel-card {
            background: var(--panel-bg); border: 1px solid var(--border-color); backdrop-filter: blur(15px);
            border-radius: 24px; padding: 28px; box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3); margin-bottom: 24px;
        }
        .panel-title {
            font-size: 16px; font-weight: 700; margin-bottom: 20px; border-left: 4px solid var(--accent);
            padding-left: 12px; display: flex; justify-content: space-between; align-items: center;
        }

        .search-control {
            background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); border-radius: 8px;
            padding: 6px 12px; color: white; font-size: 12px; outline: none; font-family: var(--font-family); width: 220px;
        }
        .search-control:focus { border-color: var(--accent); }

        .form-control {
            background: rgba(8, 12, 24, 0.7); border: 1px solid var(--border-color); border-radius: 12px;
            padding: 14px 18px; color: white; font-family: var(--font-family); outline: none; font-size: 13px;
        }
        .form-control:focus { border-color: var(--accent); }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent) 0%, #0d9488 100%); color: #0b0f19; border: none;
            border-radius: 12px; padding: 14px 20px; font-weight: 800; font-size: 13px; text-transform: uppercase;
            letter-spacing: 0.5px; cursor: pointer; transition: all 0.3s;
        }
        .btn-submit.btn-success { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); color: white; }

        .debt-table { width: 100%; border-collapse: collapse; text-align: left; }
        .debt-table th {
            color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase;
            padding-bottom: 12px; border-bottom: 1px solid var(--border-color);
        }
        .debt-table td { padding: 14px 10px 14px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.03); font-size: 13px; }

        .btn-quick-action {
            background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main);
            border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.3s;
        }
        .btn-quick-action:hover { background: var(--accent-glow); border-color: var(--accent); color: var(--accent); }

        .details-drawer {
            display: none; background: rgba(255,255,255,0.012); border: 1px solid rgba(255,255,255,0.03);
            border-radius: 16px; padding: 20px; margin-top: 10px;
        }

        .repay-drawer {
            display: none; border: 1px solid rgba(45, 212, 191, 0.25);
            background: linear-gradient(180deg, rgba(11, 15, 25, 0.95) 0%, rgba(11, 15, 25, 0.98) 100%);
            border-radius: 14px; padding: 18px 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.4); margin: 12px 0;
        }
        .chip {
            background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border-color); color: var(--text-main);
            font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 6px; cursor: pointer;
        }
        .chip.chip-accent { background: rgba(45, 212, 191, 0.1); border-color: var(--accent); color: var(--accent); }

        .alert-box { padding: 14px 20px; border-radius: 14px; margin-bottom: 20px; font-size: 13px; font-weight: 700; border: 1px solid; }
        .alert-box.success { background: rgba(52, 211, 153, 0.08); border-color: var(--success); color: var(--success); }
        .alert-box.danger  { background: rgba(248, 113, 113, 0.08); border-color: var(--danger); color: var(--danger); }

        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body>
<div class="app-container">

    <nav class="navbar">
        <div class="nav-logo">StoreManager <span>Pro</span></div>
        <div class="nav-menu">
            <a href="/pos" class="nav-item">Ventes / POS</a>
            <a href="/dettes" class="nav-item active">Gestion Dettes</a>
        </div>
    </nav>

    <?php if ($messageSucces): ?>
        <div class="alert-box success">✓ <?= htmlspecialchars($messageSucces) ?></div>
    <?php endif; ?>
    <?php if ($messageErreur): ?>
        <div class="alert-box danger">✕ <?= htmlspecialchars($messageErreur) ?></div>
    <?php endif; ?>

    <!-- Cartes statistiques -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
        <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--danger);">
            <div>
                <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Créances Actives</span>
                <div style="font-size: 18px; font-weight: 800; color: white; margin-top: 4px;"><?= number_format($stats['creances_actives'], 0, ',', ' ') ?> F</div>
            </div>
            <span style="font-size: 24px;">💸</span>
        </div>
        <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--warning);">
            <div>
                <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Clients Débiteurs</span>
                <div style="font-size: 18px; font-weight: 800; color: white; margin-top: 4px;"><?= $stats['clients_debiteurs'] ?> clients</div>
            </div>
            <span style="font-size: 24px;">👥</span>
        </div>
        <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--success);">
            <div>
                <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Total Recouvrements</span>
                <div style="font-size: 18px; font-weight: 800; color: white; margin-top: 4px;"><?= number_format($stats['total_recouvrements'], 0, ',', ' ') ?> F</div>
            </div>
            <span style="font-size: 24px;">📈</span>
        </div>
    </div>

    <!-- Registre des dettes -->
    <div class="panel-card">
        <div class="panel-title">
            <span>Registre des Dettes Actives</span>
            <input type="text" id="debt-search" class="search-control" placeholder="Rechercher un client..." onkeyup="filterDebtsTable()">
        </div>
        <table class="debt-table" id="debts-main-table">
            <thead>
                <tr>
                    <th>ID Dette</th><th>Date Création</th><th>Client</th>
                    <th>Montant Initial</th><th>Montant Payé</th><th>Reste Dû</th>
                    <th>Statut</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dettesActives as $item): ?>
                    <?php
                        $dette = $item['dette'];
                        $montantPaye = $dette->getMontantInitial() - $dette->getMontantRestant();
                        $nomComplet = strtolower($item['client_nom'] . ' ' . ($item['client_prenom'] ?? '') . ' ' . $item['client_telephone']);
                    ?>
                    <tr data-client-name="<?= htmlspecialchars($nomComplet) ?>">
                        <td style="font-weight: 700; color: var(--text-muted);">
                            #DT-<?= $dette->getId() ?>
                            <span style="font-size: 10px; color: var(--text-muted); display: block; font-weight: normal; margin-top: 2px;">#CMD-<?= $dette->getVenteId() ?></span>
                        </td>
                        <td style="font-size: 12px;"><?= date('d M Y', strtotime($dette->getDateCreation())) ?></td>
                        <td style="font-weight: 700;">
                            <?= htmlspecialchars($item['client_nom'] . ' ' . ($item['client_prenom'] ?? '')) ?>
                            <div style="font-size:11px; color:var(--text-muted); font-weight:normal;">Tél : <?= htmlspecialchars($item['client_telephone']) ?></div>
                        </td>
                        <td style="font-weight: 700;"><?= number_format($dette->getMontantInitial(), 0, ',', ' ') ?> F</td>
                        <td style="font-weight: 700; color: var(--success);"><?= number_format($montantPaye, 0, ',', ' ') ?> F</td>
                        <td style="color: var(--danger); font-weight: 800;"><?= number_format($dette->getMontantRestant(), 0, ',', ' ') ?> F</td>
                        <td><span class="badge badge-danger">NON SOLDEE</span></td>
                        <td style="display: flex; gap: 6px;">
                            <button type="button" class="btn-quick-action" onclick="toggleDetails('debt-lines-<?= $dette->getId() ?>')">Articles</button>
                            <button type="button" class="btn-quick-action" style="border-color: var(--accent); color: var(--accent);" onclick="toggleDetails('debt-details-<?= $dette->getId() ?>')">💳 Paiements</button>
                            <button type="button" class="btn-quick-action" style="border-color: var(--warning); color: var(--warning);" onclick="toggleDetails('debt-repay-<?= $dette->getId() ?>')">Rembourser</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" style="padding: 0; border: none;">

                            <!-- Tiroir Paiements -->
                            <div class="details-drawer" id="debt-details-<?= $dette->getId() ?>">
                                <div style="font-weight: 700; font-size: 12px; color: var(--accent); margin-bottom: 8px;">Paiements enregistrés :</div>
                                <table class="debt-table" style="font-size: 11px;">
                                    <thead><tr><th>Date</th><th>Versement</th><th>Mode</th></tr></thead>
                                    <tbody>
                                        <?php if (empty($item['remboursements'])): ?>
                                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Aucun acompte versé.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($item['remboursements'] as $r): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($r->getDateRemboursement()) ?></td>
                                                    <td style="color: var(--accent); font-weight:700;"><?= number_format($r->getMontant(), 0, ',', ' ') ?> F</td>
                                                    <td><?= htmlspecialchars($r->getModePaiement()) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tiroir Articles -->
                            <div class="details-drawer" id="debt-lines-<?= $dette->getId() ?>">
                                <div style="font-weight: 700; font-size: 12px; color: var(--accent); margin-bottom: 8px;">Articles de la Vente à Crédit :</div>
                                <table class="debt-table" style="font-size: 11px;">
                                    <thead><tr><th>Produit</th><th>Qté</th><th>P.U.</th><th>Sous-total</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($item['lignes_vente'] as $ligne): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($ligne['produit_nom']) ?></td>
                                                <td><?= $ligne['quantite'] ?></td>
                                                <td><?= number_format((float) $ligne['prix_unitaire'], 0, ',', ' ') ?> F</td>
                                                <td style="font-weight: 700; color: var(--accent);"><?= number_format((float) $ligne['sous_total'], 0, ',', ' ') ?> F</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tiroir Rembourser -->
                            <div class="repay-drawer" id="debt-repay-<?= $dette->getId() ?>">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 16px;">💳</span>
                                        <span style="font-weight: 800; font-size: 13px;">
                                            Nouveau Remboursement — <span style="color: var(--accent);"><?= htmlspecialchars($item['client_nom'] . ' ' . ($item['client_prenom'] ?? '')) ?></span>
                                        </span>
                                    </div>
                                    <div style="background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.3); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; color: var(--danger);">
                                        Reste dû : <?= number_format($dette->getMontantRestant(), 0, ',', ' ') ?> FCFA
                                    </div>
                                </div>

                                <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 16px;">
                                    <span style="font-size: 10px; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Raccourcis :</span>
                                    <button type="button" class="chip chip-accent" onclick="setRepayAmount(<?= $dette->getId() ?>, <?= $dette->getMontantRestant() ?>)">Tout solder (<?= number_format($dette->getMontantRestant(), 0, ',', ' ') ?> F)</button>
                                    <button type="button" class="chip" onclick="setRepayAmount(<?= $dette->getId() ?>, <?= round($dette->getMontantRestant() / 2) ?>)">50% (<?= number_format(round($dette->getMontantRestant() / 2), 0, ',', ' ') ?> F)</button>
                                </div>

                                <form method="post" action="" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                                    <input type="hidden" name="dette_id" value="<?= $dette->getId() ?>">

                                    <div style="flex: 1; min-width: 200px;">
                                        <label style="font-size: 10px; color: var(--text-muted); display: block; margin-bottom: 6px; text-transform: uppercase; font-weight: 700;">Montant du Versement (FCFA)</label>
                                        <input type="number" name="montant_verse" id="repay-input-<?= $dette->getId() ?>" class="form-control"
                                               max="<?= $dette->getMontantRestant() ?>" value="<?= $dette->getMontantRestant() ?>" min="1" required style="width: 100%;">
                                    </div>

                                    <div style="flex: 1; min-width: 200px;">
                                        <label style="font-size: 10px; color: var(--text-muted); display: block; margin-bottom: 6px; text-transform: uppercase; font-weight: 700;">Canal de Paiement</label>
                                        <select name="mode_paiement" class="form-control" style="width: 100%;" required>
                                            <option value="Orange Money">🟠 Orange Money</option>
                                            <option value="Wave">🌊 Wave</option>
                                            <option value="Especes">💵 Espèces (Cash)</option>
                                            <option value="Virement">🏦 Virement Bceao</option>
                                        </select>
                                    </div>

                                    <div>
                                        <button type="submit" class="btn-submit btn-success">✓ Enregistrer le Remboursement</button>
                                    </div>
                                </form>
                            </div>

                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($dettesActives)): ?>
                    <tr><td colspan="8" style="color: var(--text-muted);">Aucune dette active.</td></tr>
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

    function setRepayAmount(debtId, amount) {
        const input = document.getElementById("repay-input-" + debtId);
        if (input) {
            input.value = amount;
            input.focus();
        }
    }

    function filterDebtsTable() {
        const query = document.getElementById("debt-search").value.toLowerCase();
        const rows = document.querySelectorAll("#debts-main-table tbody > tr");

        rows.forEach(row => {
            const cell = row.querySelector("td");
            if (cell && cell.getAttribute("colspan") !== null) return;

            const searchVal = row.getAttribute("data-client-name");
            if (searchVal) {
                row.style.display = searchVal.includes(query) ? "" : "none";
            }
        });
    }
</script>

</body>
</html>