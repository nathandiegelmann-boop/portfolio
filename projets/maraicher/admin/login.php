<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Page de connexion admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];
    
    if (empty($email) || !validate_email($email)) {
        $errors[] = 'Email invalide';
    }
    
    if (empty($password)) {
        $errors[] = 'Mot de passe requis';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, nom, prenom, email, password, role FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            redirect('dashboard.php');
        } else {
            $errors[] = 'Identifiants incorrects';
        }
    }
}

$page_title = 'Administration - Connexion';
include '../includes/header.php';
?>

<section class="login-section">
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>🔐 Administration</h1>
                    <p>Connectez-vous pour accéder au panneau d'administration</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="" class="login-form">
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>"
                               placeholder="admin@maraicher.local">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required
                               placeholder="Votre mot de passe">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full btn-large">
                        Se connecter
                    </button>
                </form>
                
                <div class="login-footer">
                    <p>Accès réservé aux administrateurs</p>
                    <a href="../index.php">← Retour au site</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.login-section {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 50px 0;
}

.login-container {
    max-width: 400px;
    width: 100%;
}

.login-card {
    background: white;
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h1 {
    color: #4CAF50;
    margin-bottom: 10px;
}

.login-form .form-group {
    margin-bottom: 20px;
}

.login-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.login-form input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.login-form input:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 0 2px rgba(76,175,80,0.2);
}

.login-footer {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.login-footer p {
    color: #666;
    margin-bottom: 10px;
}

.login-footer a {
    color: #4CAF50;
    text-decoration: none;
}

.login-footer a:hover {
    text-decoration: underline;
}
</style>

<?php include '../includes/footer.php'; ?>