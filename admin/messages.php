<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$success = '';
$error = '';

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Marquer comme lu
if ($action === 'mark_read' && $id) {
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Message marqué comme lu';
    }
    $action = 'list';
}

// Marquer comme non lu
if ($action === 'mark_unread' && $id) {
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 0 WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Message marqué comme non lu';
    }
    $action = 'list';
}

// Suppression
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Message supprimé avec succès';
    } else {
        $error = 'Erreur lors de la suppression';
    }
    $action = 'list';
}

// Voir le détail d'un message
if ($action === 'view' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$id]);
    $message = $stmt->fetch();
    
    if ($message) {
        // Marquer automatiquement comme lu quand on le consulte
        $stmt_read = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt_read->execute([$id]);
    } else {
        $error = 'Message introuvable';
        $action = 'list';
    }
}

// Statistiques
if ($action === 'list') {
    $total_messages = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    $unread_messages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
    
    // Récupération des messages (les plus récents en premier)
    $messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages de Contact - Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .message-row {
            background: #fff;
            transition: all 0.2s;
        }
        .message-row:hover {
            background: #f8fafc;
            transform: translateX(2px);
        }
        .message-row.unread {
            background: #eff6ff;
            border-left: 3px solid #3B82F6;
        }
        .message-row.unread:hover {
            background: #dbeafe;
        }
        .message-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #000000;
            font-weight: 600;
        }
        .message-detail {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .message-header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }
        .message-header h2 {
            color: #000000;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        .message-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #000000;
            font-size: 0.95rem;
            font-weight: 600;
        }
        .meta-item strong,
        .meta-item a {
            color: #000000;
            font-weight: 700;
        }
        .meta-item i {
            color: #3B82F6;
        }
        .message-body {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 2rem;
            line-height: 2;
            white-space: pre-wrap;
            font-size: 1.05rem;
            color: #000000;
            font-weight: 600;
        }
        .stats-mini {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-mini {
            background: white;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .stat-mini i {
            font-size: 2rem;
            color: #3B82F6;
        }
        .stat-mini-content h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
        }
        .stat-mini-content p {
            font-size: 0.875rem;
            color: #64748B;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-envelope"></i> Messages de Contact</h1>
                    <p class="subtitle">Gérez les messages reçus via le formulaire de contact</p>
                </div>
            </div>
            
            <!-- Mini statistiques -->
            <div class="stats-mini">
                <div class="stat-mini">
                    <i class="fas fa-envelope"></i>
                    <div class="stat-mini-content">
                        <h3><?php echo $total_messages; ?></h3>
                        <p>Total messages</p>
                    </div>
                </div>
                <div class="stat-mini">
                    <i class="fas fa-envelope-open"></i>
                    <div class="stat-mini-content">
                        <h3><?php echo $unread_messages; ?></h3>
                        <p>Non lus</p>
                    </div>
                </div>
            </div>
            
            <div class="section-card">
                <?php if (!empty($messages)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Sujet</th>
                                <th>Aperçu</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                            <tr class="message-row <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                                <td>
                                    <?php if ($msg['is_read']): ?>
                                        <span class="badge" title="Lu">
                                            <i class="fas fa-envelope-open"></i>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-info" title="Non lu">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #000000; font-weight: 700;"><?php echo htmlspecialchars($msg['sender_name']); ?></td>
                                <td style="color: #000000; font-weight: 600;">
                                    <a href="mailto:<?php echo htmlspecialchars($msg['sender_email']); ?>" style="color: #3B82F6; text-decoration: none;">
                                        <?php echo htmlspecialchars($msg['sender_email']); ?>
                                    </a>
                                </td>
                                <td style="color: #000000; font-weight: 600;"><?php echo htmlspecialchars($msg['subject']); ?></td>
                                <td>
                                    <div class="message-preview">
                                        <?php echo htmlspecialchars(substr($msg['message'], 0, 50)); ?>...
                                    </div>
                                </td>
                                <td style="color: #000000; font-weight: 600;">
                                    <?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?action=view&id=<?php echo $msg['id']; ?>" class="btn-icon" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (!$msg['is_read']): ?>
                                            <a href="?action=mark_read&id=<?php echo $msg['id']; ?>" class="btn-icon" title="Marquer comme lu">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="?action=mark_unread&id=<?php echo $msg['id']; ?>" class="btn-icon" title="Marquer comme non lu">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="?action=delete&id=<?php echo $msg['id']; ?>" class="btn-icon btn-icon-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucun message</h3>
                    <p>Vous n'avez pas encore reçu de messages de contact.</p>
                </div>
                <?php endif; ?>
            </div>
            
        <?php elseif ($action === 'view' && isset($message)): ?>
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-envelope-open"></i> Détails du Message</h1>
                </div>
                <a href="messages.php" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
            
            <div class="message-detail">
                <div class="message-header">
                    <h2><?php echo htmlspecialchars($message['subject']); ?></h2>
                    <div class="message-meta">
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <strong><?php echo htmlspecialchars($message['sender_name']); ?></strong>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo htmlspecialchars($message['sender_email']); ?>">
                                <?php echo htmlspecialchars($message['sender_email']); ?>
                            </a>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <?php echo date('d/m/Y à H:i', strtotime($message['created_at'])); ?>
                        </div>
                    </div>
                </div>
                
                <div class="message-body">
                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                </div>
                
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <a href="mailto:<?php echo htmlspecialchars($message['sender_email']); ?>?subject=Re: <?php echo urlencode($message['subject']); ?>" class="btn-primary">
                        <i class="fas fa-reply"></i> Répondre par Email
                    </a>
                    <?php if (!$message['is_read']): ?>
                        <a href="?action=mark_read&id=<?php echo $message['id']; ?>" class="btn-secondary">
                            <i class="fas fa-check"></i> Marquer comme lu
                        </a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?php echo $message['id']; ?>" class="btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                        <i class="fas fa-trash"></i> Supprimer
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
