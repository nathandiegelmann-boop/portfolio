<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$success = '';
$error = '';
$skill = null;

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Suppression
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Compétence supprimée avec succès';
        $action = 'list';
    } else {
        $error = 'Erreur lors de la suppression';
    }
}

// Récupération pour édition
if (($action === 'edit') && $id) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([$id]);
    $skill = $stmt->fetch();
    if (!$skill) {
        $error = 'Compétence introuvable';
        $action = 'list';
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $level_percentage = $_POST['level_percentage'] ?? 0;
    $icon = $_POST['icon'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO skills (name, category, level_percentage, icon, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt->execute([$name, $category, $level_percentage, $icon, $description])) {
            $success = 'Compétence ajoutée avec succès';
            $action = 'list';
        } else {
            $error = 'Erreur lors de l\'ajout';
        }
    } elseif ($action === 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE skills SET name = ?, category = ?, level_percentage = ?, icon = ?, description = ? WHERE id = ?");
        if ($stmt->execute([$name, $category, $level_percentage, $icon, $description, $id])) {
            $success = 'Compétence modifiée avec succès';
            $action = 'list';
        } else {
            $error = 'Erreur lors de la modification';
        }
    }
}

// Liste des compétences
if ($action === 'list') {
    $skills = $pdo->query("SELECT * FROM skills ORDER BY category, level_percentage DESC")->fetchAll();
    
    // Regrouper par catégorie
    $skills_by_category = [];
    foreach ($skills as $sk) {
        $skills_by_category[$sk['category']][] = $sk;
    }
}

// Catégories disponibles
$categories = [
    'programming' => 'Programmation',
    'languages' => 'Langues',
    'tools' => 'Outils & Design',
    'soft_skills' => 'Compétences Relationnelles',
    'sports' => 'Sport & Forme'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Compétences - Administration</title>
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
                    <h1><i class="fas fa-brain"></i> Gestion des Compétences</h1>
                    <p class="subtitle">Gérez vos compétences affichées sur le portfolio</p>
                </div>
                <a href="?action=add" class="btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Compétence
                </a>
            </div>
            
            <?php foreach ($skills_by_category as $cat => $cat_skills): ?>
            <div class="section-card">
                <h2><i class="fas fa-folder"></i> <?php echo $categories[$cat] ?? $cat; ?> (<?php echo count($cat_skills); ?>)</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Icône</th>
                                <th>Nom</th>
                                <th>Niveau</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cat_skills as $sk): ?>
                            <tr>
                                <td>
                                    <?php if (isset($sk['icon']) && $sk['icon']): ?>
                                        <i class="<?php echo htmlspecialchars($sk['icon']); ?>" style="font-size: 24px; color: #F59E0B;"></i>
                                    <?php else: ?>
                                        <i class="fas fa-circle" style="font-size: 24px; color: #64748B;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($sk['name']); ?></strong></td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $sk['level_percentage']; ?>%"></div>
                                    </div>
                                    <span class="badge"><?php echo $sk['level_percentage']; ?>%</span>
                                </td>
                                <td><?php echo htmlspecialchars(substr($sk['description'] ?? '', 0, 50)); ?><?php echo strlen($sk['description'] ?? '') > 50 ? '...' : ''; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?action=edit&id=<?php echo $sk['id']; ?>" class="btn-icon" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=delete&id=<?php echo $sk['id']; ?>" class="btn-icon btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette compétence ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($skills)): ?>
                <div class="section-card">
                    <p class="no-data">Aucune compétence pour le moment</p>
                </div>
            <?php endif; ?>
        
        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?>"></i> <?php echo $action === 'add' ? 'Nouvelle' : 'Modifier la'; ?> Compétence</h1>
                </div>
                <a href="?action=list" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            
            <div class="section-card">
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Nom de la compétence *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($skill['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Catégorie *</label>
                            <select id="category" name="category" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($categories as $cat_key => $cat_label): ?>
                                <option value="<?php echo $cat_key; ?>" <?php echo ($skill['category'] ?? '') === $cat_key ? 'selected' : ''; ?>>
                                    <?php echo $cat_label; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="level_percentage">Niveau (%) *</label>
                            <input type="number" id="level_percentage" name="level_percentage" min="0" max="100" value="<?php echo htmlspecialchars($skill['level_percentage'] ?? 50); ?>" required>
                            <small>Entre 0 et 100</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="icon">Icône (Font Awesome)</label>
                            <input type="text" id="icon" name="icon" value="<?php echo htmlspecialchars($skill['icon'] ?? ''); ?>" placeholder="fas fa-code">
                            <small>Ex: fas fa-code, fab fa-js, fas fa-brain</small>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($skill['description'] ?? ''); ?></textarea>
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
            
            <div class="section-card">
                <h3><i class="fas fa-info-circle"></i> Aide pour les icônes</h3>
                <p>Utilisez les classes Font Awesome 6. Exemples :</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                    <div><i class="fab fa-php"></i> <code>fab fa-php</code></div>
                    <div><i class="fab fa-js"></i> <code>fab fa-js</code></div>
                    <div><i class="fab fa-python"></i> <code>fab fa-python</code></div>
                    <div><i class="fab fa-html5"></i> <code>fab fa-html5</code></div>
                    <div><i class="fab fa-css3-alt"></i> <code>fab fa-css3-alt</code></div>
                    <div><i class="fas fa-database"></i> <code>fas fa-database</code></div>
                    <div><i class="fas fa-code"></i> <code>fas fa-code</code></div>
                    <div><i class="fas fa-language"></i> <code>fas fa-language</code></div>
                </div>
                <p style="margin-top: 15px;"><a href="https://fontawesome.com/search" target="_blank" class="btn-secondary">Voir toutes les icônes <i class="fas fa-external-link-alt"></i></a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
