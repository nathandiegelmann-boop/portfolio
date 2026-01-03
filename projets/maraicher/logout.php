<?php
require_once 'includes/config.php';

// Déconnexion
if (isset($_SESSION['user_id'])) {
    // Détruire toutes les données de session
    session_unset();
    session_destroy();
    
    // Redémarrer une nouvelle session pour les messages
    session_start();
    $_SESSION['message'] = 'Vous avez été déconnecté avec succès';
}

redirect('index.php');
?>