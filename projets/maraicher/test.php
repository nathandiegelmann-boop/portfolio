<?php
/**
 * Script de test pour vérifier l'installation du site maraîcher
 */

echo "<h1>🧪 Test d'installation - Site Maraîcher</h1>";

// Test de la configuration
echo "<h2>1. Configuration</h2>";
if (file_exists('../includes/config.php')) {
    require_once '../includes/config.php';
    echo "✅ Fichier de configuration trouvé<br>";
    
    // Test de la connexion à la base de données
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM produits");
        $result = $stmt->fetch();
        echo "✅ Connexion à la base de données: OK<br>";
        echo "📦 Produits en base: " . $result['count'] . "<br>";
    } catch (Exception $e) {
        echo "❌ Erreur de connexion: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Fichier de configuration manquant<br>";
}

// Test des images
echo "<h2>2. Images des produits</h2>";
$images_required = [
    'tomates-cerises.jpg',
    'courgettes.jpg',
    'carottes.jpg',
    'pommes-golden.jpg',
    'fraises.jpg',
    'panier-decouverte.jpg',
    'basilic.jpg',
    'salade-bio.jpg'
];

$images_path = '../assets/uploads/';
foreach ($images_required as $image) {
    if (file_exists($images_path . $image)) {
        $size = round(filesize($images_path . $image) / 1024, 1);
        echo "✅ $image ($size KB)<br>";
    } else {
        echo "❌ $image manquante<br>";
    }
}

// Test des fonctionnalités
echo "<h2>3. Fonctionnalités</h2>";

if (function_exists('session_start')) {
    echo "✅ Sessions PHP: OK<br>";
} else {
    echo "❌ Sessions PHP: Erreur<br>";
}

if (class_exists('PDO')) {
    echo "✅ PDO (Base de données): OK<br>";
} else {
    echo "❌ PDO: Non disponible<br>";
}

if (function_exists('password_hash')) {
    echo "✅ Hachage des mots de passe: OK<br>";
} else {
    echo "❌ Fonctions de hachage: Non disponibles<br>";
}

// Test des permissions
echo "<h2>4. Permissions des dossiers</h2>";

if (is_writable('../assets/uploads/')) {
    echo "✅ Dossier uploads: Accessible en écriture<br>";
} else {
    echo "⚠️ Dossier uploads: Permissions à vérifier<br>";
}

// Test de la base de données
echo "<h2>5. Données de test</h2>";

if (isset($pdo)) {
    try {
        // Vérifier les catégories
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
        $categories = $stmt->fetch();
        echo "📂 Catégories: " . $categories['count'] . "<br>";
        
        // Vérifier l'utilisateur admin
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $admin = $stmt->fetch();
        echo "👤 Administrateurs: " . $admin['count'] . "<br>";
        
        // Vérifier les commandes d'exemple
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM commandes");
        $orders = $stmt->fetch();
        echo "🛒 Commandes d'exemple: " . $orders['count'] . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Erreur lors de la vérification: " . $e->getMessage() . "<br>";
    }
}

// Liens de test
echo "<h2>6. Navigation de test</h2>";
echo "<a href='../index.php' target='_blank'>🏠 Page d'accueil</a><br>";
echo "<a href='../catalogue.php' target='_blank'>📋 Catalogue des produits</a><br>";
echo "<a href='../client/panier.php' target='_blank'>🛒 Panier</a><br>";
echo "<a href='../admin/login.php' target='_blank'>🔐 Administration</a><br>";

echo "<h2>✅ Test terminé</h2>";
echo "<p><strong>Identifiants admin par défaut:</strong><br>";
echo "Email: admin@maraicher.local<br>";
echo "Mot de passe: admin123</p>";

echo "<style>
body { font-family: Arial, sans-serif; margin: 40px; }
h1 { color: #4CAF50; }
h2 { color: #2E7D32; border-bottom: 2px solid #4CAF50; padding-bottom: 5px; }
a { color: #4CAF50; text-decoration: none; margin-right: 20px; }
a:hover { text-decoration: underline; }
</style>";
?>