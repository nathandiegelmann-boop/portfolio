<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$success = '';
$error = '';

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Débannir une IP
if ($action === 'unban' && $id) {
    $stmt = $pdo->prepare("DELETE FROM spam_blacklist WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'IP débannie avec succès';
    } else {
        $error = 'Erreur lors du débannissement';
    }
    $action = 'list';
}

// Bannir manuellement une IP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $ip_address = trim($_POST['ip_address'] ?? '');
    $reason = trim($_POST['reason'] ?? '');
    $duration = (int)($_POST['duration'] ?? 24);
    
    if (empty($ip_address)) {
        $error = 'L\'adresse IP est obligatoire';
    } elseif (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
        $error = 'Adresse IP invalide';
    } else {
        $sql = "INSERT INTO spam_blacklist (ip_address, reason, banned_until, created_at) 
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? HOUR), NOW())
                ON DUPLICATE KEY UPDATE reason = ?, banned_until = DATE_ADD(NOW(), INTERVAL ? HOUR)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$ip_address, $reason, $duration, $reason, $duration])) {
            $success = "IP $ip_address bannie pour $duration heures";
            $action = 'list';
        } else {
            $error = 'Erreur lors du bannissement';
        }
    }
}

// Liste des IPs bannies
if ($action === 'list') {
    // IPs actuellement bannies
    $active_bans = $pdo->query("
        SELECT *, TIMESTAMPDIFF(MINUTE, NOW(), banned_until) as minutes_left 
        FROM spam_blacklist 
        WHERE banned_until > NOW() 
        ORDER BY created_at DESC
    ")->fetchAll();
    
    // IPs expirées (historique)
    $expired_bans = $pdo->query("
        SELECT *, TIMESTAMPDIFF(HOUR, banned_until, NOW()) as hours_ago 
        FROM spam_blacklist 
        WHERE banned_until <= NOW() 
        ORDER BY banned_until DESC 
        LIMIT 20
    ")->fetchAll();
    
    $total_active = count($active_bans);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Anti-Spam - Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .ban-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .ban-status.active {
            background: #FEE2E2;
            color: #DC2626;
        }
        .ban-status.expired {
            background: #E5E7EB;
            color: #6B7280;
        }
        .ip-address {
            font-family: 'Courier New', monospace;
            background: #F3F4F6;
            padding: 4px 8px;
            border-radius: 4px;
            color: #1F2937;
            font-weight: 600;
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
                    <h1><i class="fas fa-shield-alt"></i> Gestion Anti-Spam</h1>
                    <p class="subtitle">Gérez la liste noire des IPs bannies</p>
                </div>
                <a href="?action=add" class="btn-primary">
                    <i class="fas fa-ban"></i> Bannir une IP
                </a>
            </div>
            
            <div class="section-card">
                <h2><i class="fas fa-exclamation-triangle"></i> IPs Bannies Actives (<?php echo $total_active; ?>)</h2>
                <?php if (!empty($active_bans)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Adresse IP</th>
                                <th>Raison</th>
                                <th>Banni le</th>
                                <th>Expire dans</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($active_bans as $ban): ?>
                            <tr>
                                <td>
                                    <span class="ban-status active">
                                        <i class="fas fa-ban"></i> Actif
                                    </span>
                                </td>
                                <td>
                                    <span class="ip-address"><?php echo htmlspecialchars($ban['ip_address']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($ban['reason']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($ban['created_at'])); ?></td>
                                <td>
                                    <?php 
                                    $minutes = $ban['minutes_left'];
                                    if ($minutes >= 60) {
                                        echo round($minutes / 60, 1) . ' heures';
                                    } else {
                                        echo $minutes . ' minutes';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?action=unban&id=<?php echo $ban['id']; ?>" class="btn-icon btn-icon-success" title="Débannir" onclick="return confirm('Êtes-vous sûr de vouloir débannir cette IP ?')">
                                            <i class="fas fa-check"></i>
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
                    <i class="fas fa-check-circle"></i>
                    <h3>Aucune IP bannie</h3>
                    <p>Aucune IP n'est actuellement bannie.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($expired_bans)): ?>
            <div class="section-card" style="margin-top: 2rem;">
                <h2><i class="fas fa-history"></i> Historique des Bannissements</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Adresse IP</th>
                                <th>Raison</th>
                                <th>Banni le</th>
                                <th>Expiré depuis</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expired_bans as $ban): ?>
                            <tr style="opacity: 0.7;">
                                <td>
                                    <span class="ban-status expired">
                                        <i class="fas fa-clock"></i> Expiré
                                    </span>
                                </td>
                                <td>
                                    <span class="ip-address"><?php echo htmlspecialchars($ban['ip_address']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($ban['reason']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($ban['created_at'])); ?></td>
                                <td>
                                    <?php 
                                    $hours = $ban['hours_ago'];
                                    if ($hours >= 24) {
                                        echo round($hours / 24) . ' jours';
                                    } else {
                                        echo $hours . ' heures';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
        <?php elseif ($action === 'add'): ?>
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-ban"></i> Bannir une IP</h1>
                </div>
                <a href="spam.php" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            
            <div class="section-card">
                <form method="POST" action="?action=add">
                    <div class="form-group">
                        <label for="ip_address">Adresse IP *</label>
                        <input type="text" id="ip_address" name="ip_address" class="form-control" 
                               placeholder="Ex: 192.168.1.100" required>
                        <small>Format IPv4 ou IPv6</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">Raison du bannissement *</label>
                        <input type="text" id="reason" name="reason" class="form-control" 
                               placeholder="Ex: Spam, Tentative d'intrusion, etc." required>
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Durée du bannissement (en heures)</label>
                        <select id="duration" name="duration" class="form-control">
                            <option value="1">1 heure</option>
                            <option value="6">6 heures</option>
                            <option value="12">12 heures</option>
                            <option value="24" selected>24 heures (1 jour)</option>
                            <option value="48">48 heures (2 jours)</option>
                            <option value="168">168 heures (7 jours)</option>
                            <option value="720">720 heures (30 jours)</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-ban"></i> Bannir cette IP
                        </button>
                        <a href="spam.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
