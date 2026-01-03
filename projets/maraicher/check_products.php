<?php
require_once 'includes/config.php';

// Récupérer tous les produits
$stmt = $pdo->query("SELECT id, nom, image FROM produits ORDER BY id");
$produits = $stmt->fetchAll();

echo "<h2>Produits dans la base de données :</h2>\n";
echo "<table border='1'>\n";
echo "<tr><th>ID</th><th>Nom</th><th>Image</th><th>Fichier existe ?</th></tr>\n";

foreach ($produits as $produit) {
    $image_path = 'assets/uploads/' . $produit['image'];
    $exists = file_exists($image_path) ? '✅ Oui' : '❌ Non';
    $image_status = empty($produit['image']) ? '❌ Pas d\'image définie' : $produit['image'];
    
    echo "<tr>";
    echo "<td>" . $produit['id'] . "</td>";
    echo "<td>" . htmlspecialchars($produit['nom']) . "</td>";
    echo "<td>" . $image_status . "</td>";
    echo "<td>" . $exists . "</td>";
    echo "</tr>\n";
}

echo "</table>\n";

// Compter les produits sans images
$stmt = $pdo->query("SELECT COUNT(*) as count FROM produits WHERE image IS NULL OR image = ''");
$count = $stmt->fetchColumn();
echo "<p><strong>Produits sans image : " . $count . "</strong></p>\n";
?>