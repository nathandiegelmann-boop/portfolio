-- ================================================
-- Script pour créer un nouvel utilisateur administrateur
-- Portfolio Nathan Diegelmann
-- ================================================

-- INSTRUCTIONS:
-- 1. Remplacez 'nouveau_admin' par le nom d'utilisateur souhaité
-- 2. Remplacez 'Nom Complet' par le nom complet de l'administrateur
-- 3. Remplacez 'email@example.com' par l'email de l'administrateur
-- 4. Générez un mot de passe hashé en PHP avec:
--    echo password_hash('votre_mot_de_passe', PASSWORD_DEFAULT);
-- 5. Remplacez le hash dans la requête ci-dessous

-- Exemple pour créer un nouvel admin avec le mot de passe 'MonMotDePasse123!'
INSERT INTO `admin_users` (`username`, `password`, `full_name`, `email`, `is_active`) 
VALUES (
  'nouveau_admin',                                                           -- Nom d'utilisateur
  '$2y$10$YFHdQnZxZ8qLN6vKJ5j9F.rMXx7nZ8qKNqHYvW7LJWoLKN6xYqGBm',  -- Hash du mot de passe
  'Nom Complet',                                                             -- Nom complet
  'email@example.com',                                                       -- Email
  1                                                                          -- Actif (1 = oui, 0 = non)
);

-- ================================================
-- Pour désactiver un compte admin:
-- ================================================
-- UPDATE `admin_users` SET `is_active` = 0 WHERE `username` = 'nom_utilisateur';

-- ================================================
-- Pour réactiver un compte admin:
-- ================================================
-- UPDATE `admin_users` SET `is_active` = 1 WHERE `username` = 'nom_utilisateur';

-- ================================================
-- Pour changer le mot de passe d'un admin:
-- ================================================
-- UPDATE `admin_users` SET `password` = '$2y$10$NouveauHash...' WHERE `username` = 'nom_utilisateur';
