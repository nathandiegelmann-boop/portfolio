USE nathan_portfolio;

-- Supprimer les anciennes compétences de programmation
DELETE FROM skills WHERE category = 'programming';

-- Ajouter les vraies compétences de Nathan en programmation
INSERT INTO skills (name, category, level_percentage, description, icon_class, is_primary, display_order) VALUES
('HTML5', 'programming', 85, 'Maîtrise solide du HTML5 sémantique, structuration de pages web modernes avec les dernières balises et bonnes pratiques d''accessibilité.', 'fab fa-html5', 1, 1),
('CSS3', 'programming', 80, 'Compétences avancées en CSS3 : Flexbox, Grid, animations, responsive design, préprocesseurs et méthodologies BEM.', 'fab fa-css3-alt', 1, 2),
('PHP', 'programming', 75, 'Développement backend avec PHP : gestion de bases de données PDO, sessions utilisateur, formulaires sécurisés et architecture MVC.', 'fab fa-php', 1, 3),
('MySQL', 'programming', 70, 'Base de données relationnelle : conception de schémas normalisés, requêtes SQL complexes, jointures et optimisation des performances.', 'fas fa-database', 1, 4);