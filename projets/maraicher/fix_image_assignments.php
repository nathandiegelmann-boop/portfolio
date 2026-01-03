<?php
// Script pour corriger l'attribution spécifique des images aux produits
require_once 'includes/config.php';

echo "<h2>🔧 Correction de l'attribution des images aux produits</h2>\n";

// Attribution correcte et spécifique de chaque image à son produit
$correct_assignments = [
    // Produits avec leurs images spécifiques correctes
    'Tomates cerises' => 'tomates-cerises.jpg',
    'Courgettes' => 'courgettes.jpg',
    'Carottes' => 'carottes.jpg',
    'Pommes Golden' => 'pommes-golden.jpg',
    'Fraises' => 'fraises.jpg',
    'Panier Découverte' => 'panier-decouverte.jpg',
    'Basilic' => 'basilic.jpg',
    'Salade verte Bio' => 'salade-bio.jpg',
    
    // Légumes individuels - utiliser l'image correspondante
    'Courgette' => 'courgettes.jpg',  // Courgette utilise l'image courgettes
    'Carotte' => 'carottes.jpg',      // Carotte utilise l'image carottes
    'Tomate ronde' => 'tomates-cerises.jpg', // Tomate ronde utilise les tomates cerises
    
    // Poivrons
    'Poivron rouge' => 'poivron-rouge.jpg',
    'Poivron vert' => 'poivron-vert.jpg',
    'Poivron jaune' => 'poivron-jaune.jpg',
    
    // Oignons
    'Oignon jaune' => 'oignon-jaune.jpg',
    'Oignon rouge' => 'oignon-rouge.jpg',
    
    // Légumes verts - utiliser salade pour les feuilles
    'Brocoli' => 'brocoli.jpg',
    'Chou-fleur' => 'chou-fleur.jpg',
    'Concombre' => 'concombre.jpg',
    'Épinard' => 'salade-bio.jpg',
    'Laitue batavia' => 'salade-bio.jpg',
    'Laitue romaine' => 'salade-bio.jpg',
    'Roquette' => 'salade-bio.jpg',
    'Mâche' => 'salade-bio.jpg',
    'Bette' => 'salade-bio.jpg',
    
    // Aromates - utiliser basilic pour tous
    'Basilic frais' => 'basilic.jpg',
    'Persil plat' => 'basilic.jpg',
    'Ciboulette' => 'basilic.jpg',
    'Menthe fraîche' => 'basilic.jpg',
    'Coriandre fraîche' => 'basilic.jpg',
    'Thym frais' => 'basilic.jpg',
    'Romarin frais' => 'basilic.jpg',
    'Estragon frais' => 'basilic.jpg',
    'Aneth frais' => 'basilic.jpg',
    'Oseille' => 'basilic.jpg',
    'Cresson' => 'basilic.jpg',
    
    // Légumes racines - utiliser carotte/oignon selon la couleur
    'Petits pois' => 'courgettes.jpg',
    'Haricot vert' => 'courgettes.jpg',
    'Pomme de terre' => 'oignon-jaune.jpg',
    'Patate douce' => 'carottes.jpg',
    'Navet' => 'oignon-jaune.jpg',
    'Panais' => 'oignon-jaune.jpg',
    'Radis rose' => 'poivron-rouge.jpg',
    
    // Autres légumes
    'Aubergine' => 'aubergine.jpg',
    'Betterave crue' => 'aubergine.jpg',
    'Betterave cuite' => 'aubergine.jpg',
    'Poireau' => 'concombre.jpg',
    'Asperge verte' => 'concombre.jpg',
    'Artichaut' => 'brocoli.jpg',
    'Fenouil' => 'brocoli.jpg',
    'Céleri branche' => 'brocoli.jpg',
    'Champignon de Paris' => 'oignon-jaune.jpg',
    'Ail rose' => 'ail-rose.jpg',
    'Échalote' => 'oignon-jaune.jpg',
    'Courge spaghetti' => 'courgettes.jpg'
];

$updated = 0;
$errors = 0;

echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>\n";
echo "<tr style='background: #f5f5f5;'>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>ID</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Produit</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Ancienne Image</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Nouvelle Image</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Statut</th>\n";
echo "</tr>\n";

foreach ($correct_assignments as $product_name => $correct_image) {
    // Chercher le produit dans la base de données
    $stmt = $pdo->prepare("SELECT id, image FROM produits WHERE nom = ?");
    $stmt->execute([$product_name]);
    $produit = $stmt->fetch();
    
    if (!$produit) {
        echo "<tr style='background: #fff3cd;'>\n";
        echo "<td colspan='5' style='border: 1px solid #ddd; padding: 8px; text-align: center;'>⚠️ Produit non trouvé : {$product_name}</td>\n";
        echo "</tr>\n";
        continue;
    }
    
    $current_image = $produit['image'];
    
    // Vérifier si l'image correcte existe
    if (!file_exists('assets/uploads/' . $correct_image)) {
        echo "<tr style='background: #f8d7da;'>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$product_name}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$current_image}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$correct_image}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>❌ Fichier image manquant</td>\n";
        echo "</tr>\n";
        continue;
    }
    
    // Si c'est déjà la bonne image, pas besoin de changer
    if ($current_image === $correct_image) {
        echo "<tr style='background: #d1ecf1;'>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$product_name}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$current_image}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$correct_image}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Déjà correcte</td>\n";
        echo "</tr>\n";
        continue;
    }
    
    // Mettre à jour avec la bonne image
    try {
        $stmt = $pdo->prepare("UPDATE produits SET image = ? WHERE id = ?");
        $stmt->execute([$correct_image, $produit['id']]);
        
        echo "<tr style='background: #d4edda;'>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$product_name}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$current_image}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$correct_image}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Corrigé !</td>\n";
        echo "</tr>\n";
        $updated++;
        
    } catch (Exception $e) {
        echo "<tr style='background: #f8d7da;'>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$product_name}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$current_image}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$correct_image}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>❌ Erreur DB</td>\n";
        echo "</tr>\n";
        $errors++;
    }
}

echo "</table>\n";

echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 10px; border-left: 5px solid #28a745;'>\n";
echo "<h3>📊 Résumé de la correction :</h3>\n";
echo "<p style='color: green;'>✅ Images corrigées : {$updated}</p>\n";
echo "<p style='color: red;'>❌ Erreurs : {$errors}</p>\n";
echo "<p><strong>🎯 Maintenant chaque produit devrait avoir sa bonne image !</strong></p>\n";
echo "</div>\n";

echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='catalogue.php' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🛍️ Voir le catalogue corrigé</a>\n";
echo "<a href='verification_images.php' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🔍 Vérifier les images</a>\n";
echo "<a href='admin/produits.php' style='background: #6f42c1; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>📦 Gérer produits</a>\n";
echo "</div>\n";
?>