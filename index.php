<?php
// Nathan Diegelmann - Portfolio One Page Complet
require_once __DIR__ . '/includes/config.php';

// Tracking des visiteurs
require_once __DIR__ . '/includes/tracker.php';

// Récupération TOUS les projets
$projects_sql = "SELECT * FROM projects ORDER BY is_featured DESC, created_at DESC";
$projects_stmt = $pdo->query($projects_sql);
$all_projects = $projects_stmt->fetchAll();

// Récupération TOUTES les compétences
$skills_sql = "SELECT * FROM skills ORDER BY category, level_percentage DESC";
$skills_stmt = $pdo->query($skills_sql);
$all_skills = $skills_stmt->fetchAll();

// Organiser par catégorie
$skills_by_category = [];
foreach ($all_skills as $skill) {
    $skills_by_category[$skill['category']][] = $skill;
}

// Labels des catégories
$category_labels = [
    'programming' => '<i class="fas fa-code"></i> Programmation',
    'languages' => '<i class="fas fa-language"></i> Langues',
    'tools' => '<i class="fas fa-tools"></i> Outils & Design',
    'soft_skills' => '<i class="fas fa-users"></i> Compétences Relationnelles',
    'sports' => '<i class="fas fa-dumbbell"></i> Sport & Forme'
];

// Récupération des expériences
$experiences_sql = "SELECT * FROM experiences ORDER BY start_date DESC, display_order ASC";
$experiences_stmt = $pdo->query($experiences_sql);
// ==========================================
// GÉNÉRATION DU TOKEN CSRF (Protection anti-piratage)
// ==========================================
// Si le token n'existe pas dans la session, on en crée un nouveau
if (!isset($_SESSION['csrf_token'])) {
    // random_bytes(32) = génère 32 octets aléatoires (256 bits)
    // bin2hex() = convertit en texte hexadécimal lisible
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Le token est maintenant stocké dans la session de l'utilisateur
// Il sera différent pour chaque visiteur et chaque session
$experiences = $experiences_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Portfolio Complet</title>
    <meta name="description" content="Portfolio complet de Nathan Diegelmann - Développeur web, étudiant Digital Campus, passionné d'innovation technologique">
    
    <!-- Fonts - Dark Mode Élégant -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Fira+Code:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS - Cache Busting -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/one-page.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/responsive-fixes.css?v=<?php echo time(); ?>">
    
    <!-- Auto-refresh (décommenter pour activer) -->
    <!-- <script src="assets/js/auto-refresh.js" defer></script> -->
</head>
<body>
    <!-- Bouton de navigation flottant -->
    <button id="floatingNavBtn" style="position: fixed; top: 20px; right: 20px; z-index: 1000; width: 50px; height: 50px; border-radius: 50%; background: rgba(30, 41, 59, 0.95); border: 1px solid rgba(51, 65, 85, 0.8); cursor: pointer; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 5px; backdrop-filter: blur(20px); transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);">
        <span style="width: 24px; height: 2.5px; background: linear-gradient(90deg, #F59E0B, #EF4444); transition: all 0.3s ease; border-radius: 2px;"></span>
        <span style="width: 24px; height: 2.5px; background: linear-gradient(90deg, #F59E0B, #EF4444); transition: all 0.3s ease; border-radius: 2px;"></span>
        <span style="width: 24px; height: 2.5px; background: linear-gradient(90deg, #F59E0B, #EF4444); transition: all 0.3s ease; border-radius: 2px;"></span>
    </button>
    
    <!-- Menu de navigation -->
    <div id="floatingNavMenu" style="position: fixed; top: 80px; right: 20px; z-index: 999; background: rgba(30, 41, 59, 0.98); border: 1px solid rgba(51, 65, 85, 1); border-radius: 16px; padding: 20px; backdrop-filter: blur(20px) saturate(180%); box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6); display: none; flex-direction: column; gap: 8px; min-width: 200px;">
        <a href="#accueil" class="floating-nav-link" style="color: #E2E8F0; text-decoration: none; padding: 12px 16px; border-radius: 10px; transition: all 0.3s ease; font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.3px; cursor: pointer; display: block; background: transparent; border-left: 3px solid transparent;">ACCUEIL</a>
        <a href="#projets" class="floating-nav-link" style="color: #E2E8F0; text-decoration: none; padding: 12px 16px; border-radius: 10px; transition: all 0.3s ease; font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.3px; cursor: pointer; display: block; background: transparent; border-left: 3px solid transparent;">PROJETS</a>
        <a href="#competences" class="floating-nav-link" style="color: #E2E8F0; text-decoration: none; padding: 12px 16px; border-radius: 10px; transition: all 0.3s ease; font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.3px; cursor: pointer; display: block; background: transparent; border-left: 3px solid transparent;">COMPÉTENCES</a>
        <a href="#parcours" class="floating-nav-link" style="color: #E2E8F0; text-decoration: none; padding: 12px 16px; border-radius: 10px; transition: all 0.3s ease; font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.3px; cursor: pointer; display: block; background: transparent; border-left: 3px solid transparent;">PARCOURS</a>
        <a href="#apropos" class="floating-nav-link" style="color: #E2E8F0; text-decoration: none; padding: 12px 16px; border-radius: 10px; transition: all 0.3s ease; font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.3px; cursor: pointer; display: block; background: transparent; border-left: 3px solid transparent;">À PROPOS</a>
        <a href="#contact" class="floating-nav-link" style="color: #E2E8F0; text-decoration: none; padding: 12px 16px; border-radius: 10px; transition: all 0.3s ease; font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.3px; cursor: pointer; display: block; background: transparent; border-left: 3px solid transparent;">CONTACT</a>
        <div style="border-top: 1px solid rgba(51, 65, 85, 0.8); margin: 8px 0;"></div>
        <a href="admin/login.php" class="floating-nav-link" style="color: #94A3B8; text-decoration: none; padding: 12px 16px; border-radius: 10px; transition: all 0.3s ease; font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.3px; cursor: pointer; display: flex; align-items: center; gap: 8px; background: transparent; border-left: 3px solid transparent; font-size: 13px;">
            <i class="fas fa-shield-halved" style="font-size: 14px;"></i>
            <span>ADMINISTRATION</span>
        </a>
    </div>
    
    <!-- Section Accueil / Hero -->
    <section id="accueil" class="hero section-dark">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="status-dot"></span>
                <span class="inline-code">DISPONIBLE POUR ALTERNANCE/STAGE</span>
            </div>
            
            <h1 class="hero-title">
                <span class="neon-text"><?php echo $nathan_info['nom']; ?></span>
            </h1>
            
            <h2 class="hero-subtitle">
                <span class="syntax-highlight">
                    <span class="keyword">class</span> <span class="function">DeveloppeurWeb</span> 
                    <span class="keyword">implements</span> <span class="function">Innovation</span>
                </span>
            </h2>
            
            <div class="hero-description">
                <p>Étudiant en Bachelor Développement Web à Digital Campus de <?php echo $nathan_info['age']; ?> ans</p>
                <p>Passionné par le code et l'innovation technologique</p>
            </div>
            
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?php echo $nathan_info['age']; ?></span>
                    <span class="stat-label">ANS</span>
                </div>
                <div class="stat">
                    <span class="stat-number"><?php echo count($all_projects); ?></span>
                    <span class="stat-label">PROJETS</span>
                </div>
                <div class="stat">
                    <span class="stat-number"><?php echo count($all_skills); ?></span>
                    <span class="stat-label">COMPÉTENCES</span>
                </div>
                <div class="stat">
                    <span class="stat-number">0</span>
                    <span class="stat-label">ANS XP</span>
                </div>
            </div>
            
            <div class="hero-buttons">
                <a href="#projets" class="cyber-btn primary">
                    <span>VOIR PROJETS</span>
                    <i class="fas fa-rocket"></i>
                </a>
                <a href="#contact" class="cyber-btn secondary">
                    <span>CONTACT</span>
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Section Projets -->
    <section id="projets" class="section-dark">
        <div class="container container-1400">
            <h2 class="section-title-main">MES PROJETS</h2>
            <p class="section-subtitle">
                Découvrez mes réalisations en développement web et logiciel
            </p>
            
            <div class="projects-notice">
                <i class="fas fa-info-circle"></i>
                <p>
                    <strong>Note :</strong> Les projets présentés sont des maquettes à but démonstratif et pédagogique. 
                    Ils ne sont pas actuellement utilisables en production et servent à illustrer mes compétences techniques.
                </p>
            </div>
            
            <div class="projects-grid-full">
                <?php foreach ($all_projects as $project): ?>
                <div class="project-card-full">
                    <?php if ($project['is_featured']): ?>
                    <span class="project-featured-badge">★ VEDETTE</span>
                    <?php endif; ?>
                    
                    <h3 class="project-title">
                        <?php echo htmlspecialchars($project['title']); ?>
                    </h3>
                    
                    <p class="project-desc">
                        <?php 
                        // Utiliser description et limiter à 150 caractères
                        $desc = $project['description'] ?? '';
                        echo htmlspecialchars(strlen($desc) > 150 ? substr($desc, 0, 150) . '...' : $desc); 
                        ?>
                    </p>
                    
                    <div class="project-tech">
                        <?php 
                        $techs = explode(',', $project['technologies']);
                        foreach ($techs as $tech): 
                        ?>
                        <span class="tech-tag"><?php echo trim(htmlspecialchars($tech)); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Section Compétences -->
    <section id="competences" class="section-dark">
        <div class="container" style="max-width: 1400px; margin: 0 auto;">
            <h2 class="section-title-main">COMPÉTENCES</h2>
            <p class="section-subtitle">
                Mes compétences techniques et personnelles
            </p>
            
            <div class="skills-columns">
                <?php foreach ($skills_by_category as $category => $skills): ?>
                <div class="skills-category">
                    <h3 class="category-title">
                        <?php echo $category_labels[$category] ?? ucfirst($category); ?>
                    </h3>
                    
                    <div class="skills-list">
                        <?php foreach ($skills as $skill): ?>
                        <div class="skill-item">
                            <div class="skill-name">
                                <span><?php echo htmlspecialchars($skill['name']); ?></span>
                            </div>
                            <div class="skill-bar-bg">
                                <div class="skill-bar-fill" style="width: <?php echo $skill['level_percentage']; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Section Parcours / Timeline -->
    <section id="parcours" class="section-dark">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <h2 class="section-title-main">MON PARCOURS</h2>
            <p class="section-subtitle">
                Formation et expériences professionnelles
            </p>
            
            <div class="timeline">
                <?php foreach ($experiences as $exp): ?>
                <div class="timeline-item">
                    <div class="timeline-date">
                        <i class="far fa-calendar-alt"></i>
                        <?php 
                        $start = new DateTime($exp['start_date']);
                        $end = $exp['end_date'] ? new DateTime($exp['end_date']) : null;
                        echo $start->format('M Y');
                        echo $end ? ' - ' . $end->format('M Y') : ' - Aujourd\'hui';
                        ?>
                    </div>
                    
                    <h3 class="timeline-title">
                        <?php echo htmlspecialchars($exp['title']); ?>
                    </h3>
                    
                    <div class="timeline-company">
                        <i class="fas fa-building"></i>
                        <?php echo htmlspecialchars($exp['institution'] ?? ''); ?>
                        <?php if (!empty($exp['location'])): ?>
                        <span class="timeline-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($exp['location']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($exp['description']): ?>
                    <div class="timeline-desc">
                        <?php echo nl2br(htmlspecialchars($exp['description'])); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Section À Propos -->
    <section id="apropos" class="section-dark">
        <div class="about-content">
            <h2 class="section-title-main">QUI SUIS-JE ?</h2>
            <p class="section-subtitle">
                Développeur passionné et créatif
            </p>
            
            <div class="about-intro">
                <p class="about-intro-text">
                    Salut ! Je suis <strong>Nathan Diegelmann</strong>, 
                    un étudiant de <?php echo $nathan_info['age']; ?> ans passionné par le développement web et l'innovation technologique. 
                </p>
                <p class="about-intro-secondary">
                    Actuellement en <strong>Bachelor Développement Web à Digital Campus</strong>, 
                    je combine mes études avec une expérience professionnelle de maraîcher depuis septembre 2023 
                    chez Marcher Brunoy. J'aime créer des projets innovants et relever des défis techniques !
                </p>
            </div>
            
            <div class="about-grid">
                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="about-card-title">Formation</h3>
                    <p class="about-card-text">
                        <strong>Digital Campus</strong><br>
                        Bachelor Développement Web<br>
                        Bac obtenu en 2024
                    </p>
                </div>
                
                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="about-card-title">Localisation</h3>
                    <p class="about-card-text">
                        <strong><?php echo $nathan_info['ville']; ?></strong><br>
                        Région Île-de-France<br>
                        France
                    </p>
                </div>
                
                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-language"></i>
                    </div>
                    <h3 class="about-card-title">Langues</h3>
                    <p class="about-card-text">
                        <strong>Français</strong> - Natif<br>
                        <strong>Anglais</strong> - Intermédiaire<br>
                        <strong>Espagnol</strong> - Notions
                    </p>
                </div>
            </div>
            
            <!-- Technologies Maîtrisées -->
            <div class="technologies-section">
                <h3 class="technologies-title">
                    <i class="fas fa-code"></i> Technologies Maîtrisées
                </h3>
                <p class="technologies-subtitle">
                    Les différentes technologies que j'utilise pour concevoir mes projets et développer mes compétences durant mes études.
                </p>
                
                <div class="technologies-grid">
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h4 class="tech-name">HTML-CSS</h4>
                        <p class="tech-description">
                            Le HTML est un langage de structuration pour une page web couplé avec CSS qui, quant à lui applique du style. C'est-à-dire du design, de l'esthétisme à cette même structure.
                        </p>
                    </div>
                    
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fab fa-php"></i>
                        </div>
                        <h4 class="tech-name">PHP</h4>
                        <p class="tech-description">
                            Langage de programmation côté serveur permettant de créer des sites web dynamiques et interactifs. Idéal pour la gestion de bases de données et l'authentification.
                        </p>
                    </div>
                    
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h4 class="tech-name">SQL / MySQL</h4>
                        <p class="tech-description">
                            Système de gestion de bases de données relationnelles. Permet de stocker, organiser et récupérer efficacement des données pour vos applications web.
                        </p>
                    </div>
                    
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fab fa-python"></i>
                        </div>
                        <h4 class="tech-name">Python</h4>
                        <p class="tech-description">
                            Langage polyvalent utilisé pour le développement web, l'automatisation, l'analyse de données et le machine learning. Simple et puissant.
                        </p>
                    </div>
                    
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fab fa-js"></i>
                        </div>
                        <h4 class="tech-name">JavaScript</h4>
                        <p class="tech-description">
                            Langage de programmation très populaire intégré à chaque navigateur, permettant de créer des interfaces interactives et dynamiques.
                        </p>
                    </div>
                    
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fab fa-git-alt"></i>
                        </div>
                        <h4 class="tech-name">Git / GitHub</h4>
                        <p class="tech-description">
                            Système de contrôle de version permettant de suivre les modifications du code, collaborer efficacement et gérer les projets en équipe.
                        </p>
                    </div>
                    
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fas fa-pencil-ruler"></i>
                        </div>
                        <h4 class="tech-name">Adobe Illustrator</h4>
                        <p class="tech-description">
                            Logiciel de création graphique vectorielle professionnel. Permet de concevoir des logos, illustrations, icônes et designs avec une précision parfaite.
                        </p>
                    </div>
                    
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fas fa-image"></i>
                        </div>
                        <h4 class="tech-name">Adobe Photoshop</h4>
                        <p class="tech-description">
                            Logiciel de retouche et de création d'images. Idéal pour la manipulation de photos, le webdesign et la création de visuels professionnels.
                        </p>
                    </div>
                    
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fab fa-wordpress"></i>
                        </div>
                        <h4 class="tech-name">WordPress</h4>
                        <p class="tech-description">
                            Système de gestion de contenu (CMS) le plus populaire au monde. Permet de créer et gérer des sites web professionnels avec une grande flexibilité.
                        </p>
                    </div>
                    
                    <div class="tech-card">
                        <div class="tech-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h4 class="tech-name">UML</h4>
                        <p class="tech-description">
                            Langage de modélisation unifié pour la conception et la documentation de systèmes logiciels. Essentiel pour la planification et l'architecture de projets.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Contact -->
    <section id="contact" class="section-dark">
        <div class="container container-1200">
            <h2 class="section-title-main">CONTACT</h2>
            <p class="section-subtitle">
                Travaillons ensemble sur votre prochain projet !
            </p>
            
            <div class="contact-grid">
                <a href="mailto:<?php echo $nathan_info['email']; ?>" class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <div class="contact-info-label">Email</div>
                        <div class="contact-info-value"><?php echo $nathan_info['email']; ?></div>
                    </div>
                </a>
                
                <a href="tel:<?php echo $nathan_info['telephone']; ?>" class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <div class="contact-info-label">Téléphone</div>
                        <div class="contact-info-value"><?php echo $nathan_info['telephone']; ?></div>
                    </div>
                </a>
                
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="contact-info-label">Disponibilité</div>
                        <div class="contact-info-value">Réponse sous 48h</div>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire de contact -->
            <div class="contact-form-container">
                <h3 class="contact-form-title">
                    <i class="fas fa-paper-plane"></i> Envoyez-moi un message
                    <?php
                    // Afficher le statut du dernier test automatique
                    $status_file = __DIR__ . '/logs/contact_test_status.json';
                    if (file_exists($status_file)) {
                        $status = json_decode(file_get_contents($status_file), true);
                        if ($status) {
                            $statusClass = $status['success'] ? 'test-success' : 'test-error';
                            $statusIcon = $status['success'] ? 'fa-check-circle' : 'fa-times-circle';
                            $statusTitle = $status['success'] 
                                ? 'Dernier test automatique réussi le ' . $status['date_fr']
                                : 'Dernier test automatique échoué le ' . $status['date_fr'] . ' - ' . $status['message'];
                            echo '<span class="form-test-status ' . $statusClass . '" title="' . htmlspecialchars($statusTitle) . '">';
                            echo '<i class="fas ' . $statusIcon . '"></i>';
                            echo '</span>';
                        }
                    }
                    ?>
                </h3>
                <p class="contact-form-subtitle">
                    Vous avez un projet ? Une opportunité d'alternance ? Remplissez le formulaire ci-dessous.
                </p>
                
                <form id="contactForm">
                    <!-- Honeypot anti-spam (champ invisible) -->
                    <input type="text" name="website" style="display: none !important; position: absolute; left: -9999px;" tabindex="-1" autocomplete="off">
                    <!-- Token CSRF - Protection contre les attaques par falsification -->
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Sujet *</label>
                        <input type="text" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" required rows="6"></textarea>
                    </div>
                    
                    <button type="submit" class="cyber-btn primary form-submit-btn">
                        <i class="fas fa-paper-plane"></i>
                        <span>ENVOYER LE MESSAGE</span>
                    </button>
                </form>
                
                <div id="formMessage"></div>
            </div>
            
            <div class="alternance-box">
                <h3 class="alternance-title">
                    <i class="fas fa-briefcase"></i> Recherche d'Alternance/Stage
                </h3>
                <p class="alternance-text">
                    Je recherche un <strong>stage de 3 mois à partir de février 2026</strong> ou une <strong>alternance à partir de septembre 2026</strong> en développement web.<br>
                    N'hésitez pas à me contacter pour discuter de votre projet !
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="portfolio-footer">
        <div class="footer-container">
            <div class="footer-nav">
                <a href="#accueil">Accueil</a>
                <a href="#projets">Projets</a>
                <a href="#competences">Compétences</a>
                <a href="#parcours">Parcours</a>
                <a href="#apropos">À Propos</a>
                <a href="#contact" id="footerContactBtn">Contact</a>
                <a href="cgu.php">CGU</a>
            </div>
            
            <div class="footer-divider">
                <p class="footer-copyright">
                    © <?php echo date('Y'); ?> Nathan Diegelmann - Développeur Web
                </p>
                <p class="footer-signature">
                    &lt;/&gt; avec 💚 et ☕
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts - Cache Busting -->
    <script src="assets/js/mobile-menu.js?v=<?php echo time(); ?>"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du bouton de navigation flottant
            const floatingNavBtn = document.getElementById('floatingNavBtn');
            const floatingNavMenu = document.getElementById('floatingNavMenu');
            
            if (!floatingNavBtn || !floatingNavMenu) {
                console.error('Menu burger non trouvé');
                return;
            }
            
            console.log('Menu burger initialisé');
            
            floatingNavBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
            // Son de clic (identique aux boutons cyber-btn)
            if (typeof playSound === 'function') {
                playSound('click');
            } else {
                // Fallback si la fonction n'est pas disponible
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.setValueAtTime(1200, audioContext.currentTime);
                oscillator.frequency.exponentialRampToValueAtTime(600, audioContext.currentTime + 0.1);
                gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
                
                oscillator.start();
                oscillator.stop(audioContext.currentTime + 0.1);
            }
            
            if (floatingNavMenu.style.display === 'none' || floatingNavMenu.style.display === '') {
                floatingNavMenu.style.display = 'flex';
                floatingNavBtn.style.transform = 'rotate(90deg)';
            } else {
                floatingNavMenu.style.display = 'none';
                floatingNavBtn.style.transform = 'rotate(0deg)';
            }
        });
        
        // Fermer le menu lors du clic sur un lien
        document.querySelectorAll('#floatingNavMenu a, .floating-nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Si c'est un lien d'ancrage (commence par #), on fait le smooth scroll
                if (href.startsWith('#')) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const target = document.querySelector(href);
                    
                    if (target) {
                        window.scrollTo({
                            top: target.offsetTop,
                            behavior: 'smooth'
                        });
                    }
                    
                    floatingNavMenu.style.display = 'none';
                    floatingNavBtn.style.transform = 'rotate(0deg)';
                } else {
                    // Pour les liens externes (comme admin/login.php), on laisse le comportement normal
                    // On ferme juste le menu
                    floatingNavMenu.style.display = 'none';
                    floatingNavBtn.style.transform = 'rotate(0deg)';
                }
            });
            
            // Effet hover
            link.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(245, 158, 11, 0.1)';
                this.style.paddingLeft = '15px';
            });
            link.addEventListener('mouseleave', function() {
                this.style.background = 'transparent';
                this.style.paddingLeft = '10px';
            });
        });
        
        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!floatingNavBtn.contains(e.target) && !floatingNavMenu.contains(e.target)) {
                floatingNavMenu.style.display = 'none';
                floatingNavBtn.style.transform = 'rotate(0deg)';
            }
        });
        
        // Smooth scroll pour les liens d'ancrage
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop,
                        behavior: 'smooth'
                    });
                    // Fermer le menu après navigation
                    floatingNavMenu.style.display = 'none';
                    floatingNavBtn.style.transform = 'rotate(0deg)';
                }
            });
        });
        
        // Animation des barres de compétences au scroll
        const skillsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bars = entry.target.querySelectorAll('.skill-bar-fill');
                    bars.forEach(bar => {
                        const width = bar.style.width;
                        bar.style.width = '0';
                        setTimeout(() => {
                            bar.style.width = width;
                        }, 100);
                    });
                }
            });
        }, { threshold: 0.3 });
        
        document.querySelectorAll('.skills-category').forEach(category => {
            skillsObserver.observe(category);
        });
        
        // Animation des projets au scroll
        const projectsObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.project-card-full').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            projectsObserver.observe(card);
        });
        
        // Highlight nav actif au scroll
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');
        
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 100)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
        
        // Gestion du formulaire de contact
        document.getElementById('contactForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const formMessage = document.getElementById('formMessage');
            
            // Désactiver le bouton
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>ENVOI EN COURS...</span>';
            
            try {
                const response = await fetch('send_message.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    formMessage.style.display = 'block';
                    formMessage.style.background = 'rgba(34, 197, 94, 0.2)';
                    formMessage.style.border = '1px solid #22C55E';
                    formMessage.style.color = '#22C55E';
                    formMessage.innerHTML = '<i class="fas fa-check-circle"></i> ' + result.message;
                    e.target.reset();
                } else {
                    formMessage.style.display = 'block';
                    formMessage.style.background = 'rgba(255, 0, 0, 0.2)';
                    formMessage.style.border = '1px solid #ff0000';
                    formMessage.style.color = '#ff0000';
                    formMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + result.message;
                }
            } catch (error) {
                formMessage.style.display = 'block';
                formMessage.style.background = 'rgba(255, 0, 0, 0.2)';
                formMessage.style.border = '1px solid #ff0000';
                formMessage.style.color = '#ff0000';
                formMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> Erreur lors de l\'envoi. Veuillez réessayer.';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <span>ENVOYER LE MESSAGE</span>';
                
                // Masquer le message après 5 secondes
                setTimeout(() => {
                    formMessage.style.display = 'none';
                }, 5000);
            }
        });
        
        // Animation des inputs du formulaire
        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = '#F59E0B';
                this.style.boxShadow = '0 0 0 3px rgba(245, 158, 11, 0.2)';
            });
            
            input.addEventListener('blur', function() {
                this.style.borderColor = '#334155';
                this.style.boxShadow = 'none';
            });
        });
        
        
        }); // Fin DOMContentLoaded
        // Changement de titre quand on quitte l'onglet
        const originalTitle = document.title;
        const messages = [
            '👋 Reviens par ici !',
            '👀 Hey ! Regarde par là !',
            '✨ N\'oublie pas mon portfolio !',
            '🚀 Reviens voir mes projets !',
            '💼 Une opportunité t\'attend ici !',
            '👨‍💻 Nathan Diegelmann - Développeur Web',
            '⚡ Retourne sur mon portfolio !'
        ];
        
        let messageIndex = 0;
        let titleInterval = null;
        
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // L'utilisateur a quitté l'onglet
                messageIndex = Math.floor(Math.random() * messages.length);
                
                // Changer le titre toutes les 2 secondes
                titleInterval = setInterval(() => {
                    document.title = messages[messageIndex];
                    messageIndex = (messageIndex + 1) % messages.length;
                }, 2000);
                
                // Afficher le premier message immédiatement
                document.title = messages[messageIndex];
            } else {
                // L'utilisateur est revenu sur l'onglet
                if (titleInterval) {
                    clearInterval(titleInterval);
                }
                document.title = originalTitle;
            }
        });
        
        // Détecter quand l'utilisateur quitte la page
        window.addEventListener('beforeunload', function() {
            // Envoyer une requête pour terminer la session
            if (navigator.sendBeacon) {
                navigator.sendBeacon('<?php echo SITE_URL; ?>/includes/end_session.php');
            }
        });
        
    </script>
</body>
</html>

