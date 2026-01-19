<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$success = '';
$error = '';
$experience = null;

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Suppression
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM experiences WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Expérience supprimée avec succès';
        $action = 'list';
    } else {
        $error = 'Erreur lors de la suppression';
    }
}

// Récupération pour édition
if (($action === 'edit') && $id) {
    $stmt = $pdo->prepare("SELECT * FROM experiences WHERE id = ?");
    $stmt->execute([$id]);
    $experience = $stmt->fetch();
    if (!$experience) {
        $error = 'Expérience introuvable';
        $action = 'list';
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $company = $_POST['company'] ?? '';
    $location = $_POST['location'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? null;
    $is_current = isset($_POST['is_current']) ? 1 : 0;
    $description = $_POST['description'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;
    
    // Si c'est actuel, on met end_date à null
    if ($is_current) {
        $end_date = null;
    }
    
    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO experiences (title, company, location, start_date, end_date, is_current, description, display_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        if ($stmt->execute([$title, $company, $location, $start_date, $end_date, $is_current, $description, $display_order])) {
            $success = 'Expérience ajoutée avec succès';
            $action = 'list';
        } else {
            $error = 'Erreur lors de l\'ajout';
        }
    } elseif ($action === 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE experiences SET title = ?, company = ?, location = ?, start_date = ?, end_date = ?, is_current = ?, description = ?, display_order = ? WHERE id = ?");
        if ($stmt->execute([$title, $company, $location, $start_date, $end_date, $is_current, $description, $display_order, $id])) {
            $success = 'Expérience modifiée avec succès';
            $action = 'list';
        } else {
            $error = 'Erreur lors de la modification';
        }
    }
}

// Liste des expériences
if ($action === 'list') {
    $experiences = $pdo->query("SELECT * FROM experiences ORDER BY start_date DESC, display_order ASC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Expériences - Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
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
                    <h1><i class="fas fa-briefcase"></i> Gestion des Expériences</h1>
                    <p class="subtitle">Gérez votre parcours professionnel et académique</p>
                </div>
                <a href="?action=add" class="btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Expérience
                </a>
            </div>
            
            <div class="section-card">
                <?php if (!empty($experiences)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Entreprise/École</th>
                                <th>Lieu</th>
                                <th>Période</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($experiences as $exp): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($exp['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($exp['company']); ?></td>
                                <td><?php echo htmlspecialchars($exp['location'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                    $start = date('m/Y', strtotime($exp['start_date']));
                                    $end = $exp['is_current'] ? 'Actuellement' : date('m/Y', strtotime($exp['end_date']));
                                    echo "$start - $end";
                                    ?>
                                </td>
                                <td>
                                    <?php if ($exp['is_current']): ?>
                                        <span class="badge badge-success">En cours</span>
                                    <?php else: ?>
                                        <span class="badge">Terminé</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?action=edit&id=<?php echo $exp['id']; ?>" class="btn-icon" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=delete&id=<?php echo $exp['id']; ?>" class="btn-icon btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette expérience ?')">
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
                    <p class="no-data">Aucune expérience pour le moment</p>
                <?php endif; ?>
            </div>
        
        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?>"></i> <?php echo $action === 'add' ? 'Nouvelle' : 'Modifier l\''; ?>Expérience</h1>
                </div>
                <a href="?action=list" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            
            <div class="section-card">
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="title">Titre/Poste *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($experience['title'] ?? ''); ?>" required placeholder="Ex: Développeur Web Junior">
                        </div>
                        
                        <div class="form-group">
                            <label for="company">Entreprise/École *</label>
                            <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($experience['company'] ?? ''); ?>" required placeholder="Ex: Digital Campus">
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Lieu</label>
                            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($experience['location'] ?? ''); ?>" placeholder="Ex: Paris, France">
                        </div>
                        
                        <div class="form-group">
                            <label for="display_order">Ordre d'affichage</label>
                            <input type="number" id="display_order" name="display_order" value="<?php echo htmlspecialchars($experience['display_order'] ?? 0); ?>" min="0">
                            <small>Plus petit = affiché en premier</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="start_date">Date de début *</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($experience['start_date'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">Date de fin</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($experience['end_date'] ?? ''); ?>" <?php echo ($experience['is_current'] ?? 0) ? 'disabled' : ''; ?>>
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_current" value="1" <?php echo ($experience['is_current'] ?? 0) ? 'checked' : ''; ?> onchange="document.getElementById('end_date').disabled = this.checked; if(this.checked) document.getElementById('end_date').value = '';">
                                <span>Poste actuel</span>
                            </label>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="5" placeholder="Décrivez vos missions, réalisations, technologies utilisées..."><?php echo htmlspecialchars($experience['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="?action=list" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
