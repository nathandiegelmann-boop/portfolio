# 🛡️ Panneau d'Administration - Portfolio

Système d'administration complet pour gérer le contenu du portfolio Nathan Diegelmann.

## 🚀 Installation

### 1. Installer la base de données

Exécutez le fichier SQL d'installation :

```sql
-- Dans phpMyAdmin ou votre gestionnaire MySQL
source admin/install.sql
```

Ou importez manuellement le fichier `admin/install.sql` dans votre base de données.

### 2. Compte administrateur par défaut

**Identifiants par défaut :**
- **Utilisateur :** `admin`
- **Mot de passe :** `admin123`

⚠️ **IMPORTANT : Changez le mot de passe immédiatement après la première connexion !**

### 3. Accès au panneau d'administration

Accédez à : `http://localhost/portfolio/admin/login.php`

## 📁 Structure

```
admin/
├── login.php              # Page de connexion
├── dashboard.php          # Tableau de bord principal
├── projects.php           # Gestion des projets
├── skills.php             # Gestion des compétences
├── experiences.php        # Gestion des expériences
├── logout.php             # Déconnexion
├── install.sql            # Script d'installation
├── create_admin.sql       # Script pour créer un admin
├── includes/
│   ├── auth.php          # Vérification authentification
│   └── header.php        # En-tête admin
└── assets/
    └── css/
        └── admin.css     # Styles du panneau admin

```

## 🔐 Sécurité

### Créer un nouvel administrateur

1. Générez un hash de mot de passe en PHP :

```php
<?php
echo password_hash('VotreMotDePasse', PASSWORD_DEFAULT);
?>
```

2. Utilisez le script `create_admin.sql` et remplacez le hash généré

3. Exécutez la requête SQL

### Désactiver un compte admin

```sql
UPDATE admin_users SET is_active = 0 WHERE username = 'nom_utilisateur';
```

### Changer un mot de passe

```sql
UPDATE admin_users 
SET password = '$2y$10$NouveauHashIci...' 
WHERE username = 'nom_utilisateur';
```

## 📋 Fonctionnalités

### Gestion des Projets
- ✅ Ajouter/Modifier/Supprimer des projets
- ✅ Télécharger des images de projet
- ✅ Définir des projets en vedette
- ✅ Ajouter des liens GitHub et démo
- ✅ Gérer le statut (terminé/en cours)

### Gestion des Compétences
- ✅ Ajouter/Modifier/Supprimer des compétences
- ✅ Catégoriser les compétences
- ✅ Définir le niveau de maîtrise (%)
- ✅ Ajouter des icônes Font Awesome
- ✅ Descriptions détaillées

### Gestion des Expériences
- ✅ Ajouter/Modifier/Supprimer des expériences
- ✅ Définir les périodes (début/fin)
- ✅ Marquer comme poste actuel
- ✅ Ordre d'affichage personnalisé
- ✅ Descriptions complètes

## 🎨 Interface

- Design moderne et professionnel
- Thème sombre élégant
- Responsive (mobile, tablette, desktop)
- Navigation intuitive
- Tableaux de données optimisés

## 📱 Responsive

Le panneau d'administration est entièrement responsive et fonctionne sur :
- 💻 Desktop (1920px+)
- 💻 Laptop (1024px - 1919px)
- 📱 Tablette (768px - 1023px)
- 📱 Mobile (< 768px)

## 🔗 Navigation

- **Dashboard** : Vue d'ensemble et statistiques
- **Projets** : Gestion complète des projets
- **Compétences** : Gestion des compétences par catégorie
- **Expériences** : Gestion du parcours professionnel

## ⚙️ Configuration

Le système utilise la configuration existante dans `includes/config.php` :
- Connexion à la base de données
- Variables de session
- Paramètres du site

## 🆘 Support

Pour toute question ou problème :
1. Vérifiez que `install.sql` a été correctement exécuté
2. Vérifiez les identifiants de connexion
3. Assurez-vous que les sessions PHP fonctionnent
4. Vérifiez les permissions des fichiers

## 📝 Notes importantes

- Toutes les données sont stockées dans la base de données MySQL
- Les sessions sont utilisées pour l'authentification
- Les mots de passe sont hashés avec `password_hash()` (bcrypt)
- Protection CSRF intégrée
- Validation des entrées utilisateur

## 🔄 Mise à jour

Pour mettre à jour un administrateur existant, utilisez les requêtes SQL dans `create_admin.sql`

---

**Développé pour Portfolio Nathan Diegelmann**
Version 1.0 - 2026
