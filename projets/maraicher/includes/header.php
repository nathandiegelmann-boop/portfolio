<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Précommandez vos fruits et légumes frais directement chez votre maraîcher local'; ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>assets/css/style.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>assets/images/favicon.ico">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>index.php">
                        <h1><span class="logo-leaf">🌱</span> <?php echo SITE_NAME; ?></h1>
                    </a>
                </div>
                
                <nav class="main-nav">
                    <ul>
                        <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>index.php">Accueil</a></li>
                        <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>catalogue.php">Nos Produits</a></li>
                        <li>
                            <a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>client/panier.php" class="cart-link">
                                🛒 Panier 
                                <span class="cart-count">
                                    <?php 
                                    $cart_count = 0;
                                    if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
                                        $cart_count = array_sum($_SESSION['panier']);
                                    }
                                    echo $cart_count;
                                    ?>
                                </span>
                            </a>
                        </li>
                        <?php if (is_logged_in()): ?>
                            <?php if (is_admin()): ?>
                                <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>admin/dashboard.php">Administration</a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>client/compte.php">Mon Compte</a></li>
                            <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>logout.php">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>connexion.php">Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <?php 
        // Affichage des messages
        if (isset($_GET['message'])) {
            switch ($_GET['message']) {
                case 'ajout_ok':
                    echo '<div class="alert alert-success">Produit ajouté au panier avec succès !</div>';
                    break;
                case 'stock_insuffisant':
                    echo '<div class="alert alert-error">Stock insuffisant pour ce produit.</div>';
                    break;
                case 'commande_ok':
                    echo '<div class="alert alert-success">Votre commande a été enregistrée avec succès !</div>';
                    break;
                case 'erreur':
                    echo '<div class="alert alert-error">Une erreur est survenue. Veuillez réessayer.</div>';
                    break;
            }
        }
        
        // Affichage des messages de session
        display_message();
        ?>