<?php
// Script pour créer des images spécifiques et réalistes pour chaque produit
require_once 'includes/config.php';

echo "<h2>🎯 Création d'images spécifiques pour chaque produit</h2>\n";

// Fonction pour créer une image réaliste avec couleurs appropriées
function createRealisticImage($filename, $productName, $backgroundColor, $accentColor) {
    $width = 400;
    $height = 300;
    
    $image = imagecreatetruecolor($width, $height);
    
    // Couleurs
    $bg = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
    $accent = imagecolorallocate($image, $accentColor[0], $accentColor[1], $accentColor[2]);
    $white = imagecolorallocate($image, 255, 255, 255);
    $shadow = imagecolorallocate($image, max(0, $backgroundColor[0]-50), max(0, $backgroundColor[1]-50), max(0, $backgroundColor[2]-50));
    
    // Fond dégradé
    imagefill($image, 0, 0, $bg);
    
    // Créer un motif spécifique selon le type de légume
    if (strpos(strtolower($productName), 'tomate') !== false) {
        // Motif rond pour les tomates
        for ($i = 0; $i < 8; $i++) {
            $x = rand(50, $width-50);
            $y = rand(50, $height-50);
            imagefilledellipse($image, $x, $y, rand(30, 60), rand(30, 60), $accent);
        }
    } elseif (strpos(strtolower($productName), 'carotte') !== false || strpos(strtolower($productName), 'radis') !== false) {
        // Motif allongé pour les légumes racines
        for ($i = 0; $i < 6; $i++) {
            $x = rand(30, $width-30);
            $y = rand(20, $height-20);
            imagefilledrectangle($image, $x, $y, $x + rand(15, 25), $y + rand(60, 100), $accent);
        }
    } elseif (strpos(strtolower($productName), 'salade') !== false || strpos(strtolower($productName), 'laitue') !== false || strpos(strtolower($productName), 'épinard') !== false) {
        // Motif feuille pour les salades
        for ($i = 0; $i < 12; $i++) {
            $points = array(
                rand(50, $width-50), rand(50, $height-50),
                rand(50, $width-50), rand(50, $height-50),
                rand(50, $width-50), rand(50, $height-50),
                rand(50, $width-50), rand(50, $height-50)
            );
            imagefilledpolygon($image, $points, 4, $accent);
        }
    } else {
        // Motif général avec cercles
        for ($i = 0; $i < 10; $i++) {
            $x = rand(30, $width-30);
            $y = rand(30, $height-30);
            imagefilledellipse($image, $x, $y, rand(20, 40), rand(20, 40), $accent);
        }
    }
    
    // Ajouter une ombre au texte pour plus de lisibilité
    $font_size = 5;
    $lines = explode(' ', $productName);
    $y_start = $height / 2 - (count($lines) * 25) / 2;
    
    foreach ($lines as $i => $line) {
        $text_width = imagefontwidth($font_size) * strlen($line);
        $x = ($width - $text_width) / 2;
        $y = $y_start + ($i * 25);
        
        // Ombre
        imagestring($image, $font_size, $x + 2, $y + 2, $line, $shadow);
        // Texte principal
        imagestring($image, $font_size, $x, $y, $line, $white);
    }
    
    // Bordure décorative
    imagerectangle($image, 0, 0, $width-1, $height-1, $accent);
    imagerectangle($image, 5, 5, $width-6, $height-6, $accent);
    
    $filepath = 'assets/uploads/' . $filename;
    imagejpeg($image, $filepath, 90);
    imagedestroy($image);
    
    return true;
}

// Définitions spécifiques des produits avec leurs couleurs appropriées
$specific_products = [
    // Légumes verts
    'concombre.jpg' => ['Concombre', [76, 175, 80], [139, 195, 74]],
    'courgette.jpg' => ['Courgette', [76, 175, 80], [129, 199, 132]],
    'brocoli.jpg' => ['Brocoli', [46, 125, 50], [76, 175, 80]],
    'chou-fleur.jpg' => ['Chou-fleur', [245, 245, 245], [200, 200, 200]],
    'epinard.jpg' => ['Épinard', [27, 94, 32], [76, 175, 80]],
    'laitue-batavia.jpg' => ['Laitue Batavia', [129, 199, 132], [165, 214, 167]],
    'laitue-romaine.jpg' => ['Laitue Romaine', [102, 187, 106], [129, 199, 132]],
    'roquette.jpg' => ['Roquette', [56, 142, 60], [76, 175, 80]],
    'mache.jpg' => ['Mâche', [46, 125, 50], [102, 187, 106]],
    'bette.jpg' => ['Bette', [76, 175, 80], [129, 199, 132]],
    'poireau.jpg' => ['Poireau', [139, 195, 74], [165, 214, 167]],
    'asperge-verte.jpg' => ['Asperge verte', [76, 175, 80], [129, 199, 132]],
    'artichaut.jpg' => ['Artichaut', [102, 187, 106], [139, 195, 74]],
    'fenouil.jpg' => ['Fenouil', [200, 230, 201], [165, 214, 167]],
    'celeri-branche.jpg' => ['Céleri branche', [129, 199, 132], [165, 214, 167]],
    'petits-pois.jpg' => ['Petits pois', [102, 187, 106], [129, 199, 132]],
    'haricot-vert.jpg' => ['Haricot vert', [76, 175, 80], [102, 187, 106]],
    'oseille.jpg' => ['Oseille', [56, 142, 60], [76, 175, 80]],
    'cresson.jpg' => ['Cresson', [46, 125, 50], [76, 175, 80]],
    
    // Légumes orange/rouges
    'carotte.jpg' => ['Carotte', [255, 152, 0], [255, 183, 77]],
    'tomate-ronde.jpg' => ['Tomate ronde', [244, 67, 54], [239, 83, 80]],
    'poivron-rouge.jpg' => ['Poivron rouge', [211, 47, 47], [244, 67, 54]],
    'aubergine.jpg' => ['Aubergine', [74, 20, 140], [156, 39, 176]],
    'betterave-crue.jpg' => ['Betterave crue', [136, 14, 79], [173, 20, 87]],
    'betterave-cuite.jpg' => ['Betterave cuite', [106, 27, 154], [136, 14, 79]],
    'patate-douce.jpg' => ['Patate douce', [255, 152, 0], [255, 183, 77]],
    'radis-rose.jpg' => ['Radis rose', [233, 30, 99], [240, 98, 146]],
    
    // Légumes jaunes
    'poivron-jaune.jpg' => ['Poivron jaune', [255, 235, 59], [255, 241, 118]],
    'oignon-jaune.jpg' => ['Oignon jaune', [255, 193, 7], [255, 213, 79]],
    
    // Légumes blancs/beiges
    'navet.jpg' => ['Navet', [245, 245, 245], [224, 224, 224]],
    'panais.jpg' => ['Panais', [255, 224, 178], [255, 204, 128]],
    'pomme-de-terre.jpg' => ['Pomme de terre', [215, 204, 200], [188, 170, 164]],
    'champignon-paris.jpg' => ['Champignon', [250, 250, 250], [224, 224, 224]],
    'ail-rose.jpg' => ['Ail rose', [255, 235, 238], [248, 187, 208]],
    'echalote.jpg' => ['Échalote', [215, 204, 200], [188, 170, 164]],
    
    // Légumes violets
    'oignon-rouge.jpg' => ['Oignon rouge', [156, 39, 176], [186, 104, 200]],
    'poivron-vert.jpg' => ['Poivron vert', [76, 175, 80], [102, 187, 106]],
    'courge-spaghetti.jpg' => ['Courge spaghetti', [255, 213, 79], [255, 224, 130]],
    
    // Aromates (tous verts mais avec nuances différentes)
    'basilic-frais.jpg' => ['Basilic frais', [27, 94, 32], [76, 175, 80]],
    'persil-plat.jpg' => ['Persil plat', [46, 125, 50], [102, 187, 106]],
    'ciboulette.jpg' => ['Ciboulette', [76, 175, 80], [129, 199, 132]],
    'menthe.jpg' => ['Menthe fraîche', [102, 187, 106], [139, 195, 74]],
    'coriandre.jpg' => ['Coriandre', [56, 142, 60], [76, 175, 80]],
    'thym.jpg' => ['Thym frais', [139, 195, 74], [165, 214, 167]],
    'romarin.jpg' => ['Romarin', [46, 125, 50], [76, 175, 80]],
    'estragon.jpg' => ['Estragon', [102, 187, 106], [129, 199, 132]],
    'aneth.jpg' => ['Aneth frais', [129, 199, 132], [165, 214, 167]]
];

$created = 0;
$errors = 0;

// Vérifier si GD est disponible
if (!extension_loaded('gd')) {
    echo "<p style='color: red;'>❌ Extension GD non disponible. Activation nécessaire dans php.ini</p>\n";
    echo "<p>Les images ne peuvent pas être créées automatiquement.</p>\n";
} else {
    echo "<p style='color: green;'>✅ Extension GD disponible</p>\n";
    
    foreach ($specific_products as $filename => $data) {
        $productName = $data[0];
        $bgColor = $data[1];
        $accentColor = $data[2];
        
        $filepath = 'assets/uploads/' . $filename;
        
        if (file_exists($filepath)) {
            echo "<p style='color: orange;'>⚠️ {$productName} - Fichier existe déjà : {$filename}</p>\n";
            continue;
        }
        
        try {
            createRealisticImage($filename, $productName, $bgColor, $accentColor);
            echo "<p style='color: green;'>✅ {$productName} - Créé : {$filename}</p>\n";
            $created++;
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur {$productName}: " . $e->getMessage() . "</p>\n";
            $errors++;
        }
    }
}

echo "<div style='background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 10px;'>\n";
echo "<h3>📊 Résumé de création :</h3>\n";
echo "<p style='color: green;'>✅ Images créées : {$created}</p>\n";
echo "<p style='color: red;'>❌ Erreurs : {$errors}</p>\n";
echo "</div>\n";

echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='verification_images.php' style='background: #4CAF50; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🔍 Vérifier les images</a>\n";
echo "<a href='admin/produits.php' style='background: #2196F3; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>📦 Voir les produits</a>\n";
echo "<a href='catalogue.php' style='background: #FF9800; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🛍️ Catalogue client</a>\n";
echo "</div>\n";
?>