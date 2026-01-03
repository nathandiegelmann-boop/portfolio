<?php
/**
 * Fonctions utilitaires pour le site maraîcher
 */

/**
 * Validation et sécurité
 */

// Validation email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validation téléphone français
function validate_phone($phone) {
    $pattern = '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/';
    return preg_match($pattern, $phone);
}

// Validation quantité
function validate_quantity($quantity) {
    return is_numeric($quantity) && $quantity > 0 && $quantity <= 999;
}

/**
 * Gestion du panier
 */

// Ajouter un produit au panier
function add_to_cart($product_id, $quantity) {
    global $pdo;
    
    // Vérifier si le produit existe et est actif
    $stmt = $pdo->prepare("SELECT id, nom, prix, stock FROM produits WHERE id = ? AND actif = 1");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        return ['success' => false, 'message' => 'Produit non trouvé'];
    }
    
    // Vérifier le stock disponible
    $current_cart_quantity = isset($_SESSION['panier'][$product_id]) ? $_SESSION['panier'][$product_id] : 0;
    $total_requested = $current_cart_quantity + $quantity;
    
    if ($total_requested > $product['stock']) {
        return ['success' => false, 'message' => 'Stock insuffisant'];
    }
    
    // Ajouter au panier
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
    
    $_SESSION['panier'][$product_id] = $total_requested;
    
    return ['success' => true, 'message' => 'Produit ajouté au panier'];
}

// Mettre à jour la quantité d'un produit dans le panier
function update_cart_item($product_id, $quantity) {
    global $pdo;
    
    if ($quantity <= 0) {
        unset($_SESSION['panier'][$product_id]);
        return ['success' => true, 'message' => 'Produit retiré du panier'];
    }
    
    // Vérifier le stock
    $stmt = $pdo->prepare("SELECT stock FROM produits WHERE id = ? AND actif = 1");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product || $quantity > $product['stock']) {
        return ['success' => false, 'message' => 'Stock insuffisant'];
    }
    
    $_SESSION['panier'][$product_id] = $quantity;
    return ['success' => true, 'message' => 'Panier mis à jour'];
}

// Supprimer un produit du panier
function remove_from_cart($product_id) {
    unset($_SESSION['panier'][$product_id]);
    return ['success' => true, 'message' => 'Produit retiré du panier'];
}

// Vider le panier
function clear_cart() {
    unset($_SESSION['panier']);
    return ['success' => true, 'message' => 'Panier vidé'];
}

// Obtenir les détails du panier
function get_cart_details() {
    global $pdo;
    
    if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
        return [];
    }
    
    $cart_items = [];
    $placeholders = str_repeat('?,', count($_SESSION['panier']) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT id, nom, prix, stock, image FROM produits WHERE id IN ($placeholders) AND actif = 1");
    $stmt->execute(array_keys($_SESSION['panier']));
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $quantity = $_SESSION['panier'][$product['id']];
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $product['prix'] * $quantity
        ];
    }
    
    return $cart_items;
}

/**
 * Gestion des commandes
 */

// Créer une nouvelle commande
function create_order($order_data, $cart_items) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Insérer la commande
        $stmt = $pdo->prepare("
            INSERT INTO commandes (user_id, nom_client, prenom_client, email_client, telephone_client, 
                                 total, adresse_livraison, date_livraison, methode_paiement, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $order_data['user_id'] ?? null,
            $order_data['nom'],
            $order_data['prenom'],
            $order_data['email'],
            $order_data['telephone'],
            $order_data['total'],
            $order_data['adresse'],
            $order_data['date_livraison'],
            $order_data['methode_paiement'],
            $order_data['notes'] ?? null
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // Insérer les produits de la commande et mettre à jour les stocks
        foreach ($cart_items as $item) {
            // Insérer le détail de commande
            $stmt = $pdo->prepare("
                INSERT INTO commande_produits (commande_id, produit_id, quantite, prix_unitaire) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $order_id,
                $item['product']['id'],
                $item['quantity'],
                $item['product']['prix']
            ]);
            
            // Mettre à jour le stock
            $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product']['id']]);
        }
        
        $pdo->commit();
        return ['success' => true, 'order_id' => $order_id];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Erreur lors de la création de la commande : ' . $e->getMessage()];
    }
}

/**
 * Utilitaires d'affichage
 */

// Générer les options pour un select de catégories
function get_categories_options($selected_id = null) {
    global $pdo;
    
    $stmt = $pdo->query("SELECT id, nom FROM categories WHERE actif = 1 ORDER BY nom");
    $categories = $stmt->fetchAll();
    
    $options = '<option value="">Toutes les catégories</option>';
    foreach ($categories as $category) {
        $selected = ($category['id'] == $selected_id) ? 'selected' : '';
        $options .= '<option value="' . $category['id'] . '" ' . $selected . '>' . 
                   htmlspecialchars($category['nom']) . '</option>';
    }
    
    return $options;
}

// Générer une pagination
function generate_pagination($current_page, $total_pages, $base_url, $params = []) {
    if ($total_pages <= 1) return '';
    
    $pagination = '<div class="pagination">';
    
    // Page précédente
    if ($current_page > 1) {
        $prev_params = array_merge($params, ['page' => $current_page - 1]);
        $prev_url = $base_url . '?' . http_build_query($prev_params);
        $pagination .= '<a href="' . $prev_url . '" class="prev">&laquo; Précédent</a>';
    }
    
    // Numéros de pages
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $page_params = array_merge($params, ['page' => $i]);
        $page_url = $base_url . '?' . http_build_query($page_params);
        $active = ($i == $current_page) ? ' class="active"' : '';
        $pagination .= '<a href="' . $page_url . '"' . $active . '>' . $i . '</a>';
    }
    
    // Page suivante
    if ($current_page < $total_pages) {
        $next_params = array_merge($params, ['page' => $current_page + 1]);
        $next_url = $base_url . '?' . http_build_query($next_params);
        $pagination .= '<a href="' . $next_url . '" class="next">Suivant &raquo;</a>';
    }
    
    $pagination .= '</div>';
    return $pagination;
}

/**
 * Utilitaires de fichiers
 */

// Upload sécurisé d'image
function upload_image($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Vérifications de base
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erreur lors de l\'upload du fichier'];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Type de fichier non autorisé. Utilisez JPG, PNG ou WebP'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Fichier trop volumineux (max 5MB)'];
    }
    
    // Vérifier l'extension du fichier
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_extensions)) {
        return ['success' => false, 'message' => 'Extension de fichier non autorisée'];
    }
    
    // Générer un nom unique avec timestamp
    $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
    $target_path = UPLOADS_DIR . $filename;
    
    // Créer le dossier si nécessaire
    if (!is_dir(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0755, true);
    }
    
    // Vérifier que c'est bien une image avec getimagesize
    if (!getimagesize($file['tmp_name'])) {
        return ['success' => false, 'message' => 'Le fichier n\'est pas une image valide'];
    }
    
    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de la sauvegarde du fichier'];
    }
}

/**
 * Fonctions de statistiques (pour l'admin)
 */

// Obtenir les statistiques des ventes
function get_sales_stats($period = 'month') {
    global $pdo;
    
    $date_condition = '';
    switch ($period) {
        case 'today':
            $date_condition = 'DATE(date_commande) = CURDATE()';
            break;
        case 'week':
            $date_condition = 'YEARWEEK(date_commande) = YEARWEEK(NOW())';
            break;
        case 'month':
            $date_condition = 'YEAR(date_commande) = YEAR(NOW()) AND MONTH(date_commande) = MONTH(NOW())';
            break;
        case 'year':
            $date_condition = 'YEAR(date_commande) = YEAR(NOW())';
            break;
    }
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_commandes, COALESCE(SUM(total), 0) as chiffre_affaires 
        FROM commandes 
        WHERE statut != 'annulee' AND $date_condition
    ");
    $stmt->execute();
    
    return $stmt->fetch();
}

// Obtenir les produits les plus vendus
function get_top_products($limit = 5) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT p.nom, SUM(cp.quantite) as total_vendu, SUM(cp.quantite * cp.prix_unitaire) as chiffre_affaires
        FROM commande_produits cp
        JOIN produits p ON cp.produit_id = p.id
        JOIN commandes c ON cp.commande_id = c.id
        WHERE c.statut != 'annulee'
        GROUP BY p.id, p.nom
        ORDER BY total_vendu DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    
    return $stmt->fetchAll();
}
?>