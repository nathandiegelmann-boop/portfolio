<?php
// Forcer le rafraîchissement des images en ajoutant un timestamp
require_once 'includes/config.php';

echo "<h2>🔄 FORCE REFRESH DES IMAGES</h2>\n";

// Ajouter un paramètre de version aux images pour forcer le reload
$timestamp = time();

echo "<h3>✨ Images avec cache-busting :</h3>\n";

// Récupérer quelques produits pour test
$stmt = $pdo->prepare("SELECT id, nom, image FROM produits WHERE id IN (2, 3, 59, 61) ORDER BY id");
$stmt->execute();
$produits = $stmt->fetchAll();

echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;'>\n";

foreach ($produits as $produit) {
    $imagePath = 'assets/uploads/' . $produit['image'] . '?v=' . $timestamp;
    
    echo "<div style='background: white; border: 2px solid #4CAF50; border-radius: 10px; padding: 15px; text-align: center;'>\n";
    echo "<h4 style='color: #2E7D32; margin: 0 0 10px 0;'>{$produit['nom']}</h4>\n";
    echo "<img src='{$imagePath}' style='width: 100%; max-width: 250px; height: 200px; object-fit: cover; border-radius: 8px;' alt='{$produit['nom']}'>\n";
    echo "<p style='margin: 10px 0 0 0; font-size: 12px; color: #666;'>Image: {$produit['image']}</p>\n";
    echo "</div>\n";
}

echo "</div>\n";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 10px; margin: 20px 0;'>\n";
echo "<h3>🎯 INSTRUCTIONS :</h3>\n";
echo "<p><strong>1.</strong> Videz le cache de votre navigateur (Ctrl+Shift+Delete)</p>\n";
echo "<p><strong>2.</strong> Ou faites un rechargement forcé (Ctrl+F5)</p>\n";
echo "<p><strong>3.</strong> Ou ouvrez le catalogue en navigation privée</p>\n";
echo "<p><strong>4.</strong> Vérifiez si les images sont maintenant correctes</p>\n";
echo "</div>\n";

echo "<div style='text-align: center; margin: 20px 0;'>\n";
echo "<a href='catalogue.php?v={$timestamp}' style='background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; font-weight: bold;'>🛒 VOIR LE CATALOGUE (CACHE FORCÉ)</a>\n";
echo "</div>\n";

?>

<script>
// Force le rechargement des images côté client aussi
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        const src = img.src;
        img.src = '';
        img.src = src;
    });
    
    console.log('🔄 Images rechargées côté client');
});
</script>