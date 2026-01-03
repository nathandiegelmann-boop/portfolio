<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Vérifier que l'utilisateur est connecté et admin
if (!is_logged_in() || !is_admin()) {
    redirect('login.php');
}

$page_title = 'Tableau de bord - Administration';

// Récupération des statistiques
$stats_today = get_sales_stats('today');
$stats_week = get_sales_stats('week');
$stats_month = get_sales_stats('month');
$top_products = get_top_products(5);

// Commandes récentes
$stmt = $pdo->prepare("
    SELECT c.*, COUNT(cp.produit_id) as nb_produits 
    FROM commandes c 
    LEFT JOIN commande_produits cp ON c.id = cp.commande_id 
    GROUP BY c.id 
    ORDER BY c.date_commande DESC 
    LIMIT 10
");
$stmt->execute();
$recent_orders = $stmt->fetchAll();

// Produits en stock faible
$stmt = $pdo->query("
    SELECT nom, stock FROM produits 
    WHERE actif = 1 AND stock <= 5 
    ORDER BY stock ASC
");
$low_stock_products = $stmt->fetchAll();

include '../includes/header.php';
?>

<section class="admin-header">
    <div class="container">
        <h1>📊 Tableau de bord</h1>
        <p>Vue d'ensemble de votre activité</p>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-content">
                    <h3>Aujourd'hui</h3>
                    <div class="stat-number"><?php echo $stats_today['total_commandes']; ?></div>
                    <div class="stat-label">commandes</div>
                    <div class="stat-value"><?php echo format_price($stats_today['chiffre_affaires']); ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <h3>Cette semaine</h3>
                    <div class="stat-number"><?php echo $stats_week['total_commandes']; ?></div>
                    <div class="stat-label">commandes</div>
                    <div class="stat-value"><?php echo format_price($stats_week['chiffre_affaires']); ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🗓️</div>
                <div class="stat-content">
                    <h3>Ce mois</h3>
                    <div class="stat-number"><?php echo $stats_month['total_commandes']; ?></div>
                    <div class="stat-label">commandes</div>
                    <div class="stat-value"><?php echo format_price($stats_month['chiffre_affaires']); ?></div>
                </div>
            </div>
            
            <div class="stat-card alert-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <h3>Stocks faibles</h3>
                    <div class="stat-number"><?php echo count($low_stock_products); ?></div>
                    <div class="stat-label">produits</div>
                    <div class="stat-value">à réapprovisionner</div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-content">
            <!-- Recent Orders -->
            <div class="dashboard-section">
                <h2>Commandes récentes</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Nb Produits</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_orders)): ?>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($order['prenom_client'] . ' ' . $order['nom_client']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['date_commande'])); ?></td>
                                        <td><?php echo $order['nb_produits']; ?></td>
                                        <td><?php echo format_price($order['total']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['statut']; ?>">
                                                <?php 
                                                $statuses = [
                                                    'en_attente' => 'En attente',
                                                    'validee' => 'Validée',
                                                    'prete' => 'Prête',
                                                    'livree' => 'Livrée'
                                                ];
                                                echo $statuses[$order['statut']] ?? $order['statut'];
                                                ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <a href="commandes.php?action=view&id=<?php echo $order['id']; ?>" class="btn btn-small btn-outline">Voir</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="no-data">Aucune commande récente</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="section-footer">
                    <a href="commandes.php" class="btn btn-primary">Voir toutes les commandes</a>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <!-- Top Products -->
                <div class="dashboard-widget">
                    <h3>Produits les plus vendus</h3>
                    <div class="top-products">
                        <?php if (!empty($top_products)): ?>
                            <?php foreach ($top_products as $index => $product): ?>
                                <div class="product-rank">
                                    <div class="rank-number"><?php echo $index + 1; ?></div>
                                    <div class="product-info">
                                        <div class="product-name"><?php echo htmlspecialchars($product['nom']); ?></div>
                                        <div class="product-stats">
                                            <?php echo $product['total_vendu']; ?> kg vendus
                                            • <?php echo format_price($product['chiffre_affaires']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-data">Aucune vente enregistrée</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Low Stock Alert -->
                <div class="dashboard-widget alert-widget">
                    <h3>⚠️ Alertes stock</h3>
                    <div class="stock-alerts">
                        <?php if (!empty($low_stock_products)): ?>
                            <?php foreach ($low_stock_products as $product): ?>
                                <div class="stock-alert">
                                    <div class="product-name"><?php echo htmlspecialchars($product['nom']); ?></div>
                                    <div class="stock-level stock-critical"><?php echo $product['stock']; ?> kg restant</div>
                                </div>
                            <?php endforeach; ?>
                            <div class="widget-footer">
                                <a href="produits.php" class="btn btn-warning btn-small">Gérer les stocks</a>
                            </div>
                        <?php else: ?>
                            <p class="no-data">✅ Tous les stocks sont corrects</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="quick-actions">
    <div class="container">
        <h2>Actions rapides</h2>
        <div class="actions-grid">
            <a href="produits.php?action=add" class="action-card">
                <div class="action-icon">➕</div>
                <h3>Ajouter un produit</h3>
                <p>Référencer un nouveau produit dans le catalogue</p>
            </a>
            
            <a href="commandes.php?status=en_attente" class="action-card">
                <div class="action-icon">📋</div>
                <h3>Commandes en attente</h3>
                <p>Traiter les nouvelles commandes clients</p>
            </a>
            
            <a href="categories.php" class="action-card">
                <div class="action-icon">📁</div>
                <h3>Gérer les catégories</h3>
                <p>Organiser les produits par catégorie</p>
            </a>
            
            <a href="users.php" class="action-card">
                <div class="action-icon">👥</div>
                <h3>Gestion utilisateurs</h3>
                <p>Administrer les comptes clients</p>
            </a>
            
            <a href="produits.php" class="action-card">
                <div class="action-icon">📦</div>
                <h3>Gérer les stocks</h3>
                <p>Mettre à jour les quantités disponibles</p>
            </a>
            
            <a href="../catalogue.php" target="_blank" class="action-card">
                <div class="action-icon">👁️</div>
                <h3>Voir le site</h3>
                <p>Afficher le site tel que le voient les clients</p>
            </a>
        </div>
    </div>
</section>

<style>
.admin-header {
    background: linear-gradient(135deg, #4CAF50, #45a049);
    color: white;
    padding: 30px 0;
    margin-bottom: 30px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
}

.stat-card.alert-card {
    background: linear-gradient(135deg, #ff9800, #f57c00);
    color: white;
}

.stat-icon {
    font-size: 2.5rem;
}

.stat-content h3 {
    margin: 0 0 10px 0;
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
}

.alert-card .stat-content h3 {
    color: rgba(255,255,255,0.9);
}

.stat-number {
    font-size: 2.2rem;
    font-weight: bold;
    color: #4CAF50;
    margin-bottom: 5px;
}

.alert-card .stat-number {
    color: white;
}

.stat-label {
    font-size: 0.9rem;
    color: #888;
    margin-bottom: 5px;
}

.alert-card .stat-label {
    color: rgba(255,255,255,0.9);
}

.stat-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: #4CAF50;
}

.alert-card .stat-value {
    color: white;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.admin-table th,
.admin-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.admin-table tbody tr:hover {
    background: #f8f9fa;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-en_attente { background: #fff3cd; color: #856404; }
.status-validee { background: #d1ecf1; color: #0c5460; }
.status-prete { background: #d4edda; color: #155724; }
.status-livree { background: #d4edda; color: #155724; }

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.dashboard-widget {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.alert-widget {
    border-left: 4px solid #ff9800;
}

.product-rank {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.product-rank:last-child {
    border-bottom: none;
}

.rank-number {
    width: 30px;
    height: 30px;
    background: #4CAF50;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.product-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.product-stats {
    font-size: 0.9rem;
    color: #666;
}

.stock-alert {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.stock-alert:last-child {
    border-bottom: none;
}

.stock-level {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.stock-critical {
    background: #ffebee;
    color: #c62828;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
}

.action-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    text-decoration: none;
    color: #333;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    text-align: center;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    text-decoration: none;
    color: #333;
}

.action-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.action-card h3 {
    margin: 0 0 10px 0;
    color: #4CAF50;
}

.action-card p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.no-data {
    text-align: center;
    color: #888;
    font-style: italic;
    padding: 20px;
}

.section-footer {
    padding: 15px 0 0 0;
    text-align: center;
}

.widget-footer {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}
</style>

<?php include '../includes/footer.php'; ?>