<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Vérifier que l'utilisateur est connecté et admin
if (!is_logged_in() || !is_admin()) {
    redirect('login.php');
}

$page_title = 'Gestion des Utilisateurs - Administration';

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$errors = [];

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_user'])) {
        $nom = clean_input($_POST['nom'] ?? '');
        $prenom = clean_input($_POST['prenom'] ?? '');
        $email = clean_input($_POST['email'] ?? '');
        $telephone = clean_input($_POST['telephone'] ?? '');
        $adresse = clean_input($_POST['adresse'] ?? '');
        $role = clean_input($_POST['role'] ?? 'client');
        $actif = isset($_POST['actif']) ? 1 : 0;
        
        // Validation
        if (empty($nom)) $errors[] = 'Le nom est obligatoire';
        if (empty($prenom)) $errors[] = 'Le prénom est obligatoire';
        if (empty($email) || !validate_email($email)) $errors[] = 'Email invalide';
        if (!empty($telephone) && !validate_phone($telephone)) $errors[] = 'Numéro de téléphone invalide';
        if (!in_array($role, ['client', 'admin'])) $errors[] = 'Rôle invalide';
        
        // Vérifier que l'email n'est pas déjà utilisé (sauf pour l'utilisateur actuel)
        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $errors[] = 'Cette adresse email est déjà utilisée';
            }
        }
        
        if (empty($errors)) {
            try {
                if ($action === 'add') {
                    $password = $_POST['password'] ?? '';
                    if (strlen($password) < 6) {
                        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
                    } else {
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        
                        $stmt = $pdo->prepare("
                            INSERT INTO users (nom, prenom, email, telephone, adresse, password, role, actif) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([$nom, $prenom, $email, $telephone, $adresse, $password_hash, $role, $actif]);
                        $_SESSION['message'] = 'Utilisateur ajouté avec succès';
                        redirect('users.php');
                    }
                } elseif ($action === 'edit' && $user_id > 0) {
                    $password_update = '';
                    $password_params = [];
                    
                    // Mise à jour du mot de passe si fourni
                    if (!empty($_POST['password'])) {
                        if (strlen($_POST['password']) < 6) {
                            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
                        } else {
                            $password_update = ', password = ?';
                            $password_params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        }
                    }
                    
                    if (empty($errors)) {
                        $sql = "UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ?, role = ?, actif = ?" . $password_update . " WHERE id = ?";
                        $params = array_merge([$nom, $prenom, $email, $telephone, $adresse, $role, $actif], $password_params, [$user_id]);
                        
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $_SESSION['message'] = 'Utilisateur modifié avec succès';
                        redirect('users.php');
                    }
                }
            } catch (Exception $e) {
                $errors[] = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['toggle_status'])) {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $new_status = (int)($_POST['new_status'] ?? 0);
        
        if ($user_id > 0) {
            // Vérifier qu'on ne désactive pas le dernier admin
            if ($new_status == 0) {
                $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                if ($user && $user['role'] === 'admin') {
                    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND actif = 1");
                    $admin_count = $stmt->fetchColumn();
                    
                    if ($admin_count <= 1) {
                        $_SESSION['message'] = 'Impossible de désactiver le dernier administrateur';
                        redirect('users.php');
                        exit;
                    }
                }
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE users SET actif = ? WHERE id = ?");
                $stmt->execute([$new_status, $user_id]);
                $_SESSION['message'] = 'Statut de l\'utilisateur mis à jour';
                redirect('users.php');
            } catch (Exception $e) {
                $_SESSION['message'] = 'Erreur lors de la mise à jour';
                redirect('users.php');
            }
        }
    }
}

// Suppression d'un utilisateur
if ($action === 'delete' && $user_id > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Vérifier qu'on ne supprime pas le dernier admin
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user && $user['role'] === 'admin') {
            $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
            $admin_count = $stmt->fetchColumn();
            
            if ($admin_count <= 1) {
                $_SESSION['message'] = 'Impossible de supprimer le dernier administrateur';
                redirect('users.php');
                exit;
            }
        }
        
        // Vérifier s'il y a des commandes liées
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $orders_count = $stmt->fetchColumn();
        
        if ($orders_count > 0) {
            // Ne pas supprimer, juste désactiver
            $stmt = $pdo->prepare("UPDATE users SET actif = 0 WHERE id = ?");
            $stmt->execute([$user_id]);
            $_SESSION['message'] = 'Utilisateur désactivé (possède des commandes)';
        } else {
            // Supprimer l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $_SESSION['message'] = 'Utilisateur supprimé avec succès';
        }
        redirect('users.php');
    } catch (Exception $e) {
        $_SESSION['message'] = 'Erreur lors de la suppression : ' . $e->getMessage();
        redirect('users.php');
    }
}

// Récupération des données selon l'action
$user = null;

// Récupération de l'utilisateur pour édition
if (($action === 'edit' || $action === 'view') && $user_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $_SESSION['message'] = 'Utilisateur non trouvé';
        redirect('users.php');
    }
}

// Liste des utilisateurs pour la vue principale
$users = [];
if ($action === 'list') {
    $search = $_GET['search'] ?? '';
    $role_filter = $_GET['role'] ?? 'all';
    $status_filter = $_GET['status'] ?? 'all';
    
    $where_conditions = ['1=1'];
    $params = [];
    
    if (!empty($search)) {
        $where_conditions[] = '(nom LIKE ? OR prenom LIKE ? OR email LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($role_filter !== 'all') {
        $where_conditions[] = 'role = ?';
        $params[] = $role_filter;
    }
    
    if ($status_filter === 'active') {
        $where_conditions[] = 'actif = 1';
    } elseif ($status_filter === 'inactive') {
        $where_conditions[] = 'actif = 0';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    $stmt = $pdo->prepare("
        SELECT u.*, 
               (SELECT COUNT(*) FROM commandes c WHERE c.user_id = u.id) as nb_commandes,
               (SELECT COALESCE(SUM(c.total), 0) FROM commandes c WHERE c.user_id = u.id AND c.statut != 'annulee') as total_achats
        FROM users u 
        WHERE $where_clause
        ORDER BY u.date_inscription DESC
    ");
    $stmt->execute($params);
    $users = $stmt->fetchAll();
}

include '../includes/header.php';
?>

<section class="admin-header">
    <div class="container">
        <h1>👥 Gestion des Utilisateurs</h1>
        <div class="admin-actions">
            <?php if ($action !== 'add'): ?>
                <a href="?action=add" class="btn btn-primary">➕ Ajouter un utilisateur</a>
            <?php endif; ?>
            <?php if ($action !== 'list'): ?>
                <a href="users.php" class="btn btn-secondary">← Retour à la liste</a>
            <?php endif; ?>
            <a href="dashboard.php" class="btn btn-outline">📊 Tableau de bord</a>
        </div>
    </div>
</section>

<section class="admin-content">
    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
            <!-- Liste des utilisateurs -->
            <div class="users-management">
                <!-- Filtres -->
                <div class="filters-section">
                    <form method="get" action="" class="admin-filters">
                        <div class="filter-group">
                            <input type="text" name="search" placeholder="Rechercher par nom, prénom ou email..." 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <select name="role">
                                <option value="all" <?php echo ($role_filter === 'all') ? 'selected' : ''; ?>>Tous les rôles</option>
                                <option value="admin" <?php echo ($role_filter === 'admin') ? 'selected' : ''; ?>>Administrateurs</option>
                                <option value="client" <?php echo ($role_filter === 'client') ? 'selected' : ''; ?>>Clients</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <select name="status">
                                <option value="all" <?php echo ($status_filter === 'all') ? 'selected' : ''; ?>>Tous les statuts</option>
                                <option value="active" <?php echo ($status_filter === 'active') ? 'selected' : ''; ?>>Actifs</option>
                                <option value="inactive" <?php echo ($status_filter === 'inactive') ? 'selected' : ''; ?>>Inactifs</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <a href="users.php" class="btn btn-outline">Réinitialiser</a>
                    </form>
                </div>
                
                <!-- Tableau des utilisateurs -->
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Contact</th>
                                <th>Rôle</th>
                                <th>Commandes</th>
                                <th>Total achats</th>
                                <th>Inscription</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $usr): ?>
                                    <tr class="<?php echo $usr['actif'] ? '' : 'inactive-row'; ?>">
                                        <td class="user-info">
                                            <div class="user-name">
                                                <strong><?php echo htmlspecialchars($usr['prenom'] . ' ' . $usr['nom']); ?></strong>
                                                <?php if ($usr['role'] === 'admin'): ?>
                                                    <span class="admin-badge">👑</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="user-email">
                                                <?php echo htmlspecialchars($usr['email']); ?>
                                            </div>
                                        </td>
                                        <td class="contact-info">
                                            <?php if ($usr['telephone']): ?>
                                                <div class="phone">📞 <?php echo htmlspecialchars($usr['telephone']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($usr['adresse']): ?>
                                                <div class="address">🏠 <?php echo htmlspecialchars(substr($usr['adresse'], 0, 30)); ?>...</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="role-badge role-<?php echo $usr['role']; ?>">
                                                <?php echo $usr['role'] === 'admin' ? '👑 Admin' : '👤 Client'; ?>
                                            </span>
                                        </td>
                                        <td class="orders-cell">
                                            <?php if ($usr['nb_commandes'] > 0): ?>
                                                <a href="commandes.php?search=<?php echo urlencode($usr['email']); ?>" class="orders-link">
                                                    <?php echo $usr['nb_commandes']; ?> commandes
                                                </a>
                                            <?php else: ?>
                                                <span class="no-orders">Aucune commande</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="total-purchases">
                                            <?php echo format_price($usr['total_achats']); ?>
                                        </td>
                                        <td class="registration-date">
                                            <?php echo date('d/m/Y', strtotime($usr['date_inscription'])); ?>
                                        </td>
                                        <td>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $usr['id']; ?>">
                                                <input type="hidden" name="new_status" value="<?php echo $usr['actif'] ? 0 : 1; ?>">
                                                <input type="hidden" name="toggle_status" value="1">
                                                <button type="submit" class="status-toggle <?php echo $usr['actif'] ? 'active' : 'inactive'; ?>"
                                                        title="<?php echo $usr['actif'] ? 'Désactiver' : 'Activer'; ?>">
                                                    <?php echo $usr['actif'] ? '✅ Actif' : '❌ Inactif'; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="actions-cell">
                                            <div class="action-buttons">
                                                <a href="?action=view&id=<?php echo $usr['id']; ?>" 
                                                   class="btn btn-small btn-outline" title="Voir">👁️</a>
                                                <a href="?action=edit&id=<?php echo $usr['id']; ?>" 
                                                   class="btn btn-small btn-primary" title="Modifier">✏️</a>
                                                <?php if ($usr['nb_commandes'] == 0 && !($usr['role'] === 'admin' && $usr['actif'])): ?>
                                                    <form method="post" action="?action=delete&id=<?php echo $usr['id']; ?>" 
                                                          style="display: inline;" 
                                                          onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                        <button type="submit" class="btn btn-small btn-danger" title="Supprimer">🗑️</button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-small btn-disabled" title="Utilisateur protégé" disabled>🗑️</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="no-data">
                                        <?php if (!empty($search) || $role_filter !== 'all' || $status_filter !== 'all'): ?>
                                            Aucun utilisateur ne correspond aux critères de recherche.
                                        <?php else: ?>
                                            Aucun utilisateur enregistré. <a href="?action=add">Ajouter le premier utilisateur</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-stats">
                    <p><?php echo count($users); ?> utilisateur(s) affiché(s)</p>
                </div>
            </div>
            
        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- Formulaire d'ajout/modification -->
            <div class="user-form-section">
                <h2><?php echo $action === 'add' ? '➕ Ajouter un nouvel utilisateur' : '✏️ Modifier l\'utilisateur'; ?></h2>
                
                <form method="post" action="" class="user-form">
                    <input type="hidden" name="update_user" value="1">
                    
                    <div class="form-grid">
                        <div class="form-section">
                            <h3>Informations personnelles</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="prenom">Prénom *</label>
                                    <input type="text" id="prenom" name="prenom" required 
                                           value="<?php echo htmlspecialchars($user['prenom'] ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="nom">Nom *</label>
                                    <input type="text" id="nom" name="nom" required 
                                           value="<?php echo htmlspecialchars($user['nom'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required 
                                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="telephone">Téléphone</label>
                                <input type="tel" id="telephone" name="telephone" 
                                       value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>"
                                       placeholder="0123456789">
                            </div>
                            
                            <div class="form-group">
                                <label for="adresse">Adresse complète</label>
                                <textarea id="adresse" name="adresse" rows="3" 
                                          placeholder="Numéro, rue, ville, code postal"><?php echo htmlspecialchars($user['adresse'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h3>Paramètres de compte</h3>
                            
                            <div class="form-group">
                                <label for="password">Mot de passe <?php echo $action === 'edit' ? '(laisser vide pour conserver)' : '*'; ?></label>
                                <input type="password" id="password" name="password" 
                                       <?php echo $action === 'add' ? 'required' : ''; ?>
                                       placeholder="<?php echo $action === 'add' ? 'Mot de passe (min. 6 caractères)' : 'Nouveau mot de passe (optionnel)'; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="role">Rôle *</label>
                                <select id="role" name="role" required>
                                    <option value="client" <?php echo (isset($user) && $user['role'] === 'client') ? 'selected' : ''; ?>>👤 Client</option>
                                    <option value="admin" <?php echo (isset($user) && $user['role'] === 'admin') ? 'selected' : ''; ?>>👑 Administrateur</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="actif" value="1" 
                                           <?php echo (!isset($user) || $user['actif']) ? 'checked' : ''; ?>>
                                    Compte actif
                                </label>
                                <small>Les comptes inactifs ne peuvent pas se connecter</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            <?php echo $action === 'add' ? '➕ Créer l\'utilisateur' : '💾 Enregistrer les modifications'; ?>
                        </button>
                        <a href="users.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
            
        <?php elseif ($action === 'view'): ?>
            <!-- Vue détaillée de l'utilisateur -->
            <div class="user-view-section">
                <div class="user-details">
                    <div class="user-header">
                        <div class="user-info">
                            <h2>
                                <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="admin-badge">👑 Administrateur</span>
                                <?php endif; ?>
                            </h2>
                            <div class="user-meta">
                                <span class="status-badge <?php echo $user['actif'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $user['actif'] ? '✅ Actif' : '❌ Inactif'; ?>
                                </span>
                                <span class="role-badge role-<?php echo $user['role']; ?>">
                                    <?php echo $user['role'] === 'admin' ? '👑 Admin' : '👤 Client'; ?>
                                </span>
                            </div>
                            
                            <div class="user-actions">
                                <a href="?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-primary">✏️ Modifier</a>
                                <?php if ($user['nb_commandes'] > 0): ?>
                                    <a href="commandes.php?search=<?php echo urlencode($user['email']); ?>" class="btn btn-outline">📋 Voir les commandes</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="user-content">
                        <div class="user-grid">
                            <!-- Informations de contact -->
                            <div class="contact-section">
                                <h3>📧 Contact</h3>
                                <div class="contact-details">
                                    <div class="detail-item">
                                        <label>Email :</label>
                                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                                    </div>
                                    <?php if ($user['telephone']): ?>
                                        <div class="detail-item">
                                            <label>Téléphone :</label>
                                            <span><?php echo htmlspecialchars($user['telephone']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($user['adresse']): ?>
                                        <div class="detail-item">
                                            <label>Adresse :</label>
                                            <span><?php echo nl2br(htmlspecialchars($user['adresse'])); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Informations d'inscription -->
                            <div class="account-section">
                                <h3>👤 Compte</h3>
                                <div class="account-details">
                                    <div class="detail-item">
                                        <label>Date d'inscription :</label>
                                        <span><?php echo date('d/m/Y à H:i', strtotime($user['date_inscription'])); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Dernière modification :</label>
                                        <span><?php echo $user['date_modification'] ? date('d/m/Y à H:i', strtotime($user['date_modification'])) : 'Jamais'; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Statut du compte :</label>
                                        <span class="status-badge <?php echo $user['actif'] ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo $user['actif'] ? '✅ Actif' : '❌ Inactif'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                        // Statistiques de l'utilisateur
                        $stmt = $pdo->prepare("
                            SELECT 
                                COUNT(*) as nb_commandes,
                                COUNT(CASE WHEN statut = 'livree' THEN 1 END) as nb_livrees,
                                COALESCE(SUM(CASE WHEN statut != 'annulee' THEN total ELSE 0 END), 0) as total_achats,
                                MAX(date_commande) as derniere_commande
                            FROM commandes 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([$user['id']]);
                        $stats = $stmt->fetch();
                        ?>
                        
                        <div class="user-stats">
                            <h3>📊 Statistiques d'achat</h3>
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo $stats['nb_commandes']; ?></div>
                                    <div class="stat-label">Commandes total</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo $stats['nb_livrees']; ?></div>
                                    <div class="stat-label">Commandes livrées</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo format_price($stats['total_achats']); ?></div>
                                    <div class="stat-label">Total des achats</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">
                                        <?php echo $stats['derniere_commande'] ? date('d/m/Y', strtotime($stats['derniere_commande'])) : 'Jamais'; ?>
                                    </div>
                                    <div class="stat-label">Dernière commande</div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($stats['nb_commandes'] > 0): ?>
                            <?php
                            // Dernières commandes
                            $stmt = $pdo->prepare("
                                SELECT id, date_commande, total, statut
                                FROM commandes 
                                WHERE user_id = ?
                                ORDER BY date_commande DESC
                                LIMIT 5
                            ");
                            $stmt->execute([$user['id']]);
                            $recent_orders = $stmt->fetchAll();
                            ?>
                            
                            <div class="recent-orders">
                                <h3>🛒 Commandes récentes</h3>
                                <div class="orders-list">
                                    <?php foreach ($recent_orders as $order): ?>
                                        <div class="order-item">
                                            <div class="order-info">
                                                <strong>Commande #<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></strong>
                                                <span class="order-date"><?php echo date('d/m/Y', strtotime($order['date_commande'])); ?></span>
                                            </div>
                                            <div class="order-details">
                                                <span class="order-total"><?php echo format_price($order['total']); ?></span>
                                                <span class="status-badge status-<?php echo $order['statut']; ?>">
                                                    <?php
                                                    $status_labels = [
                                                        'en_attente' => '⏳ En attente',
                                                        'confirmee' => '✅ Confirmée',
                                                        'preparee' => '📦 Préparée',
                                                        'livree' => '🚚 Livrée',
                                                        'annulee' => '❌ Annulée'
                                                    ];
                                                    echo $status_labels[$order['statut']] ?? $order['statut'];
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="order-actions">
                                                <a href="commandes.php?action=view&id=<?php echo $order['id']; ?>" class="btn btn-small btn-outline">Voir</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <?php if ($stats['nb_commandes'] > 5): ?>
                                    <div class="view-more">
                                        <a href="commandes.php?search=<?php echo urlencode($user['email']); ?>" class="btn btn-outline">
                                            Voir toutes les commandes (<?php echo $stats['nb_commandes']; ?>)
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.admin-header {
    background: linear-gradient(135deg, #3F51B5, #303F9F);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.admin-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.admin-filters {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto auto;
    gap: 1rem;
    align-items: end;
}

.user-info {
    font-size: 0.9rem;
}

.user-name {
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.admin-badge {
    font-size: 0.8rem;
    background: #ffd700;
    color: #333;
    padding: 0.1rem 0.3rem;
    border-radius: 8px;
}

.user-email {
    color: var(--text-light);
    font-size: 0.85rem;
}

.contact-info {
    font-size: 0.85rem;
    color: var(--text-light);
}

.phone, .address {
    margin-bottom: 0.25rem;
}

.role-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.role-admin {
    background: #ffd700;
    color: #333;
}

.role-client {
    background: var(--bg-light);
    color: var(--text-color);
}

.orders-cell {
    font-size: 0.85rem;
}

.orders-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: bold;
}

.orders-link:hover {
    text-decoration: underline;
}

.no-orders {
    color: var(--text-light);
}

.total-purchases {
    font-weight: bold;
    color: var(--primary-color);
}

.registration-date {
    font-size: 0.85rem;
    color: var(--text-light);
}

.status-toggle {
    padding: 0.25rem 0.75rem;
    border: none;
    border-radius: 15px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.status-toggle.active {
    background: #d4edda;
    color: #155724;
}

.status-toggle.inactive {
    background: #f8d7da;
    color: #721c24;
}

.status-toggle:hover {
    transform: scale(1.05);
}

.inactive-row {
    opacity: 0.6;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.btn-disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.user-form {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-large);
    box-shadow: var(--shadow);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.form-section {
    border: 1px solid var(--border-color);
    padding: 1.5rem;
    border-radius: var(--radius);
}

.form-section h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.user-view-section {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-large);
    box-shadow: var(--shadow);
}

.user-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.user-meta {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
    flex-wrap: wrap;
}

.user-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.user-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.contact-section,
.account-section {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius);
}

.contact-section h3,
.account-section h3 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.detail-item {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.detail-item label {
    font-weight: bold;
    color: var(--text-light);
}

.user-stats {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius);
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
    margin-top: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1.5rem;
    background: white;
    border-radius: var(--radius);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary-color);
}

.stat-label {
    color: var(--text-light);
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

.recent-orders {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius);
}

.orders-list {
    display: grid;
    gap: 1rem;
    margin-top: 1rem;
}

.order-item {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: var(--radius);
    align-items: center;
}

.order-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.order-date {
    color: var(--text-light);
    font-size: 0.85rem;
}

.order-details {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.order-total {
    font-weight: bold;
    color: var(--primary-color);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.status-active { background: #d4edda; color: #155724; }
.status-inactive { background: #f8d7da; color: #721c24; }
.status-en_attente { background: #fff3cd; color: #856404; }
.status-confirmee { background: #d4edda; color: #155724; }
.status-preparee { background: #cce5ff; color: #004085; }
.status-livree { background: #d4edda; color: #155724; }
.status-annulee { background: #f8d7da; color: #721c24; }

.view-more {
    text-align: center;
    margin-top: 1rem;
}

.table-stats {
    margin-top: 1rem;
    color: var(--text-light);
    text-align: center;
}

@media (max-width: 768px) {
    .admin-filters {
        grid-template-columns: 1fr;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .user-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .order-item {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .detail-item {
        grid-template-columns: 1fr;
        gap: 0.25rem;
    }
    
    .admin-actions {
        justify-content: center;
    }
}
</style>

<script>
function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php include '../includes/footer.php'; ?>