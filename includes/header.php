<?php
// Vérifier que ce fichier est inclus et non accédé directement
if (!defined('PORTFOLIO_INIT')) {
    die('Accès direct non autorisé');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Portfolio de Nathan Diegelmann - Développeur web HTML/CSS/PHP/SQL et powerlifter. Étudiant en Bachelor Développement Web.'; ?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'terminal-green': '#00ff41',
                        'code-blue': '#61dafb',
                        'darker-bg': '#0d1117',
                        'dark-bg': '#161b22',
                        'code-bg': '#21262d',
                        'border-color': '#30363d',
                        'text-primary': '#f0f6fc',
                        'text-secondary': '#8b949e'
                    },
                    fontFamily: {
                        'mono': ['Fira Code', 'JetBrains Mono', 'monospace'],
                        'primary': ['system-ui', '-apple-system', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <!-- CSS personnalisé si nécessaire -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- CSS supplémentaires spécifiques à la page -->
    <?php if (isset($additional_css) && is_array($additional_css)): ?>
        <?php foreach ($additional_css as $css_file): ?>
            <link rel="stylesheet" href="<?php echo $css_file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- CSS inline spécifique à la page -->
    <?php if (isset($extra_css)): ?>
        <style><?php echo $extra_css; ?></style>
    <?php endif; ?>
    
    <!-- Meta supplémentaires -->
    <?php if (isset($extra_meta)): ?>
        <?php echo $extra_meta; ?>
    <?php endif; ?>
</head>
<body<?php echo isset($body_class) ? ' class="' . $body_class . '"' : ''; ?>><?php echo isset($body_style) ? ' style="' . $body_style . '"' : ''; ?>>