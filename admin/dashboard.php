<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Statistiques
$stats = [
    'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'skills' => $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn(),
    'experiences' => $pdo->query("SELECT COUNT(*) FROM experiences")->fetchColumn(),
    'messages' => $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
    'unread_messages' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn(),
];

$admin_name = $_SESSION['admin_name'] ?? $_SESSION['admin_username'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <p class="subtitle">Bienvenue, <?php echo htmlspecialchars($admin_name); ?></p>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['projects']; ?></h3>
                    <p>Projets</p>
                </div>
                <a href="projects.php" class="stat-link">Gérer <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10B981, #059669);">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['skills']; ?></h3>
                    <p>Compétences</p>
                </div>
                <a href="skills.php" class="stat-link">Gérer <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['experiences']; ?></h3>
                    <p>Expériences</p>
                </div>
                <a href="experiences.php" class="stat-link">Gérer <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED);">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['messages']; ?></h3>
                    <p>Messages 
                        <?php if ($stats['unread_messages'] > 0): ?>
                            <span style="background: #EF4444; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; margin-left: 5px;">
                                <?php echo $stats['unread_messages']; ?> non lu<?php echo $stats['unread_messages'] > 1 ? 's' : ''; ?>
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
                <a href="messages.php" class="stat-link">Gérer <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="section-card">
            <h2><i class="fas fa-bolt"></i> Actions rapides</h2>
            <div class="quick-actions">
                <a href="projects.php?action=add" class="action-btn">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouveau Projet</span>
                </a>
                <a href="skills.php?action=add" class="action-btn">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvelle Compétence</span>
                </a>
                <a href="experiences.php?action=add" class="action-btn">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvelle Expérience</span>
                </a>
                <a href="../index.php" target="_blank" class="action-btn">
                    <i class="fas fa-eye"></i>
                    <span>Voir le Portfolio</span>
                </a>
            </div>
        </div>
        
        <!-- Derniers projets -->
        <div class="section-card">
            <h2><i class="fas fa-clock"></i> Derniers projets</h2>
            <?php
            $recent_projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5")->fetchAll();
            if ($recent_projects):
            ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Technologies</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_projects as $project): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                            </td>
                            <td>
                                <span class="badge"><?php echo htmlspecialchars($project['type'] ?? 'N/A'); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($project['technologies'] ?? ''); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($project['created_at'])); ?></td>
                            <td>
                                <a href="projects.php?action=edit&id=<?php echo $project['id']; ?>" class="btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="no-data">Aucun projet pour le moment</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
