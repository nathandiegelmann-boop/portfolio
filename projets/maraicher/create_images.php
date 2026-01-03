<?php
// Script pour créer des images placeholder pour tous les produits
require_once 'includes/config.php';

// Fonction pour créer une image placeholder
function createPlaceholderImage($filename, $productName, $color = [76, 175, 80]) {
    $width = 400;
    $height = 300;
    
    // Créer une image
    $image = imagecreate($width, $height);
    
    // Définir les couleurs
    $background = imagecolorallocate($image, $color[0], $color[1], $color[2]);
    $white = imagecolorallocate($image, 255, 255, 255);
    $darker = imagecolorallocate($image, max(0, $color[0]-30), max(0, $color[1]-30), max(0, $color[2]-30));
    
    // Remplir le background
    imagefill($image, 0, 0, $background);
    
    // Ajouter un motif
    for ($x = 0; $x < $width; $x += 40) {
        for ($y = 0; $y < $height; $y += 40) {
            imagefilledellipse($image, $x, $y, 20, 20, $darker);
        }
    }
    
    // Ajouter le texte du nom du produit
    $font_size = 4;
    $text_width = imagefontwidth($font_size) * strlen($productName);
    $text_height = imagefontheight($font_size);
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $x, $y, $productName, $white);
    
    // Sauvegarder l'image
    $filepath = 'assets/uploads/' . $filename;
    imagejpeg($image, $filepath, 85);
    imagedestroy($image);
    
    return $filepath;
}

// Vérifier si GD est activé
if (!extension_loaded('gd')) {
    die('Extension GD non disponible. Activez-la dans php.ini');
}

echo "<h2>Création d'images placeholder pour les produits...</h2>\n";

// Couleurs par type de légume
$colors = [
    // Légumes verts
    'concombre' => [76, 175, 80],
    'courgette' => [76, 175, 80], 
    'brocoli' => [76, 175, 80],
    'épinard' => [76, 175, 80],
    'laitue' => [76, 175, 80],
    'roquette' => [76, 175, 80],
    'mâche' => [76, 175, 80],
    'bette' => [76, 175, 80],
    'poireau' => [76, 175, 80],
    'basilic' => [76, 175, 80],
    'persil' => [76, 175, 80],
    'ciboulette' => [76, 175, 80],
    'menthe' => [76, 175, 80],
    'coriandre' => [76, 175, 80],
    'thym' => [76, 175, 80],
    'romarin' => [76, 175, 80],
    'estragon' => [76, 175, 80],
    'aneth' => [76, 175, 80],
    'oseille' => [76, 175, 80],
    'cresson' => [76, 175, 80],
    'asperge' => [76, 175, 80],
    'artichaut' => [76, 175, 80],
    'fenouil' => [76, 175, 80],
    'céleri' => [76, 175, 80],
    'petits' => [76, 175, 80],
    'haricot' => [76, 175, 80],
    
    // Légumes orange/rouge
    'carotte' => [255, 152, 0],
    'tomate' => [244, 67, 54],
    'poivron rouge' => [244, 67, 54],
    'aubergine' => [156, 39, 176],
    'betterave' => [136, 14, 79],
    'patate' => [255, 152, 0],
    'potiron' => [255, 152, 0],
    'courge' => [255, 152, 0],
    
    // Légumes jaunes
    'poivron jaune' => [255, 235, 59],
    'oignon jaune' => [255, 235, 59],
    
    // Légumes blancs/beiges
    'chou-fleur' => [224, 224, 224],
    'navet' => [224, 224, 224],
    'panais' => [224, 224, 224],
    'pomme de terre' => [205, 164, 133],
    'champignon' => [224, 224, 224],
    'ail' => [240, 240, 240],
    'oignon' => [240, 240, 240],
    'échalote' => [205, 164, 133],
    
    // Légumes violets/rouges
    'oignon rouge' => [156, 39, 176],
    'radis' => [244, 67, 54],
    'poivron vert' => [76, 175, 80]
];

function getColorForProduct($productName, $colors) {
    $name = strtolower($productName);
    foreach ($colors as $keyword => $color) {
        if (strpos($name, $keyword) !== false) {
            return $color;
        }
    }
    return [76, 175, 80]; // Vert par défaut
}

// Récupérer TOUS les produits pour créer les images manquantes
$stmt = $pdo->query("SELECT id, nom, image FROM produits ORDER BY id");
$all_products = $stmt->fetchAll();

$created = 0;
$skipped = 0;

foreach ($all_products as $product) {
    $filename = $product['image'];
    
    // Si pas de nom de fichier défini, en créer un
    if (empty($filename)) {
        $filename = strtolower(str_replace([' ', 'é', 'è', 'à', 'ç', 'ù', "'", '(', ')', ','], ['-', 'e', 'e', 'a', 'c', 'u', '', '', '', ''], $product['nom'])) . '.jpg';
        $filename = preg_replace('/-+/', '-', $filename); // Supprimer les tirets multiples
        $filename = trim($filename, '-'); // Supprimer les tirets en début/fin
        
        // Mettre à jour la base de données
        $stmt = $pdo->prepare("UPDATE produits SET image = ? WHERE id = ?");
        $stmt->execute([$filename, $product['id']]);
    }
    
    $image_path = 'assets/uploads/' . $filename;
    
    // Vérifier si l'image existe déjà
    if (file_exists($image_path)) {
        echo "<p style='color: green;'>✅ {$product['nom']} - Image existe déjà : {$filename}</p>\n";
        $skipped++;
        continue;
    }
    
    // Obtenir la couleur appropriée
    $color = getColorForProduct($product['nom'], $colors);
    
    // Créer l'image placeholder
    try {
        $created_path = createPlaceholderImage($filename, $product['nom'], $color);
        echo "<p style='color: blue;'>🎨 {$product['nom']} - Image créée : {$filename}</p>\n";
        $created++;
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur pour {$product['nom']} : " . $e->getMessage() . "</p>\n";
    }
}

echo "<h3>Résumé :</h3>\n";
echo "<p>✅ Images déjà présentes : {$skipped}</p>\n";
echo "<p>🎨 Images créées : {$created}</p>\n";
echo "<p><strong>Total : " . ($skipped + $created) . " produits traités</strong></p>\n";

echo "<p><a href='admin/produits.php'>Voir tous les produits dans l'admin</a></p>\n";
?>