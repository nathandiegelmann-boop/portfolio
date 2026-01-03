<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Traitement des actions sur le panier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    
    switch ($action) {
        case 'add':
            if (isset($_POST['produit_id']) && isset($_POST['quantite'])) {
                $product_id = (int)$_POST['produit_id'];
                $quantity = (float)$_POST['quantite'];
                
                if (validate_quantity($quantity)) {
                    $result = add_to_cart($product_id, $quantity);
                    if ($result['success']) {
                        $_SESSION['message'] = $result['message'];
                        redirect('../catalogue.php?message=ajout_ok');
                    } else {
                        $_SESSION['message'] = $result['message'];
                        redirect('../catalogue.php?message=erreur');
                    }
                } else {
                    $_SESSION['message'] = 'Quantité invalide';
                    redirect('../catalogue.php?message=erreur');
                }
            }
            break;
            
        case 'update':
            if (isset($_POST['produit_id']) && isset($_POST['quantite'])) {
                $product_id = (int)$_POST['produit_id'];
                $quantity = (float)$_POST['quantite'];
                
                $result = update_cart_item($product_id, $quantity);
                $_SESSION['message'] = $result['message'];
                redirect('panier.php');
            }
            break;
            
        case 'remove':
            if (isset($_POST['produit_id'])) {
                $product_id = (int)$_POST['produit_id'];
                $result = remove_from_cart($product_id);
                $_SESSION['message'] = $result['message'];
                redirect('panier.php');
            }
            break;
            
        case 'clear':
            clear_cart();
            $_SESSION['message'] = 'Panier vidé';
            redirect('panier.php');
            break;
    }
}

$page_title = 'Mon Panier';
$page_description = 'Vérifiez et modifiez le contenu de votre panier avant de passer commande.';

// Récupération des détails du panier
$cart_items = get_cart_details();
$cart_total = calculate_cart_total($_SESSION['panier'] ?? []);

include '../includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>🛒 Mon Panier</h1>
        <p>Vérifiez et modifiez votre commande avant de finaliser</p>
    </div>
</section>

<section class="cart-section">
    <div class="container">
        <?php if (!empty($cart_items)): ?>
            <div class="cart-content">
                <div class="cart-items">
                    <h2>Produits sélectionnés</h2>
                    
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <?php if ($item['product']['image']): ?>
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($item['product']['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product']['nom']); ?>">
                                <?php else: ?>
                                    <div class="no-image">📦</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['product']['nom']); ?></h3>
                                <p class="item-price">
                                    <?php echo format_price($item['product']['prix']); ?> / kg
                                </p>
                                <p class="item-stock">
                                    Stock disponible: <?php echo $item['product']['stock']; ?> kg
                                </p>
                            </div>
                            
                            <div class="item-quantity">
                                <form method="post" action="" class="quantity-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="produit_id" value="<?php echo $item['product']['id']; ?>">
                                    <input type="hidden" name="prix" value="<?php echo $item['product']['prix']; ?>">
                                    
                                    <label for="quantite_<?php echo $item['product']['id']; ?>">Quantité:</label>
                                    <div class="quantity-controls">
                                        <button type="button" class="qty-btn" onclick="decreaseQuantity(<?php echo $item['product']['id']; ?>)">-</button>
                                        <input type="number" 
                                               id="quantite_<?php echo $item['product']['id']; ?>"
                                               name="quantite" 
                                               min="0.5" 
                                               max="<?php echo $item['product']['stock']; ?>" 
                                               step="0.5" 
                                               value="<?php echo $item['quantity']; ?>"
                                               onchange="this.form.submit()">
                                        <button type="button" class="qty-btn" onclick="increaseQuantity(<?php echo $item['product']['id']; ?>, <?php echo $item['product']['stock']; ?>)">+</button>
                                    </div>
                                    <span class="quantity-unit">kg</span>
                                </form>
                            </div>
                            
                            <div class="item-subtotal">
                                <span class="subtotal-amount"><?php echo format_price($item['subtotal']); ?></span>
                            </div>
                            
                            <div class="item-actions">
                                <form method="post" action="" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir retirer ce produit du panier ?')">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="produit_id" value="<?php echo $item['product']['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-small">
                                        🗑️ Retirer
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="cart-actions">
                        <form method="post" action="" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir vider entièrement votre panier ?')" class="clear-cart-form">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-outline">
                                🗑️ Vider le panier
                            </button>
                        </form>
                        <a href="../catalogue.php" class="btn btn-secondary">
                            ← Continuer mes achats
                        </a>
                    </div>
                </div>
                
                <div class="cart-summary">
                    <div class="summary-card">
                        <h3>Récapitulatif de la commande</h3>
                        
                        <div class="summary-line">
                            <span>Sous-total:</span>
                            <span class="cart-total"><?php echo format_price($cart_total); ?></span>
                        </div>
                        
                        <?php if ($cart_total >= COMMANDE_MIN): ?>
                            <div class="summary-line">
                                <span>Frais de livraison:</span>
                                <span><?php echo format_price(FRAIS_LIVRAISON); ?></span>
                            </div>
                            
                            <div class="summary-line total-line">
                                <span><strong>Total TTC:</strong></span>
                                <span class="total-amount"><strong><?php echo format_price($cart_total + FRAIS_LIVRAISON); ?></strong></span>
                            </div>
                        <?php else: ?>
                            <div class="minimum-order-notice">
                                <p>⚠️ Commande minimum: <?php echo format_price(COMMANDE_MIN); ?></p>
                                <p>Il vous manque <?php echo format_price(COMMANDE_MIN - $cart_total); ?> pour passer commande.</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="delivery-options">
                            <h4>Options de récupération:</h4>
                            <ul>
                                <li>🚚 <strong>Livraison à domicile:</strong> <?php echo format_price(FRAIS_LIVRAISON); ?></li>
                                <li>🏪 <strong>Retrait à la ferme:</strong> Gratuit</li>
                                <li>🎪 <strong>Retrait au marché:</strong> Gratuit</li>
                            </ul>
                        </div>
                        
                        <?php if ($cart_total >= COMMANDE_MIN): ?>
                            <a href="commande.php" class="btn btn-primary btn-large btn-full">
                                ✅ Passer commande
                            </a>
                        <?php else: ?>
                            <button class="btn btn-disabled btn-full" disabled>
                                Montant minimum non atteint
                            </button>
                        <?php endif; ?>
                        
                        <div class="security-info">
                            <p>🔒 Paiement 100% sécurisé</p>
                            <p>📞 Besoin d'aide ? 01 23 45 67 89</p>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Votre panier est vide</h2>
                <p>Découvrez nos délicieux produits frais de saison et commencez vos achats !</p>
                <a href="../catalogue.php" class="btn btn-primary btn-large">
                    🌱 Découvrir nos produits
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($cart_items)): ?>
<section class="cart-info">
    <div class="container">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">📦</div>
                <h4>Préparation des commandes</h4>
                <p>Vos produits sont préparés le matin même pour garantir leur fraîcheur optimale.</p>
            </div>
            <div class="info-item">
                <div class="info-icon">🕐</div>
                <h4>Délais de livraison</h4>
                <p>Commandez avant 18h pour une livraison le lendemain matin (selon disponibilités).</p>
            </div>
            <div class="info-item">
                <div class="info-icon">💳</div>
                <h4>Moyens de paiement</h4>
                <p>Espèces, carte bancaire, chèque ou paiement en ligne sécurisé.</p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function increaseQuantity(productId, maxStock) {
    const input = document.getElementById('quantite_' + productId);
    const currentValue = parseFloat(input.value);
    const newValue = currentValue + 0.5;
    
    if (newValue <= maxStock) {
        input.value = newValue;
        input.form.submit();
    }
}

function decreaseQuantity(productId) {
    const input = document.getElementById('quantite_' + productId);
    const currentValue = parseFloat(input.value);
    const newValue = currentValue - 0.5;
    
    if (newValue >= 0.5) {
        input.value = newValue;
        input.form.submit();
    } else {
        // Si on descend en dessous de 0.5, on supprime le produit
        if (confirm('Retirer ce produit du panier ?')) {
            input.value = 0;
            input.form.submit();
        }
    }
}

// Update totals on quantity change
document.addEventListener('DOMContentLoaded', function() {
    updateCartTotals();
});
</script>

<?php include '../includes/footer.php'; ?>