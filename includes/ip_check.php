<?php
// Vérification si l'IP est bannie - Bloque l'accès au site
if (!defined('SKIP_IP_CHECK')) {
    $visitor_ip = getRealIpAddress();
    
    // Ne pas bloquer l'accès aux pages admin
    $current_page = $_SERVER['REQUEST_URI'] ?? '';
    $is_admin_page = (strpos($current_page, '/admin/') !== false);
    
    if (!$is_admin_page && isset($pdo) && $visitor_ip !== 'unknown') {
        try {
            // Vérifier si l'IP est bannie et le bannissement est encore actif
            $sql = "SELECT * FROM spam_blacklist WHERE ip_address = ? AND banned_until > NOW() LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$visitor_ip]);
            $ban = $stmt->fetch();
            
            if ($ban) {
                // L'IP est bannie - Bloquer l'accès
                $banned_until = date('d/m/Y à H:i', strtotime($ban['banned_until']));
                $reason = htmlspecialchars($ban['reason']);
                
                http_response_code(403);
                ?>
                <!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Accès Refusé</title>
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        body {
                            font-family: 'Inter', sans-serif;
                            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                            min-height: 100vh;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                        }
                        .error-container {
                            text-align: center;
                            max-width: 600px;
                            padding: 2rem;
                        }
                        .error-icon {
                            font-size: 6rem;
                            color: #EF4444;
                            margin-bottom: 2rem;
                            animation: pulse 2s infinite;
                        }
                        @keyframes pulse {
                            0%, 100% { opacity: 1; }
                            50% { opacity: 0.6; }
                        }
                        h1 {
                            font-size: 2.5rem;
                            font-weight: 800;
                            margin-bottom: 1rem;
                            color: #EF4444;
                        }
                        .error-code {
                            font-size: 1.2rem;
                            color: #94a3b8;
                            margin-bottom: 2rem;
                        }
                        .error-message {
                            background: rgba(239, 68, 68, 0.1);
                            border: 2px solid #EF4444;
                            border-radius: 12px;
                            padding: 1.5rem;
                            margin-bottom: 2rem;
                        }
                        .error-message p {
                            margin-bottom: 1rem;
                            font-size: 1.1rem;
                            line-height: 1.6;
                        }
                        .error-message strong {
                            color: #FCA5A5;
                        }
                        .info-box {
                            background: rgba(59, 130, 246, 0.1);
                            border: 1px solid #3B82F6;
                            border-radius: 8px;
                            padding: 1rem;
                            margin-top: 1rem;
                            font-size: 0.95rem;
                            color: #93c5fd;
                        }
                        .ip-display {
                            font-family: 'Courier New', monospace;
                            background: rgba(15, 23, 42, 0.5);
                            padding: 0.5rem 1rem;
                            border-radius: 6px;
                            display: inline-block;
                            margin-top: 1rem;
                            color: #cbd5e1;
                        }
                    </style>
                </head>
                <body>
                    <div class="error-container">
                        <div class="error-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <h1>Accès Refusé</h1>
                        <div class="error-code">Erreur 403 - Forbidden</div>
                        
                        <div class="error-message">
                            <p><strong>Votre adresse IP a été bloquée.</strong></p>
                            <p><strong>Raison :</strong> <?php echo $reason; ?></p>
                            <p><strong>Bannissement actif jusqu'au :</strong> <?php echo $banned_until; ?></p>
                        </div>
                        
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administrateur du site.
                        </div>
                        
                        <div class="ip-display">
                            <i class="fas fa-network-wired"></i> Votre IP : <?php echo htmlspecialchars($visitor_ip); ?>
                        </div>
                    </div>
                </body>
                </html>
                <?php
                exit;
            }
        } catch (PDOException $e) {
            // En cas d'erreur, on laisse passer (ne pas bloquer le site)
            error_log("IP check error: " . $e->getMessage());
        }
    }
}
