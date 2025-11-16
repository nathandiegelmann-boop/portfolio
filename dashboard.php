<?php
// Nathan Diegelmann - Dashboard Admin
require_once __DIR__ . '/includes/config.php';

// Vérification admin
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Statistiques rapides
$stats = [];

// Messages
$stats['messages'] = $pdo->query("
    SELECT COUNT(*) as total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread 
    FROM contact_messages
")->fetch();

// Projets
$stats['projects'] = $pdo->query("
    SELECT COUNT(*) as total, SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured 
    FROM projects
")->fetch();

// Compétences
$stats['skills'] = $pdo->query("
    SELECT COUNT(*) as total, AVG(level_percentage) as avg_level 
    FROM skills
")->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background: var(--dark-bg);
            color: var(--text-primary);
            font-family: var(--font-primary);
        }
        
        .admin-header {
            background: var(--darker-bg);
            border-bottom: 2px solid var(--terminal-green);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-title {
            font-family: var(--font-mono);
            color: var(--terminal-green);
            font-size: 1.5rem;
            margin: 0;
        }
        
        .admin-nav {
            display: flex;
            gap: 1rem;
        }
        
        .admin-nav a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-family: var(--font-mono);
            transition: var(--transition);
        }
        
        .admin-nav a:hover {
            background: var(--code-bg);
            color: var(--terminal-green);
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 65, 0.1);
        }
        
        .stat-icon {
            font-size: 3rem;
            color: var(--terminal-green);
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-family: var(--font-mono);
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--terminal-green);
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-sublabel {
            color: var(--code-blue);
            font-family: var(--font-mono);
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .action-card {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
        }
        
        .action-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
            background: var(--terminal-green);
            color: var(--dark-bg);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .action-title {
            font-family: var(--font-mono);
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }
        
        .action-buttons {
            display: grid;
            gap: 1rem;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            background: var(--darker-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            text-decoration: none;
            font-family: var(--font-mono);
            transition: var(--transition);
        }
        
        .action-btn:hover {
            border-color: var(--terminal-green);
            background: rgba(0, 255, 65, 0.1);
            color: var(--terminal-green);
        }
        
        .action-btn i {
            width: 20px;
            text-align: center;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, var(--code-bg), var(--darker-bg));
            border: 1px solid var(--terminal-green);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 3rem;
            text-align: center;
        }
        
        .welcome-title {
            font-family: var(--font-mono);
            color: var(--terminal-green);
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .welcome-text {
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .stats-grid,
            .actions-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header Admin -->
    <div class="admin-header">
        <h1 class="admin-title">
            <i class="fas fa-tachometer-alt"></i> Dashboard Admin
        </h1>
        <div class="admin-nav">
            <a href="index.php">Retour au site</a>
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>
    
    <div class="dashboard-container">
        <!-- Section Bienvenue -->
        <div class="welcome-section">
            <h2 class="welcome-title">Bienvenue, Nathan !</h2>
            <p class="welcome-text">
                Tableau de bord d'administration de votre portfolio. 
                Gérez vos projets, compétences et messages de contact depuis cette interface.
            </p>
        </div>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <span class="stat-number"><?php echo $stats['messages']['total']; ?></span>
                <span class="stat-label">Messages</span>
                <?php if ($stats['messages']['unread'] > 0): ?>
                    <div class="stat-sublabel"><?php echo $stats['messages']['unread']; ?> non lus</div>
                <?php endif; ?>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <span class="stat-number"><?php echo $stats['projects']['total']; ?></span>
                <span class="stat-label">Projets</span>
                <div class="stat-sublabel"><?php echo $stats['projects']['featured']; ?> mis en avant</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-code"></i>
                </div>
                <span class="stat-number"><?php echo $stats['skills']['total']; ?></span>
                <span class="stat-label">Compétences</span>
                <div class="stat-sublabel">Niveau moyen: <?php echo round($stats['skills']['avg_level']); ?>%</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="stat-number">Online</span>
                <span class="stat-label">Statut Site</span>
                <div class="stat-sublabel">Portfolio actif</div>
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="actions-grid">
            <!-- Gestion Messages -->
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="action-title">Messages Contact</h3>
                </div>
                <div class="action-buttons">
                    <a href="messages.php" class="action-btn">
                        <i class="fas fa-list"></i>
                        Voir tous les messages
                    </a>
                    <a href="messages.php?filter=unread" class="action-btn">
                        <i class="fas fa-envelope-open"></i>
                        Messages non lus (<?php echo $stats['messages']['unread']; ?>)
                    </a>
                </div>
            </div>
            
            <!-- Gestion Projets -->
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3 class="action-title">Projets</h3>
                </div>
                <div class="action-buttons">
                    <a href="projets.php" class="action-btn">
                        <i class="fas fa-eye"></i>
                        Voir les projets
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-plus"></i>
                        Ajouter un projet
                    </a>
                </div>
            </div>
            
            <!-- Gestion Compétences -->
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3 class="action-title">Compétences</h3>
                </div>
                <div class="action-buttons">
                    <a href="competences.php" class="action-btn">
                        <i class="fas fa-eye"></i>
                        Voir les compétences
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-edit"></i>
                        Modifier les niveaux
                    </a>
                </div>
            </div>
            
            <!-- Configuration -->
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h3 class="action-title">Configuration</h3>
                </div>
                <div class="action-buttons">
                    <a href="a-propos.php" class="action-btn">
                        <i class="fas fa-user"></i>
                        Profil personnel
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-palette"></i>
                        Personnaliser thème
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>