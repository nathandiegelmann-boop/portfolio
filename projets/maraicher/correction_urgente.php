<?php
// CORRECTION URGENTE - Remplacer les mauvaises images
require_once 'includes/config.php';

echo "<h2>🚨 CORRECTION URGENTE DES IMAGES</h2>\n";

// Vérifier d'abord quelles images posent problème
$stmt = $pdo->query("SELECT id, nom, image FROM produits WHERE nom LIKE '%courgette%' OR nom LIKE '%Courgette%'");
$courgettes = $stmt->fetchAll();

echo "<h3>🔍 État actuel des courgettes :</h3>\n";
foreach ($courgettes as $produit) {
    echo "<p><strong>ID {$produit['id']}</strong>: {$produit['nom']} → {$produit['image']}</p>\n";
}

// Vérifier si on a de vraies images de courgettes
$vraiImages = [
    'courgette.jpg',
    'courgettes.jpg' 
];

echo "<h3>📁 Vérification des vraies images de courgettes :</h3>\n";
foreach ($vraiImages as $image) {
    $path = 'assets/uploads/' . $image;
    if (file_exists($path)) {
        $size = round(filesize($path) / 1024, 1);
        echo "<p>✅ {$image} existe ({$size}KB)</p>\n";
        echo "<img src='{$path}' style='width: 150px; height: 100px; object-fit: cover; margin: 10px; border: 2px solid green;'><br>\n";
    } else {
        echo "<p>❌ {$image} manquante</p>\n";
    }
}

// CORRECTION FORCÉE
echo "<h3>🔧 CORRECTION EN COURS...</h3>\n";

try {
    // Forcer courgette.jpg comme bonne image pour toutes les courgettes  
    $stmt = $pdo->prepare("UPDATE produits SET image = 'courgette.jpg' WHERE nom LIKE '%courgette%' OR nom LIKE '%Courgette%'");
    $result = $stmt->execute();
    
    if ($result) {
        echo "<p style='color: green; font-weight: bold;'>✅ CORRECTION RÉUSSIE !</p>\n";
        
        // Vérifier le résultat
        $stmt = $pdo->query("SELECT id, nom, image FROM produits WHERE nom LIKE '%courgette%' OR nom LIKE '%Courgette%'");
        $nouveaux = $stmt->fetchAll();
        
        echo "<h3>🎯 Résultat après correction :</h3>\n";
        foreach ($nouveaux as $produit) {
            echo "<div style='background: #e8f5e8; padding: 10px; margin: 5px; border-radius: 5px;'>\n";
            echo "<strong>ID {$produit['id']}</strong>: {$produit['nom']} → {$produit['image']}\n";
            echo "<br><img src='assets/uploads/{$produit['image']}?v=" . time() . "' style='width: 100px; height: 70px; object-fit: cover; margin: 5px;'>\n";
            echo "</div>\n";
        }
    } else {
        echo "<p style='color: red;'>❌ Erreur lors de la correction</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>\n";
}

// Bouton pour tester
$timestamp = time();
echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='catalogue.php?v={$timestamp}' style='background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; font-weight: bold;'>🛒 TESTER LE CATALOGUE MAINTENANT</a>\n";
echo "</div>\n";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
</style>