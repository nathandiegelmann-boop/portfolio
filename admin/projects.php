<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$success = '';
$error = '';
$project = null;

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Suppression
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Projet supprimé avec succès';
        $action = 'list';
    } else {
        $error = 'Erreur lors de la suppression';
    }
}

// Récupération pour édition
if (($action === 'edit') && $id) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) {
        $error = 'Projet introuvable';
        $action = 'list';
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $type = $_POST['type'] ?? '';
    $technologies = $_POST['technologies'] ?? '';
    $github_url = $_POST['github_url'] ?? '';
    $demo_url = $_POST['demo_url'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = $_POST['status'] ?? 'completed';
    
    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, type, technologies, github_url, demo_url, image_url, is_featured, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        if ($stmt->execute([$title, $description, $type, $technologies, $github_url, $demo_url, $image_url, $is_featured, $status])) {
            $success = 'Projet ajouté avec succès';
            $action = 'list';
        } else {
            $error = 'Erreur lors de l\'ajout';
        }
    } elseif ($action === 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, type = ?, technologies = ?, github_url = ?, demo_url = ?, image_url = ?, is_featured = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$title, $description, $type, $technologies, $github_url, $demo_url, $image_url, $is_featured, $status, $id])) {
            $success = 'Projet modifié avec succès';
            $action = 'list';
        } else {
            $error = 'Erreur lors de la modification';
        }
    }
}

// Liste des projets
if ($action === 'list') {
    $projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Projets - Administration</title>
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
                    <h1><i class="fas fa-folder-open"></i> Gestion des Projets</h1>
                    <p class="subtitle">Gérez vos projets affichés sur le portfolio</p>
                </div>
                <a href="?action=add" class="btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Projet
                </a>
            </div>
            
            <div class="section-card">
                <?php if (!empty($projects)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Technologies</th>
                                <th>Statut</th>
                                <th>En vedette</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $proj): ?>
                            <tr>
                                <td>
                                    <?php if ($proj['image_url']): ?>
                                        <img src="../<?php echo htmlspecialchars($proj['image_url']); ?>" alt="" style="width: 60px; height: 40px; object-fit: cover; border-radius: 6px;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 40px; background: #334155; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image" style="color: #64748B;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($proj['title']); ?></strong></td>
                                <td><span class="badge"><?php echo htmlspecialchars($proj['type'] ?? 'N/A'); ?></span></td>
                                <td><?php echo htmlspecialchars(substr($proj['technologies'] ?? '', 0, 30)); ?>...</td>
                                <td>
                                    <?php
                                    $status_class = $proj['status'] === 'completed' ? 'success' : 'warning';
                                    $status_text = $proj['status'] === 'completed' ? 'Terminé' : 'En cours';
                                    ?>
                                    <span class="badge badge-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                                <td>
                                    <?php if ($proj['is_featured']): ?>
                                        <span class="badge badge-info">⭐ Oui</span>
                                    <?php else: ?>
                                        <span class="badge">Non</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?action=edit&id=<?php echo $proj['id']; ?>" class="btn-icon" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=delete&id=<?php echo $proj['id']; ?>" class="btn-icon btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">
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
                    <p class="no-data">Aucun projet pour le moment</p>
                <?php endif; ?>
            </div>
        
        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?>"></i> <?php echo $action === 'add' ? 'Nouveau' : 'Modifier le'; ?> Projet</h1>
                </div>
                <a href="?action=list" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            
            <div class="section-card">
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="title">Titre du projet *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($project['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="type">Type de projet</label>
                            <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($project['type'] ?? ''); ?>" placeholder="Ex: Site Web, Application, API...">
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="technologies">Technologies utilisées</label>
                            <input type="text" id="technologies" name="technologies" value="<?php echo htmlspecialchars($project['technologies'] ?? ''); ?>" placeholder="Ex: PHP, MySQL, JavaScript, HTML, CSS">
                        </div>
                        
                        <div class="form-group">
                            <label for="github_url">URL GitHub</label>
                            <input type="url" id="github_url" name="github_url" value="<?php echo htmlspecialchars($project['github_url'] ?? ''); ?>" placeholder="https://github.com/...">
                        </div>
                        
                        <div class="form-group">
                            <label for="demo_url">URL Démo</label>
                            <input type="url" id="demo_url" name="demo_url" value="<?php echo htmlspecialchars($project['demo_url'] ?? ''); ?>" placeholder="https://demo.example.com">
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="image_url">Image du projet (chemin relatif)</label>
                            <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($project['image_url'] ?? ''); ?>" placeholder="assets/images/projets/mon-projet.jpg">
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <option value="completed" <?php echo ($project['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Terminé</option>
                                <option value="in_progress" <?php echo ($project['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>En cours</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_featured" value="1" <?php echo ($project['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                <span>Mettre en vedette (afficher en premier)</span>
                            </label>
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
