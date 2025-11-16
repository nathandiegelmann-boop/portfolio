<?php
// Nathan Diegelmann - Gestion Messages Contact
require_once __DIR__ . '/includes/config.php';

// Vérification admin
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Actions AJAX
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'mark_read':
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
            echo json_encode(['success' => $stmt->execute([$id])]);
            exit;
            
        case 'mark_unread':
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 0 WHERE id = ?");
            echo json_encode(['success' => $stmt->execute([$id])]);
            exit;
            
        case 'delete':
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            echo json_encode(['success' => $stmt->execute([$id])]);
            exit;
    }
}

// Récupération des messages
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$sql = "SELECT * FROM contact_messages WHERE 1=1";
$params = [];

if ($filter === 'unread') {
    $sql .= " AND is_read = 0";
} elseif ($filter === 'read') {
    $sql .= " AND is_read = 1";
}

if ($search) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $searchParam = '%' . $search . '%';
    $params = array_fill(0, 4, $searchParam);
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

// Statistiques
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
    SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
FROM contact_messages";
$stats = $pdo->query($stats_sql)->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages Contact - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
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
        
        .messages-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
        }
        
        .stat-number {
            font-family: var(--font-mono);
            font-size: 2rem;
            font-weight: 700;
            color: var(--terminal-green);
            display: block;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .filters-bar {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .filter-btn {
            padding: 0.5rem 1rem;
            background: var(--darker-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            color: var(--text-secondary);
            text-decoration: none;
            font-family: var(--font-mono);
            font-size: 0.8rem;
            transition: var(--transition);
        }
        
        .filter-btn.active,
        .filter-btn:hover {
            background: var(--terminal-green);
            color: var(--dark-bg);
            border-color: var(--terminal-green);
        }
        
        .search-box {
            flex: 1;
            max-width: 300px;
        }
        
        .search-input {
            width: 100%;
            padding: 0.5rem 1rem;
            background: var(--darker-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            font-family: var(--font-mono);
        }
        
        .messages-list {
            display: grid;
            gap: 1rem;
        }
        
        .message-card {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            transition: var(--transition);
        }
        
        .message-card.unread {
            border-left: 4px solid var(--terminal-green);
            background: rgba(0, 255, 65, 0.02);
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .message-info {
            flex: 1;
        }
        
        .message-from {
            font-family: var(--font-mono);
            color: var(--terminal-green);
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .message-email {
            color: var(--code-blue);
            font-size: 0.9rem;
            margin: 0.2rem 0;
        }
        
        .message-date {
            color: var(--text-secondary);
            font-family: var(--font-mono);
            font-size: 0.8rem;
        }
        
        .message-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            background: var(--darker-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.4rem 0.8rem;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 0.8rem;
            transition: var(--transition);
        }
        
        .action-btn:hover {
            background: var(--terminal-green);
            color: var(--dark-bg);
        }
        
        .action-btn.delete:hover {
            background: #dc3545;
            color: white;
        }
        
        .message-subject {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .message-content {
            color: var(--text-secondary);
            line-height: 1.6;
            white-space: pre-wrap;
            background: var(--darker-bg);
            padding: 1rem;
            border-radius: var(--border-radius);
            border-left: 3px solid var(--code-blue);
        }
        
        .status-badge {
            font-family: var(--font-mono);
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            text-transform: uppercase;
        }
        
        .status-badge.unread {
            background: rgba(0, 255, 65, 0.2);
            color: var(--terminal-green);
        }
        
        .status-badge.read {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--border-color);
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .messages-container {
                padding: 1rem;
            }
            
            .filters-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .message-header {
                flex-direction: column;
            }
            
            .message-actions {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body style="background: var(--dark-bg); color: var(--text-primary);">
    <!-- Header Admin -->
    <div class="admin-header">
        <h1 class="admin-title">
            <i class="fas fa-envelope"></i> Messages Contact
        </h1>
        <div class="admin-nav">
            <a href="index.php">Retour au site</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>
    
    <div class="messages-container">
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['total']; ?></span>
                <span class="stat-label">Total Messages</span>
            </div>
            <div class="stat-card">
                <span class="stat-number" style="color: var(--terminal-green);"><?php echo $stats['unread']; ?></span>
                <span class="stat-label">Non Lus</span>
            </div>
            <div class="stat-card">
                <span class="stat-number" style="color: #6c757d;"><?php echo $stats['read']; ?></span>
                <span class="stat-label">Lus</span>
            </div>
            <div class="stat-card">
                <span class="stat-number" style="color: var(--code-blue);"><?php echo $stats['today']; ?></span>
                <span class="stat-label">Aujourd'hui</span>
            </div>
        </div>
        
        <!-- Filtres et recherche -->
        <div class="filters-bar">
            <div class="filter-buttons">
                <a href="?filter=all" class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">
                    Tous
                </a>
                <a href="?filter=unread" class="filter-btn <?php echo $filter === 'unread' ? 'active' : ''; ?>">
                    Non lus
                </a>
                <a href="?filter=read" class="filter-btn <?php echo $filter === 'read' ? 'active' : ''; ?>">
                    Lus
                </a>
            </div>
            
            <div class="search-box">
                <form method="GET">
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="Rechercher..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                </form>
            </div>
        </div>
        
        <!-- Liste des messages -->
        <div class="messages-list">
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3>Aucun message</h3>
                    <p>Aucun message ne correspond à vos critères de recherche.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message-card <?php echo $message['is_read'] ? 'read' : 'unread'; ?>" data-id="<?php echo $message['id']; ?>">
                        <div class="message-header">
                            <div class="message-info">
                                <div class="message-from"><?php echo htmlspecialchars($message['name']); ?></div>
                                <div class="message-email">
                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                        <?php echo htmlspecialchars($message['email']); ?>
                                    </a>
                                </div>
                                <div class="message-date">
                                    <?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?>
                                </div>
                            </div>
                            <div class="message-actions">
                                <span class="status-badge <?php echo $message['is_read'] ? 'read' : 'unread'; ?>">
                                    <?php echo $message['is_read'] ? 'Lu' : 'Non lu'; ?>
                                </span>
                                <button 
                                    class="action-btn mark-btn" 
                                    data-action="<?php echo $message['is_read'] ? 'mark_unread' : 'mark_read'; ?>"
                                    title="<?php echo $message['is_read'] ? 'Marquer non lu' : 'Marquer lu'; ?>"
                                >
                                    <i class="fas fa-<?php echo $message['is_read'] ? 'eye-slash' : 'eye'; ?>"></i>
                                </button>
                                <button class="action-btn delete" data-action="delete" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="message-subject">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($message['subject']); ?>
                        </div>
                        
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Gestion des actions AJAX
        document.addEventListener('DOMContentLoaded', () => {
            const messageCards = document.querySelectorAll('.message-card');
            
            messageCards.forEach(card => {
                const actionBtns = card.querySelectorAll('.action-btn');
                const messageId = card.dataset.id;
                
                actionBtns.forEach(btn => {
                    btn.addEventListener('click', async (e) => {
                        const action = e.currentTarget.dataset.action;
                        
                        if (action === 'delete') {
                            if (!confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
                                return;
                            }
                        }
                        
                        try {
                            const formData = new FormData();
                            formData.append('action', action);
                            formData.append('id', messageId);
                            
                            const response = await fetch('messages.php', {
                                method: 'POST',
                                body: formData
                            });
                            
                            const result = await response.json();
                            
                            if (result.success) {
                                if (action === 'delete') {
                                    card.remove();
                                } else {
                                    // Recharger la page pour mettre à jour l'affichage
                                    location.reload();
                                }
                            } else {
                                alert('Erreur lors de l\'action. Veuillez réessayer.');
                            }
                        } catch (error) {
                            console.error('Erreur:', error);
                            alert('Erreur de connexion. Veuillez réessayer.');
                        }
                    });
                });
            });
            
            // Auto-submit du formulaire de recherche
            const searchInput = document.querySelector('.search-input');
            let searchTimeout;
            
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchInput.closest('form').submit();
                }, 500);
            });
        });
    </script>
</body>
</html>