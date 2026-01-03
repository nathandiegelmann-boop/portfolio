<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Vérifier qu'un ID de commande est fourni
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$order_id) {
    $_SESSION['message'] = 'Commande non trouvée';
    redirect('../index.php');
}

// Récupération des détails de la commande
$stmt = $pdo->prepare("
    SELECT c.*, u.nom as user_nom, u.prenom as user_prenom 
    FROM commandes c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['message'] = 'Commande non trouvée';
    redirect('../index.php');
}

// Récupération des produits de la commande
$stmt = $pdo->prepare("
    SELECT cp.*, p.nom, p.image 
    FROM commande_produits cp 
    JOIN produits p ON cp.produit_id = p.id 
    WHERE cp.commande_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();

$page_title = 'Commande confirmée';
$page_description = 'Votre commande a été enregistrée avec succès. Merci pour votre confiance !';

include '../includes/header.php';
?>

<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-content">
            <div class="confirmation-header">
                <div class="success-icon">✅</div>
                <h1>Commande confirmée !</h1>
                <p class="confirmation-message">
                    Merci <?php echo htmlspecialchars($order['prenom_client']); ?> ! 
                    Votre commande a été enregistrée avec succès.
                </p>
                <div class="order-number">
                    <strong>Numéro de commande : #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                </div>
            </div>
            
            <div class="confirmation-details">
                <div class="order-details-section">
                    <h2>Détails de votre commande</h2>
                    
                    <div class="order-info-grid">
                        <div class="info-group">
                            <h3>Informations client</h3>
                            <p><strong><?php echo htmlspecialchars($order['prenom_client'] . ' ' . $order['nom_client']); ?></strong></p>
                            <p>📧 <?php echo htmlspecialchars($order['email_client']); ?></p>
                            <p>📞 <?php echo htmlspecialchars($order['telephone_client']); ?></p>
                        </div>
                        
                        <div class="info-group">
                            <h3>Livraison</h3>
                            <p><strong>Date souhaitée :</strong><br>
                            <?php echo date('d/m/Y', strtotime($order['date_livraison'])); ?></p>
                            <p><strong>Adresse :</strong><br>
                            <?php echo nl2br(htmlspecialchars($order['adresse_livraison'])); ?></p>
                        </div>
                        
                        <div class="info-group">
                            <h3>Paiement</h3>
                            <p><strong>Méthode :</strong><br>
                            <?php 
                            echo $order['methode_paiement'] === 'en_ligne' 
                                ? 'Paiement en ligne' 
                                : 'Paiement à la livraison'; 
                            ?></p>
                            <p><strong>Statut :</strong><br>
                            <span class="status-badge status-<?php echo $order['statut']; ?>">
                                <?php 
                                $statuses = [
                                    'en_attente' => 'En attente',
                                    'validee' => 'Validée',
                                    'prete' => 'Prête',
                                    'livree' => 'Livrée',
                                    'annulee' => 'Annulée'
                                ];
                                echo $statuses[$order['statut']] ?? $order['statut'];
                                ?>
                            </span></p>
                        </div>
                    </div>
                    
                    <?php if ($order['notes']): ?>
                        <div class="order-notes">
                            <h3>Notes</h3>
                            <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="order-items-section">
                    <h3>Produits commandés</h3>
                    <div class="order-items-list">
                        <?php foreach ($order_items as $item): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <?php if ($item['image']): ?>
                                        <img src="../assets/uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['nom']); ?>">
                                    <?php else: ?>
                                        <div class="no-image">📦</div>
                                    <?php endif; ?>
                                </div>
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($item['nom']); ?></h4>
                                    <p><?php echo $item['quantite']; ?> kg × <?php echo format_price($item['prix_unitaire']); ?></p>
                                </div>
                                <div class="item-total">
                                    <?php echo format_price($item['quantite'] * $item['prix_unitaire']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-total">
                        <div class="total-line">
                            <span><strong>Total TTC : <?php echo format_price($order['total']); ?></strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="next-steps">
    <div class="container">
        <h2>Et maintenant ?</h2>
        <div class="steps-grid">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Préparation</h3>
                    <p>Nous préparons votre commande avec soin. Vos produits sont sélectionnés et conditionnés le matin même de la livraison.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Confirmation</h3>
                    <p>Nous vous contactons la veille de la livraison pour confirmer le créneau et vous donner des détails pratiques.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Livraison</h3>
                    <p>Réception de vos produits frais à l'adresse indiquée, ou retrait directement à la ferme ou sur le marché.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="confirmation-actions">
    <div class="container">
        <div class="actions-content">
            <div class="email-confirmation">
                <div class="email-icon">📧</div>
                <div class="email-text">
                    <h3>Confirmation par email</h3>
                    <p>Un email de confirmation a été envoyé à <strong><?php echo htmlspecialchars($order['email_client']); ?></strong></p>
                    <p>Vérifiez également votre dossier spam.</p>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="../catalogue.php" class="btn btn-primary">
                    🌱 Continuer mes achats
                </a>
                <a href="../index.php" class="btn btn-secondary">
                    🏠 Retour à l'accueil
                </a>
                <button onclick="window.print()" class="btn btn-outline">
                    🖨️ Imprimer cette page
                </button>
            </div>
        </div>
    </div>
</section>

<section class="customer-service">
    <div class="container">
        <div class="service-content">
            <h3>Besoin d'aide ou de modifications ?</h3>
            <div class="service-grid">
                <div class="service-item">
                    <div class="service-icon">📞</div>
                    <h4>Contactez-nous</h4>
                    <p>01 23 45 67 89<br>Du lundi au samedi, 8h-18h</p>
                </div>
                <div class="service-item">
                    <div class="service-icon">📧</div>
                    <h4>Écrivez-nous</h4>
                    <p><?php echo SITE_EMAIL; ?><br>En mentionnant votre n° de commande</p>
                </div>
                <div class="service-item">
                    <div class="service-icon">📍</div>
                    <h4>Visitez-nous</h4>
                    <p>123 Chemin des Champs<br>75000 Paris</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.confirmation-section {
    padding: 40px 0;
}

.success-icon {
    font-size: 4rem;
    text-align: center;
    margin-bottom: 20px;
}

.confirmation-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px;
    background: linear-gradient(135deg, #4CAF50, #45a049);
    color: white;
    border-radius: 10px;
}

.order-number {
    font-size: 1.2rem;
    margin-top: 15px;
    padding: 10px 20px;
    background: rgba(255,255,255,0.2);
    border-radius: 5px;
    display: inline-block;
}

.order-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-group {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.info-group h3 {
    color: #4CAF50;
    margin-bottom: 15px;
    border-bottom: 2px solid #4CAF50;
    padding-bottom: 5px;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: bold;
}

.status-en_attente { background: #fff3cd; color: #856404; }
.status-validee { background: #d1ecf1; color: #0c5460; }
.status-prete { background: #d4edda; color: #155724; }
.status-livree { background: #d4edda; color: #155724; }

.order-items-list {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.order-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 60px;
    height: 60px;
    margin-right: 15px;
}

.item-image img, .no-image {
    width: 100%;
    height: 100%;
    border-radius: 5px;
    object-fit: cover;
}

.no-image {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    font-size: 1.5rem;
}

.item-details {
    flex: 1;
}

.item-details h4 {
    margin: 0 0 5px 0;
    color: #333;
}

.item-total {
    font-weight: bold;
    color: #4CAF50;
    font-size: 1.1rem;
}

.order-total {
    background: #4CAF50;
    color: white;
    padding: 15px 20px;
    text-align: right;
    border-radius: 0 0 8px 8px;
    font-size: 1.2rem;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.step {
    text-align: center;
    padding: 20px;
}

.step-number {
    width: 50px;
    height: 50px;
    background: #4CAF50;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 auto 15px;
}

.email-confirmation {
    display: flex;
    align-items: center;
    gap: 20px;
    background: #e8f5e8;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.email-icon {
    font-size: 3rem;
}

.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    justify-content: center;
}

.service-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.service-item {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.service-icon {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

@media print {
    .main-header, .main-footer, .confirmation-actions, .customer-service {
        display: none !important;
    }
}
</style>

<?php include '../includes/footer.php'; ?>