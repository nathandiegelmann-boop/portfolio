<div class="admin-header">
    <div class="header-content">        <!-- Bouton menu mobile -->
        <button class="mobile-menu-toggle" id="mobileMenuBtn" style="display: none;">
            <span></span>
            <span></span>
            <span></span>
        </button>
                <div class="logo">
            <i class="fas fa-shield-halved"></i>
            <span>Admin Panel</span>
        </div>
        
        <nav class="admin-nav">
            <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            <a href="analytics.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Statistiques
            </a>
            
            <!-- Menu déroulant Contenu -->
            <div class="nav-dropdown">
                <button class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['projects.php', 'skills.php', 'experiences.php']) ? 'active' : ''; ?>">
                    <i class="fas fa-folder"></i> Contenu <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="projects.php" class="dropdown-item">
                        <i class="fas fa-folder-open"></i> Projets
                    </a>
                    <a href="skills.php" class="dropdown-item">
                        <i class="fas fa-brain"></i> Compétences
                    </a>
                    <a href="experiences.php" class="dropdown-item">
                        <i class="fas fa-briefcase"></i> Expériences
                    </a>
                </div>
            </div>
            
            <!-- Menu déroulant Communication -->
            <div class="nav-dropdown">
                <button class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['messages.php', 'spam.php']) ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Communication 
                    <?php
                    $unread_count = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
                    if ($unread_count > 0):
                    ?>
                        <span style="background: #EF4444; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75rem; margin-left: 5px;"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="messages.php" class="dropdown-item">
                        <i class="fas fa-envelope"></i> Messages
                        <?php if ($unread_count > 0): ?>
                            <span style="background: #EF4444; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: 5px;"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="spam.php" class="dropdown-item">
                        <i class="fas fa-shield-alt"></i> Anti-Spam
                    </a>
                </div>
            </div>
            
            <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Paramètres
            </a>
        </nav>
        
        <div class="header-actions">
            <a href="../index.php" target="_blank" class="btn-secondary">
                <i class="fas fa-eye"></i> Voir le site
            </a>
            <a href="logout.php" class="btn-danger">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
</div>

<style>
.nav-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle {
    cursor: pointer;
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dropdown-toggle .fa-chevron-down {
    font-size: 0.7rem;
    margin-left: 0.25rem;
    transition: transform 0.3s;
}

.nav-dropdown:hover .fa-chevron-down {
    transform: rotate(180deg);
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #1e293b;
    min-width: 200px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    border-radius: 8px;
    margin-top: 0.5rem;
    z-index: 1000;
    padding: 0.5rem 0;
}

.nav-dropdown:hover .dropdown-menu {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    color: #cbd5e1;
    text-decoration: none;
    transition: all 0.2s;
    font-size: 0.95rem;
}

.dropdown-item:hover {
    background: #334155;
    color: #ffffff;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
}
</style>
<!-- Overlay pour le menu mobile -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<script>
// Script pour le menu mobile
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const adminNav = document.getElementById('adminNav');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const dropdowns = document.querySelectorAll('.nav-dropdown');
    
    // Toggle menu mobile
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            adminNav.classList.toggle('active');
            mobileOverlay.classList.toggle('active');
            document.body.style.overflow = adminNav.classList.contains('active') ? 'hidden' : '';
        });
    }
    
    // Fermer le menu en cliquant sur l'overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            mobileMenuBtn.classList.remove('active');
            adminNav.classList.remove('active');
            this.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // Toggle dropdowns sur mobile
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        if (toggle) {
            toggle.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    dropdown.classList.toggle('active');
                }
            });
        }
    });
    
    // Fermer le menu quand on clique sur un lien
    const navLinks = document.querySelectorAll('.nav-link:not(.dropdown-toggle), .dropdown-item');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                mobileMenuBtn.classList.remove('active');
                adminNav.classList.remove('active');
                mobileOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
});
</script>