<?php
// Vérifier que ce fichier est inclus et non accédé directement
if (!defined('PORTFOLIO_INIT')) {
    die('Accès direct non autorisé');
}

// S'assurer que $nathan_info est disponible avec des valeurs par défaut
if (!isset($nathan_info)) {
    // Inclure le config si pas déjà fait
    if (!isset($pdo)) {
        require_once __DIR__ . '/config.php';
    }
}

// Valeurs par défaut sécurisées
$safe_nathan_info = [
    'email' => $nathan_info['email'] ?? 'nathan.diegelmann@gmail.com',
    'telephone' => $nathan_info['telephone'] ?? '06 35 21 84 95',
    'instagram' => $nathan_info['instagram'] ?? '@nth.dgl',
    'ville' => $nathan_info['ville'] ?? 'Boussy-Saint-Antoine'
];
?>
    <!-- Footer -->
    <footer class="cyber-footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4 class="footer-title">Nathan Diegelmann</h4>
                    <p class="footer-description">
                        Développeur web passionné spécialisé en HTML, CSS, PHP et SQL. 
                        Étudiant en Bachelor Développement Web avec un goût pour les défis physiques et techniques.
                    </p>
                    <div class="footer-social">
                        <a href="mailto:<?php echo $safe_nathan_info['email']; ?>" class="social-link" title="Email">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="tel:<?php echo $safe_nathan_info['telephone']; ?>" class="social-link" title="Téléphone">
                            <i class="fas fa-phone"></i>
                        </a>
                        <a href="https://instagram.com/<?php echo ltrim($safe_nathan_info['instagram'], '@'); ?>" class="social-link" target="_blank" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" target="_blank" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-title">Navigation</h4>
                    <div class="footer-links">
                        <a href="index.php" class="footer-link">Accueil</a>
                        <a href="projets.php" class="footer-link">Projets</a>
                        <a href="competences.php" class="footer-link">Compétences</a>
                        <a href="a-propos.php" class="footer-link">À Propos</a>
                        <a href="contact.php" class="footer-link">Contact</a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-title">Compétences</h4>
                    <div class="footer-skills">
                        <span class="skill-badge">HTML5</span>
                        <span class="skill-badge">CSS3</span>
                        <span class="skill-badge">PHP</span>
                        <span class="skill-badge">MySQL</span>
                        <span class="skill-badge">Responsive</span>
                    </div>
                    <div class="footer-status">
                        <span class="status-indicator">
                            <span class="status-dot"></span>
                            Disponible pour projets
                        </span>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-title">Contact Rapide</h4>
                    <div class="footer-contact">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo $safe_nathan_info['ville']; ?></span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Bachelor Développement Web</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-dumbbell"></i>
                            <span>Powerlifting Passion</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="footer-copyright">
                        <p>&copy; <?php echo date('Y'); ?> Nathan Diegelmann. Tous droits réservés.</p>
                    </div>
                    <div class="footer-tech">
                        <span class="tech-info">
                            <i class="fas fa-code"></i>
                            Fait avec PHP & MySQL
                        </span>
                        <span class="tech-info">
                            <i class="fas fa-heart"></i>
                            Développé avec passion
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <!-- Scripts supplémentaires spécifiques à la page -->
    <?php if (isset($extra_js)): ?>
        <script><?php echo $extra_js; ?></script>
    <?php endif; ?>
    
    <!-- Scripts externes supplémentaires -->
    <?php if (isset($extra_js_files)): ?>
        <?php foreach ($extra_js_files as $js_file): ?>
            <script src="<?php echo $js_file; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>