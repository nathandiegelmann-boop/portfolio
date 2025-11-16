<?php
// Nathan Diegelmann - Page Projets
require_once __DIR__ . '/includes/config.php';

// Récupération des projets avec filtres
$category_filter = $_GET['category'] ?? 'all';
$status_filter = $_GET['status'] ?? 'all';

$sql = "SELECT * FROM projects WHERE 1=1";
$params = [];

if ($category_filter !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
}

if ($status_filter !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY is_featured DESC, created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Statistiques projets
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'development' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured
    FROM projects";
$stats = $pdo->query($stats_sql)->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Découvrez les projets Python et C++ de Nathan Diegelmann - Applications desktop, scripts d'automatisation et outils innovants.">
    
    <!-- CSS & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .projects-hero {
            background: linear-gradient(135deg, var(--darker-bg), var(--dark-bg));
            padding: 8rem 2rem 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .projects-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 800px;
            margin: 3rem auto 0;
        }
        
        .stat-box {
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid var(--neon-cyan);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
        }
        
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.3);
        }
        
        .stat-number {
            font-family: 'Orbitron', monospace;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--neon-cyan);
            text-shadow: 0 0 10px var(--neon-cyan);
        }
        
        .stat-label {
            color: var(--text-secondary);
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .filters-section {
            background: var(--dark-bg);
            padding: 2rem;
            border-bottom: 1px solid rgba(0, 255, 255, 0.2);
        }
        
        .filters-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .filter-label {
            font-weight: 600;
            color: var(--neon-cyan);
        }
        
        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 0.5rem 1rem;
            background: transparent;
            border: 1px solid rgba(0, 255, 255, 0.3);
            color: var(--text-secondary);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: rgba(0, 255, 255, 0.2);
            color: var(--neon-cyan);
            border-color: var(--neon-cyan);
        }
        
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .project-card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(0, 255, 255, 0.2);
            border-radius: var(--border-radius);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
        }
        
        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--neon-cyan), var(--neon-blue));
            transform: scaleX(0);
            transition: var(--transition);
        }
        
        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 255, 255, 0.3);
        }
        
        .project-card:hover::before {
            transform: scaleX(1);
        }
        
        .project-card.featured {
            border-color: var(--neon-pink);
            box-shadow: 0 0 20px rgba(255, 0, 128, 0.2);
        }
        
        .project-image {
            height: 200px;
            background: linear-gradient(45deg, var(--darker-bg), var(--dark-bg));
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .project-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(0, 255, 255, 0.1) 10px,
                rgba(0, 255, 255, 0.1) 20px
            );
        }
        
        .project-image i {
            font-size: 4rem;
            color: var(--neon-cyan);
            opacity: 0.7;
            z-index: 2;
            position: relative;
        }
        
        .project-content {
            padding: 1.5rem;
        }
        
        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .project-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .project-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-completed {
            background: rgba(0, 255, 0, 0.2);
            color: #00ff00;
            border: 1px solid #00ff00;
        }
        
        .status-development {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa500;
            border: 1px solid #ffa500;
        }
        
        .status-concept {
            background: rgba(128, 0, 255, 0.2);
            color: #8000ff;
            border: 1px solid #8000ff;
        }
        
        .project-description {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .project-tech {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .tech-tag {
            background: rgba(0, 128, 255, 0.2);
            color: var(--neon-blue);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid var(--neon-blue);
        }
        
        .project-actions {
            display: flex;
            gap: 1rem;
        }
        
        .project-btn {
            flex: 1;
            padding: 0.8rem;
            background: transparent;
            border: 1px solid var(--neon-cyan);
            color: var(--neon-cyan);
            border-radius: var(--border-radius);
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .project-btn:hover {
            background: rgba(0, 255, 255, 0.1);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
        }
        
        .project-btn.secondary {
            border-color: var(--text-muted);
            color: var(--text-muted);
        }
        
        .project-btn.secondary:hover {
            border-color: var(--neon-blue);
            color: var(--neon-blue);
            background: rgba(0, 128, 255, 0.1);
        }
        
        .featured-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--neon-pink);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 700;
            z-index: 3;
            animation: pulse 2s ease-in-out infinite alternate;
        }
        
        @media (max-width: 768px) {
            .projects-grid {
                grid-template-columns: 1fr;
                padding: 2rem 1rem;
            }
            
            .filters-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="cyber-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php" style="text-decoration: none; color: inherit;">
                    <span>&lt;NATHAN/&gt;</span>
                </a>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link">ACCUEIL</a>
                <a href="projets.php" class="nav-link active">PROJETS</a>
                <a href="competences.php" class="nav-link">COMPÉTENCES</a>
                <a href="a-propos.php" class="nav-link">À PROPOS</a>
                <a href="contact.php" class="nav-link">CONTACT</a>
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
        </div>
    </nav>

    <!-- Hero Projects -->
    <section class="projects-hero">
        <div class="container">
            <h1 class="section-title">
                <span class="glitch" data-text="MES PROJETS">MES PROJETS</span>
            </h1>
            <p class="hero-description">
                Découvrez mes créations en <strong>Python</strong> et <strong>C++</strong> - 
                Des outils d'automatisation aux applications desktop innovantes
            </p>
            
            <div class="projects-stats">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $stats['total']; ?></div>
                    <div class="stat-label">Projets Total</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $stats['completed']; ?></div>
                    <div class="stat-label">Terminés</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $stats['in_progress']; ?></div>
                    <div class="stat-label">En cours</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $stats['featured']; ?></div>
                    <div class="stat-label">Mis en avant</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtres -->
    <section class="filters-section">
        <div class="filters-container">
            <div class="filter-group">
                <span class="filter-label">CATÉGORIE:</span>
                <div class="filter-buttons">
                    <a href="?category=all&status=<?php echo $status_filter; ?>" 
                       class="filter-btn <?php echo $category_filter === 'all' ? 'active' : ''; ?>">
                        Tous
                    </a>
                    <a href="?category=python&status=<?php echo $status_filter; ?>" 
                       class="filter-btn <?php echo $category_filter === 'python' ? 'active' : ''; ?>">
                        Python
                    </a>
                    <a href="?category=cpp&status=<?php echo $status_filter; ?>" 
                       class="filter-btn <?php echo $category_filter === 'cpp' ? 'active' : ''; ?>">
                        C++
                    </a>
                    <a href="?category=automation&status=<?php echo $status_filter; ?>" 
                       class="filter-btn <?php echo $category_filter === 'automation' ? 'active' : ''; ?>">
                        Automation
                    </a>
                    <a href="?category=game&status=<?php echo $status_filter; ?>" 
                       class="filter-btn <?php echo $category_filter === 'game' ? 'active' : ''; ?>">
                        Gaming
                    </a>
                </div>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">STATUT:</span>
                <div class="filter-buttons">
                    <a href="?category=<?php echo $category_filter; ?>&status=all" 
                       class="filter-btn <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                        Tous
                    </a>
                    <a href="?category=<?php echo $category_filter; ?>&status=completed" 
                       class="filter-btn <?php echo $status_filter === 'completed' ? 'active' : ''; ?>">
                        Terminés
                    </a>
                    <a href="?category=<?php echo $category_filter; ?>&status=development" 
                       class="filter-btn <?php echo $status_filter === 'development' ? 'active' : ''; ?>">
                        En cours
                    </a>
                    <a href="?category=<?php echo $category_filter; ?>&status=concept" 
                       class="filter-btn <?php echo $status_filter === 'concept' ? 'active' : ''; ?>">
                        Concept
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Grille des projets -->
    <section class="projects-grid">
        <?php if (empty($projects)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
                <i class="fas fa-search" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 2rem;"></i>
                <h3 style="color: var(--text-secondary); margin-bottom: 1rem;">Aucun projet trouvé</h3>
                <p style="color: var(--text-muted);">Essayez de modifier les filtres ou 
                   <a href="projets.php" style="color: var(--neon-cyan);">voir tous les projets</a>
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card <?php echo $project['is_featured'] ? 'featured' : ''; ?>">
                    <?php if ($project['is_featured']): ?>
                        <div class="featured-badge">
                            <i class="fas fa-star"></i> FEATURED
                        </div>
                    <?php endif; ?>
                    
                    <div class="project-image">
                        <?php
                        $icons = [
                            'python' => 'fab fa-python',
                            'cpp' => 'fas fa-code',
                            'automation' => 'fas fa-robot',
                            'game' => 'fas fa-gamepad',
                            'web' => 'fas fa-globe',
                            'tool' => 'fas fa-tools'
                        ];
                        $icon = $icons[$project['category']] ?? 'fas fa-laptop-code';
                        ?>
                        <i class="<?php echo $icon; ?>"></i>
                    </div>
                    
                    <div class="project-content">
                        <div class="project-header">
                            <h3 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                            <span class="project-status status-<?php echo $project['status']; ?>">
                                <?php 
                                $status_labels = [
                                    'completed' => 'Terminé',
                                    'development' => 'En cours',
                                    'concept' => 'Concept'
                                ];
                                echo $status_labels[$project['status']];
                                ?>
                            </span>
                        </div>
                        
                        <p class="project-description">
                            <?php echo htmlspecialchars($project['short_description'] ?: substr($project['description'], 0, 120) . '...'); ?>
                        </p>
                        
                        <div class="project-tech">
                            <?php 
                            $languages = explode(',', $project['languages']);
                            foreach ($languages as $lang): 
                            ?>
                                <span class="tech-tag"><?php echo trim(htmlspecialchars($lang)); ?></span>
                            <?php endforeach; ?>
                            
                            <?php if ($project['technologies']): 
                                $techs = explode(',', $project['technologies']);
                                foreach (array_slice($techs, 0, 2) as $tech): 
                            ?>
                                <span class="tech-tag" style="border-color: var(--neon-purple); color: var(--neon-purple);">
                                    <?php echo trim(htmlspecialchars($tech)); ?>
                                </span>
                            <?php endforeach; endif; ?>
                        </div>
                        
                        <div class="project-actions">
                            <?php if ($project['github_link'] && $project['github_link'] !== '#'): ?>
                                <a href="<?php echo htmlspecialchars($project['github_link']); ?>" 
                                   class="project-btn" target="_blank">
                                    <i class="fab fa-github"></i> Code
                                </a>
                            <?php else: ?>
                                <span class="project-btn secondary">
                                    <i class="fas fa-lock"></i> Privé
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($project['demo_link']): ?>
                                <a href="<?php echo htmlspecialchars($project['demo_link']); ?>" 
                                   class="project-btn" target="_blank">
                                    <i class="fas fa-play"></i> Démo
                                </a>
                            <?php else: ?>
                                <a href="projet.php?id=<?php echo $project['id']; ?>" class="project-btn secondary">
                                    <i class="fas fa-info-circle"></i> Détails
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- CTA Admin -->
    <?php if (isAdmin()): ?>
        <section style="background: var(--darker-bg); padding: 3rem 2rem; text-align: center;">
            <h3 style="color: var(--neon-cyan); margin-bottom: 2rem;">Zone Administration</h3>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="admin/add-project.php" class="cyber-btn primary">
                    <i class="fas fa-plus"></i> NOUVEAU PROJET
                </a>
                <a href="dashboard.php" class="cyber-btn secondary">
                    <i class="fas fa-tachometer-alt"></i> DASHBOARD
                </a>
            </div>
        </section>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Animation des cartes au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);
                }
            });
        }, observerOptions);
        
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.project-card');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                cardObserver.observe(card);
            });
        });
    </script>
</body>
</html>