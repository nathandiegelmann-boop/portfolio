<?php
// Script pour créer un aperçu des nouvelles images téléchargées
require_once 'includes/config.php';

echo "<h2>🖼️ Aperçu des nouvelles images téléchargées</h2>\n";

// Récupérer quelques produits avec leurs nouvelles images
$stmt = $pdo->prepare("SELECT id, nom as nom_produit, image, prix FROM produits WHERE image IS NOT NULL ORDER BY nom LIMIT 20");
$stmt->execute();
$produits = $stmt->fetchAll();

echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;'>\n";

foreach ($produits as $produit) {
    $imagePath = 'assets/uploads/' . $produit['image'];
    if (file_exists($imagePath)) {
        echo "<div style='background: white; border: 2px solid #ddd; border-radius: 10px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>\n";
        echo "<img src='{$imagePath}?v=" . time() . "' style='width: 100%; height: 200px; object-fit: cover; border-radius: 8px;' alt='{$produit['nom_produit']}'>\n";
        echo "<h3 style='margin: 15px 0 8px 0; color: #2c3e50;'>{$produit['nom_produit']}</h3>\n";
        echo "<p style='margin: 5px 0; color: #27ae60; font-weight: bold; font-size: 18px;'>{$produit['prix']}€/kg</p>\n";
        echo "<p style='margin: 5px 0; color: #7f8c8d; font-size: 12px;'>ID: {$produit['id']} | 📁 {$produit['image']}</p>\n";
        echo "</div>\n";
    }
}

echo "</div>\n";

echo "<div style='background: #e8f6f3; padding: 20px; border-radius: 10px; border-left: 5px solid #27ae60; margin: 30px 0;'>\n";
echo "<h3>🎉 Améliorations apportées :</h3>\n";
echo "<ul>\n";
echo "<li>✅ <strong>Images réelles</strong> téléchargées depuis Unsplash</li>\n";
echo "<li>✅ <strong>Chaque produit</strong> a maintenant sa propre image unique</li>\n";
echo "<li>✅ <strong>Plus de confusion</strong> entre carottes et courgettes</li>\n";
echo "<li>✅ <strong>Qualité professionnelle</strong> pour votre catalogue</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='catalogue.php?v=" . time() . "' style='background: #27ae60; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; font-size: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>🛍️ Voir le catalogue final</a>\n";
echo "<a href='admin/produits.php' style='background: #3498db; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 10px; font-size: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>📦 Gérer les produits</a>\n";
echo "</div>\n";

// Compter les nouvelles images
$totalImages = count(glob('assets/uploads/*.jpg'));
echo "<div style='text-align: center; background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;'>\n";
echo "<h4>📊 Total des images : <span style='color: #27ae60;'>{$totalImages} fichiers</span></h4>\n";
echo "</div>\n";
?>