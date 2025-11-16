<?php
/**
 * Template exemple pour nouvelles pages
 * Copier ce fichier et modifier selon les besoins
 */

// Inclure le système d'initialisation
require_once __DIR__ . '/includes/init.php';

// Configuration de la page
$page_config = [
    'title' => 'Titre de la page',
    'description' => 'Description SEO de la page',
    'current_page' => 'nom-page', // Sans .php
    'extra_css' => '
        /* CSS spécifique à cette page */
        .custom-section {
            padding: 4rem 2rem;
            background: var(--code-bg);
        }
    ',
    'extra_js' => '
        // JavaScript spécifique à cette page
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Page chargée !");
        });
    ',
    'extra_js_files' => [
        // 'assets/js/custom-library.js'
    ],
    'body_class' => '', // Classes CSS pour le body
    'body_style' => ''  // Styles inline pour le body
];

// Inclure header et navigation
include_header($page_config);
include_nav();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1 class="section-title">
            <span class="glitch" data-text="TITRE PAGE">TITRE PAGE</span>
        </h1>
        <p class="hero-description">
            Description de la page
        </p>
    </div>
</section>

<!-- Contenu Principal -->
<section class="main-content">
    <div class="container">
        <h2>Contenu de la page</h2>
        <p>Votre contenu ici...</p>
    </div>
</section>

<?php
// Inclure le footer
include_footer();
?>