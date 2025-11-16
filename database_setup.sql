-- Nathan Diegelmann Portfolio - Base de données Cyberpunk
-- Schéma optimisé pour portfolio futuriste

CREATE DATABASE IF NOT EXISTS nathan_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nathan_portfolio;

-- Table utilisateurs (Nathan admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Table projets Nathan
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    short_description VARCHAR(300),
    languages VARCHAR(255) NOT NULL, -- "Python, C++"
    technologies VARCHAR(255), -- Frameworks, libs
    category ENUM('python', 'cpp', 'web', 'automation', 'game', 'tool') NOT NULL,
    github_link VARCHAR(500),
    demo_link VARCHAR(500),
    image_url VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('concept', 'development', 'completed') DEFAULT 'development',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table compétences avec niveaux
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    level_percentage INT CHECK (level_percentage BETWEEN 0 AND 100),
    category ENUM('programming', 'languages', 'tools', 'soft_skills') NOT NULL,
    icon_class VARCHAR(100),
    color_hex VARCHAR(7) DEFAULT '#00ffff',
    description TEXT,
    is_primary BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0
);

-- Table messages de contact
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(100) NOT NULL,
    sender_email VARCHAR(150) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    sender_ip VARCHAR(45),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table expériences (études, travail)
CREATE TABLE experiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('education', 'work', 'certification') NOT NULL,
    title VARCHAR(150) NOT NULL,
    institution VARCHAR(150) NOT NULL,
    location VARCHAR(100),
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NULL, -- NULL si en cours
    is_current BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0
);

-- Données initiales Nathan
INSERT INTO users (username, email, password_hash, role) VALUES 
('nathan', 'nathan.diegelmann@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Mot de passe par défaut: nathan2024

-- Compétences de Nathan
INSERT INTO skills (name, level_percentage, category, icon_class, color_hex, description, is_primary, display_order) VALUES
('HTML', 85, 'programming', 'fab fa-html5', '#e34f26', 'Structure et sémantique web - maîtrise avancée', TRUE, 1),
('CSS', 80, 'programming', 'fab fa-css3-alt', '#1572b6', 'Stylisation moderne, responsive design, animations', TRUE, 2),
('PHP', 75, 'programming', 'fab fa-php', '#777bb4', 'Développement backend et applications web dynamiques', TRUE, 3),
('SQL', 70, 'programming', 'fas fa-database', '#336791', 'MySQL - conception BDD et requêtes avancées', TRUE, 4),
('Recherche de projets', 90, 'tools', 'fas fa-search', '#00ffff', 'Veille technologique et identification d\'opportunités', FALSE, 5),
('Git & GitHub', 65, 'tools', 'fab fa-git-alt', '#f05032', 'Contrôle de version basique et collaboration', FALSE, 6),
('Logiciels informatiques', 80, 'tools', 'fas fa-laptop-code', '#ff6b35', 'Maîtrise suite Office et outils développement', FALSE, 7),
('Service client', 85, 'soft_skills', 'fas fa-user-friends', '#28a745', 'Expérience chez Marché Bruno - relation clientèle', FALSE, 8),
('Espagnol', 60, 'languages', 'fas fa-flag', '#c41e3a', 'Niveau B1 - communication courante', FALSE, 9),
('Anglais', 75, 'languages', 'fas fa-flag-usa', '#0052cc', 'Usage quotidien - technique et conversationnel', FALSE, 10);

-- Expériences de Nathan
INSERT INTO experiences (type, title, institution, location, description, start_date, end_date, is_current, display_order) VALUES
('education', 'Bachelor Développement Web', 'École Supérieure', 'France', 'Formation supérieure spécialisée en développement web full-stack', '2024-09-01', NULL, TRUE, 1),
('work', 'Employé polyvalent', 'Marché Bruno', 'Boussy-Saint-Antoine', 'Mise en place des étalages, service clientèle, nettoyage et organisation', '2023-01-01', NULL, TRUE, 2),
('education', 'Baccalauréat obtenu', 'Lycée Christophe Colomb', 'France', 'Baccalauréat général obtenu avec succès - spécialisation scientifique', '2021-09-01', '2024-07-01', FALSE, 3),
('education', 'Brevet des Collèges', 'Collège La Guinette', 'Villecresnes', 'Formation secondaire obtenue avec succès', '2018-09-01', '2021-07-01', FALSE, 4);

-- Projets exemples pour Nathan
INSERT INTO projects (title, description, short_description, languages, technologies, category, github_link, is_featured, status) VALUES
('Portfolio Futuriste', 'Site web personnel complet avec design programmer moderne, système de gestion de contenu dynamique et interface d\'administration. Responsive design avec animations CSS et effets visuels avancés.', 'Portfolio personnel avec CMS intégré', 'HTML, CSS, PHP', 'MySQL, CSS Grid/Flexbox, Animations CSS', 'web', '#', TRUE, 'completed'),

('Tracker PowerLifting Web', 'Application web pour suivre ses performances en powerlifting. Interface responsive permettant d\'enregistrer les séances, calculer les PR et visualiser les progrès avec des graphiques CSS purs.', 'Suivi performance powerlifting en ligne', 'HTML, CSS, PHP', 'MySQL, PHP Sessions, CSS Charts', 'web', '#', TRUE, 'development'),

('Système Gestion Marché', 'Interface web de gestion pour petit commerce avec gestion des stocks, suivi des ventes et génération de rapports. Développé pour optimiser les processus chez Marché Bruno.', 'Gestion de stock et ventes web', 'HTML, CSS, PHP', 'MySQL, PHP Forms, CSS Dashboard', 'web', '#', FALSE, 'concept'),

('Site Vitrine Responsive', 'Site vitrine moderne et entièrement responsive pour une entreprise locale. Design mobile-first avec animations CSS, formulaire de contact fonctionnel et galerie photos interactive.', 'Site vitrine responsive moderne', 'HTML, CSS, PHP', 'CSS Grid, PHP Mailer, MySQL Forms', 'web', '#', TRUE, 'development'),

('Blog Personnel Dynamique', 'Blog personnel avec système de publication d\'articles, commentaires et catégories. Interface d\'administration complète pour la gestion du contenu et modération des commentaires.', 'CMS blog avec admin complet', 'HTML, CSS, PHP', 'MySQL, PHP Sessions, WYSIWYG Editor', 'web', '#', FALSE, 'concept');