<?php
/**
 * Configuration de la base de données
 * Site de précommande pour maraîcher
 */

// Configuration de la base de données
$host = 'localhost';
$dbname = 'maraicher';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Configuration du site
define('SITE_NAME', 'Maraîcher Bio - Précommandes');
define('SITE_EMAIL', 'contact@maraicher.local');
define('UPLOADS_PATH', 'assets/uploads/');
define('UPLOADS_DIR', __DIR__ . '/../assets/uploads/');

// Paramètres généraux
define('FRAIS_LIVRAISON', 3.50);
define('COMMANDE_MIN', 10.00); // Montant minimum de commande

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fonction pour nettoyer les données d'entrée
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Fonction pour vérifier si l'utilisateur est connecté
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Fonction pour vérifier si l'utilisateur est admin
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Fonction pour rediriger
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Fonction pour afficher les messages d'erreur ou de succès
 */
function display_message($type = 'info') {
    if (isset($_SESSION['message'])) {
        $class = '';
        switch ($type) {
            case 'error':
                $class = 'alert-error';
                break;
            case 'success':
                $class = 'alert-success';
                break;
            default:
                $class = 'alert-info';
        }
        
        echo '<div class="alert ' . $class . '">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
    }
}

/**
 * Fonction pour calculer le total du panier
 */
function calculate_cart_total($cart) {
    global $pdo;
    $total = 0;
    
    if (!empty($cart)) {
        foreach ($cart as $product_id => $quantity) {
            $stmt = $pdo->prepare("SELECT prix FROM produits WHERE id = ? AND actif = 1");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if ($product) {
                $total += $product['prix'] * $quantity;
            }
        }
    }
    
    return $total;
}

/**
 * Fonction pour formater le prix
 */
function format_price($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Fonction pour générer un numéro de commande unique
 */
function generate_order_number() {
    return 'CMD' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
}
?>