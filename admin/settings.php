<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$success = '';
$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    
    // Informations personnelles
    $nom = trim($_POST['nom'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $statut_actuel = trim($_POST['statut_actuel'] ?? '');
    $niveau_etude = trim($_POST['niveau_etude'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    
    // Configuration site
    $admin_email = trim($_POST['admin_email'] ?? '');
    $site_name = trim($_POST['site_name'] ?? '');
    
    // Créer le contenu PHP mis à jour
    $config_content = file_get_contents(__DIR__ . '/../includes/config.php');
    
    // Mettre à jour les constantes
    $config_content = preg_replace(
        "/define\('ADMIN_EMAIL',\s*'[^']*'\);/",
        "define('ADMIN_EMAIL', '$admin_email');",
        $config_content
    );
    
    $config_content = preg_replace(
        "/define\('SITE_NAME',\s*'[^']*'\);/",
        "define('SITE_NAME', '$site_name');",
        $config_content
    );
    
    // Mettre à jour les informations Nathan
    $nathan_updates = [
        'nom' => $nom,
        'statut_actuel' => $statut_actuel,
        'niveau_etude' => $niveau_etude,
        'ville' => $ville,
        'telephone' => $telephone,
        'email' => $email,
        'instagram' => $instagram,
    ];
    
    foreach ($nathan_updates as $key => $value) {
        $pattern = "/'$key'\s*=>\s*'[^']*'/";
        $replacement = "'$key' => '$value'";
        $config_content = preg_replace($pattern, $replacement, $config_content);
    }
    
    // Sauvegarder le fichier
    if (file_put_contents(__DIR__ . '/../includes/config.php', $config_content)) {
        $success = 'Paramètres mis à jour avec succès !';
        // Recharger la config
        header('Location: settings.php?success=1');
        exit;
    } else {
        $error = 'Erreur lors de la sauvegarde des paramètres';
    }
}

// Message de succès après redirection
if (isset($_GET['success'])) {
    $success = 'Paramètres mis à jour avec succès !';
}

// Charger les paramètres actuels
global $nathan_info;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Administration</title>
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
        
        <div class="page-header">
            <div>
                <h1><i class="fas fa-cog"></i> Paramètres du Portfolio</h1>
                <p class="subtitle">Gérez les informations affichées sur votre portfolio</p>
            </div>
        </div>
        
        <form method="POST">
            <div class="section-card">
                <h2><i class="fas fa-user"></i> Informations Personnelles</h2>
                
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label for="nom">Nom complet *</label>
                        <input type="text" id="nom" name="nom" class="form-control" 
                               value="<?php echo htmlspecialchars($nathan_info['nom'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group" style="flex: 1;">
                        <label for="ville">Ville</label>
                        <input type="text" id="ville" name="ville" class="form-control" 
                               value="<?php echo htmlspecialchars($nathan_info['ville'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label for="email">Email personnel</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($nathan_info['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group" style="flex: 1;">
                        <label for="telephone">Téléphone</label>
                        <input type="text" id="telephone" name="telephone" class="form-control" 
                               value="<?php echo htmlspecialchars($nathan_info['telephone'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="statut_actuel">Statut actuel</label>
                    <input type="text" id="statut_actuel" name="statut_actuel" class="form-control" 
                           value="<?php echo htmlspecialchars($nathan_info['statut_actuel'] ?? ''); ?>"
                           placeholder="Ex: Étudiant en Bachelor Développement Web">
                </div>
                
                <div class="form-group">
                    <label for="niveau_etude">Niveau d'étude</label>
                    <input type="text" id="niveau_etude" name="niveau_etude" class="form-control" 
                           value="<?php echo htmlspecialchars($nathan_info['niveau_etude'] ?? ''); ?>"
                           placeholder="Ex: Bac+1 (Bachelor en cours)">
                </div>
                
                <div class="form-group">
                    <label for="instagram">Instagram</label>
                    <input type="text" id="instagram" name="instagram" class="form-control" 
                           value="<?php echo htmlspecialchars($nathan_info['instagram'] ?? ''); ?>"
                           placeholder="Ex: @nth.dgl">
                </div>
            </div>
            
            <div class="section-card" style="margin-top: 2rem;">
                <h2><i class="fas fa-globe"></i> Configuration du Site</h2>
                
                <div class="form-group">
                    <label for="site_name">Nom du site</label>
                    <input type="text" id="site_name" name="site_name" class="form-control" 
                           value="<?php echo htmlspecialchars(SITE_NAME); ?>" required>
                    <small>Affiché dans le titre de la page et le header</small>
                </div>
                
                <div class="form-group">
                    <label for="admin_email">Email administrateur</label>
                    <input type="email" id="admin_email" name="admin_email" class="form-control" 
                           value="<?php echo htmlspecialchars(ADMIN_EMAIL); ?>" required>
                    <small>Reçoit les notifications des messages de contact</small>
                </div>
            </div>
            
            <div class="section-card" style="margin-top: 2rem;">
                <h2><i class="fas fa-info-circle"></i> Informations</h2>
                <div class="alert" style="background: #EFF6FF; border-left: 3px solid #3B82F6; color: #1e293b;">
                    <i class="fas fa-lightbulb" style="color: #3B82F6;"></i>
                    <div>
                        <strong>Note importante :</strong> Ces paramètres modifient directement le fichier de configuration.
                        Assurez-vous que les informations saisies sont correctes avant de sauvegarder.
                    </div>
                </div>
            </div>
            
            <div class="form-actions" style="margin-top: 2rem;">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
                <a href="dashboard.php" class="btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>
