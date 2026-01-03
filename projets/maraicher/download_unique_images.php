<?php
// Script pour télécharger des images réelles et uniques pour chaque produit
require_once 'includes/config.php';

echo "<h2>📸 Téléchargement d'images réelles pour chaque produit</h2>\n";

// Liste des URLs d'images spécifiques pour chaque produit
$specific_images = [
    // Légumes verts
    'courgettes.jpg' => 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=400&h=300&fit=crop&crop=center',
    'courgette.jpg' => 'https://images.unsplash.com/photo-1472476443507-c7a5948772fc?w=400&h=300&fit=crop&crop=center',
    'concombre.jpg' => 'https://images.unsplash.com/photo-1582693424067-cb88b0b8c8c5?w=400&h=300&fit=crop&crop=center',
    'brocoli.jpg' => 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=400&h=300&fit=crop&crop=center',
    'chou-fleur.jpg' => 'https://images.unsplash.com/photo-1510627498534-cf7e9002facc?w=400&h=300&fit=crop&crop=center',
    'epinard.jpg' => 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?w=400&h=300&fit=crop&crop=center',
    'salade-bio.jpg' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=300&fit=crop&crop=center',
    'roquette.jpg' => 'https://images.unsplash.com/photo-1594282486552-05b4d80fbb9f?w=400&h=300&fit=crop&crop=center',
    'poireau.jpg' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&h=300&fit=crop&crop=center',
    'asperge-verte.jpg' => 'https://images.unsplash.com/photo-1581142146351-d27de8328f95?w=400&h=300&fit=crop&crop=center',
    'artichaut.jpg' => 'https://images.unsplash.com/photo-1528719227889-cdcb300d902b?w=400&h=300&fit=crop&crop=center',
    'fenouil.jpg' => 'https://images.unsplash.com/photo-1621532338440-4bf8e9d34a5d?w=400&h=300&fit=crop&crop=center',
    'petits-pois.jpg' => 'https://images.unsplash.com/photo-1463740839922-2d3b7e426a56?w=400&h=300&fit=crop&crop=center',
    'haricot-vert.jpg' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=300&fit=crop&crop=center',
    
    // Légumes oranges/rouges
    'carottes.jpg' => 'https://images.unsplash.com/photo-1445282768818-728615cc910a?w=400&h=300&fit=crop&crop=center',
    'carotte.jpg' => 'https://images.unsplash.com/photo-1582284540020-8acbe03f4924?w=400&h=300&fit=crop&crop=center',
    'tomates-cerises.jpg' => 'https://images.unsplash.com/photo-1546470427-227fde8dce24?w=400&h=300&fit=crop&crop=center',
    'tomate-ronde.jpg' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=400&h=300&fit=crop&crop=center',
    'poivron-rouge.jpg' => 'https://images.unsplash.com/photo-1525607551316-4a8e16d1f9ba?w=400&h=300&fit=crop&crop=center',
    'aubergine.jpg' => 'https://images.unsplash.com/photo-1520986606214-8b456906c4e8?w=400&h=300&fit=crop&crop=center',
    'patate-douce.jpg' => 'https://images.unsplash.com/photo-1477506410409-a0d19c121b2c?w=400&h=300&fit=crop&crop=center',
    'radis-rose.jpg' => 'https://images.unsplash.com/photo-1598030585644-c72fa0e5b3f9?w=400&h=300&fit=crop&crop=center',
    
    // Légumes jaunes
    'poivron-jaune.jpg' => 'https://images.unsplash.com/photo-1581063572207-0cf3f24c2e95?w=400&h=300&fit=crop&crop=center',
    'oignon-jaune.jpg' => 'https://images.unsplash.com/photo-1508313880080-c4bec8ca3b43?w=400&h=300&fit=crop&crop=center',
    
    // Légumes violets/blancs
    'oignon-rouge.jpg' => 'https://images.unsplash.com/photo-1515543237350-b3eea1ec8082?w=400&h=300&fit=crop&crop=center',
    'poivron-vert.jpg' => 'https://images.unsplash.com/photo-1563453392212-326f5e854473?w=400&h=300&fit=crop&crop=center',
    'navet.jpg' => 'https://images.unsplash.com/photo-1590739834509-0c4ff44f4e5e?w=400&h=300&fit=crop&crop=center',
    'panais.jpg' => 'https://images.unsplash.com/photo-1612198188060-c7c2a3b66eae?w=400&h=300&fit=crop&crop=center',
    'pomme-de-terre.jpg' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=400&h=300&fit=crop&crop=center',
    'champignon-paris.jpg' => 'https://images.unsplash.com/photo-1506976785307-8732e854ad03?w=400&h=300&fit=crop&crop=center',
    'ail-rose.jpg' => 'https://images.unsplash.com/photo-1567352331120-4a1c0e490013?w=400&h=300&fit=crop&crop=center',
    
    // Aromates - chacun avec sa propre image
    'basilic.jpg' => 'https://images.unsplash.com/photo-1618164435735-413d3b066c9a?w=400&h=300&fit=crop&crop=center',
    'persil-plat.jpg' => 'https://images.unsplash.com/photo-1583137645299-b9b64b2b1e53?w=400&h=300&fit=crop&crop=center',
    'ciboulette.jpg' => 'https://images.unsplash.com/photo-1630014049384-99cddfee6185?w=400&h=300&fit=crop&crop=center',
    'menthe.jpg' => 'https://images.unsplash.com/photo-1628556270448-4d4e4148e1b1?w=400&h=300&fit=crop&crop=center',
    'coriandre.jpg' => 'https://images.unsplash.com/photo-1509358271058-acd22cc93898?w=400&h=300&fit=crop&crop=center',
    'thym.jpg' => 'https://images.unsplash.com/photo-1516537276692-7c5b2c6d6c3d?w=400&h=300&fit=crop&crop=center',
    'romarin.jpg' => 'https://images.unsplash.com/photo-1553395300-c2d346fc4ad6?w=400&h=300&fit=crop&crop=center',
    
    // Fruits
    'fraises.jpg' => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=400&h=300&fit=crop&crop=center',
    'pommes-golden.jpg' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=400&h=300&fit=crop&crop=center',
    
    // Légumes spéciaux
    'courge-spaghetti.jpg' => 'https://images.unsplash.com/photo-1507967985105-65bbf4996351?w=400&h=300&fit=crop&crop=center'
];

function downloadImage($url, $filename) {
    $filepath = 'assets/uploads/' . $filename;
    
    // Créer le contexte avec User-Agent
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]
    ]);
    
    // Télécharger l'image
    $imageData = @file_get_contents($url, false, $context);
    
    if ($imageData !== false) {
        if (file_put_contents($filepath, $imageData)) {
            return true;
        }
    }
    
    return false;
}

$downloaded = 0;
$errors = 0;
$skipped = 0;

echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>\n";
echo "<tr style='background: #f5f5f5;'>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Image</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>URL Source</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Statut</th>\n";
echo "</tr>\n";

foreach ($specific_images as $filename => $url) {
    $filepath = 'assets/uploads/' . $filename;
    
    // Sauvegarder l'ancienne image si elle existe
    if (file_exists($filepath)) {
        $backupPath = 'assets/uploads/backup_' . $filename;
        copy($filepath, $backupPath);
    }
    
    echo "<tr>\n";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$filename}</td>\n";
    echo "<td style='border: 1px solid #ddd; padding: 8px; font-size: 11px;'>" . substr($url, 0, 60) . "...</td>\n";
    
    if (downloadImage($url, $filename)) {
        echo "<td style='border: 1px solid #ddd; padding: 8px; color: green;'>✅ Téléchargée</td>\n";
        $downloaded++;
    } else {
        echo "<td style='border: 1px solid #ddd; padding: 8px; color: red;'>❌ Erreur</td>\n";
        $errors++;
        
        // Restaurer l'ancienne image en cas d'erreur
        $backupPath = 'assets/uploads/backup_' . $filename;
        if (file_exists($backupPath)) {
            copy($backupPath, $filepath);
        }
    }
    
    echo "</tr>\n";
    
    // Pause pour éviter de surcharger Unsplash
    usleep(200000); // 0.2 seconde
}

echo "</table>\n";

echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 10px; border-left: 5px solid #28a745;'>\n";
echo "<h3>📊 Résumé du téléchargement :</h3>\n";
echo "<p style='color: green;'>✅ Images téléchargées : {$downloaded}</p>\n";
echo "<p style='color: red;'>❌ Erreurs : {$errors}</p>\n";
echo "<p><strong>🎯 Maintenant chaque produit a sa propre image unique !</strong></p>\n";
echo "</div>\n";

echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='catalogue.php' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🛍️ Voir le catalogue avec les nouvelles images</a>\n";
echo "<a href='verification_images.php' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🔍 Vérifier les images</a>\n";
echo "<a href='admin/produits.php' style='background: #6f42c1; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>📦 Gérer produits</a>\n";
echo "</div>\n";
?>