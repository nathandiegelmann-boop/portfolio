<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Vérifier que l'utilisateur est connecté et admin
if (!is_logged_in() || !is_admin()) {
    redirect('login.php');
}

$page_title = 'Gestion des Produits - Administration';

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$errors = [];

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = clean_input($_POST['nom'] ?? '');
    $description = clean_input($_POST['description'] ?? '');
    $prix = (float)($_POST['prix'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $categorie_id = (int)($_POST['categorie_id'] ?? 0);
    $actif = isset($_POST['actif']) ? 1 : 0;
    
    // Validation
    if (empty($nom)) $errors[] = 'Le nom du produit est obligatoire';
    if (empty($description)) $errors[] = 'La description est obligatoire';
    if ($prix <= 0) $errors[] = 'Le prix doit être supérieur à 0';
    if ($stock < 0) $errors[] = 'Le stock ne peut pas être négatif';
    if ($categorie_id <= 0) $errors[] = 'Veuillez sélectionner une catégorie';
    
    if (empty($errors)) {
        if ($action === 'add') {
            // Gestion de l'upload d'image
            $image_filename = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = upload_image($_FILES['image']);
                if ($upload_result['success']) {
                    $image_filename = $upload_result['filename'];
                } else {
                    $errors[] = $upload_result['message'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO produits (nom, description, prix, stock, categorie_id, image, actif) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$nom, $description, $prix, $stock, $categorie_id, $image_filename, $actif]);
                    $_SESSION['message'] = 'Produit ajouté avec succès';
                    redirect('produits.php');
                } catch (Exception $e) {
                    $errors[] = 'Erreur lors de l\'ajout : ' . $e->getMessage();
                }
            }
        } elseif ($action === 'edit' && $product_id > 0) {
            // Gestion de l'upload d'image (optionnel pour la modification)
            $image_update = '';
            $image_params = [];
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = upload_image($_FILES['image']);
                if ($upload_result['success']) {
                    $image_update = ', image = ?';
                    $image_params[] = $upload_result['filename'];
                    
                    // Supprimer l'ancienne image
                    $stmt = $pdo->prepare("SELECT image FROM produits WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $old_product = $stmt->fetch();
                    if ($old_product && $old_product['image'] && file_exists(UPLOADS_DIR . $old_product['image'])) {
                        unlink(UPLOADS_DIR . $old_product['image']);
                    }
                } else {
                    $errors[] = $upload_result['message'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $sql = "UPDATE produits SET nom = ?, description = ?, prix = ?, stock = ?, categorie_id = ?, actif = ?" . $image_update . " WHERE id = ?";
                    $params = array_merge([$nom, $description, $prix, $stock, $categorie_id, $actif], $image_params, [$product_id]);
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $_SESSION['message'] = 'Produit modifié avec succès';
                    redirect('produits.php');
                } catch (Exception $e) {
                    $errors[] = 'Erreur lors de la modification : ' . $e->getMessage();
                }
            }
        }
    }
}

// Suppression d'un produit
if ($action === 'delete' && $product_id > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Vérifier que le produit n'est pas dans des commandes
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM commande_produits WHERE produit_id = ?");
        $stmt->execute([$product_id]);
        $orders_count = $stmt->fetchColumn();
        
        if ($orders_count > 0) {
            // Ne pas supprimer, juste désactiver
            $stmt = $pdo->prepare("UPDATE produits SET actif = 0 WHERE id = ?");
            $stmt->execute([$product_id]);
            $_SESSION['message'] = 'Produit désactivé (présent dans des commandes)';
        } else {
            // Supprimer l'image
            $stmt = $pdo->prepare("SELECT image FROM produits WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            if ($product && $product['image'] && file_exists(UPLOADS_DIR . $product['image'])) {
                unlink(UPLOADS_DIR . $product['image']);
            }
            
            // Supprimer le produit
            $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
            $stmt->execute([$product_id]);
            $_SESSION['message'] = 'Produit supprimé avec succès';
        }
        redirect('produits.php');
    } catch (Exception $e) {
        $_SESSION['message'] = 'Erreur lors de la suppression : ' . $e->getMessage();
        redirect('produits.php');
    }
}

// Récupération des données selon l'action
$product = null;
$categories = [];

// Récupération des catégories pour les formulaires
$stmt = $pdo->query("SELECT id, nom FROM categories WHERE actif = 1 ORDER BY nom");
$categories = $stmt->fetchAll();

// Récupération du produit pour édition
if (($action === 'edit' || $action === 'view') && $product_id > 0) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nom as categorie_nom 
        FROM produits p 
        LEFT JOIN categories c ON p.categorie_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        $_SESSION['message'] = 'Produit non trouvé';
        redirect('produits.php');
    }
}

// Liste des produits pour la vue principale
$products = [];
if ($action === 'list') {
    $search = $_GET['search'] ?? '';
    $category_filter = (int)($_GET['category'] ?? 0);
    $status_filter = $_GET['status'] ?? 'all';
    
    $where_conditions = ['1=1'];
    $params = [];
    
    if (!empty($search)) {
        $where_conditions[] = '(p.nom LIKE ? OR p.description LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($category_filter > 0) {
        $where_conditions[] = 'p.categorie_id = ?';
        $params[] = $category_filter;
    }
    
    if ($status_filter === 'active') {
        $where_conditions[] = 'p.actif = 1';
    } elseif ($status_filter === 'inactive') {
        $where_conditions[] = 'p.actif = 0';
    } elseif ($status_filter === 'low_stock') {
        $where_conditions[] = 'p.stock <= 5 AND p.actif = 1';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    $stmt = $pdo->prepare("
        SELECT p.*, c.nom as categorie_nom,
               (SELECT COUNT(*) FROM commande_produits cp WHERE cp.produit_id = p.id) as nb_commandes
        FROM produits p 
        LEFT JOIN categories c ON p.categorie_id = c.id 
        WHERE $where_clause
        ORDER BY p.nom ASC
    ");
    $stmt->execute($params);
    $products = $stmt->fetchAll();
}

include '../includes/header.php';
?>

<section class="admin-header">
    <div class="container">
        <h1>📦 Gestion des Produits</h1>
        <div class="admin-actions">
            <?php if ($action !== 'add'): ?>
                <a href="?action=add" class="btn btn-primary">➕ Ajouter un produit</a>
            <?php endif; ?>
            <?php if ($action !== 'list'): ?>
                <a href="produits.php" class="btn btn-secondary">← Retour à la liste</a>
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
            <!-- Liste des produits -->
            <div class="products-management">
                <!-- Filtres -->
                <div class="filters-section">
                    <form method="get" action="" class="admin-filters">
                        <div class="filter-group">
                            <input type="text" name="search" placeholder="Rechercher un produit..." 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <select name="category">
                                <option value="0">Toutes les catégories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo ($category_filter == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <select name="status">
                                <option value="all" <?php echo ($status_filter === 'all') ? 'selected' : ''; ?>>Tous les statuts</option>
                                <option value="active" <?php echo ($status_filter === 'active') ? 'selected' : ''; ?>>Actifs</option>
                                <option value="inactive" <?php echo ($status_filter === 'inactive') ? 'selected' : ''; ?>>Inactifs</option>
                                <option value="low_stock" <?php echo ($status_filter === 'low_stock') ? 'selected' : ''; ?>>Stock faible</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <a href="produits.php" class="btn btn-outline">Réinitialiser</a>
                    </form>
                </div>
                
                <!-- Tableau des produits -->
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Stock</th>
                                <th>Statut</th>
                                <th>Commandes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $prod): ?>
                                    <tr class="<?php echo $prod['actif'] ? '' : 'inactive-row'; ?>">
                                        <td class="product-image-cell">
                                            <?php if ($prod['image']): ?>
                                                <img src="../assets/uploads/<?php echo htmlspecialchars($prod['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($prod['nom']); ?>"
                                                     class="product-thumbnail">
                                            <?php else: ?>
                                                <div class="no-image-thumb">📦</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($prod['nom']); ?></strong>
                                            <div class="product-description">
                                                <?php echo htmlspecialchars(substr($prod['description'], 0, 60)); ?>...
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($prod['categorie_nom'] ?? 'Aucune'); ?></td>
                                        <td class="price-cell"><?php echo format_price($prod['prix']); ?>/kg</td>
                                        <td class="stock-cell">
                                            <span class="stock-badge <?php echo $prod['stock'] <= 5 ? 'stock-low' : ($prod['stock'] <= 15 ? 'stock-medium' : 'stock-good'); ?>">
                                                <?php echo $prod['stock']; ?> kg
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $prod['actif'] ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo $prod['actif'] ? 'Actif' : 'Inactif'; ?>
                                            </span>
                                        </td>
                                        <td class="orders-cell"><?php echo $prod['nb_commandes']; ?></td>
                                        <td class="actions-cell">
                                            <div class="action-buttons">
                                                <a href="?action=view&id=<?php echo $prod['id']; ?>" 
                                                   class="btn btn-small btn-outline" title="Voir">👁️</a>
                                                <a href="?action=edit&id=<?php echo $prod['id']; ?>" 
                                                   class="btn btn-small btn-primary" title="Modifier">✏️</a>
                                                <form method="post" action="?action=delete&id=<?php echo $prod['id']; ?>" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                                    <button type="submit" class="btn btn-small btn-danger" title="Supprimer">🗑️</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="no-data">
                                        <?php if (!empty($search) || $category_filter > 0 || $status_filter !== 'all'): ?>
                                            Aucun produit ne correspond aux critères de recherche.
                                        <?php else: ?>
                                            Aucun produit enregistré. <a href="?action=add">Ajouter le premier produit</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-stats">
                    <p><?php echo count($products); ?> produit(s) affiché(s)</p>
                </div>
            </div>
            
        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- Formulaire d'ajout/modification -->
            <div class="product-form-section">
                <h2><?php echo $action === 'add' ? '➕ Ajouter un nouveau produit' : '✏️ Modifier le produit'; ?></h2>
                
                <form method="post" action="" enctype="multipart/form-data" class="product-form">
                    <div class="form-grid">
                        <div class="form-section">
                            <h3>Informations générales</h3>
                            
                            <div class="form-group">
                                <label for="nom">Nom du produit *</label>
                                <input type="text" id="nom" name="nom" required 
                                       value="<?php echo htmlspecialchars($product['nom'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description *</label>
                                <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="categorie_id">Catégorie *</label>
                                    <select id="categorie_id" name="categorie_id" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" 
                                                    <?php echo (isset($product) && $product['categorie_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['nom']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="actif" value="1" 
                                               <?php echo (!isset($product) || $product['actif']) ? 'checked' : ''; ?>>
                                        Produit actif
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h3>Prix et stock</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="prix">Prix au kg (€) *</label>
                                    <input type="number" id="prix" name="prix" step="0.01" min="0" required 
                                           value="<?php echo $product['prix'] ?? ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="stock">Stock disponible (kg) *</label>
                                    <input type="number" id="stock" name="stock" min="0" required 
                                           value="<?php echo $product['stock'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h3>Image du produit</h3>
                            
                            <?php if (isset($product) && $product['image']): ?>
                                <div class="current-image">
                                    <label>Image actuelle :</label>
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['nom']); ?>"
                                         class="current-product-image">
                                </div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="image"><?php echo isset($product) ? 'Nouvelle image (optionnel)' : 'Image du produit'; ?></label>
                                <input type="file" id="image" name="image" accept="image/*">
                                <small>Formats acceptés : JPG, PNG, WebP. Taille max : 5MB</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            <?php echo $action === 'add' ? '➕ Ajouter le produit' : '💾 Enregistrer les modifications'; ?>
                        </button>
                        <a href="produits.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
            
        <?php elseif ($action === 'view'): ?>
            <!-- Vue détaillée du produit -->
            <div class="product-view-section">
                <div class="product-details">
                    <div class="product-header">
                        <div class="product-image-large">
                            <?php if ($product['image']): ?>
                                <img src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['nom']); ?>">
                            <?php else: ?>
                                <div class="no-image-large">📦</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h2><?php echo htmlspecialchars($product['nom']); ?></h2>
                            <div class="product-meta">
                                <span class="category-tag"><?php echo htmlspecialchars($product['categorie_nom']); ?></span>
                                <span class="status-badge <?php echo $product['actif'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $product['actif'] ? 'Actif' : 'Inactif'; ?>
                                </span>
                            </div>
                            
                            <div class="price-stock">
                                <div class="price-display"><?php echo format_price($product['prix']); ?> / kg</div>
                                <div class="stock-display">
                                    <span class="stock-badge <?php echo $product['stock'] <= 5 ? 'stock-low' : ($product['stock'] <= 15 ? 'stock-medium' : 'stock-good'); ?>">
                                        Stock: <?php echo $product['stock']; ?> kg
                                    </span>
                                </div>
                            </div>
                            
                            <div class="product-actions">
                                <a href="?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-primary">✏️ Modifier</a>
                                <a href="../catalogue.php" target="_blank" class="btn btn-outline">👁️ Voir sur le site</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                    
                    <?php
                    // Statistiques du produit
                    $stmt = $pdo->prepare("
                        SELECT 
                            COUNT(cp.commande_id) as nb_commandes,
                            COALESCE(SUM(cp.quantite), 0) as total_vendu,
                            COALESCE(SUM(cp.quantite * cp.prix_unitaire), 0) as chiffre_affaires
                        FROM commande_produits cp
                        JOIN commandes c ON cp.commande_id = c.id
                        WHERE cp.produit_id = ? AND c.statut != 'annulee'
                    ");
                    $stmt->execute([$product['id']]);
                    $stats = $stmt->fetch();
                    ?>
                    
                    <div class="product-stats">
                        <h3>Statistiques de vente</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $stats['nb_commandes']; ?></div>
                                <div class="stat-label">Commandes</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $stats['total_vendu']; ?> kg</div>
                                <div class="stat-label">Quantité vendue</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo format_price($stats['chiffre_affaires']); ?></div>
                                <div class="stat-label">Chiffre d'affaires</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.admin-header {
    background: linear-gradient(135deg, #4CAF50, #45a049);
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
    grid-template-columns: 2fr 1fr 1fr auto auto;
    gap: 1rem;
    align-items: end;
}

.product-image-cell {
    width: 60px;
}

.product-thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: var(--radius);
}

.no-image-thumb {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    border-radius: var(--radius);
    font-size: 1.5rem;
}

.product-description {
    font-size: 0.85rem;
    color: var(--text-light);
    margin-top: 0.25rem;
}

.price-cell {
    font-weight: bold;
    color: var(--primary-color);
}

.stock-cell .stock-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.stock-good { background: #d4edda; color: #155724; }
.stock-medium { background: #fff3cd; color: #856404; }
.stock-low { background: #f8d7da; color: #721c24; }

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

.product-form {
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.current-image {
    margin-bottom: 1rem;
}

.current-product-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.product-view-section {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-large);
    box-shadow: var(--shadow);
}

.product-header {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.product-image-large img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.no-image-large {
    width: 100%;
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    border-radius: var(--radius);
    font-size: 6rem;
    color: #ccc;
}

.product-meta {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
}

.category-tag {
    background: var(--bg-light);
    color: var(--text-color);
    padding: 0.5rem 1rem;
    border-radius: var(--radius-large);
    font-size: 0.9rem;
}

.price-stock {
    display: flex;
    gap: 2rem;
    align-items: center;
    margin: 1.5rem 0;
}

.price-display {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
}

.product-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
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

.table-stats {
    margin-top: 1rem;
    color: var(--text-light);
    text-align: center;
}

@media (max-width: 768px) {
    .admin-filters {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .product-header {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-actions {
        justify-content: center;
    }
}
</style>

<?php include '../includes/footer.php'; ?>