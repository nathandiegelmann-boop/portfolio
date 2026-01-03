<?php
// Script pour assigner automatiquement les bonnes images aux produits selon leur nom
require_once 'includes/config.php';

echo "<h2>🎯 Attribution automatique des images aux produits</h2>\n";

// Récupérer tous les produits
$stmt = $pdo->prepare("SELECT id, nom as nom_produit, image FROM produits ORDER BY nom");
$stmt->execute();
$produits = $stmt->fetchAll();

// Mapping intelligent des noms de produits vers leurs images appropriées
$product_image_mapping = [
    // Légumes verts
    'concombre' => 'concombre.jpg',
    'courgette' => 'courgette.jpg',
    'brocoli' => 'brocoli.jpg',
    'chou-fleur' => 'chou-fleur.jpg',
    'épinard' => 'epinard.jpg',
    'spinach' => 'epinard.jpg',
    'laitue batavia' => 'laitue-batavia.jpg',
    'batavia' => 'laitue-batavia.jpg',
    'laitue romaine' => 'laitue-romaine.jpg',
    'romaine' => 'laitue-romaine.jpg',
    'roquette' => 'roquette.jpg',
    'mâche' => 'mache.jpg',
    'mache' => 'mache.jpg',
    'bette' => 'bette.jpg',
    'poireau' => 'poireau.jpg',
    'asperge verte' => 'asperge-verte.jpg',
    'asperge' => 'asperge-verte.jpg',
    'artichaut' => 'artichaut.jpg',
    'fenouil' => 'fenouil.jpg',
    'céleri branche' => 'celeri-branche.jpg',
    'celeri' => 'celeri-branche.jpg',
    'petits pois' => 'petits-pois.jpg',
    'pois' => 'petits-pois.jpg',
    'haricot vert' => 'haricot-vert.jpg',
    'haricot' => 'haricot-vert.jpg',
    'oseille' => 'oseille.jpg',
    'cresson' => 'cresson.jpg',
    
    // Légumes orange/rouges
    'carotte' => 'carotte.jpg',
    'tomate ronde' => 'tomate-ronde.jpg',
    'tomate' => 'tomate-ronde.jpg',
    'poivron rouge' => 'poivron-rouge.jpg',
    'aubergine' => 'aubergine.jpg',
    'betterave crue' => 'betterave-crue.jpg',
    'betterave cuite' => 'betterave-cuite.jpg',
    'betterave' => 'betterave-crue.jpg',
    'patate douce' => 'patate-douce.jpg',
    'radis rose' => 'radis-rose.jpg',
    'radis' => 'radis-rose.jpg',
    
    // Légumes jaunes
    'poivron jaune' => 'poivron-jaune.jpg',
    'oignon jaune' => 'oignon-jaune.jpg',
    
    // Légumes blancs/beiges
    'navet' => 'navet.jpg',
    'panais' => 'panais.jpg',
    'pomme de terre' => 'pomme-de-terre.jpg',
    'pommes de terre' => 'pomme-de-terre.jpg',
    'champignon de paris' => 'champignon-paris.jpg',
    'champignon' => 'champignon-paris.jpg',
    'ail rose' => 'ail-rose.jpg',
    'ail' => 'ail-rose.jpg',
    'échalote' => 'echalote.jpg',
    'echalote' => 'echalote.jpg',
    
    // Légumes violets/rouges
    'oignon rouge' => 'oignon-rouge.jpg',
    'poivron vert' => 'poivron-vert.jpg',
    'courge spaghetti' => 'courge-spaghetti.jpg',
    'courge' => 'courge-spaghetti.jpg',
    
    // Aromates
    'basilic frais' => 'basilic-frais.jpg',
    'basilic' => 'basilic-frais.jpg',
    'persil plat' => 'persil-plat.jpg',
    'persil' => 'persil-plat.jpg',
    'ciboulette' => 'ciboulette.jpg',
    'menthe fraîche' => 'menthe.jpg',
    'menthe' => 'menthe.jpg',
    'coriandre' => 'coriandre.jpg',
    'thym frais' => 'thym.jpg',
    'thym' => 'thym.jpg',
    'romarin' => 'romarin.jpg',
    'estragon' => 'estragon.jpg',
    'aneth frais' => 'aneth.jpg',
    'aneth' => 'aneth.jpg'
];

// Fonction pour trouver la meilleure image correspondante
function findBestImageMatch($productName, $mapping) {
    $productLower = strtolower($productName);
    
    // Recherche exacte
    if (isset($mapping[$productLower])) {
        return $mapping[$productLower];
    }
    
    // Recherche par mot-clé
    foreach ($mapping as $keyword => $image) {
        if (strpos($productLower, $keyword) !== false) {
            return $image;
        }
    }
    
    // Images par défaut selon le type détecté
    if (strpos($productLower, 'salade') !== false || strpos($productLower, 'laitue') !== false) {
        return 'laitue-batavia.jpg';
    } elseif (strpos($productLower, 'tomate') !== false) {
        return 'tomate-ronde.jpg';
    } elseif (strpos($productLower, 'carotte') !== false) {
        return 'carotte.jpg';
    } elseif (strpos($productLower, 'oignon') !== false) {
        return 'oignon-jaune.jpg';
    } elseif (strpos($productLower, 'poivron') !== false) {
        return 'poivron-rouge.jpg';
    } elseif (strpos($productLower, 'herbe') !== false || strpos($productLower, 'aromate') !== false) {
        return 'basilic-frais.jpg';
    }
    
    return null; // Aucune correspondance trouvée
}



$updated = 0;
$not_found = 0;
$skipped = 0;

echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>\n";
echo "<tr style='background: #f5f5f5;'>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>ID</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Produit</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Ancienne Image</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Nouvelle Image</th>\n";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Statut</th>\n";
echo "</tr>\n";

foreach ($produits as $produit) {
    $newImage = findBestImageMatch($produit['nom_produit'], $product_image_mapping);
    $currentImage = $produit['image'];
    
    if ($newImage === null) {
        echo "<tr style='background: #fff3cd;'>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['nom_produit']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($currentImage ?: 'Aucune') . "</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>❌ Non trouvée</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>⚠️ Pas de correspondance</td>\n";
        echo "</tr>\n";
        $not_found++;
        continue;
    }
    
    // Vérifier si l'image existe physiquement
    $imagePath = 'assets/uploads/' . $newImage;
    if (!file_exists($imagePath)) {
        echo "<tr style='background: #f8d7da;'>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['nom_produit']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($currentImage ?: 'Aucune') . "</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$newImage}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>❌ Fichier manquant</td>\n";
        echo "</tr>\n";
        continue;
    }
    
    // Si l'image est déjà la bonne, ne pas la changer
    if ($currentImage === $newImage) {
        echo "<tr style='background: #d1ecf1;'>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['nom_produit']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$currentImage}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$newImage}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Déjà correcte</td>\n";
        echo "</tr>\n";
        $skipped++;
        continue;
    }
    
    // Mettre à jour l'image
    try {
        $stmt = $pdo->prepare("UPDATE produits SET image = ? WHERE id = ?");
        $stmt->execute([$newImage, $produit['id']]);
        
        echo "<tr style='background: #d4edda;'>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['nom_produit']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($currentImage ?: 'Aucune') . "</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$newImage}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Mise à jour</td>\n";
        echo "</tr>\n";
        $updated++;
        
    } catch (Exception $e) {
        echo "<tr style='background: #f8d7da;'>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['id']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$produit['nom_produit']}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($currentImage ?: 'Aucune') . "</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$newImage}</td>\n";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>❌ Erreur DB</td>\n";
        echo "</tr>\n";
    }
}

echo "</table>\n";

echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 10px; border-left: 5px solid #007bff;'>\n";
echo "<h3>📊 Résumé de l'attribution :</h3>\n";
echo "<p style='color: green;'>✅ Images mises à jour : {$updated}</p>\n";
echo "<p style='color: blue;'>ℹ️ Déjà correctes : {$skipped}</p>\n";
echo "<p style='color: orange;'>⚠️ Non trouvées : {$not_found}</p>\n";
echo "<p><strong>Total produits traités : " . count($produits) . "</strong></p>\n";
echo "</div>\n";

echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='verification_images.php' style='background: #4CAF50; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🔍 Vérifier les images</a>\n";
echo "<a href='admin/produits.php' style='background: #2196F3; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>📦 Voir les produits</a>\n";
echo "<a href='catalogue.php' style='background: #FF9800; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>🛍️ Catalogue client</a>\n";
echo "</div>\n";
?>