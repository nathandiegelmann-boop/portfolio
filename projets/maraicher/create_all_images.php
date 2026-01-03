<?php
// Script simplifié pour créer des images pour tous les produits
require_once 'includes/config.php';

echo "<h2>Création d'images pour tous les produits</h2>\n";

// Vérifier si GD est disponible
if (!extension_loaded('gd')) {
    die('<p style="color: red;">❌ Extension GD non disponible!</p>');
}

// Liste des produits avec leurs noms d'images souhaités
$products_images = [
    // Produits déjà existants (à ignorer)
    'tomates-cerises.jpg' => 'Tomates cerises',
    'courgettes.jpg' => 'Courgettes', 
    'carottes.jpg' => 'Carottes',
    'pommes-golden.jpg' => 'Pommes Golden',
    'fraises.jpg' => 'Fraises',
    'panier-decouverte.jpg' => 'Panier Découverte',
    'basilic.jpg' => 'Basilic',
    'salade-bio.jpg' => 'Salade verte Bio',
    
    // Nouveaux produits à créer
    'concombre.jpg' => 'Concombre',
    'courgette.jpg' => 'Courgette',
    'tomate-ronde.jpg' => 'Tomate ronde',
    'carotte.jpg' => 'Carotte',
    'poivron-rouge.jpg' => 'Poivron rouge',
    'poivron-vert.jpg' => 'Poivron vert',
    'poivron-jaune.jpg' => 'Poivron jaune',
    'brocoli.jpg' => 'Brocoli',
    'chou-fleur.jpg' => 'Chou-fleur',
    'epinard.jpg' => 'Épinard',
    'laitue-batavia.jpg' => 'Laitue batavia',
    'laitue-romaine.jpg' => 'Laitue romaine',
    'roquette.jpg' => 'Roquette',
    'mache.jpg' => 'Mâche',
    'bette.jpg' => 'Bette',
    'aubergine.jpg' => 'Aubergine',
    'oignon-jaune.jpg' => 'Oignon jaune',
    'oignon-rouge.jpg' => 'Oignon rouge',
    'ail-rose.jpg' => 'Ail rose',
    'petits-pois.jpg' => 'Petits pois',
    'haricot-vert.jpg' => 'Haricot vert',
    'pomme-de-terre.jpg' => 'Pomme de terre',
    'patate-douce.jpg' => 'Patate douce',
    'celeri-branche.jpg' => 'Céleri branche',
    'radis-rose.jpg' => 'Radis rose',
    'betterave-crue.jpg' => 'Betterave crue',
    'betterave-cuite.jpg' => 'Betterave cuite',
    'asperge-verte.jpg' => 'Asperge verte',
    'champignon-paris.jpg' => 'Champignon de Paris',
    'poireau.jpg' => 'Poireau',
    'navet.jpg' => 'Navet',
    'artichaut.jpg' => 'Artichaut',
    'fenouil.jpg' => 'Fenouil',
    'courge-spaghetti.jpg' => 'Courge spaghetti',
    'panais.jpg' => 'Panais',
    'basilic-frais.jpg' => 'Basilic frais',
    'persil-plat.jpg' => 'Persil plat',
    'ciboulette.jpg' => 'Ciboulette',
    'menthe.jpg' => 'Menthe fraîche',
    'coriandre.jpg' => 'Coriandre fraîche',
    'thym.jpg' => 'Thym frais',
    'romarin.jpg' => 'Romarin frais',
    'estragon.jpg' => 'Estragon frais',
    'aneth.jpg' => 'Aneth frais',
    'oseille.jpg' => 'Oseille',
    'echalote.jpg' => 'Échalote',
    'cresson.jpg' => 'Cresson'
];

// Couleurs par légume
$colors = [
    'vert' => [76, 175, 80],
    'rouge' => [244, 67, 54], 
    'orange' => [255, 152, 0],
    'jaune' => [255, 235, 59],
    'violet' => [156, 39, 176],
    'blanc' => [224, 224, 224],
    'beige' => [205, 164, 133]
];

function getColor($filename) {
    if (strpos($filename, 'rouge') !== false || strpos($filename, 'tomate') !== false || strpos($filename, 'radis') !== false || strpos($filename, 'betterave') !== false) {
        return [244, 67, 54]; // Rouge
    } elseif (strpos($filename, 'jaune') !== false || strpos($filename, 'oignon-jaune') !== false) {
        return [255, 235, 59]; // Jaune
    } elseif (strpos($filename, 'orange') !== false || strpos($filename, 'carotte') !== false || strpos($filename, 'patate') !== false) {
        return [255, 152, 0]; // Orange
    } elseif (strpos($filename, 'violet') !== false || strpos($filename, 'aubergine') !== false || strpos($filename, 'oignon-rouge') !== false) {
        return [156, 39, 176]; // Violet
    } elseif (strpos($filename, 'blanc') !== false || strpos($filename, 'chou') !== false || strpos($filename, 'navet') !== false || strpos($filename, 'champignon') !== false) {
        return [224, 224, 224]; // Blanc
    } elseif (strpos($filename, 'pomme-de-terre') !== false || strpos($filename, 'echalote') !== false) {
        return [205, 164, 133]; // Beige
    } else {
        return [76, 175, 80]; // Vert par défaut
    }
}

function createImage($filename, $productName) {
    $width = 400;
    $height = 300;
    
    $image = imagecreate($width, $height);
    $color = getColor($filename);
    
    $background = imagecolorallocate($image, $color[0], $color[1], $color[2]);
    $white = imagecolorallocate($image, 255, 255, 255);
    $darker = imagecolorallocate($image, max(0, $color[0]-40), max(0, $color[1]-40), max(0, $color[2]-40));
    
    imagefill($image, 0, 0, $background);
    
    // Motif décoratif
    for ($i = 0; $i < 30; $i++) {
        $x = rand(0, $width);
        $y = rand(0, $height);
        $size = rand(10, 30);
        imagefilledellipse($image, $x, $y, $size, $size, $darker);
    }
    
    // Texte centré
    $font_size = 4;
    $lines = explode(' ', $productName);
    $y_start = $height / 2 - (count($lines) * 20) / 2;
    
    foreach ($lines as $i => $line) {
        $text_width = imagefontwidth($font_size) * strlen($line);
        $x = ($width - $text_width) / 2;
        $y = $y_start + ($i * 20);
        imagestring($image, $font_size, $x, $y, $line, $white);
    }
    
    $filepath = 'assets/uploads/' . $filename;
    imagejpeg($image, $filepath, 85);
    imagedestroy($image);
    
    return true;
}

$created = 0;
$skipped = 0;

foreach ($products_images as $filename => $productName) {
    $filepath = 'assets/uploads/' . $filename;
    
    if (file_exists($filepath)) {
        echo "<p style='color: green;'>✅ {$productName} - {$filename} (existe déjà)</p>\n";
        $skipped++;
    } else {
        try {
            createImage($filename, $productName);
            echo "<p style='color: blue;'>🎨 {$productName} - {$filename} (créé)</p>\n";
            $created++;
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur {$productName}: " . $e->getMessage() . "</p>\n";
        }
    }
}

echo "<h3>Résumé :</h3>\n";
echo "<p>✅ Images existantes : {$skipped}</p>\n";
echo "<p>🎨 Images créées : {$created}</p>\n";
echo "<p><strong>Total traité : " . ($created + $skipped) . " images</strong></p>\n";

echo "<p><a href='admin/produits.php' style='background: #4CAF50; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>📦 Voir les produits dans l'admin</a></p>\n";
?>