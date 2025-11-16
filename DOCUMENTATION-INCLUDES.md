# Système d'Includes Portfolio Nathan

## 📁 Structure des fichiers

```
includes/
├── init.php          # Initialisation et fonctions principales
├── header.php        # En-tête HTML et meta tags
├── nav.php          # Navigation principale
├── footer.php       # Footer avec liens et infos
└── config.php       # Configuration existante (BDD, etc.)
```

## 🚀 Utilisation rapide

### 1. Page simple (Recommandé)
```php
<?php
require_once __DIR__ . '/includes/init.php';

$page_config = [
    'title' => 'Ma Page',
    'description' => 'Description SEO',
    'current_page' => 'ma-page',
    'extra_css' => '/* CSS spécifique */',
    'extra_js' => '/* JS spécifique */'
];

include_header($page_config);
include_nav();
?>

<!-- Votre contenu HTML -->
<section class="main-content">
    <h1>Contenu de la page</h1>
</section>

<?php include_footer(); ?>
```

### 2. Page ultra-simple
```php
<?php
require_once __DIR__ . '/includes/init.php';

render_page('content/ma-page.php', [
    'title' => 'Ma Page',
    'description' => 'Description'
]);
?>
```

## ⚙️ Options de configuration

### Configuration de page disponible :
- **title** : Titre dans `<title>` (automatiquement suffixé par SITE_NAME)
- **description** : Meta description SEO
- **current_page** : Page active dans la navigation (auto-détectée si omise)
- **extra_css** : CSS spécifique à la page (dans `<style>`)
- **extra_js** : JavaScript spécifique à la page (dans `<script>`)
- **extra_js_files** : Array de fichiers JS externes à inclure
- **extra_meta** : Meta tags supplémentaires
- **body_class** : Classes CSS pour `<body>`
- **body_style** : Styles inline pour `<body>`

### Exemple complet :
```php
$page_config = [
    'title' => 'Contact',
    'description' => 'Contactez Nathan Diegelmann',
    'current_page' => 'contact',
    'extra_css' => '.contact-form { padding: 2rem; }',
    'extra_js' => 'console.log("Contact loaded");',
    'extra_js_files' => ['assets/js/contact-validation.js'],
    'body_class' => 'contact-page dark-theme',
    'body_style' => 'background: #000;'
];
```

## 🎯 Avantages du système

### ✅ Réduction du code
- **Header/Footer centralisés** → Plus de duplication
- **Navigation automatique** → Active link auto-détecté
- **Meta tags dynamiques** → SEO simplifié
- **Scripts centralisés** → Maintenance facilitée

### ✅ Maintenance simplifiée
- Modification du footer → 1 seul fichier à changer
- Ajout d'un lien nav → Modification dans nav.php seulement
- Nouveau meta tag → Header.php seulement
- CSS global → style.css, CSS spécifique → extra_css

### ✅ Fonctionnalités automatiques
- **Page active** détectée automatiquement dans la nav
- **Title** automatiquement formaté avec SITE_NAME
- **Mobile menu** inclus dans la navigation
- **Footer social links** générés depuis config
- **Sécurité** : Accès direct aux includes bloqué

## 📱 Navigation responsive

Le système inclut automatiquement :
- Menu mobile toggle
- Navigation responsive
- Footer adaptatif
- Classes CSS appropriées

## 🛡️ Sécurité

- Protection contre l'accès direct aux includes
- Variables définies avec `PORTFOLIO_INIT`
- Validation des configurations
- Échappement automatique des variables

## 📝 Migration des pages existantes

### Étapes pour migrer une page :
1. Remplacer `require_once config.php` par `require_once init.php`
2. Définir `$page_config` avec title, description, etc.
3. Remplacer le HTML header par `include_header($page_config)`
4. Remplacer la nav par `include_nav()`
5. Remplacer scripts/body par `include_footer()`
6. Déplacer CSS spécifique vers `extra_css`
7. Déplacer JS spécifique vers `extra_js`

### Exemple migration contact.php :
```diff
- require_once __DIR__ . '/includes/config.php';
+ require_once __DIR__ . '/includes/init.php';

+ $page_config = [
+     'title' => 'Contact',
+     'description' => 'Contactez Nathan',
+     'current_page' => 'contact'
+ ];

- <!DOCTYPE html>...
- <nav class="cyber-nav">...
+ include_header($page_config);
+ include_nav();

<!-- Contenu de la page -->

- <script src="assets/js/main.js"></script>
- </body></html>
+ include_footer();
```

## 🎨 Personnalisation

### CSS personnalisé par page :
```php
'extra_css' => '
    .ma-section {
        background: var(--terminal-green);
        padding: 2rem;
    }
    
    @media (max-width: 768px) {
        .ma-section { padding: 1rem; }
    }
'
```

### JavaScript personnalisé :
```php
'extra_js' => '
    document.addEventListener("DOMContentLoaded", function() {
        // Votre code JS
        const btn = document.querySelector(".mon-bouton");
        btn.addEventListener("click", handleClick);
    });
'
```

### Fichiers externes :
```php
'extra_js_files' => [
    'assets/js/particles.js',
    'assets/js/animations.js',
    'https://cdn.example.com/library.js'
]
```

## 🔧 Maintenance

### Modifier le footer :
Éditer `includes/footer.php` → Toutes les pages mises à jour

### Ajouter un lien nav :
Modifier `includes/nav.php` dans `.nav-menu`

### Changer le design global :
Modifier `assets/css/style.css`

### Nouveau meta tag :
Ajouter dans `includes/header.php`

Cette architecture modularise le code et facilite grandement la maintenance ! 🚀