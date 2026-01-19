<?php
/**
 * Générateur de hash de mot de passe pour l'administration
 * Utilisez ce fichier pour générer des hashs sécurisés pour les mots de passe admin
 */

// Fonction pour générer un hash
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Si un mot de passe est fourni via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];
    $hash = generatePasswordHash($password);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de Hash - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(51, 65, 85, 0.8);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        
        h1 {
            color: #E2E8F0;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        h1 i {
            background: linear-gradient(135deg, #F59E0B, #EF4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .subtitle {
            color: #94A3B8;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #E2E8F0;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        input {
            width: 100%;
            padding: 14px 15px;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(51, 65, 85, 0.8);
            border-radius: 12px;
            color: #E2E8F0;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
        }
        
        input:focus {
            outline: none;
            border-color: #F59E0B;
        }
        
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #F59E0B, #EF4444);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
        }
        
        .result {
            margin-top: 25px;
            padding: 20px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
        }
        
        .result h3 {
            color: #6EE7B7;
            margin-bottom: 12px;
            font-size: 16px;
        }
        
        .hash-output {
            background: rgba(15, 23, 42, 0.8);
            padding: 15px;
            border-radius: 8px;
            color: #E2E8F0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            word-break: break-all;
            line-height: 1.6;
        }
        
        .copy-btn {
            margin-top: 12px;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #6EE7B7;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .copy-btn:hover {
            background: rgba(16, 185, 129, 0.3);
        }
        
        .info-box {
            margin-top: 25px;
            padding: 20px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 12px;
            color: #93C5FD;
            font-size: 14px;
        }
        
        .info-box h4 {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-box code {
            background: rgba(15, 23, 42, 0.8);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        
        .back-link {
            text-align: center;
            margin-top: 25px;
        }
        
        .back-link a {
            color: #94A3B8;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link a:hover {
            color: #F59E0B;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-key"></i> Générateur de Hash</h1>
        <p class="subtitle">Créez un hash sécurisé pour un mot de passe administrateur</p>
        
        <form method="POST">
            <div class="form-group">
                <label for="password">Mot de passe à hasher</label>
                <input type="text" id="password" name="password" required placeholder="Entrez le mot de passe">
            </div>
            
            <button type="submit">
                <i class="fas fa-lock"></i> Générer le hash
            </button>
        </form>
        
        <?php if (isset($hash)): ?>
        <div class="result">
            <h3><i class="fas fa-check-circle"></i> Hash généré avec succès !</h3>
            <div class="hash-output" id="hashOutput"><?php echo $hash; ?></div>
            <button class="copy-btn" onclick="copyHash()">
                <i class="fas fa-copy"></i> Copier le hash
            </button>
        </div>
        <?php endif; ?>
        
        <div class="info-box">
            <h4><i class="fas fa-info-circle"></i> Comment utiliser ce hash ?</h4>
            <ol style="margin-left: 20px; margin-top: 10px; line-height: 1.8;">
                <li>Copiez le hash généré ci-dessus</li>
                <li>Ouvrez le fichier <code>create_admin.sql</code></li>
                <li>Remplacez le hash dans la requête INSERT</li>
                <li>Exécutez la requête SQL dans phpMyAdmin</li>
            </ol>
        </div>
        
        <div class="back-link">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
        </div>
    </div>
    
    <script>
        function copyHash() {
            const hashText = document.getElementById('hashOutput').textContent;
            navigator.clipboard.writeText(hashText).then(() => {
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copié !';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            });
        }
    </script>
</body>
</html>
