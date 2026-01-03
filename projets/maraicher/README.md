# Site de Précommande pour Maraîcher

Un système complet de précommande en ligne pour un maraîcher, développé en PHP natif, MySQL, HTML5 et CSS3.

## 🌱 Fonctionnalités

### Frontend (Clients)
- **Page d'accueil** avec présentation du maraîcher et produits en vedette
- **Catalogue de produits** avec système de recherche et filtrage par catégories
- **Système de panier** avec gestion des quantités et validation des stocks
- **Processus de commande** complet avec formulaire de livraison
- **Page de confirmation** avec récapitulatif détaillé
- **Design responsive** adapté mobile et tablette

### Backend (Administration)
- **Tableau de bord** avec statistiques de vente et alertes
- **Gestion des produits** (ajout, modification, suppression, gestion des stocks)
- **Gestion des commandes** avec suivi du statut
- **Interface d'administration** sécurisée

## 🛠️ Technologies Utilisées

- **PHP 7.4+** (natif, sans framework)
- **MySQL** pour la base de données
- **HTML5** et **CSS3** (design moderne et responsive)
- **JavaScript vanille** pour les interactions
- **PDO** pour la sécurité des requêtes SQL

## 📋 Prérequis

- Serveur web avec PHP 7.4+
- MySQL 5.7+ ou MariaDB
- Extension PHP PDO activée

## 🚀 Installation

1. **Cloner ou télécharger** le projet dans votre dossier web (ex: `htdocs` pour XAMPP)

2. **Créer la base de données** :
   - Importer le fichier `database/schema.sql` dans MySQL
   - Ou exécuter les commandes SQL du fichier

3. **Configurer la connexion** :
   - Modifier `includes/config.php` avec vos paramètres de base de données

4. **Permissions** :
   - S'assurer que le dossier `assets/uploads/` est accessible en écriture

## 👤 Comptes par défaut

### Administrateur
- **Email** : admin@maraicher.local
- **Mot de passe** : admin123

## 📁 Structure du Projet

```
maraicher/
├── assets/
│   ├── css/
│   │   └── style.css           # Styles principaux
│   ├── js/                     # Scripts JavaScript
│   └── uploads/                # Images des produits
├── includes/
│   ├── config.php              # Configuration BDD
│   ├── functions.php           # Fonctions utilitaires
│   ├── header.php              # En-tête du site
│   └── footer.php              # Pied de page
├── admin/
│   ├── dashboard.php           # Tableau de bord admin
│   ├── login.php               # Connexion admin
│   ├── produits.php            # Gestion des produits
│   └── commandes.php           # Gestion des commandes
├── client/
│   ├── panier.php              # Gestion du panier
│   ├── commande.php            # Formulaire de commande
│   └── confirmation.php        # Confirmation de commande
├── database/
│   └── schema.sql              # Structure de la BDD
├── index.php                   # Page d'accueil
├── catalogue.php               # Catalogue des produits
└── logout.php                  # Déconnexion
```

## 🔒 Sécurité

- **Mots de passe hachés** avec `password_hash()`
- **Requêtes préparées** pour prévenir les injections SQL
- **Validation des données** côté serveur
- **Protection XSS** avec `htmlspecialchars()`
- **Sessions sécurisées** pour l'authentification

## 🎨 Design

- **Thème vert naturel** rappelant l'agriculture
- **Design responsive** (mobile-first)
- **Interface moderne** avec cartes et animations
- **UX optimisée** pour la conversion

## 📊 Fonctionnalités Avancées

### Gestion des Stocks
- Vérification automatique des stocks lors de l'ajout au panier
- Alertes pour les stocks faibles dans l'admin
- Mise à jour automatique après chaque commande

### Système de Commande
- Validation des quantités et montant minimum
- Gestion des frais de livraison
- États de commande (en attente, validée, prête, livrée)

### Analytics
- Statistiques de vente (jour/semaine/mois)
- Produits les plus vendus
- Chiffre d'affaires

## 🔧 Configuration

### Paramètres dans `includes/config.php` :
- `FRAIS_LIVRAISON` : Coût de la livraison (défaut: 3.50€)
- `COMMANDE_MIN` : Montant minimum de commande (défaut: 10€)
- `SITE_NAME` : Nom du site
- `SITE_EMAIL` : Email de contact

## 📱 Responsive Design

Le site est entièrement responsive et s'adapte à tous les écrans :
- **Mobile** : Navigation simplifiée, cartes empilées
- **Tablette** : Grille adaptée, menus optimisés  
- **Desktop** : Interface complète avec sidebars

## 🐛 Dépannage

### Erreurs courantes :

1. **Erreur de connexion BDD** :
   - Vérifier les paramètres dans `config.php`
   - S'assurer que MySQL est démarré

2. **Images non affichées** :
   - Vérifier les permissions du dossier `assets/uploads/`
   - S'assurer que les images existent

3. **Session non fonctionnelle** :
   - Vérifier que `session_start()` est appelé
   - Contrôler les permissions du dossier de sessions

## 📈 Évolutions Possibles

- **Paiement en ligne** (Stripe, PayPal)
- **Notifications email** automatiques
- **Gestion des utilisateurs** clients
- **Système de fidélité**
- **API REST** pour application mobile
- **Gestion multi-langues**

## 👥 Support

Pour toute question ou problème :
- Consulter la documentation technique dans le code
- Vérifier les logs d'erreurs PHP
- Tester avec les données d'exemple fournies

## 📄 Licence

Ce projet est libre d'utilisation pour des projets éducatifs et commerciaux.

---

*Développé avec ❤️ pour soutenir l'agriculture locale*