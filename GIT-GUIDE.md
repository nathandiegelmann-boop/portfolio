# Configuration Git pour le Portfolio

## 🔧 Commandes Git utiles

### Workflow quotidien
```bash
# Ajouter des modifications
git add .

# Commit avec message descriptif  
git commit -m "✨ feat: description de la fonctionnalité"

# Pousser vers GitHub
git push origin main
```

### Types de commits conventionnels
- `✨ feat:` Nouvelle fonctionnalité
- `🐛 fix:` Correction de bug
- `🎨 style:` Amélioration design/CSS
- `📝 docs:` Documentation
- `🔧 config:` Configuration
- `🚀 deploy:` Déploiement
- `♻️ refactor:` Refactorisation code

### Branches
```bash
# Créer une nouvelle branche
git checkout -b feature/nouvelle-fonctionnalite

# Changer de branche
git checkout main

# Fusionner une branche
git merge feature/nouvelle-fonctionnalite
```

### État du repository
```bash
# Voir l'état des fichiers
git status

# Voir l'historique
git log --oneline

# Voir les modifications
git diff
```

## 🔐 Configuration SSH (optionnel)

Pour éviter de taper mot de passe à chaque push :

1. Générer une clé SSH :
```bash
ssh-keygen -t ed25519 -C "nathan.diegelmann@gmail.com"
```

2. Ajouter la clé publique à GitHub dans Settings > SSH and GPG keys

3. Changer l'URL remote :
```bash
git remote set-url origin git@github.com:nathandiegelmann-boop/portfolio.git
```

## 📁 Fichiers ignorés (.gitignore)

Le fichier `.gitignore` exclut automatiquement :
- Fichiers de configuration sensibles
- Logs et fichiers temporaires
- Fichiers IDE et système
- Variables d'environnement

## 🚀 Déploiement

Pour déployer sur un serveur :
1. Cloner le repository
2. Configurer la base de données
3. Adapter `includes/config.php`
4. Configurer le serveur web