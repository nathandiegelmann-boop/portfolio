<?php
// Vérification de l'authentification admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Fonction pour vérifier les permissions
function checkAdminPermission() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Fonction de déconnexion
function adminLogout() {
    session_destroy();
    header('Location: login.php');
    exit();
}
