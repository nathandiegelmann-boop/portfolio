<?php
// Diagnostic complet du problème d'images
require_once 'includes/config.php';

echo "<h2>🔍 DIAGNOSTIC COMPLET DU PROBLÈME</h2>\n";

// Vérifier les premiers produits pour voir le problème
$stmt = $pdo->prepare("SELECT id, nom, image, prix FROM produits ORDER BY id LIMIT 10");
$stmt->execute();
$produits = $stmt->fetchAll();

echo "<h3>🎯 ÉTAT ACTUEL DES 10 PREMIERS PRODUITS :</h3>\n";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>\n";
echo "<tr style='background: #f5f5f5;'>\n";
echo "<th style='padding: 10px;'>ID</th>\n";
echo "<th style='padding: 10px;'>Nom du produit</th>\n";
echo "<th style='padding: 10px;'>Image assignée</th>\n";
echo "<th style='padding: 10px;'>Fichier existe ?</th>\n";
echo "<th style='padding: 10px;'>Aperçu</th>\n";
echo "</tr>\n";

foreach ($produits as $produit) {
    $imagePath = 'assets/uploads/' . $produit['image'];
    $exists = file_exists($imagePath) ? '✅ OUI' : '❌ NON';
    $bgColor = file_exists($imagePath) ? '#e8f5e8' : '#ffebee';
    
    echo "<tr style='background: {$bgColor};'>\n";
    echo "<td style='padding: 8px; text-align: center;'><strong>{$produit['id']}</strong></td>\n";
    echo "<td style='padding: 8px;'><strong>{$produit['nom']}</strong></td>\n";
    echo "<td style='padding: 8px;'>{$produit['image']}</td>\n";
    echo "<td style='padding: 8px; text-align: center;'>{$exists}</td>\n";
    
    if (file_exists($imagePath)) {
        echo "<td style='padding: 8px;'><img src='{$imagePath}' style='width: 80px; height: 60px; object-fit: cover; border-radius: 4px;'></td>\n";
    } else {
        echo "<td style='padding: 8px; color: red;'>MANQUANTE</td>\n";
    }
    echo "</tr>\n";
}
echo "</table>\n";

// Vérifier ce qui se passe avec les courgettes spécifiquement
echo "<h3>🥒 FOCUS SUR LES COURGETTES :</h3>\n";
$stmt = $pdo->prepare("SELECT id, nom, image FROM produits WHERE nom LIKE '%courgette%' OR nom LIKE '%Courgette%'");
$stmt->execute();
$courgettes = $stmt->fetchAll();

echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0; background: #f0f8ff;'>\n";
echo "<tr style='background: #4CAF50; color: white;'>\n";
echo "<th style='padding: 10px;'>ID</th><th style='padding: 10px;'>Nom</th><th style='padding: 10px;'>Image</th><th style='padding: 10px;'>Fichier</th>\n";
echo "</tr>\n";

foreach ($courgettes as $courgette) {
    $imagePath = 'assets/uploads/' . $courgette['image'];
    $exists = file_exists($imagePath) ? '✅' : '❌';
    
    echo "<tr>\n";
    echo "<td style='padding: 8px;'>{$courgette['id']}</td>\n";
    echo "<td style='padding: 8px;'><strong>{$courgette['nom']}</strong></td>\n";
    echo "<td style='padding: 8px;'>{$courgette['image']}</td>\n";
    echo "<td style='padding: 8px;'>{$exists} " . ($exists === '✅' ? 'EXISTE' : 'MANQUE') . "</td>\n";
    echo "</tr>\n";
}
echo "</table>\n";

// Vérifier ce qui se passe avec les carottes
echo "<h3>🥕 FOCUS SUR LES CAROTTES :</h3>\n";
$stmt = $pdo->prepare("SELECT id, nom, image FROM produits WHERE nom LIKE '%carotte%' OR nom LIKE '%Carotte%'");
$stmt->execute();
$carottes = $stmt->fetchAll();

echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0; background: #fff8e1;'>\n";
echo "<tr style='background: #FF9800; color: white;'>\n";
echo "<th style='padding: 10px;'>ID</th><th style='padding: 10px;'>Nom</th><th style='padding: 10px;'>Image</th><th style='padding: 10px;'>Fichier</th>\n";
echo "</tr>\n";

foreach ($carottes as $carotte) {
    $imagePath = 'assets/uploads/' . $carotte['image'];
    $exists = file_exists($imagePath) ? '✅' : '❌';
    
    echo "<tr>\n";
    echo "<td style='padding: 8px;'>{$carotte['id']}</td>\n";
    echo "<td style='padding: 8px;'><strong>{$carotte['nom']}</strong></td>\n";
    echo "<td style='padding: 8px;'>{$carotte['image']}</td>\n";
    echo "<td style='padding: 8px;'>{$exists} " . ($exists === '✅' ? 'EXISTE' : 'MANQUE') . "</td>\n";
    echo "</tr>\n";
}
echo "</table>\n";

// Lister les fichiers d'images disponibles
echo "<h3>📁 FICHIERS D'IMAGES DISPONIBLES :</h3>\n";
$files = glob('assets/uploads/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 8px;'>\n";
echo "<p><strong>Nombre total de fichiers : " . count($files) . "</strong></p>\n";
foreach ($files as $file) {
    $filename = basename($file);
    $size = round(filesize($file) / 1024, 1);
    echo "<span style='display: inline-block; background: white; margin: 3px; padding: 5px 8px; border-radius: 4px; border: 1px solid #ddd;'>{$filename} ({$size}KB)</span>\n";
}
echo "</div>\n";

echo "<div style='text-align: center; margin: 30px 0; padding: 20px; background: #ffebee; border-radius: 10px;'>\n";
echo "<h3 style='color: #d32f2f;'>🚨 SI LE PROBLÈME PERSISTE :</h3>\n";
echo "<p>Identifiez exactement quel produit affiche la mauvaise image et je corrigerai immédiatement !</p>\n";
echo "</div>\n";
?>