<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreManager Pro — Vente / POS</title>
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
        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: var(--font-family);
            min-height: 100vh;
            padding: 0; margin: 0;
        }
        .app-container { width: 100%; max-width: 100%; padding: 24px; }

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

        .badge {
            display: inline-block; padding: 4px 8px; border-radius: 6px;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
        }
        .badge-danger  { background: rgba(244, 63, 94, 0.12); color: var(--danger); }
        .badge-warning { background: rgba(245, 158, 11, 0.12); color: var(--warning); }
        .badge-success { background: rgba(16, 185, 129, 0.12); color: var(--success); }

        .panel-card {
            background: var(--panel-bg); border: 1px solid var(--border-color);
            backdrop-filter: blur(15px); border-radius: 24px; padding: 28px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3); margin-bottom: 24px;
        }
        .panel-title {
            font-size: 16px; font-weight: 700; margin-bottom: 20px;
            border-left: 4px solid var(--accent); padding-left: 12px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .keypad-container {
            background: #090e1a; border: 1px solid var(--border-color); border-radius: 16px;
            padding: 12px; display: none; grid-template-columns: repeat(3, 1fr);
            gap: 8px; margin-top: 12px; max-width: 280px;
        }
        .keypad-btn {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);
            border-radius: 10px; color: white; font-size: 16px; font-weight: 700;
            padding: 12px 0; cursor: pointer; transition: all 0.2s; text-align: center;
        }
        .keypad-btn:hover { background: var(--accent-glow); color: var(--accent); }
        .keypad-btn:active { transform: scale(0.95); }

        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; position: relative; }
        .form-group label { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control {
            background: rgba(8, 12, 24, 0.7); border: 1px solid var(--border-color);
            border-radius: 12px; padding: 14px 18px; color: white;
            font-family: var(--font-family); outline: none; font-size: 13px; transition: all 0.3s;
        }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 12px rgba(59, 130, 246, 0.1); }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent) 0%, #0d9488 100%);
            color: #0b0f19; border: none; border-radius: 12px; padding: 14px 20px;
            font-weight: 800; font-size: 13px; text-transform: uppercase;
            letter-spacing: 0.5px; cursor: pointer; width: 100%; transition: all 0.3s;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(45, 212, 191, 0.3); }
        .btn-submit.btn-success { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); color: white; }

        .debt-table { width: 100%; border-collapse: collapse; text-align: left; }
        .debt-table th {
            color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase;
            padding-bottom: 12px; border-bottom: 1px solid var(--border-color);
        }
        .debt-table td { padding: 14px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.03); font-size: 13px; }

        .btn-quick-action {
            background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main);
            border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 700;
            cursor: pointer; transition: all 0.3s;
        }
        .btn-quick-action:hover { background: var(--accent-glow); border-color: var(--accent); color: var(--accent); }

        .details-drawer {
            display: none; background: rgba(255,255,255,0.012); border: 1px solid rgba(255,255,255,0.03);
            border-radius: 16px; padding: 20px; margin-top: 10px;
        }

        .alert-box {
            padding: 14px 20px; border-radius: 14px; margin-bottom: 20px;
            font-size: 13px; font-weight: 700; border: 1px solid;
        }
        .alert-box.success { background: rgba(52, 211, 153, 0.08); border-color: var(--success); color: var(--success); }
        .alert-box.danger  { background: rgba(248, 113, 113, 0.08); border-color: var(--danger); color: var(--danger); }

        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
</head>
<body>
<div class="app-container">

    <nav class="navbar">
        <div class="nav-logo">StoreManager <span>Pro</span></div>
        <div class="nav-menu">
            <a href="/pos" class="nav-item active">Ventes / POS</a>
            <a href="/dettes" class="nav-item">Gestion Dettes</a>
        </div>
    </nav>

    <?php if ($messageSucces): ?>
        <div class="alert-box success">✓ <?= htmlspecialchars($messageSucces) ?></div>
    <?php endif; ?>
    <?php if ($messageErreur): ?>
        <div class="alert-box danger">✕ <?= htmlspecialchars($messageErreur) ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 600px 1fr; gap: 32px; align-items: start;">

        <!-- Colonne gauche : création de vente -->
        <div class="panel-card" style="border: 1px solid rgba(59, 130, 246, 0.2); position: sticky; top: 24px;">
            <div class="panel-title">
                <span>🛒 Nouvelle Vente</span>
                <span style="font-size: 11px; font-weight: 600; color: var(--text-muted); background: rgba(255,255,255,0.03); padding: 4px 8px; border-radius: 6px;">Terminal POS</span>
            </div>

            <form method="post" action="/pos/vente" id="order-creation-form">

                <div class="form-group">
                    <label for="client-select">Client Acheteur</label>
                    <select name="client_id" id="client-select" class="form-control" onchange="updateClientLimitInfo()">
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client->getId() ?>" data-limit="<?= $client->getLimiteCredit() ?>">
                                <?= htmlspecialchars($client->getNom() . ' ' . ($client->getPrenom() ?? '')) ?>
                                (<?= htmlspecialchars($client->getTelephone()) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span id="credit-limit-info" style="font-size:11px; color:var(--text-muted); font-weight:600;"></span>
                </div>

                <div style="border-top: 1px dashed var(--border-color); padding-top: 16px; margin-top: 16px; margin-bottom: 16px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--accent); display: block; margin-bottom: 8px; text-transform: uppercase;">Sélection des Articles</label>

                    <div style="display: grid; grid-template-columns: 2.2fr 0.8fr auto; gap: 8px; align-items: flex-end; margin-bottom: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="pos-item-select" style="font-size: 10px;">Article</label>
                            <select id="pos-item-select" class="form-control" style="padding: 10px; font-size: 12px;">
                                <?php foreach ($produitsAvecStock as $item): ?>
                                    <?php
                                        $p = $item['produit'];
                                        $q = $item['quantite'];
                                        // Seuil d'alerte fixe (5) car l'entité Produit
                                        // n'expose pas encore getSeuilAlerte().
                                        $seuilAlerteAffichage = 5;
                                        $pastille = $q === 0 ? '🔴' : ($q <= $seuilAlerteAffichage ? '🟡' : '🟢');
                                    ?>
                                    <option value="<?= $p->getId() ?>"
                                            data-price="<?= $p->getPrix() ?>"
                                            data-name="<?= htmlspecialchars($p->getNom()) ?>"
                                            data-stock="<?= $q ?>">
                                        <?= $pastille ?> <?= htmlspecialchars($p->getNom()) ?> (<?= $q ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="pos-qty" style="font-size: 10px;">Qté</label>
                            <input type="number" id="pos-qty" class="form-control" value="1" min="1" style="padding: 10px; font-size: 12px;" onfocus="showKeypad('pos-qty')">
                        </div>
                        <button type="button" class="btn-submit" onclick="addToCart(event)" style="height: 38px; width: 38px; font-size: 18px; display: flex; justify-content: center; align-items: center; border-radius: 8px; padding: 0; flex-shrink: 0; min-width: 38px;">+</button>
                    </div>

                    <div class="keypad-container" id="pos-keypad">
                        <button type="button" class="keypad-btn" onclick="pressKey(1)">1</button>
                        <button type="button" class="keypad-btn" onclick="pressKey(2)">2</button>
                        <button type="button" class="keypad-btn" onclick="pressKey(3)">3</button>
                        <button type="button" class="keypad-btn" onclick="pressKey(4)">4</button>
                        <button type="button" class="keypad-btn" onclick="pressKey(5)">5</button>
                        <button type="button" class="keypad-btn" onclick="pressKey(6)">6</button>
                        <button type="button" class="keypad-btn" onclick="pressKey(7)">7</button>
                        <button type="button" class="keypad-btn" onclick="pressKey(8)">8</button>
                        <button type="button" class="keypad-btn" onclick="pressKey(9)">9</button>
                        <button type="button" class="keypad-btn" onclick="pressKey('C')" style="color: var(--danger);">C</button>
                        <button type="button" class="keypad-btn" onclick="pressKey(0)">0</button>
                        <button type="button" class="keypad-btn" onclick="hideKeypad()" style="color: var(--success); font-size: 12px;">OK</button>
                    </div>

                    <table class="debt-table" style="font-size: 11px; margin-top: 16px;">
                        <thead>
                            <tr><th>Produit</th><th>Qté</th><th>Total</th><th></th></tr>
                        </thead>
                        <tbody id="cart-rows">
                            <tr id="empty-cart-row">
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 16px 0; border-bottom: none;">Panier vide. Ajoutez des articles.</td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="hidden-cart-inputs"></div>
                </div>

                <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(30, 41, 59, 0.4) 100%); border: 1px solid rgba(59, 130, 246, 0.15); border-radius: 16px; padding: 14px; text-align: center; margin-bottom: 20px;">
                    <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 1px; display: block; margin-bottom: 4px;">Montant Total Net à Payer</span>
                    <div style="font-size: 24px; font-weight: 900; color: #60a5fa; font-family: monospace;">
                        <span id="montant_total_display_text">0</span> <span style="font-size: 14px; font-weight: 700;">FCFA</span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="mode_paiement" style="font-size: 10px;">Règlement</label>
                        <select name="mode_paiement" id="mode_paiement" class="form-control" style="padding: 10px; font-size: 12px;">
                            <option value="Wave">Wave</option>
                            <option value="Orange Money">Orange Money</option>
                            <option value="Especes">Espèces</option>
                            <option value="Virement">Virement</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="pos-montant-verse" style="font-size: 10px;">Versé (Avance)</label>
                        <input type="number" name="montant_verse" id="pos-montant-verse" class="form-control" value="0" min="0" style="padding: 10px; font-size: 12px;" onfocus="showKeypad('pos-montant-verse')">
                    </div>
                </div>

                <button type="submit" class="btn-submit btn-success" style="padding: 14px; font-weight: 800; font-size: 13px;">Valider la Vente</button>
            </form>
        </div>

        <!-- Colonne droite : registre des ventes -->
        <div class="panel-card">
            <div class="panel-title">Registre Général des Ventes & Commandes</div>
            <table class="debt-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Client</th><th>Total Facture</th><th>Statut</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventesRecentes as $vente): ?>
                        <?php
                            $panelId = 'order-details-' . $vente['id'];
                            $estCredit = $vente['statut_dette'] === 'NON SOLDEE';
                            $estCreditTotal = $estCredit && (float) $vente['montant_paye'] == 0;
                        ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-muted);">#<?= $vente['id'] ?></td>
                            <td style="font-weight: 700;">
                                <?= htmlspecialchars($vente['client_nom'] . ' ' . ($vente['client_prenom'] ?? '')) ?>
                                <div style="font-size:11px; color:var(--text-muted); font-weight:normal;">Tél : <?= htmlspecialchars($vente['client_telephone']) ?></div>
                            </td>
                            <td style="font-weight: 800; color: var(--accent);"><?= number_format((float) $vente['montant_total'], 0, ',', ' ') ?> F</td>
                            <td>
                                <?php if (!$estCredit): ?>
                                    <span class="badge badge-success">COMPTANT</span>
                                <?php elseif ($estCreditTotal): ?>
                                    <span class="badge badge-danger">CRÉDIT TOTAL</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">AVANCE (CRÉDIT)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn-quick-action" onclick="toggleDetails('<?= $panelId ?>')">Lignes</button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="padding: 0; border: none;">
                                <div class="details-drawer" id="<?= $panelId ?>">
                                    <div style="font-weight: 700; font-size: 12px; color: var(--accent); margin-bottom: 8px;">Détails Facture :</div>
                                    <table class="debt-table" style="font-size: 11px;">
                                        <thead>
                                            <tr><th>Produit</th><th>Qté</th><th>P.U.</th><th>Sous-total</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($vente['lignes'] as $ligne): ?>
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
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ventesRecentes)): ?>
                        <tr><td colspan="5" style="color:var(--text-muted);">Aucune vente enregistrée.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    let cart = [];
    let activeInputId = null;

    function addToCart(event) {
        event.preventDefault();
        const select = document.getElementById("pos-item-select");
        const price = parseFloat(select.options[select.selectedIndex].getAttribute("data-price"));
        const name = select.options[select.selectedIndex].getAttribute("data-name");
        const stock = parseInt(select.options[select.selectedIndex].getAttribute("data-stock"));
        const id = select.value;
        const qty = parseInt(document.getElementById("pos-qty").value);

        if (!id || qty <= 0) return;

        if (qty > stock) {
            alert(`Stock insuffisant pour ${name} (${stock} disponible) !`);
            return;
        }

        const existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty + qty > stock) {
                alert(`Stock insuffisant (${stock} disponible) !`);
                return;
            }
            existing.qty += qty;
            existing.total = existing.qty * price;
        } else {
            cart.push({ id, name, price, qty, total: qty * price });
        }

        renderCart();
        hideKeypad();
    }

    function removeCartItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function renderCart() {
        const body = document.getElementById("cart-rows");
        const textDisplay = document.getElementById("montant_total_display_text");
        const hiddenInputs = document.getElementById("hidden-cart-inputs");

        if (cart.length === 0) {
            body.innerHTML = `
                <tr id="empty-cart-row">
                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 16px 0; border-bottom: none;">Panier vide. Ajoutez des articles.</td>
                </tr>
            `;
            textDisplay.innerText = "0";
            hiddenInputs.innerHTML = "";
            document.getElementById("pos-montant-verse").value = 0;
            return;
        }

        body.innerHTML = "";
        hiddenInputs.innerHTML = "";
        let overallTotal = 0;

        cart.forEach((item, index) => {
            overallTotal += item.total;
            body.innerHTML += `
                <tr>
                    <td style="padding: 8px 0; font-weight:700;">${item.name}</td>
                    <td style="padding: 8px 0;">${item.qty}</td>
                    <td style="padding: 8px 0; font-weight:800; color:var(--accent);">${new Intl.NumberFormat('fr-FR').format(item.total)} F</td>
                    <td style="padding: 8px 0; text-align:right;">
                        <button type="button" onclick="removeCartItem(${index})" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:14px;">🗑️</button>
                    </td>
                </tr>
            `;
            // Ces deux noms (produit_id[] / quantite[]) correspondent à ce
            // que POSController::extrairePanierDuFormulaire() attend.
            hiddenInputs.innerHTML += `
                <input type="hidden" name="produit_id[]" value="${item.id}">
                <input type="hidden" name="quantite[]" value="${item.qty}">
            `;
        });

        textDisplay.innerText = new Intl.NumberFormat('fr-FR').format(overallTotal);

        // Par défaut, le paiement est au comptant (versé = total).
        document.getElementById("pos-montant-verse").value = overallTotal;
    }

    function showKeypad(inputId) {
        activeInputId = inputId;
        document.getElementById("pos-keypad").style.display = "grid";
    }

    function pressKey(key) {
        if (!activeInputId) return;
        const input = document.getElementById(activeInputId);
        if (key === 'C') {
            input.value = "";
        } else {
            input.value = (input.value === "1" || input.value === "0") && activeInputId === 'pos-qty'
                ? key
                : input.value + key;
        }
    }

    function hideKeypad() {
        document.getElementById("pos-keypad").style.display = "none";
        activeInputId = null;
    }

    function updateClientLimitInfo() {
        const select = document.getElementById("client-select");
        if (!select || select.selectedIndex < 0) return;
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.getAttribute("data-limit")) {
            document.getElementById("credit-limit-info").innerText = "";
            return;
        }
        const limit = parseFloat(opt.getAttribute("data-limit"));
        document.getElementById("credit-limit-info").innerText =
            `Limite de crédit autorisée : ${new Intl.NumberFormat('fr-FR').format(limit)} FCFA`;
    }

    function toggleDetails(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;
        const isVisible = window.getComputedStyle(panel).display !== 'none';
        panel.style.display = isVisible ? 'none' : 'block';
    }

    document.addEventListener("DOMContentLoaded", () => {
        updateClientLimitInfo();
    });
</script>

</body>
</html>