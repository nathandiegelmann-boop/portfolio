<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Catalogue des produits';
$page_description = 'Découvrez tous nos produits frais : légumes de saison, fruits, paniers garnis et aromates cultivés localement.';

// Paramètres de recherche et filtrage
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$category_filter = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$sort_by = isset($_GET['sort']) ? clean_input($_GET['sort']) : 'nom';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 12;

// Construction de la requête
$where_conditions = ['p.actif = 1'];
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

$where_clause = implode(' AND ', $where_conditions);

// Options de tri
$sort_options = [
    'nom' => 'p.nom ASC',
    'prix_asc' => 'p.prix ASC',
    'prix_desc' => 'p.prix DESC',
    'stock' => 'p.stock DESC',
    'nouveau' => 'p.date_creation DESC'
];

$order_by = isset($sort_options[$sort_by]) ? $sort_options[$sort_by] : 'p.nom ASC';

// Compter le total pour la pagination
$count_query = "
    SELECT COUNT(*) 
    FROM produits p 
    LEFT JOIN categories c ON p.categorie_id = c.id 
    WHERE $where_clause
";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_products = $stmt->fetchColumn();
$total_pages = ceil($total_products / $per_page);

// Récupération des produits avec pagination
$offset = ($page - 1) * $per_page;
$products_query = "
    SELECT p.*, c.nom as categorie_nom
    FROM produits p 
    LEFT JOIN categories c ON p.categorie_id = c.id 
    WHERE $where_clause
    ORDER BY $order_by
    LIMIT $per_page OFFSET $offset
";
$stmt = $pdo->prepare($products_query);
$stmt->execute($params);
$produits = $stmt->fetchAll();

// Récupération des catégories pour le filtre
$stmt = $pdo->query("SELECT * FROM categories WHERE actif = 1 ORDER BY nom");
$categories = $stmt->fetchAll();

include 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Notre Catalogue</h1>
        <p>Découvrez tous nos produits frais de saison</p>
    </div>
</section>

<section class="catalog-section">
    <div class="container">
        <!-- Filtres et recherche -->
        <div class="catalog-filters">
            <form method="get" action="" class="filters-form">
                <div class="filter-group">
                    <label for="search">Rechercher :</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Nom du produit...">
                </div>
                
                <div class="filter-group">
                    <label for="categorie">Catégorie :</label>
                    <select id="categorie" name="categorie">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo ($category['id'] == $category_filter) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sort">Trier par :</label>
                    <select id="sort" name="sort">
                        <option value="nom" <?php echo ($sort_by === 'nom') ? 'selected' : ''; ?>>Nom A-Z</option>
                        <option value="prix_asc" <?php echo ($sort_by === 'prix_asc') ? 'selected' : ''; ?>>Prix croissant</option>
                        <option value="prix_desc" <?php echo ($sort_by === 'prix_desc') ? 'selected' : ''; ?>>Prix décroissant</option>
                        <option value="stock" <?php echo ($sort_by === 'stock') ? 'selected' : ''; ?>>Stock disponible</option>
                        <option value="nouveau" <?php echo ($sort_by === 'nouveau') ? 'selected' : ''; ?>>Plus récents</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Filtrer</button>
                
                <?php if (!empty($search) || $category_filter > 0): ?>
                    <a href="catalogue.php" class="btn btn-outline">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Résultats -->
        <div class="catalog-results">
            <div class="results-info">
                <p><?php echo $total_products; ?> produit<?php echo $total_products > 1 ? 's' : ''; ?> trouvé<?php echo $total_products > 1 ? 's' : ''; ?></p>
                <?php if (!empty($search)): ?>
                    <p>Recherche pour : "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($produits)): ?>
                <div class="products-grid">
                    <?php foreach ($produits as $produit): ?>
                        <div class="product-card <?php echo $produit['stock'] <= 0 ? 'out-of-stock' : ''; ?>">
                            <div class="product-image">
                                <?php if ($produit['image']): ?>
                                    <img src="assets/uploads/<?php echo htmlspecialchars($produit['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="no-image">📦</div>
                                <?php endif; ?>
                                
                                <?php if ($produit['stock'] > 0): ?>
                                    <div class="product-badge available">Disponible</div>
                                <?php else: ?>
                                    <div class="product-badge out-of-stock">Rupture</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <div class="product-category">
                                    <?php echo htmlspecialchars($produit['categorie_nom'] ?? 'Divers'); ?>
                                </div>
                                <h3 class="product-title"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                                <p class="product-description">
                                    <?php echo htmlspecialchars($produit['description']); ?>
                                </p>
                                
                                <div class="product-details">
                                    <div class="product-price"><?php echo format_price($produit['prix']); ?>/kg</div>
                                    <div class="product-stock">
                                        <?php if ($produit['stock'] > 0): ?>
                                            Stock: <?php echo $produit['stock']; ?> kg
                                        <?php else: ?>
                                            <span class="stock-zero">Rupture de stock</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($produit['stock'] > 0): ?>
                                    <form method="post" action="client/panier.php" onsubmit="return addToCart(this)" class="add-to-cart-form">
                                        <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
                                        <div class="quantity-controls">
                                            <label for="quantite_<?php echo $produit['id']; ?>">Quantité (kg):</label>
                                            <input type="number" 
                                                   id="quantite_<?php echo $produit['id']; ?>"
                                                   name="quantite" 
                                                   min="0.5" 
                                                   max="<?php echo $produit['stock']; ?>" 
                                                   step="0.5" 
                                                   value="1"
                                                   class="quantity-input">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-full">
                                            🛒 Ajouter au panier
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-disabled btn-full" disabled>
                                        Produit indisponible
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination-wrapper">
                        <?php 
                        $pagination_params = [];
                        if (!empty($search)) $pagination_params['search'] = $search;
                        if ($category_filter > 0) $pagination_params['categorie'] = $category_filter;
                        if ($sort_by !== 'nom') $pagination_params['sort'] = $sort_by;
                        
                        echo generate_pagination($page, $total_pages, 'catalogue.php', $pagination_params);
                        ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h3>Aucun produit trouvé</h3>
                    <p>Essayez de modifier vos critères de recherche ou parcourez toutes nos catégories.</p>
                    <a href="catalogue.php" class="btn btn-primary">Voir tous les produits</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="catalog-info">
    <div class="container">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">🌱</div>
                <h4>Produits de saison</h4>
                <p>Nous privilégions les produits de saison pour vous garantir le meilleur goût et respecter les cycles naturels.</p>
            </div>
            <div class="info-item">
                <div class="info-icon">📦</div>
                <h4>Commande minimum</h4>
                <p>Commande minimum de <?php echo format_price(COMMANDE_MIN); ?> pour bénéficier de la livraison à domicile.</p>
            </div>
            <div class="info-item">
                <div class="info-icon">⏰</div>
                <h4>Fraîcheur garantie</h4>
                <p>Tous nos produits sont récoltés le matin même de votre commande pour une fraîcheur optimale.</p>
            </div>
        </div>
    </div>
</section>

<script>
// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('#categorie, #sort');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>