<?php
// Vérifier que ce fichier est inclus et non accédé directement
if (!defined('PORTFOLIO_INIT')) {
    die('Accès direct non autorisé');
}

// Définir la page active pour la navigation
$current_page = $current_page ?? basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- Navigation -->
<nav class="cyber-nav">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="index.php" style="text-decoration: none; color: inherit;">
                <span>&lt;NATHAN/&gt;</span>
            </a>
        </div>
        
        <div class="nav-menu">
            <a href="index.php" class="nav-link<?php echo $current_page === 'index' ? ' active' : ''; ?>">ACCUEIL</a>
            <a href="projets.php" class="nav-link<?php echo $current_page === 'projets' ? ' active' : ''; ?>">PROJETS</a>
            <a href="competences.php" class="nav-link<?php echo $current_page === 'competences' ? ' active' : ''; ?>">COMPÉTENCES</a>
            <a href="a-propos.php" class="nav-link<?php echo $current_page === 'a-propos' ? ' active' : ''; ?>">À PROPOS</a>
            <a href="contact.php" class="nav-link<?php echo $current_page === 'contact' ? ' active' : ''; ?>">CONTACT</a>
        </div>
        
        <div class="nav-actions">
            <?php if (isAdmin()): ?>
                <a href="dashboard.php" class="admin-btn">
                    <i class="fas fa-cog"></i> ADMIN
                </a>
            <?php else: ?>
                <a href="login.php" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> LOGIN
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Menu mobile -->
        <div class="mobile-menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>