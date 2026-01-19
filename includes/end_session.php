<?php
// Script pour marquer la session comme terminée quand on quitte le site
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if (isset($_SESSION['visitor_session_id'])) {
    $session_id = $_SESSION['visitor_session_id'];
    
    try {
        // Supprimer la session active
        $sql = "DELETE FROM active_sessions WHERE session_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$session_id]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No session']);
}
