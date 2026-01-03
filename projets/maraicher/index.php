<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Accueil';
$page_description = 'Précommandez vos fruits et légumes frais directement chez votre maraîcher local. Produits de saison, cultivés avec passion.';

include 'includes/header.php';

// Récupération des produits en vedette (stock > 0)
$stmt = $pdo->prepare("
    SELECT p.*, c.nom as categorie_nom 
    FROM produits p 
    LEFT JOIN categories c ON p.categorie_id = c.id 
    WHERE p.actif = 1 AND p.stock > 0 
    ORDER BY p.date_creation DESC 
    LIMIT 6
");
$stmt->execute();
$produits_vedette = $stmt->fetchAll();

// Récupération des catégories actives
$stmt = $pdo->query("SELECT * FROM categories WHERE actif = 1 ORDER BY nom");
$categories = $stmt->fetchAll();
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h2>Fruits & Légumes frais<br><span class="highlight">de votre maraîcher local</span></h2>
            <p class="hero-text">
                Découvrez nos produits de saison, cultivés avec passion dans le respect de la nature. 
                Précommandez en ligne et récupérez vos produits frais directement à la ferme ou sur le marché.
            </p>
            <div class="hero-actions">
                <a href="catalogue.php" class="btn btn-primary btn-large">
                    🛒 Voir nos produits
                </a>
                <a href="#about" class="btn btn-secondary btn-large">
                    🌱 En savoir plus
                </a>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-badge">
                <span class="badge-text">100% Local</span>
                <span class="badge-subtext">Cultivé avec ❤️</span>
            </div>
        </div>
    </div>
</section>

<section class="categories-section">
    <div class="container">
        <h2 class="section-title">Nos catégories de produits</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $categorie): ?>
                <div class="category-card">
                    <div class="category-icon">
                        <?php
                        $icons = [
                            'Légumes de saison' => '🥕',
                            'Fruits' => '🍎',
                            'Paniers garnis' => '🧺',
                            'Bio' => '🌿',
                            'Aromates' => '🌱'
                        ];
                        echo $icons[$categorie['nom']] ?? '🥬';
                        ?>
                    </div>
                    <h3><?php echo htmlspecialchars($categorie['nom']); ?></h3>
                    <p><?php echo htmlspecialchars($categorie['description']); ?></p>
                    <a href="catalogue.php?categorie=<?php echo $categorie['id']; ?>" class="btn btn-outline">
                        Découvrir
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="featured-products">
    <div class="container">
        <h2 class="section-title">Produits en vedette</h2>
        <p class="section-subtitle">Nos derniers produits de saison, fraîchement récoltés</p>
        
        <?php if (!empty($produits_vedette)): ?>
            <div class="products-grid">
                <?php foreach ($produits_vedette as $produit): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($produit['image']): ?>
                                <img src="assets/uploads/<?php echo htmlspecialchars($produit['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                            <?php else: ?>
                                <div class="no-image">📦</div>
                            <?php endif; ?>
                            <div class="product-badge">
                                <span>Disponible</span>
                            </div>
                        </div>
                        
                        <div class="product-info">
                            <div class="product-category">
                                <?php echo htmlspecialchars($produit['categorie_nom'] ?? 'Divers'); ?>
                            </div>
                            <h3 class="product-title"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                            <p class="product-description">
                                <?php echo htmlspecialchars(substr($produit['description'], 0, 80)); ?>
                                <?php echo strlen($produit['description']) > 80 ? '...' : ''; ?>
                            </p>
                            
                            <div class="product-details">
                                <div class="product-price"><?php echo format_price($produit['prix']); ?>/kg</div>
                                <div class="product-stock">Stock: <?php echo $produit['stock']; ?> kg</div>
                            </div>
                            
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
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="section-actions">
                <a href="catalogue.php" class="btn btn-primary btn-large">
                    Voir tous nos produits
                </a>
            </div>
        <?php else: ?>
            <div class="no-products">
                <div class="no-products-icon">🌱</div>
                <h3>Aucun produit disponible actuellement</h3>
                <p>Nos nouveaux produits de saison arriveront bientôt. Revenez nous voir !</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="about" class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>Notre passion, votre assiette</h2>
                <p class="lead">
                    Depuis plus de 15 ans, nous cultivons avec passion nos fruits et légumes 
                    dans le respect de la nature et des saisons.
                </p>
                <div class="about-features">
                    <div class="feature">
                        <div class="feature-icon">🌱</div>
                        <div class="feature-text">
                            <h4>Agriculture responsable</h4>
                            <p>Pratiques respectueuses de l'environnement, sans pesticides chimiques</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">📍</div>
                        <div class="feature-text">
                            <h4>Circuit court</h4>
                            <p>De notre champ à votre assiette, sans intermédiaire</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">⏰</div>
                        <div class="feature-text">
                            <h4>Fraîcheur garantie</h4>
                            <p>Récolte le matin même, livraison dans la journée</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="about-image">
                <div class="stats-card">
                    <div class="stat">
                        <div class="stat-number">15+</div>
                        <div class="stat-label">années d'expérience</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">variétés cultivées</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">local & frais</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="info-section">
    <div class="container">
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">🚚</div>
                <h3>Livraison & Retrait</h3>
                <ul>
                    <li>Retrait à la ferme (gratuit)</li>
                    <li>Marchés: Mardi, Vendredi, Samedi</li>
                    <li>Livraison à domicile: <?php echo format_price(FRAIS_LIVRAISON); ?></li>
                    <li>Commande minimum: <?php echo format_price(COMMANDE_MIN); ?></li>
                </ul>
            </div>
            
            <div class="info-card">
                <div class="info-icon">💳</div>
                <h3>Paiement</h3>
                <ul>
                    <li>Espèces</li>
                    <li>Carte bancaire</li>
                    <li>Chèque</li>
                    <li>Paiement en ligne (sécurisé)</li>
                </ul>
            </div>
            
            <div class="info-card">
                <div class="info-icon">📞</div>
                <h3>Contact</h3>
                <ul>
                    <li>📧 <?php echo SITE_EMAIL; ?></li>
                    <li>📞 01 23 45 67 89</li>
                    <li>📍 123 Chemin des Champs<br>75000 Paris</li>
                    <li>🕒 Lun-Sam: 8h-18h</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>