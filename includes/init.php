<?php
// Fichier d'initialisation du portfolio - À inclure en premier dans chaque page
define('PORTFOLIO_INIT', true);

// Configuration et connexion base de données
require_once __DIR__ . '/config.php';

// Fonction pour initialiser une page
function init_page($page_config = []) {
    // Configuration par défaut
    $config = array_merge([
        'title' => '',
        'description' => '',
        'current_page' => basename($_SERVER['PHP_SELF'], '.php'),
        'additional_css' => [],
        'extra_css' => '',
        'extra_js' => '',
        'extra_js_files' => [],
        'extra_meta' => '',
        'body_class' => '',
        'body_style' => ''
    ], $page_config);
    
    // Rendre les variables disponibles globalement
    foreach ($config as $key => $value) {
        $GLOBALS['page_' . $key] = $value;
        $GLOBALS[$key] = $value; // Aussi disponible sans préfixe
    }
    
    return $config;
}

// Fonction pour inclure le header
function include_header($config = []) {
    init_page($config);
    include __DIR__ . '/header.php';
}

// Fonction pour inclure la navigation
function include_nav() {
    include __DIR__ . '/nav.php';
}

// Fonction pour inclure le footer
function include_footer() {
    include __DIR__ . '/footer.php';
}

// Fonction complète pour page simple
function render_page($content_file, $config = []) {
    include_header($config);
    include_nav();
    
    if (file_exists($content_file)) {
        include $content_file;
    } else {
        echo "<div class='error'>Contenu non trouvé: $content_file</div>";
    }
    
    include_footer();
}
?>