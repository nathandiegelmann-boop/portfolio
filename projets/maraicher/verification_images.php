<?php
// Script de vérification complète des images
require_once 'includes/config.php';

echo "<h2>🔍 Vérification complète des images produits</h2>\n";

// Récupérer tous les produits avec leurs images
$stmt = $pdo->prepare("SELECT id, nom as nom_produit, image, prix FROM produits ORDER BY nom");
$stmt->execute();
$produits = $stmt->fetchAll();

// Analyser les fichiers d'images disponibles
$uploadDir = 'assets/uploads/';
$availableImages = [];
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $availableImages[] = $file;
        }
    }
}

// Statistiques
$withImages = 0;
$withoutImages = 0;
$missingFiles = 0;
$validImages = 0;

// Grouper par statut
$products_with_valid_images = [];
$products_with_missing_files = [];
$products_without_images = [];

foreach ($produits as $produit) {
    if (empty($produit['image'])) {
        $products_without_images[] = $produit;
        $withoutImages++;
    } else {
        $imagePath = $uploadDir . $produit['image'];
        if (file_exists($imagePath)) {
            $products_with_valid_images[] = $produit;
            $withImages++;
            $validImages++;
        } else {
            $products_with_missing_files[] = $produit;
            $withImages++;
            $missingFiles++;
        }
    }
}

// Afficher les statistiques
echo "<div style='display: flex; justify-content: space-around; margin: 20px 0;'>\n";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 10px; text-align: center; min-width: 120px;'>\n";
echo "<h4 style='color: #155724; margin: 0;'>✅ Images valides</h4>\n";
echo "<p style='font-size: 24px; font-weight: bold; color: #155724; margin: 5px 0;'>{$validImages}</p>\n";
echo "</div>\n";

echo "<div style='background: #f8d7da; padding: 15px; border-radius: 10px; text-align: center; min-width: 120px;'>\n";
echo "<h4 style='color: #721c24; margin: 0;'>❌ Fichiers manquants</h4>\n";
echo "<p style='font-size: 24px; font-weight: bold; color: #721c24; margin: 5px 0;'>{$missingFiles}</p>\n";
echo "</div>\n";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 10px; text-align: center; min-width: 120px;'>\n";
echo "<h4 style='color: #856404; margin: 0;'>⚠️ Sans image</h4>\n";
echo "<p style='font-size: 24px; font-weight: bold; color: #856404; margin: 5px 0;'>{$withoutImages}</p>\n";
echo "</div>\n";

echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 10px; text-align: center; min-width: 120px;'>\n";
echo "<h4 style='color: #0c5460; margin: 0;'>📊 Total produits</h4>\n";
echo "<p style='font-size: 24px; font-weight: bold; color: #0c5460; margin: 5px 0;'>" . count($produits) . "</p>\n";
echo "</div>\n";
echo "</div>\n";

// Afficher les images disponibles
echo "<div style='background: #e9ecef; padding: 20px; border-radius: 10px; margin: 20px 0;'>\n";
echo "<h3>📁 Images disponibles dans {$uploadDir} (" . count($availableImages) . " fichiers)</h3>\n";
echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>\n";
foreach ($availableImages as $image) {
    $imageSize = file_exists($uploadDir . $image) ? filesize($uploadDir . $image) : 0;
    $sizeKB = round($imageSize / 1024, 1);
    echo "<div style='background: white; padding: 8px; border-radius: 5px; font-size: 12px;'>\n";
    echo "<strong>{$image}</strong><br>\n";
    echo "<span style='color: #666;'>{$sizeKB} KB</span>\n";
    echo "</div>\n";
}
echo "</div>\n";
echo "</div>\n";

// Section des produits avec images valides
if (!empty($products_with_valid_images)) {
    echo "<div style='margin: 30px 0;'>\n";
    echo "<h3 style='color: #155724;'>✅ Produits avec images valides ({$validImages})</h3>\n";
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;'>\n";
    
    foreach (array_slice($products_with_valid_images, 0, 12) as $produit) { // Limiter l'affichage
        $imagePath = $uploadDir . $produit['image'];
        echo "<div style='background: white; border: 1px solid #ddd; border-radius: 8px; padding: 10px;'>\n";
        echo "<img src='{$imagePath}' style='width: 100%; height: 120px; object-fit: cover; border-radius: 5px;' alt='{$produit['nom_produit']}'>\n";
        echo "<h4 style='margin: 8px 0 4px 0; font-size: 14px;'>{$produit['nom_produit']}</h4>\n";
        echo "<p style='margin: 0; color: #666; font-size: 12px;'>#{$produit['id']} - {$produit['prix']}€/kg</p>\n";
        echo "<p style='margin: 4px 0 0 0; color: #28a745; font-size: 11px;'>📁 {$produit['image']}</p>\n";
        echo "</div>\n";
    }
    
    if (count($products_with_valid_images) > 12) {
        echo "<div style='grid-column: 1/-1; text-align: center; padding: 20px; color: #666;'>\n";
        echo "... et " . (count($products_with_valid_images) - 12) . " autres produits avec images valides\n";
        echo "</div>\n";
    }
    
    echo "</div>\n";
    echo "</div>\n";
}

// Section des produits avec fichiers manquants
if (!empty($products_with_missing_files)) {
    echo "<div style='margin: 30px 0;'>\n";
    echo "<h3 style='color: #721c24;'>❌ Produits avec fichiers image manquants ({$missingFiles})</h3>\n";
    echo "<table style='width: 100%; border-collapse: collapse; background: white;'>\n";
    echo "<tr style='background: #f8d7da;'>\n";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>ID</th>\n";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Produit</th>\n";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Fichier manquant</th>\n";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Prix</th>\n";
    echo "</tr>\n";
    
    foreach ($products_with_missing_files as $produit) {
        echo "<tr>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['nom_produit']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px; color: #dc3545;'>{$produit['image']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['prix']}€/kg</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</div>\n";
}

// Section des produits sans images
if (!empty($products_without_images)) {
    echo "<div style='margin: 30px 0;'>\n";
    echo "<h3 style='color: #856404;'>⚠️ Produits sans images ({$withoutImages})</h3>\n";
    echo "<table style='width: 100%; border-collapse: collapse; background: white;'>\n";
    echo "<tr style='background: #fff3cd;'>\n";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>ID</th>\n";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Produit</th>\n";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Prix</th>\n";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Statut</th>\n";
    echo "</tr>\n";
    
    foreach ($products_without_images as $produit) {
        echo "<tr>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['nom_produit']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['prix']}€/kg</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px; color: #856404;'>Aucune image</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</div>\n";
}

// Actions recommandées
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 5px solid #007bff; margin: 30px 0;'>\n";
echo "<h3>🔧 Actions recommandées</h3>\n";

if ($missingFiles > 0) {
    echo "<p style='color: #dc3545;'>• <strong>{$missingFiles} fichiers d'images manquants</strong> - Créer ou télécharger ces images</p>\n";
}

if ($withoutImages > 0) {
    echo "<p style='color: #856404;'>• <strong>{$withoutImages} produits sans images</strong> - Assigner des images à ces produits</p>\n";
}

if ($validImages > 0) {
    echo "<p style='color: #28a745;'>• <strong>{$validImages} images fonctionnelles</strong> - Système opérationnel</p>\n";
}

$coverage = round(($validImages / count($produits)) * 100, 1);
echo "<p><strong>Couverture actuelle : {$coverage}%</strong></p>\n";
echo "</div>\n";

// Boutons d'action
echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='create_specific_images.php' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🎨 Créer des images</a>\n";
echo "<a href='auto_assign_images.php' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🎯 Auto-attribution</a>\n";
echo "<a href='admin/produits.php' style='background: #6f42c1; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>📦 Gérer produits</a>\n";
echo "<a href='catalogue.php' style='background: #fd7e14; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🛍️ Voir catalogue</a>\n";
echo "</div>\n";
?>