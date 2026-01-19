<?php
// Script de tracking des visiteurs - À inclure sur chaque page
if (!defined('TRACKING_ENABLED')) {
    define('TRACKING_ENABLED', true);
}

// Ne pas tracker les pages admin
$current_page = $_SERVER['REQUEST_URI'] ?? '';
$is_admin_page = (strpos($current_page, '/admin/') !== false);

if (TRACKING_ENABLED && isset($pdo) && !$is_admin_page) {
    // Générer ou récupérer l'ID de session
    if (!isset($_SESSION['visitor_session_id'])) {
        $_SESSION['visitor_session_id'] = bin2hex(random_bytes(32));
        $_SESSION['visitor_first_visit'] = time();
    }
    $session_id = $_SESSION['visitor_session_id'];
    
    // Informations du visiteur
    $visitor_ip = getRealIpAddress();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $page_url = $_SERVER['REQUEST_URI'] ?? '';
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    
    try {
        // Vérifier si c'est une nouvelle page ou juste un rafraîchissement
        $last_visit_key = 'last_visit_' . md5($page_url);
        $should_record = true;
        
        if (isset($_SESSION[$last_visit_key])) {
            $time_since_last = time() - $_SESSION[$last_visit_key];
            // Ne pas enregistrer si moins de 10 secondes (évite les doublons)
            if ($time_since_last < 10) {
                $should_record = false;
            }
        }
        
        if ($should_record) {
            // Enregistrer la visite
            $sql = "INSERT INTO site_visits (visitor_ip, user_agent, page_url, referrer, session_id, visit_time) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$visitor_ip, $user_agent, $page_url, $referrer, $session_id]);
            
            $_SESSION[$last_visit_key] = time();
        }
        
        // Mettre à jour ou créer la session active
        $sql_session = "INSERT INTO active_sessions (session_id, visitor_ip, last_activity, page_current) 
                        VALUES (?, ?, NOW(), ?)
                        ON DUPLICATE KEY UPDATE last_activity = NOW(), page_current = ?";
        $stmt_session = $pdo->prepare($sql_session);
        $stmt_session->execute([$session_id, $visitor_ip, $page_url, $page_url]);
        
        // Nettoyer les sessions inactives (plus de 5 minutes)
        $sql_clean = "DELETE FROM active_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
        $pdo->exec($sql_clean);
        
    } catch (PDOException $e) {
        // Ignorer les erreurs de tracking pour ne pas affecter le site
        error_log("Tracking error: " . $e->getMessage());
    }
}
