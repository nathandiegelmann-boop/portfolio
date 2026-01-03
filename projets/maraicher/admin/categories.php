<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Vérifier que l'utilisateur est connecté et admin
if (!is_logged_in() || !is_admin()) {
    redirect('login.php');
}

$page_title = 'Gestion des Catégories - Administration';

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$errors = [];

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = clean_input($_POST['nom'] ?? '');
    $description = clean_input($_POST['description'] ?? '');
    $actif = isset($_POST['actif']) ? 1 : 0;
    
    // Validation
    if (empty($nom)) $errors[] = 'Le nom de la catégorie est obligatoire';
    
    if (empty($errors)) {
        if ($action === 'add') {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (nom, description, actif) VALUES (?, ?, ?)");
                $stmt->execute([$nom, $description, $actif]);
                $_SESSION['message'] = 'Catégorie ajoutée avec succès';
                redirect('categories.php');
            } catch (Exception $e) {
                $errors[] = 'Erreur lors de l\'ajout : ' . $e->getMessage();
            }
        } elseif ($action === 'edit' && $category_id > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE categories SET nom = ?, description = ?, actif = ? WHERE id = ?");
                $stmt->execute([$nom, $description, $actif, $category_id]);
                $_SESSION['message'] = 'Catégorie modifiée avec succès';
                redirect('categories.php');
            } catch (Exception $e) {
                $errors[] = 'Erreur lors de la modification : ' . $e->getMessage();
            }
        }
    }
}

// Suppression d'une catégorie
if ($action === 'delete' && $category_id > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Vérifier s'il y a des produits dans cette catégorie
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM produits WHERE categorie_id = ?");
        $stmt->execute([$category_id]);
        $products_count = $stmt->fetchColumn();
        
        if ($products_count > 0) {
            $_SESSION['message'] = 'Impossible de supprimer : cette catégorie contient des produits';
        } else {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $_SESSION['message'] = 'Catégorie supprimée avec succès';
        }
        redirect('categories.php');
    } catch (Exception $e) {
        $_SESSION['message'] = 'Erreur lors de la suppression : ' . $e->getMessage();
        redirect('categories.php');
    }
}

// Récupération des données selon l'action
$category = null;

// Récupération de la catégorie pour édition
if (($action === 'edit' || $action === 'view') && $category_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();
    
    if (!$category) {
        $_SESSION['message'] = 'Catégorie non trouvée';
        redirect('categories.php');
    }
}

// Liste des catégories pour la vue principale
$categories = [];
if ($action === 'list') {
    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status'] ?? 'all';
    
    $where_conditions = ['1=1'];
    $params = [];
    
    if (!empty($search)) {
        $where_conditions[] = '(nom LIKE ? OR description LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($status_filter === 'active') {
        $where_conditions[] = 'actif = 1';
    } elseif ($status_filter === 'inactive') {
        $where_conditions[] = 'actif = 0';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    $stmt = $pdo->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM produits p WHERE p.categorie_id = c.id) as nb_produits,
               (SELECT COUNT(*) FROM produits p WHERE p.categorie_id = c.id AND p.actif = 1) as nb_produits_actifs
        FROM categories c 
        WHERE $where_clause
        ORDER BY c.nom ASC
    ");
    $stmt->execute($params);
    $categories = $stmt->fetchAll();
}

include '../includes/header.php';
?>

<section class="admin-header">
    <div class="container">
        <h1>📁 Gestion des Catégories</h1>
        <div class="admin-actions">
            <?php if ($action !== 'add'): ?>
                <a href="?action=add" class="btn btn-primary">➕ Ajouter une catégorie</a>
            <?php endif; ?>
            <?php if ($action !== 'list'): ?>
                <a href="categories.php" class="btn btn-secondary">← Retour à la liste</a>
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
            <!-- Liste des catégories -->
            <div class="categories-management">
                <!-- Filtres -->
                <div class="filters-section">
                    <form method="get" action="" class="admin-filters">
                        <div class="filter-group">
                            <input type="text" name="search" placeholder="Rechercher une catégorie..." 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <select name="status">
                                <option value="all" <?php echo ($status_filter === 'all') ? 'selected' : ''; ?>>Tous les statuts</option>
                                <option value="active" <?php echo ($status_filter === 'active') ? 'selected' : ''; ?>>Actives</option>
                                <option value="inactive" <?php echo ($status_filter === 'inactive') ? 'selected' : ''; ?>>Inactives</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <a href="categories.php" class="btn btn-outline">Réinitialiser</a>
                    </form>
                </div>
                
                <!-- Tableau des catégories -->
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Produits</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr class="<?php echo $cat['actif'] ? '' : 'inactive-row'; ?>">
                                        <td>
                                            <strong><?php echo htmlspecialchars($cat['nom']); ?></strong>
                                        </td>
                                        <td>
                                            <div class="category-description">
                                                <?php echo htmlspecialchars(substr($cat['description'], 0, 80)); ?>
                                                <?php if (strlen($cat['description']) > 80) echo '...'; ?>
                                            </div>
                                        </td>
                                        <td class="products-cell">
                                            <div class="products-info">
                                                <span class="total-products"><?php echo $cat['nb_produits']; ?> total</span>
                                                <span class="active-products"><?php echo $cat['nb_produits_actifs']; ?> actifs</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $cat['actif'] ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo $cat['actif'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <div class="action-buttons">
                                                <a href="?action=view&id=<?php echo $cat['id']; ?>" 
                                                   class="btn btn-small btn-outline" title="Voir">👁️</a>
                                                <a href="?action=edit&id=<?php echo $cat['id']; ?>" 
                                                   class="btn btn-small btn-primary" title="Modifier">✏️</a>
                                                <?php if ($cat['nb_produits'] == 0): ?>
                                                    <form method="post" action="?action=delete&id=<?php echo $cat['id']; ?>" 
                                                          style="display: inline;" 
                                                          onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                        <button type="submit" class="btn btn-small btn-danger" title="Supprimer">🗑️</button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-small btn-disabled" title="Catégorie non vide" disabled>🗑️</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="no-data">
                                        <?php if (!empty($search) || $status_filter !== 'all'): ?>
                                            Aucune catégorie ne correspond aux critères de recherche.
                                        <?php else: ?>
                                            Aucune catégorie enregistrée. <a href="?action=add">Ajouter la première catégorie</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-stats">
                    <p><?php echo count($categories); ?> catégorie(s) affichée(s)</p>
                </div>
            </div>
            
        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- Formulaire d'ajout/modification -->
            <div class="category-form-section">
                <h2><?php echo $action === 'add' ? '➕ Ajouter une nouvelle catégorie' : '✏️ Modifier la catégorie'; ?></h2>
                
                <form method="post" action="" class="category-form">
                    <div class="form-grid">
                        <div class="form-section">
                            <h3>Informations de la catégorie</h3>
                            
                            <div class="form-group">
                                <label for="nom">Nom de la catégorie *</label>
                                <input type="text" id="nom" name="nom" required 
                                       value="<?php echo htmlspecialchars($category['nom'] ?? ''); ?>"
                                       placeholder="ex: Légumes racines, Fruits de saison...">
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="4" 
                                          placeholder="Description détaillée de la catégorie (optionnel)"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="actif" value="1" 
                                           <?php echo (!isset($category) || $category['actif']) ? 'checked' : ''; ?>>
                                    Catégorie active
                                </label>
                                <small>Les catégories inactives ne sont pas visibles sur le site</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            <?php echo $action === 'add' ? '➕ Ajouter la catégorie' : '💾 Enregistrer les modifications'; ?>
                        </button>
                        <a href="categories.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
            
        <?php elseif ($action === 'view'): ?>
            <!-- Vue détaillée de la catégorie -->
            <div class="category-view-section">
                <div class="category-details">
                    <div class="category-header">
                        <div class="category-info">
                            <h2><?php echo htmlspecialchars($category['nom']); ?></h2>
                            <div class="category-meta">
                                <span class="status-badge <?php echo $category['actif'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $category['actif'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                            
                            <div class="category-actions">
                                <a href="?action=edit&id=<?php echo $category['id']; ?>" class="btn btn-primary">✏️ Modifier</a>
                                <a href="produits.php?category=<?php echo $category['id']; ?>" class="btn btn-outline">👁️ Voir les produits</a>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($category['description'])): ?>
                        <div class="category-description-full">
                            <h3>Description</h3>
                            <p><?php echo nl2br(htmlspecialchars($category['description'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    // Statistiques de la catégorie
                    $stmt = $pdo->prepare("
                        SELECT 
                            COUNT(*) as nb_produits,
                            COUNT(CASE WHEN actif = 1 THEN 1 END) as nb_produits_actifs,
                            COALESCE(SUM(stock), 0) as stock_total,
                            COALESCE(AVG(prix), 0) as prix_moyen
                        FROM produits 
                        WHERE categorie_id = ?
                    ");
                    $stmt->execute([$category['id']]);
                    $stats = $stmt->fetch();
                    ?>
                    
                    <div class="category-stats">
                        <h3>Statistiques de la catégorie</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $stats['nb_produits']; ?></div>
                                <div class="stat-label">Produits total</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $stats['nb_produits_actifs']; ?></div>
                                <div class="stat-label">Produits actifs</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $stats['stock_total']; ?> kg</div>
                                <div class="stat-label">Stock total</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo format_price($stats['prix_moyen']); ?></div>
                                <div class="stat-label">Prix moyen</div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($stats['nb_produits'] > 0): ?>
                        <?php
                        // Liste des produits de cette catégorie
                        $stmt = $pdo->prepare("
                            SELECT id, nom, prix, stock, actif, image
                            FROM produits 
                            WHERE categorie_id = ?
                            ORDER BY actif DESC, nom ASC
                            LIMIT 10
                        ");
                        $stmt->execute([$category['id']]);
                        $products = $stmt->fetchAll();
                        ?>
                        
                        <div class="category-products">
                            <h3>Produits de cette catégorie</h3>
                            <div class="products-preview">
                                <?php foreach ($products as $product): ?>
                                    <div class="product-preview-item <?php echo $product['actif'] ? '' : 'inactive'; ?>">
                                        <?php if ($product['image']): ?>
                                            <img src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['nom']); ?>"
                                                 class="product-preview-image">
                                        <?php else: ?>
                                            <div class="no-image-preview">📦</div>
                                        <?php endif; ?>
                                        <div class="product-preview-info">
                                            <h4><?php echo htmlspecialchars($product['nom']); ?></h4>
                                            <div class="product-preview-meta">
                                                <span class="price"><?php echo format_price($product['prix']); ?>/kg</span>
                                                <span class="stock">Stock: <?php echo $product['stock']; ?> kg</span>
                                            </div>
                                            <div class="product-preview-actions">
                                                <a href="produits.php?action=view&id=<?php echo $product['id']; ?>" class="btn btn-small btn-outline">Voir</a>
                                                <a href="produits.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-small btn-primary">Modifier</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($stats['nb_produits'] > 10): ?>
                                <div class="view-more">
                                    <a href="produits.php?category=<?php echo $category['id']; ?>" class="btn btn-outline">
                                        Voir tous les produits (<?php echo $stats['nb_produits']; ?>)
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.admin-header {
    background: linear-gradient(135deg, #FF9800, #F57C00);
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

.filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.admin-filters {
    display: grid;
    grid-template-columns: 2fr 1fr auto auto;
    gap: 1rem;
    align-items: end;
}

.category-description {
    color: var(--text-light);
    font-size: 0.9rem;
    line-height: 1.4;
}

.products-cell {
    font-size: 0.85rem;
}

.products-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.total-products {
    font-weight: bold;
    color: var(--text-color);
}

.active-products {
    color: var(--success-color);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.status-active { background: #d4edda; color: #155724; }
.status-inactive { background: #f8d7da; color: #721c24; }

.inactive-row {
    opacity: 0.6;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.btn-disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.category-form {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-large);
    box-shadow: var(--shadow);
}

.form-grid {
    display: grid;
    gap: 2rem;
}

.form-section {
    border: 1px solid var(--border-color);
    padding: 1.5rem;
    border-radius: var(--radius);
}

.form-section h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.category-view-section {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-large);
    box-shadow: var(--shadow);
}

.category-header {
    margin-bottom: 2rem;
}

.category-info h2 {
    margin-bottom: 1rem;
}

.category-meta {
    margin: 1rem 0;
}

.category-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.category-description-full {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--bg-light);
    border-radius: var(--radius);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
    margin-top: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1.5rem;
    background: var(--bg-light);
    border-radius: var(--radius);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
}

.stat-label {
    color: var(--text-light);
    margin-top: 0.5rem;
}

.category-products {
    margin-top: 2rem;
}

.products-preview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.product-preview-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-light);
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.product-preview-item:hover {
    box-shadow: var(--shadow);
    transform: translateY(-2px);
}

.product-preview-item.inactive {
    opacity: 0.6;
}

.product-preview-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: var(--radius);
}

.no-image-preview {
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

.product-preview-info {
    flex: 1;
}

.product-preview-info h4 {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.product-preview-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
    color: var(--text-light);
}

.product-preview-meta .price {
    color: var(--primary-color);
    font-weight: bold;
}

.product-preview-actions {
    display: flex;
    gap: 0.5rem;
}

.view-more {
    text-align: center;
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
        grid-template-columns: repeat(2, 1fr);
    }
    
    .products-preview {
        grid-template-columns: 1fr;
    }
    
    .product-preview-item {
        flex-direction: column;
    }
    
    .admin-actions {
        justify-content: center;
    }
}
</style>

<script>
function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php include '../includes/footer.php'; ?>