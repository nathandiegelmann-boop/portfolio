<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Vérifier que l'utilisateur est connecté et admin
if (!is_logged_in() || !is_admin()) {
    redirect('login.php');
}

$page_title = 'Gestion des Commandes - Administration';

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$errors = [];

// Traitement des actions sur les commandes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = clean_input($_POST['status'] ?? '');
    $order_id = (int)($_POST['order_id'] ?? 0);
    
    $allowed_statuses = ['en_attente', 'confirmee', 'preparee', 'livree', 'annulee'];
    
    if (in_array($new_status, $allowed_statuses) && $order_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE commandes SET statut = ?, date_modification = NOW() WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            
            // Si la commande est annulée, remettre les produits en stock
            if ($new_status === 'annulee') {
                $stmt = $pdo->prepare("
                    UPDATE produits p 
                    JOIN commande_produits cp ON p.id = cp.produit_id 
                    SET p.stock = p.stock + cp.quantite 
                    WHERE cp.commande_id = ?
                ");
                $stmt->execute([$order_id]);
            }
            
            $_SESSION['message'] = 'Statut de la commande mis à jour avec succès';
            redirect('commandes.php');
        } catch (Exception $e) {
            $errors[] = 'Erreur lors de la mise à jour : ' . $e->getMessage();
        }
    } else {
        $errors[] = 'Statut invalide';
    }
}

// Récupération des données selon l'action
$order = null;
$order_products = [];

// Récupération de la commande pour visualisation/modification
if (($action === 'view' || $action === 'edit') && $order_id > 0) {
    $stmt = $pdo->prepare("
        SELECT c.*, u.email as user_email 
        FROM commandes c 
        LEFT JOIN users u ON c.user_id = u.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        $_SESSION['message'] = 'Commande non trouvée';
        redirect('commandes.php');
    }
    
    // Récupérer les produits de la commande
    $stmt = $pdo->prepare("
        SELECT cp.*, p.nom as produit_nom, p.image as produit_image
        FROM commande_produits cp
        JOIN produits p ON cp.produit_id = p.id
        WHERE cp.commande_id = ?
        ORDER BY p.nom
    ");
    $stmt->execute([$order_id]);
    $order_products = $stmt->fetchAll();
}

// Liste des commandes pour la vue principale
$orders = [];
if ($action === 'list') {
    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status'] ?? 'all';
    $date_filter = $_GET['date'] ?? 'all';
    
    $where_conditions = ['1=1'];
    $params = [];
    
    if (!empty($search)) {
        $where_conditions[] = '(c.nom_client LIKE ? OR c.prenom_client LIKE ? OR c.email_client LIKE ? OR c.id LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($status_filter !== 'all') {
        $where_conditions[] = 'c.statut = ?';
        $params[] = $status_filter;
    }
    
    switch ($date_filter) {
        case 'today':
            $where_conditions[] = 'DATE(c.date_commande) = CURDATE()';
            break;
        case 'week':
            $where_conditions[] = 'c.date_commande >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
            break;
        case 'month':
            $where_conditions[] = 'c.date_commande >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
            break;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    $stmt = $pdo->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM commande_produits cp WHERE cp.commande_id = c.id) as nb_produits,
               u.email as user_email
        FROM commandes c 
        LEFT JOIN users u ON c.user_id = u.id 
        WHERE $where_clause
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
}

// Statistiques pour le tableau de bord
$stats = [];
if ($action === 'list') {
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_commandes,
            COUNT(CASE WHEN statut = 'en_attente' THEN 1 END) as en_attente,
            COUNT(CASE WHEN statut = 'confirmee' THEN 1 END) as confirmees,
            COUNT(CASE WHEN statut = 'preparee' THEN 1 END) as preparees,
            COUNT(CASE WHEN statut = 'livree' THEN 1 END) as livrees,
            COUNT(CASE WHEN statut = 'annulee' THEN 1 END) as annulees,
            COALESCE(SUM(CASE WHEN statut != 'annulee' THEN total ELSE 0 END), 0) as chiffre_affaires
        FROM commandes
        WHERE date_commande >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stats = $stmt->fetch();
}

include '../includes/header.php';
?>

<section class="admin-header">
    <div class="container">
        <h1>🛒 Gestion des Commandes</h1>
        <div class="admin-actions">
            <?php if ($action !== 'list'): ?>
                <a href="commandes.php" class="btn btn-secondary">← Retour à la liste</a>
            <?php endif; ?>
            <a href="dashboard.php" class="btn btn-outline">📊 Tableau de bord</a>
        </div>
    </div>
</section>

<section class="admin-content">
    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
            <!-- Statistiques rapides -->
            <div class="orders-stats">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📋</div>
                        <div class="stat-info">
                            <div class="stat-number"><?php echo $stats['total_commandes']; ?></div>
                            <div class="stat-label">Total commandes (30j)</div>
                        </div>
                    </div>
                    <div class="stat-card pending">
                        <div class="stat-icon">⏳</div>
                        <div class="stat-info">
                            <div class="stat-number"><?php echo $stats['en_attente']; ?></div>
                            <div class="stat-label">En attente</div>
                        </div>
                    </div>
                    <div class="stat-card confirmed">
                        <div class="stat-icon">✅</div>
                        <div class="stat-info">
                            <div class="stat-number"><?php echo $stats['confirmees']; ?></div>
                            <div class="stat-label">Confirmées</div>
                        </div>
                    </div>
                    <div class="stat-card delivered">
                        <div class="stat-icon">🚚</div>
                        <div class="stat-info">
                            <div class="stat-number"><?php echo $stats['livrees']; ?></div>
                            <div class="stat-label">Livrées</div>
                        </div>
                    </div>
                    <div class="stat-card revenue">
                        <div class="stat-icon">💰</div>
                        <div class="stat-info">
                            <div class="stat-number"><?php echo format_price($stats['chiffre_affaires']); ?></div>
                            <div class="stat-label">Chiffre d'affaires</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Liste des commandes -->
            <div class="orders-management">
                <!-- Filtres -->
                <div class="filters-section">
                    <form method="get" action="" class="admin-filters">
                        <div class="filter-group">
                            <input type="text" name="search" placeholder="Rechercher par nom, email ou n° commande..." 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <select name="status">
                                <option value="all" <?php echo ($status_filter === 'all') ? 'selected' : ''; ?>>Tous les statuts</option>
                                <option value="en_attente" <?php echo ($status_filter === 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                                <option value="confirmee" <?php echo ($status_filter === 'confirmee') ? 'selected' : ''; ?>>Confirmées</option>
                                <option value="preparee" <?php echo ($status_filter === 'preparee') ? 'selected' : ''; ?>>Préparées</option>
                                <option value="livree" <?php echo ($status_filter === 'livree') ? 'selected' : ''; ?>>Livrées</option>
                                <option value="annulee" <?php echo ($status_filter === 'annulee') ? 'selected' : ''; ?>>Annulées</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <select name="date">
                                <option value="all" <?php echo ($date_filter === 'all') ? 'selected' : ''; ?>>Toutes les dates</option>
                                <option value="today" <?php echo ($date_filter === 'today') ? 'selected' : ''; ?>>Aujourd'hui</option>
                                <option value="week" <?php echo ($date_filter === 'week') ? 'selected' : ''; ?>>7 derniers jours</option>
                                <option value="month" <?php echo ($date_filter === 'month') ? 'selected' : ''; ?>>30 derniers jours</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <a href="commandes.php" class="btn btn-outline">Réinitialiser</a>
                    </form>
                </div>
                
                <!-- Tableau des commandes -->
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Produits</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $cmd): ?>
                                    <tr class="order-row status-<?php echo $cmd['statut']; ?>">
                                        <td class="order-id">
                                            <strong>#<?php echo str_pad($cmd['id'], 4, '0', STR_PAD_LEFT); ?></strong>
                                        </td>
                                        <td class="client-info">
                                            <div class="client-name">
                                                <?php echo htmlspecialchars($cmd['prenom_client'] . ' ' . $cmd['nom_client']); ?>
                                            </div>
                                            <div class="client-email">
                                                <?php echo htmlspecialchars($cmd['email_client']); ?>
                                            </div>
                                            <?php if ($cmd['telephone_client']): ?>
                                                <div class="client-phone">
                                                    <?php echo htmlspecialchars($cmd['telephone_client']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="order-date">
                                            <div class="date-commande">
                                                <?php echo date('d/m/Y', strtotime($cmd['date_commande'])); ?>
                                            </div>
                                            <div class="time-commande">
                                                <?php echo date('H:i', strtotime($cmd['date_commande'])); ?>
                                            </div>
                                            <?php if ($cmd['date_livraison']): ?>
                                                <div class="date-livraison">
                                                    📅 <?php echo date('d/m/Y', strtotime($cmd['date_livraison'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="products-count">
                                            <span class="badge"><?php echo $cmd['nb_produits']; ?> produits</span>
                                        </td>
                                        <td class="order-total">
                                            <?php echo format_price($cmd['total']); ?>
                                        </td>
                                        <td class="order-status">
                                            <?php
                                            $status_labels = [
                                                'en_attente' => '⏳ En attente',
                                                'confirmee' => '✅ Confirmée',
                                                'preparee' => '📦 Préparée',
                                                'livree' => '🚚 Livrée',
                                                'annulee' => '❌ Annulée'
                                            ];
                                            ?>
                                            <span class="status-badge status-<?php echo $cmd['statut']; ?>">
                                                <?php echo $status_labels[$cmd['statut']] ?? $cmd['statut']; ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <div class="action-buttons">
                                                <a href="?action=view&id=<?php echo $cmd['id']; ?>" 
                                                   class="btn btn-small btn-outline" title="Voir détails">👁️</a>
                                                <?php if ($cmd['statut'] !== 'livree' && $cmd['statut'] !== 'annulee'): ?>
                                                    <a href="?action=edit&id=<?php echo $cmd['id']; ?>" 
                                                       class="btn btn-small btn-primary" title="Modifier">✏️</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="no-data">
                                        <?php if (!empty($search) || $status_filter !== 'all' || $date_filter !== 'all'): ?>
                                            Aucune commande ne correspond aux critères de recherche.
                                        <?php else: ?>
                                            Aucune commande enregistrée.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-stats">
                    <p><?php echo count($orders); ?> commande(s) affichée(s)</p>
                </div>
            </div>
            
        <?php elseif ($action === 'view'): ?>
            <!-- Vue détaillée de la commande -->
            <div class="order-view-section">
                <div class="order-details">
                    <div class="order-header">
                        <div class="order-info">
                            <h2>Commande #<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></h2>
                            <div class="order-meta">
                                <span class="status-badge status-<?php echo $order['statut']; ?>">
                                    <?php
                                    $status_labels = [
                                        'en_attente' => '⏳ En attente',
                                        'confirmee' => '✅ Confirmée',
                                        'preparee' => '📦 Préparée',
                                        'livree' => '🚚 Livrée',
                                        'annulee' => '❌ Annulée'
                                    ];
                                    echo $status_labels[$order['statut']] ?? $order['statut'];
                                    ?>
                                </span>
                                <span class="order-date-badge">
                                    📅 <?php echo date('d/m/Y à H:i', strtotime($order['date_commande'])); ?>
                                </span>
                            </div>
                            
                            <div class="order-actions">
                                <?php if ($order['statut'] !== 'livree' && $order['statut'] !== 'annulee'): ?>
                                    <a href="?action=edit&id=<?php echo $order['id']; ?>" class="btn btn-primary">✏️ Modifier le statut</a>
                                <?php endif; ?>
                                <button onclick="window.print()" class="btn btn-outline">🖨️ Imprimer</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-content">
                        <div class="order-grid">
                            <!-- Informations client -->
                            <div class="client-section">
                                <h3>👤 Informations client</h3>
                                <div class="client-details">
                                    <div class="detail-item">
                                        <label>Nom complet :</label>
                                        <span><?php echo htmlspecialchars($order['prenom_client'] . ' ' . $order['nom_client']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Email :</label>
                                        <span><?php echo htmlspecialchars($order['email_client']); ?></span>
                                    </div>
                                    <?php if ($order['telephone_client']): ?>
                                        <div class="detail-item">
                                            <label>Téléphone :</label>
                                            <span><?php echo htmlspecialchars($order['telephone_client']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($order['user_email']): ?>
                                        <div class="detail-item">
                                            <label>Compte utilisateur :</label>
                                            <span><?php echo htmlspecialchars($order['user_email']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Informations de livraison -->
                            <div class="delivery-section">
                                <h3>🚚 Informations de livraison</h3>
                                <div class="delivery-details">
                                    <?php if ($order['adresse_livraison']): ?>
                                        <div class="detail-item">
                                            <label>Adresse :</label>
                                            <span><?php echo nl2br(htmlspecialchars($order['adresse_livraison'])); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($order['date_livraison']): ?>
                                        <div class="detail-item">
                                            <label>Date souhaitée :</label>
                                            <span><?php echo date('d/m/Y', strtotime($order['date_livraison'])); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="detail-item">
                                        <label>Méthode de paiement :</label>
                                        <span><?php echo ucfirst(str_replace('_', ' ', $order['methode_paiement'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($order['notes']): ?>
                            <div class="notes-section">
                                <h3>📝 Notes de la commande</h3>
                                <div class="order-notes">
                                    <?php echo nl2br(htmlspecialchars($order['notes'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Produits de la commande -->
                        <div class="products-section">
                            <h3>📦 Produits commandés</h3>
                            <div class="order-products">
                                <?php foreach ($order_products as $product): ?>
                                    <div class="product-item">
                                        <?php if ($product['produit_image']): ?>
                                            <img src="../assets/uploads/<?php echo htmlspecialchars($product['produit_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['produit_nom']); ?>"
                                                 class="product-image">
                                        <?php else: ?>
                                            <div class="no-product-image">📦</div>
                                        <?php endif; ?>
                                        
                                        <div class="product-info">
                                            <h4><?php echo htmlspecialchars($product['produit_nom']); ?></h4>
                                            <div class="product-details">
                                                <span class="quantity"><?php echo $product['quantite']; ?> kg</span>
                                                <span class="unit-price"><?php echo format_price($product['prix_unitaire']); ?>/kg</span>
                                                <span class="total-price"><?php echo format_price($product['quantite'] * $product['prix_unitaire']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Total de la commande -->
                            <div class="order-total-section">
                                <div class="total-breakdown">
                                    <?php
                                    $subtotal = array_sum(array_map(function($p) { 
                                        return $p['quantite'] * $p['prix_unitaire']; 
                                    }, $order_products));
                                    $frais_livraison = $order['total'] - $subtotal;
                                    ?>
                                    <div class="total-line">
                                        <span>Sous-total produits :</span>
                                        <span><?php echo format_price($subtotal); ?></span>
                                    </div>
                                    <?php if ($frais_livraison > 0): ?>
                                        <div class="total-line">
                                            <span>Frais de livraison :</span>
                                            <span><?php echo format_price($frais_livraison); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="total-line final-total">
                                        <span>Total TTC :</span>
                                        <span><?php echo format_price($order['total']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php elseif ($action === 'edit'): ?>
            <!-- Modification du statut de la commande -->
            <div class="order-edit-section">
                <h2>✏️ Modifier la commande #<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></h2>
                
                <div class="status-update-form">
                    <form method="post" action="">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <input type="hidden" name="update_status" value="1">
                        
                        <div class="form-group">
                            <label for="status">Nouveau statut :</label>
                            <select id="status" name="status" required>
                                <option value="en_attente" <?php echo ($order['statut'] === 'en_attente') ? 'selected' : ''; ?>>⏳ En attente</option>
                                <option value="confirmee" <?php echo ($order['statut'] === 'confirmee') ? 'selected' : ''; ?>>✅ Confirmée</option>
                                <option value="preparee" <?php echo ($order['statut'] === 'preparee') ? 'selected' : ''; ?>>📦 Préparée</option>
                                <option value="livree" <?php echo ($order['statut'] === 'livree') ? 'selected' : ''; ?>>🚚 Livrée</option>
                                <option value="annulee" <?php echo ($order['statut'] === 'annulee') ? 'selected' : ''; ?>>❌ Annulée</option>
                            </select>
                        </div>
                        
                        <div class="status-info">
                            <h4>Statut actuel : 
                                <span class="status-badge status-<?php echo $order['statut']; ?>">
                                    <?php
                                    $status_labels = [
                                        'en_attente' => '⏳ En attente',
                                        'confirmee' => '✅ Confirmée',
                                        'preparee' => '📦 Préparée',
                                        'livree' => '🚚 Livrée',
                                        'annulee' => '❌ Annulée'
                                    ];
                                    echo $status_labels[$order['statut']] ?? $order['statut'];
                                    ?>
                                </span>
                            </h4>
                            <p><strong>⚠️ Attention :</strong> Si vous annulez cette commande, les produits seront remis en stock automatiquement.</p>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-large">💾 Mettre à jour le statut</button>
                            <a href="?action=view&id=<?php echo $order['id']; ?>" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
                
                <!-- Résumé de la commande -->
                <div class="order-summary">
                    <h3>Résumé de la commande</h3>
                    <div class="summary-content">
                        <p><strong>Client :</strong> <?php echo htmlspecialchars($order['prenom_client'] . ' ' . $order['nom_client']); ?></p>
                        <p><strong>Email :</strong> <?php echo htmlspecialchars($order['email_client']); ?></p>
                        <p><strong>Date de commande :</strong> <?php echo date('d/m/Y à H:i', strtotime($order['date_commande'])); ?></p>
                        <p><strong>Total :</strong> <?php echo format_price($order['total']); ?></p>
                        <p><strong>Nombre de produits :</strong> <?php echo count($order_products); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.admin-header {
    background: linear-gradient(135deg, #9C27B0, #7B1FA2);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.admin-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.orders-stats {
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border-left: 4px solid var(--primary-color);
}

.stat-card.pending { border-left-color: #ff9800; }
.stat-card.confirmed { border-left-color: #4caf50; }
.stat-card.delivered { border-left-color: #2196f3; }
.stat-card.revenue { border-left-color: #9c27b0; }

.stat-icon {
    font-size: 2rem;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--text-color);
}

.stat-label {
    color: var(--text-light);
    font-size: 0.85rem;
}

.filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.admin-filters {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto auto;
    gap: 1rem;
    align-items: end;
}

.order-row {
    transition: all 0.3s ease;
}

.order-row:hover {
    background: var(--bg-light);
}

.order-id strong {
    color: var(--primary-color);
    font-family: monospace;
}

.client-info {
    font-size: 0.9rem;
}

.client-name {
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.client-email {
    color: var(--text-light);
    font-size: 0.8rem;
}

.client-phone {
    color: var(--text-light);
    font-size: 0.8rem;
}

.order-date {
    font-size: 0.85rem;
}

.date-commande {
    font-weight: bold;
}

.time-commande {
    color: var(--text-light);
}

.date-livraison {
    color: var(--primary-color);
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.products-count .badge {
    background: var(--bg-light);
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
}

.order-total {
    font-weight: bold;
    color: var(--primary-color);
    font-size: 1.1rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
    display: inline-block;
}

.status-en_attente { background: #fff3cd; color: #856404; }
.status-confirmee { background: #d4edda; color: #155724; }
.status-preparee { background: #cce5ff; color: #004085; }
.status-livree { background: #d4edda; color: #155724; }
.status-annulee { background: #f8d7da; color: #721c24; }

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.order-view-section {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-large);
    box-shadow: var(--shadow);
}

.order-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.order-meta {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
    flex-wrap: wrap;
}

.order-date-badge {
    background: var(--bg-light);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.order-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.order-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.client-section,
.delivery-section {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius);
}

.client-section h3,
.delivery-section h3 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.detail-item {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.detail-item label {
    font-weight: bold;
    color: var(--text-light);
}

.notes-section {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius);
    margin-bottom: 2rem;
}

.order-notes {
    background: white;
    padding: 1rem;
    border-radius: var(--radius);
    border-left: 4px solid var(--primary-color);
}

.products-section {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius);
}

.order-products {
    display: grid;
    gap: 1rem;
    margin: 1rem 0;
}

.product-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: var(--radius);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: var(--radius);
}

.no-product-image {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    border-radius: var(--radius);
    font-size: 1.5rem;
    color: #ccc;
}

.product-info {
    flex: 1;
}

.product-info h4 {
    margin-bottom: 0.5rem;
}

.product-details {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
}

.quantity {
    color: var(--text-color);
    font-weight: bold;
}

.unit-price {
    color: var(--text-light);
}

.total-price {
    color: var(--primary-color);
    font-weight: bold;
}

.order-total-section {
    border-top: 2px solid var(--border-color);
    padding-top: 1rem;
    margin-top: 1rem;
}

.total-breakdown {
    max-width: 300px;
    margin-left: auto;
}

.total-line {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
}

.final-total {
    border-top: 1px solid var(--border-color);
    font-weight: bold;
    font-size: 1.1rem;
    color: var(--primary-color);
}

.order-edit-section {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-large);
    box-shadow: var(--shadow);
}

.status-update-form {
    background: var(--bg-light);
    padding: 2rem;
    border-radius: var(--radius);
    margin-bottom: 2rem;
}

.status-info {
    background: #fff3cd;
    padding: 1rem;
    border-radius: var(--radius);
    margin: 1rem 0;
    border-left: 4px solid #ffc107;
}

.order-summary {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius);
}

.summary-content {
    margin-top: 1rem;
}

.table-stats {
    margin-top: 1rem;
    color: var(--text-light);
    text-align: center;
}

@media (max-width: 768px) {
    .admin-filters {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .order-grid {
        grid-template-columns: 1fr;
    }
    
    .product-details {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .detail-item {
        grid-template-columns: 1fr;
        gap: 0.25rem;
    }
    
    .admin-actions {
        justify-content: center;
    }
}

@media print {
    .admin-header,
    .filters-section,
    .order-actions,
    .admin-actions {
        display: none;
    }
    
    .order-view-section {
        box-shadow: none;
        padding: 0;
    }
}
</style>

<?php include '../includes/footer.php'; ?>