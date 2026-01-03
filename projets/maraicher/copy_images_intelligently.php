<?php
// Script pour créer des images manquantes en utilisant les images existantes comme base
require_once 'includes/config.php';

echo "<h2>🎨 Création d'images manquantes par copie intelligente</h2>\n";

// Mapping des images existantes vers les produits similaires
$image_mapping = [
    // Utiliser basilic.jpg pour les aromates verts
    'basilic.jpg' => [
        'basilic-frais.jpg',
        'persil-plat.jpg',
        'ciboulette.jpg',
        'menthe.jpg',
        'coriandre.jpg',
        'thym.jpg',
        'romarin.jpg',
        'estragon.jpg',
        'aneth.jpg',
        'oseille.jpg',
        'cresson.jpg'
    ],
    
    // Utiliser carottes.jpg pour les légumes oranges
    'carottes.jpg' => [
        'carotte.jpg',
        'patate-douce.jpg'
    ],
    
    // Utiliser courgettes.jpg pour les légumes verts
    'courgettes.jpg' => [
        'courgette.jpg',
        'courge-spaghetti.jpg'
    ],
    
    // Utiliser salade-bio.jpg pour les légumes feuilles
    'salade-bio.jpg' => [
        'laitue-batavia.jpg',
        'laitue-romaine.jpg',
        'roquette.jpg',
        'mache.jpg',
        'epinard.jpg',
        'bette.jpg'
    ],
    
    // Utiliser brocoli.jpg pour les légumes crucifères
    'brocoli.jpg' => [
        'artichaut.jpg',
        'fenouil.jpg',
        'celeri-branche.jpg'
    ],
    
    // Utiliser oignon-jaune.jpg pour les légumes blancs/beiges
    'oignon-jaune.jpg' => [
        'navet.jpg',
        'panais.jpg',
        'pomme-de-terre.jpg',
        'champignon-paris.jpg',
        'echalote.jpg'
    ],
    
    // Utiliser aubergine.jpg pour les légumes violets/rouges
    'aubergine.jpg' => [
        'betterave-crue.jpg',
        'betterave-cuite.jpg',
        'radis-rose.jpg'
    ],
    
    // Utiliser concombre.jpg pour les légumes verts longs
    'concombre.jpg' => [
        'poireau.jpg',
        'asperge-verte.jpg'
    ],
    
    // Utiliser poivron-rouge.jpg pour la tomate
    'poivron-rouge.jpg' => [
        'tomate-ronde.jpg'
    ],
    
    // Utiliser courgettes.jpg pour les légumes verts en général
    'courgettes.jpg' => [
        'haricot-vert.jpg',
        'petits-pois.jpg'
    ]
];

$created = 0;
$skipped = 0;
$errors = 0;

foreach ($image_mapping as $source_image => $target_images) {
    $source_path = 'assets/uploads/' . $source_image;
    
    if (!file_exists($source_path)) {
        echo "<p style='color: orange;'>⚠️ Image source manquante : {$source_image}</p>\n";
        continue;
    }
    
    foreach ($target_images as $target_image) {
        $target_path = 'assets/uploads/' . $target_image;
        
        if (file_exists($target_path)) {
            echo "<p style='color: blue;'>ℹ️ Image existe déjà : {$target_image}</p>\n";
            $skipped++;
            continue;
        }
        
        try {
            if (copy($source_path, $target_path)) {
                echo "<p style='color: green;'>✅ Créé {$target_image} depuis {$source_image}</p>\n";
                $created++;
            } else {
                echo "<p style='color: red;'>❌ Erreur de copie : {$target_image}</p>\n";
                $errors++;
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Exception pour {$target_image}: " . $e->getMessage() . "</p>\n";
            $errors++;
        }
    }
}

echo "<div style='background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 10px;'>\n";
echo "<h3>📊 Résumé de création par copie :</h3>\n";
echo "<p style='color: green;'>✅ Images créées : {$created}</p>\n";
echo "<p style='color: blue;'>ℹ️ Images existantes : {$skipped}</p>\n";
echo "<p style='color: red;'>❌ Erreurs : {$errors}</p>\n";
echo "</div>\n";

echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='verification_images.php' style='background: #4CAF50; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🔍 Vérifier les résultats</a>\n";
echo "<a href='auto_assign_images.php' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🎯 Auto-attribution</a>\n";
echo "<a href='admin/produits.php' style='background: #6f42c1; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>📦 Gérer produits</a>\n";
echo "<a href='catalogue.php' style='background: #fd7e14; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🛍️ Voir catalogue</a>\n";
echo "</div>\n";
?>