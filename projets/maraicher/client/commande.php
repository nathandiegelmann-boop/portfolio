<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Vérifier que le panier n'est pas vide
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    $_SESSION['message'] = 'Votre panier est vide';
    redirect('panier.php');
}

$cart_items = get_cart_details();
$cart_total = calculate_cart_total($_SESSION['panier']);

// Vérifier le montant minimum
if ($cart_total < COMMANDE_MIN) {
    $_SESSION['message'] = 'Montant minimum de commande non atteint (' . format_price(COMMANDE_MIN) . ')';
    redirect('panier.php');
}

// Traitement du formulaire de commande
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validation des champs
    $nom = clean_input($_POST['nom'] ?? '');
    $prenom = clean_input($_POST['prenom'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $telephone = clean_input($_POST['telephone'] ?? '');
    $adresse = clean_input($_POST['adresse'] ?? '');
    $date_livraison = clean_input($_POST['date_livraison'] ?? '');
    $methode_paiement = clean_input($_POST['methode_paiement'] ?? '');
    $notes = clean_input($_POST['notes'] ?? '');
    
    // Validations
    if (empty($nom)) $errors[] = 'Le nom est obligatoire';
    if (empty($prenom)) $errors[] = 'Le prénom est obligatoire';
    if (empty($email) || !validate_email($email)) $errors[] = 'Email invalide';
    if (empty($telephone)) $errors[] = 'Le téléphone est obligatoire';
    if (empty($adresse)) $errors[] = 'L\'adresse est obligatoire';
    if (empty($date_livraison)) $errors[] = 'La date de livraison est obligatoire';
    if (!in_array($methode_paiement, ['en_ligne', 'sur_place'])) $errors[] = 'Méthode de paiement invalide';
    
    // Vérifier que la date de livraison est dans le futur
    $delivery_date = new DateTime($date_livraison);
    $today = new DateTime();
    if ($delivery_date <= $today) {
        $errors[] = 'La date de livraison doit être ultérieure à aujourd\'hui';
    }
    
    if (empty($errors)) {
        // Préparation des données de commande
        $order_data = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'adresse' => $adresse,
            'date_livraison' => $date_livraison,
            'methode_paiement' => $methode_paiement,
            'total' => $cart_total + FRAIS_LIVRAISON,
            'notes' => $notes
        ];
        
        // Créer la commande
        $result = create_order($order_data, $cart_items);
        
        if ($result['success']) {
            // Vider le panier
            clear_cart();
            
            // Rediriger vers la confirmation
            redirect('confirmation.php?order_id=' . $result['order_id']);
        } else {
            $errors[] = $result['message'];
        }
    }
}

$page_title = 'Finaliser ma commande';
$page_description = 'Saisissez vos informations de livraison et finalisez votre commande.';

include '../includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>✅ Finaliser ma commande</h1>
        <p>Plus qu'une étape pour recevoir vos produits frais !</p>
    </div>
</section>

<section class="order-section">
    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <h4>Erreurs à corriger :</h4>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="order-content">
            <div class="order-form-section">
                <h2>Informations de livraison</h2>
                
                <form method="post" action="" class="order-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" required 
                                   value="<?php echo htmlspecialchars($nom ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" required 
                                   value="<?php echo htmlspecialchars($prenom ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone">Téléphone *</label>
                            <input type="tel" id="telephone" name="telephone" required 
                                   value="<?php echo htmlspecialchars($telephone ?? ''); ?>"
                                   placeholder="01 23 45 67 89">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse">Adresse complète *</label>
                        <textarea id="adresse" name="adresse" rows="3" required 
                                  placeholder="Numéro, rue, code postal, ville"><?php echo htmlspecialchars($adresse ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="date_livraison">Date de livraison souhaitée *</label>
                            <input type="date" id="date_livraison" name="date_livraison" required 
                                   value="<?php echo htmlspecialchars($date_livraison ?? ''); ?>"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            <small>Livraison possible du mardi au samedi</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="methode_paiement">Mode de paiement *</label>
                            <select id="methode_paiement" name="methode_paiement" required>
                                <option value="">Choisir...</option>
                                <option value="sur_place" <?php echo (isset($methode_paiement) && $methode_paiement === 'sur_place') ? 'selected' : ''; ?>>
                                    Paiement à la livraison
                                </option>
                                <option value="en_ligne" <?php echo (isset($methode_paiement) && $methode_paiement === 'en_ligne') ? 'selected' : ''; ?>>
                                    Paiement en ligne (sécurisé)
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes (optionnel)</label>
                        <textarea id="notes" name="notes" rows="3" 
                                  placeholder="Instructions particulières, allergies, préférences..."><?php echo htmlspecialchars($notes ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" required>
                            J'accepte les <a href="#conditions" target="_blank">conditions générales de vente</a> *
                        </label>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox">
                            J'accepte de recevoir des offres promotionnelles par email
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <a href="panier.php" class="btn btn-secondary">
                            ← Retour au panier
                        </a>
                        <button type="submit" class="btn btn-primary btn-large">
                            🚀 Confirmer ma commande
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="order-summary-section">
                <div class="summary-card">
                    <h3>Récapitulatif de votre commande</h3>
                    
                    <div class="order-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                <div class="item-info">
                                    <span class="item-name"><?php echo htmlspecialchars($item['product']['nom']); ?></span>
                                    <span class="item-quantity"><?php echo $item['quantity']; ?> kg × <?php echo format_price($item['product']['prix']); ?></span>
                                </div>
                                <div class="item-total">
                                    <?php echo format_price($item['subtotal']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-totals">
                        <div class="summary-line">
                            <span>Sous-total:</span>
                            <span><?php echo format_price($cart_total); ?></span>
                        </div>
                        <div class="summary-line">
                            <span>Frais de livraison:</span>
                            <span><?php echo format_price(FRAIS_LIVRAISON); ?></span>
                        </div>
                        <div class="summary-line total-line">
                            <span><strong>Total TTC:</strong></span>
                            <span><strong><?php echo format_price($cart_total + FRAIS_LIVRAISON); ?></strong></span>
                        </div>
                    </div>
                    
                    <div class="delivery-info">
                        <h4>Informations de livraison</h4>
                        <ul>
                            <li>🚚 Livraison du mardi au samedi</li>
                            <li>📞 Vous serez contacté la veille</li>
                            <li>⏰ Créneau de 8h à 18h</li>
                            <li>💳 Paiement à la livraison possible</li>
                        </ul>
                    </div>
                    
                    <div class="security-badges">
                        <div class="badge">🔒 Paiement sécurisé</div>
                        <div class="badge">✅ Satisfaction garantie</div>
                        <div class="badge">📞 Support client</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="order-help">
    <div class="container">
        <div class="help-content">
            <h3>Besoin d'aide pour passer votre commande ?</h3>
            <div class="help-grid">
                <div class="help-item">
                    <div class="help-icon">📞</div>
                    <h4>Appelez-nous</h4>
                    <p>01 23 45 67 89<br>Du lundi au samedi, 8h-18h</p>
                </div>
                <div class="help-item">
                    <div class="help-icon">📧</div>
                    <h4>Écrivez-nous</h4>
                    <p><?php echo SITE_EMAIL; ?><br>Réponse sous 24h</p>
                </div>
                <div class="help-item">
                    <div class="help-icon">❓</div>
                    <h4>Questions fréquentes</h4>
                    <p>Consultez notre FAQ<br>pour plus d'informations</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>