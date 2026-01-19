# 🚀 Guide d'Installation Rapide - Admin Portfolio

## Étape 1 : Installation de la base de données

1. Ouvrez **phpMyAdmin** : `http://localhost/phpmyadmin`

2. Sélectionnez votre base de données : `u764008148_ihhhna`

3. Cliquez sur l'onglet **SQL**

4. Copiez-collez le contenu du fichier `install.sql` dans la zone de texte

5. Cliquez sur **Exécuter**

✅ La table `admin_users` est maintenant créée avec un compte admin par défaut.

## Étape 2 : Première connexion

1. Accédez à : `http://localhost/portfolio/admin/`

2. Vous serez redirigé vers la page de login

3. Utilisez les identifiants par défaut :
   - **Utilisateur :** `admin`
   - **Mot de passe :** `admin123`

4. Cliquez sur **Se connecter**

## Étape 3 : Changer le mot de passe (OBLIGATOIRE)

### Option 1 : Via generate_hash.php

1. Accédez à : `http://localhost/portfolio/admin/generate_hash.php`

2. Entrez votre nouveau mot de passe

3. Cliquez sur **Générer le hash**

4. Copiez le hash généré

5. Dans phpMyAdmin, exécutez :
   ```sql
   UPDATE admin_users 
   SET password = 'VOTRE_HASH_ICI' 
   WHERE username = 'admin';
   ```

### Option 2 : Via PHP direct

1. Créez un fichier temporaire `change_pass.php` avec :
   ```php
   <?php
   require_once '../includes/config.php';
   $new_password = 'VotreNouveauMotDePasse';
   $hash = password_hash($new_password, PASSWORD_DEFAULT);
   $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
   $stmt->execute([$hash]);
   echo "Mot de passe changé !";
   // Supprimez ce fichier après utilisation
   ?>
   ```

2. Exécutez-le une fois : `http://localhost/portfolio/admin/change_pass.php`

3. **SUPPRIMEZ le fichier immédiatement après !**

## Étape 4 : Utilisation

Vous pouvez maintenant :

✅ **Gérer les Projets** : `http://localhost/portfolio/admin/projects.php`
- Ajouter de nouveaux projets
- Modifier les projets existants
- Supprimer des projets
- Mettre en vedette

✅ **Gérer les Compétences** : `http://localhost/portfolio/admin/skills.php`
- Ajouter des compétences
- Définir le niveau (%)
- Catégoriser
- Ajouter des icônes

✅ **Gérer les Expériences** : `http://localhost/portfolio/admin/experiences.php`
- Ajouter votre parcours
- Définir les dates
- Marquer comme actuel
- Organiser l'ordre

## 📝 Créer un nouvel administrateur

1. Générez un hash avec `generate_hash.php`

2. Ouvrez phpMyAdmin

3. Exécutez :
   ```sql
   INSERT INTO admin_users (username, password, full_name, email, is_active) 
   VALUES (
     'nouveau_admin',
     'HASH_GENERE',
     'Nom Complet',
     'email@example.com',
     1
   );
   ```

## ⚠️ Sécurité

- ✅ Changez TOUJOURS le mot de passe par défaut
- ✅ Ne partagez jamais vos identifiants
- ✅ Utilisez des mots de passe forts (12+ caractères)
- ✅ Gardez le dossier `admin/` protégé
- ✅ Ne commitez jamais les identifiants sur Git

## 🆘 Problèmes courants

### "Erreur de connexion à la base de données"
→ Vérifiez `includes/config.php` (identifiants BDD)

### "Session non trouvée"
→ Vérifiez que `session_start()` fonctionne

### "Page blanche"
→ Activez l'affichage des erreurs PHP :
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### "Permission denied"
→ Vérifiez les permissions des dossiers :
```bash
chmod 755 admin/
chmod 644 admin/*.php
```

## 📞 Support

Si vous avez des problèmes, vérifiez :
1. Les logs Apache/PHP
2. La console du navigateur (F12)
3. Les erreurs MySQL dans phpMyAdmin

---

**Installation terminée ! Bon travail ! 🎉**
