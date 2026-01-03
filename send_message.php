<?php
// Nathan Diegelmann - Traitement des messages de contact avec protection anti-spam
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}
<?php
// =============================================
// VÉRIFICATION DU TOKEN CSRF
// =============================================
// Récupérer le token envoyé par le formulaire
$submitted_token = $_POST['csrf_token'] ?? '';

// Récupérer le token stocké dans la session
$session_token = $_SESSION['csrf_token'] ?? '';

// Vérifier que les deux tokens correspondent
// hash_equals() = comparaison sécurisée qui empêche les attaques par timing
if (!hash_equals($session_token, $submitted_token)) {
    // Les tokens ne correspondent pas = requête suspecte !
    echo json_encode([
        'success' => false, 
        'message' => 'Token de sécurité invalide. Veuillez rafraîchir la page.'
    ]);
    exit;
}

// Si on arrive ici, le token est valide = la requête est légitime ✓
// =============================================
// PROTECTION ANTI-SPAM
// =============================================

$user_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$current_time = time();

// 1. HONEYPOT - Champ caché qui ne doit pas être rempli
if (!empty($_POST['website'])) {
    // Un bot a rempli le champ honeypot
    sleep(2); // Ralentir le bot
    echo json_encode(['success' => false, 'message' => 'Erreur de validation']);
    exit;
}

// 2. Vérifier si l'IP n'est pas bannie
$sql_check_ban = "SELECT COUNT(*) FROM spam_blacklist WHERE ip_address = ? AND banned_until > NOW()";
$stmt_ban = $pdo->prepare($sql_check_ban);
$stmt_ban->execute([$user_ip]);
if ($stmt_ban->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'Votre IP a été temporairement bloquée pour activité suspecte.']);
    exit;
}

// 3. Vérifier le nombre de messages envoyés dans la dernière heure
$sql_count = "SELECT COUNT(*) FROM contact_messages 
              WHERE sender_ip = ? 
              AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute([$user_ip]);
$messages_last_hour = $stmt_count->fetchColumn();

if ($messages_last_hour >= 3) {
    // Bannir temporairement l'IP (1 heure)
    $sql_ban = "INSERT INTO spam_blacklist (ip_address, reason, banned_until, created_at) 
                VALUES (?, 'Trop de messages', DATE_ADD(NOW(), INTERVAL 1 HOUR), NOW())
                ON DUPLICATE KEY UPDATE banned_until = DATE_ADD(NOW(), INTERVAL 1 HOUR)";
    $stmt_ban_insert = $pdo->prepare($sql_ban);
    $stmt_ban_insert->execute([$user_ip]);
    
    echo json_encode(['success' => false, 'message' => 'Limite de messages atteinte. Veuillez réessayer dans 1 heure.']);
    exit;
}

// 4. Vérifier le délai entre deux messages (minimum 30 secondes)
$sql_last = "SELECT UNIX_TIMESTAMP(created_at) as last_time 
             FROM contact_messages 
             WHERE sender_ip = ? 
             ORDER BY created_at DESC 
             LIMIT 1";
$stmt_last = $pdo->prepare($sql_last);
$stmt_last->execute([$user_ip]);
$last_message = $stmt_last->fetch();

if ($last_message && ($current_time - $last_message['last_time']) < 30) {
    $wait_time = 30 - ($current_time - $last_message['last_time']);
    echo json_encode([
        'success' => false, 
        'message' => "Veuillez attendre encore $wait_time secondes avant d'envoyer un nouveau message."
    ]);
    exit;
}

// =============================================
// VALIDATION DES DONNÉES
// =============================================

// Récupération et validation des données
$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$subject = sanitizeInput($_POST['subject'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

// Validation de base
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
    exit;
}

// Validation de la longueur
if (strlen($name) < 2 || strlen($name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Le nom doit contenir entre 2 et 100 caractères']);
    exit;
}

if (strlen($subject) < 3 || strlen($subject) > 200) {
    echo json_encode(['success' => false, 'message' => 'Le sujet doit contenir entre 3 et 200 caractères']);
    exit;
}

if (strlen($message) < 10 || strlen($message) > 5000) {
    echo json_encode(['success' => false, 'message' => 'Le message doit contenir entre 10 et 5000 caractères']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

// 5. Détection de spam basique (mots-clés suspects)
$spam_keywords = ['viagra', 'cialis', 'casino', 'porn', 'sex', 'xxx', 'crypto', 'bitcoin', 'investment', 'click here', 'buy now'];
$content_to_check = strtolower($name . ' ' . $subject . ' ' . $message);

foreach ($spam_keywords as $keyword) {
    if (strpos($content_to_check, $keyword) !== false) {
        // Marquer comme spam et bannir
        $sql_ban = "INSERT INTO spam_blacklist (ip_address, reason, banned_until, created_at) 
                    VALUES (?, 'Contenu spam détecté', DATE_ADD(NOW(), INTERVAL 24 HOUR), NOW())";
        $stmt_ban_spam = $pdo->prepare($sql_ban);
        $stmt_ban_spam->execute([$user_ip]);
        
        echo json_encode(['success' => false, 'message' => 'Message bloqué pour contenu suspect']);
        exit;
    }
}

// =============================================
// ENREGISTREMENT DU MESSAGE
// =============================================

try {
    // Insertion dans la base de données
    $sql = "INSERT INTO contact_messages (sender_name, sender_email, subject, message, sender_ip, is_read, created_at) 
            VALUES (?, ?, ?, ?, ?, 0, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $name,
        $email,
        $subject,
        $message,
        $user_ip
    ]);
    
    // Envoi d'un email de notification (optionnel)
    $to = ADMIN_EMAIL;
    $email_subject = "Nouveau message portfolio: " . $subject;
    $email_body = "Nouveau message de contact:\n\n";
    $email_body .= "Nom: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Sujet: $subject\n\n";
    $email_body .= "Message:\n$message\n";
    
    $headers = "From: noreply@portfolio.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Tentative d'envoi d'email (peut échouer sans serveur mail configuré)
    @mail($to, $email_subject, $email_body, $headers);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Message envoyé avec succès ! Je vous répondrai dans les plus brefs délais.'
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur envoi message: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors de l\'envoi du message. Veuillez réessayer ou me contacter directement par email.'
    ]);
}
