# Guide d'Installation - Site Maraîcher

## 📋 Étapes d'installation détaillées

### 1. Prérequis
- XAMPP installé (Apache + MySQL + PHP)
- Navigateur web moderne

### 2. Démarrer les services XAMPP
1. Ouvrir le panneau de contrôle XAMPP
2. Démarrer **Apache** 
3. Démarrer **MySQL**

### 3. Créer la base de données

#### Option A : Via phpMyAdmin (Recommandé)
1. Ouvrir [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Cliquer sur "Nouvelle base de données"
3. Nom : `maraicher`
4. Interclassement : `utf8mb4_unicode_ci`
5. Cliquer sur "Créer"
6. Sélectionner la base `maraicher`
7. Aller dans l'onglet "SQL"
8. Copier-coller le contenu du fichier `database/schema.sql`
9. Cliquer sur "Exécuter"

#### Option B : Via ligne de commande
```bash
# Se placer dans le dossier MySQL de XAMPP
cd C:\xampp\mysql\bin

# Se connecter à MySQL
mysql -u root

# Créer la base de données
CREATE DATABASE maraicher CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Utiliser la base de données
USE maraicher;

# Importer le schéma (copier-coller le contenu de database/schema.sql)
```

### 4. Configuration
1. Vérifier le fichier `includes/config.php`
2. S'assurer que les paramètres correspondent à votre installation :
   ```php
   $host = 'localhost';
   $dbname = 'maraicher';
   $username = 'root';
   $password = ''; // Vide par défaut avec XAMPP
   ```

### 5. Test de l'installation
1. Ouvrir [http://localhost/maraicher](http://localhost/maraicher)
2. Vérifier que la page d'accueil s'affiche correctement
3. Tester l'administration : [http://localhost/maraicher/admin/login.php](http://localhost/maraicher/admin/login.php)
   - Email : `admin@maraicher.local`
   - Mot de passe : `admin123`

### 6. Permissions (si nécessaire)
Créer le dossier uploads s'il n'existe pas et vérifier les permissions :
```
assets/uploads/ (doit être accessible en écriture)
```

## 🔧 Résolution des problèmes courants

### Erreur "Connection failed"
- Vérifier que MySQL est démarré dans XAMPP
- Contrôler les paramètres de connexion dans `config.php`

### Page blanche / erreur 500
- Vérifier les logs d'erreur Apache dans `C:\xampp\apache\logs\error.log`
- S'assurer que PHP est activé

### Images non affichées
- Créer le dossier `assets/uploads/` s'il manque
- Vérifier les permissions du dossier

## 📊 Données de test incluses

Le fichier `schema.sql` inclut :
- Un utilisateur admin : `admin@maraicher.local` / `admin123`
- 5 catégories de produits
- 8 produits d'exemple avec prix et stocks
- Une commande d'exemple

## 🚀 Prêt à utiliser !

Une fois l'installation terminée, vous pouvez :
1. **Naviguer sur le site** pour voir l'interface client
2. **Tester le panier** en ajoutant des produits
3. **Passer une commande** test
4. **Administrer** via l'interface admin
5. **Personnaliser** les produits et catégories

## 📞 Support

Si vous rencontrez des difficultés :
1. Vérifiez que tous les services XAMPP sont démarrés
2. Consultez les logs d'erreur
3. Testez la connexion à la base de données via phpMyAdmin

---
*Installation réalisée avec succès ! 🎉*