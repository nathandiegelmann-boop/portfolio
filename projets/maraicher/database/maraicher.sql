-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 22 nov. 2025 à 14:53
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `maraicher`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `actif`) VALUES
(1, 'Légumes de saison', 'Légumes frais de saison cultivés localement', 1),
(2, 'Fruits', 'Fruits de saison cueillis à maturité', 1),
(3, 'Paniers garnis', 'Assortiments de légumes et fruits', 1),
(4, 'Bio', 'Produits issus de l\'agriculture biologique', 1),
(5, 'Aromates', 'Herbes fraîches et aromates', 1);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nom_client` varchar(100) NOT NULL,
  `prenom_client` varchar(100) NOT NULL,
  `email_client` varchar(100) NOT NULL,
  `telephone_client` varchar(20) DEFAULT NULL,
  `date_commande` datetime DEFAULT current_timestamp(),
  `statut` enum('en_attente','validee','prete','livree','annulee') DEFAULT 'en_attente',
  `total` decimal(10,2) NOT NULL,
  `adresse_livraison` text NOT NULL,
  `date_livraison` datetime DEFAULT NULL,
  `methode_paiement` enum('en_ligne','sur_place') NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `user_id`, `nom_client`, `prenom_client`, `email_client`, `telephone_client`, `date_commande`, `statut`, `total`, `adresse_livraison`, `date_livraison`, `methode_paiement`, `notes`) VALUES
(1, NULL, 'Dupont', 'Marie', 'marie.dupont@email.com', '0634567890', '2025-11-20 01:36:52', 'validee', 23.50, '123 Rue de la République, 75001 Paris', NULL, 'sur_place', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commande_produits`
--

CREATE TABLE `commande_produits` (
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` decimal(8,2) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commande_produits`
--

INSERT INTO `commande_produits` (`commande_id`, `produit_id`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 2.00, 4.50),
(1, 3, 3.00, 2.20),
(1, 5, 1.50, 6.50);

-- --------------------------------------------------------

--
-- Structure de la table `paniers`
--

CREATE TABLE `paniers` (
  `id` int(11) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` decimal(8,2) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `prix`, `stock`, `image`, `categorie_id`, `actif`, `date_creation`) VALUES
(1, 'Tomates cerises', 'Tomates cerises rouges, sucrées et parfumées', 4.50, 25, 'tomates-cerises.jpg', 1, 1, '2025-11-20 01:36:52'),
(2, 'Courgettes', 'Courgettes vertes fraîches du jour', 2.80, 30, 'courgettes.jpg', 1, 1, '2025-11-20 01:36:52'),
(3, 'Carottes', 'Carottes de Nantes, croquantes et sucrées', 2.20, 40, 'carottes.jpg', 1, 1, '2025-11-20 01:36:52'),
(4, 'Pommes Golden', 'Pommes Golden Delicious, croquantes et juteuses', 3.20, 50, 'pommes-golden.jpg', 2, 1, '2025-11-20 01:36:52'),
(5, 'Fraises', 'Fraises de saison, parfumées et sucrées', 6.50, 15, 'fraises.jpg', 2, 1, '2025-11-20 01:36:52'),
(6, 'Panier Découverte', 'Assortiment de 5 légumes de saison (3kg)', 18.00, 10, 'panier-decouverte.jpg', 3, 1, '2025-11-20 01:36:52'),
(7, 'Basilic', 'Basilic frais en pot', 2.50, 20, 'basilic.jpg', 5, 1, '2025-11-20 01:36:52'),
(8, 'Salade verte Bio', 'Laitue bio cultivée sans pesticides', 1.80, 25, 'salade-bio.jpg', 4, 1, '2025-11-20 01:36:52'),
(58, 'Concombre', 'Concombre frais de saison, croquant et désaltérant', 1.20, 150, 'concombre.jpg', 5, 1, '2025-11-21 00:41:24'),
(59, 'Courgette', 'Courgette verte bio, idéale pour vos recettes estivales', 1.50, 120, 'courgette.jpg', 5, 1, '2025-11-21 00:41:24'),
(60, 'Tomate ronde', 'Tomate ronde rouge, juteuse et savoureuse, cultivée en plein air', 2.00, 200, 'tomate-ronde.jpg', 5, 1, '2025-11-21 00:41:24'),
(61, 'Carotte', 'Carotte orange bio, riche en bêta-carotène, vendue en botte', 1.10, 250, 'carotte.jpg', 5, 1, '2025-11-21 00:41:24'),
(62, 'Poivron rouge', 'Poivron rouge doux, charnu et sucré', 2.50, 100, 'poivron-rouge.jpg', 5, 1, '2025-11-21 00:41:24'),
(63, 'Poivron vert', 'Poivron vert légèrement amer, parfait pour les plats mijotés', 2.20, 90, 'poivron-vert.jpg', 5, 1, '2025-11-21 00:41:24'),
(64, 'Poivron jaune', 'Poivron jaune, saveur douce et fruitée', 2.30, 85, 'poivron-jaune.jpg', 5, 1, '2025-11-21 00:41:24'),
(65, 'Brocoli', 'Brocoli frais bio, riche en vitamines et minéraux', 1.90, 110, 'brocoli.jpg', 5, 1, '2025-11-21 00:41:24'),
(66, 'Chou-fleur', 'Chou-fleur blanc, polyvalent en cuisine', 2.00, 95, 'chou-fleur.jpg', 5, 1, '2025-11-21 00:41:24'),
(67, 'Épinard', 'Épinard frais en botte, riche en fer', 1.70, 130, 'epinard.jpg', 5, 1, '2025-11-21 00:41:24'),
(68, 'Laitue batavia', 'Laitue batavia croquante, parfaite pour les salades', 1.30, 180, 'laitue-batavia.jpg', 5, 1, '2025-11-21 00:41:24'),
(69, 'Laitue romaine', 'Laitue romaine à feuilles allongées, riche en croquant', 1.40, 170, 'laitue-romaine.jpg', 5, 1, '2025-11-21 00:41:24'),
(70, 'Roquette', 'Roquette sauvage, légèrement poivrée', 2.00, 100, 'roquette.jpg', 5, 1, '2025-11-21 00:41:24'),
(71, 'Mâche', 'Mâche douce, riche en oméga-3', 2.50, 80, 'mache.jpg', 5, 1, '2025-11-21 00:41:24'),
(72, 'Bette', 'Bette (blette) à cardes, feuilles tendres', 1.80, 70, 'bette.jpg', 5, 1, '2025-11-21 00:41:24'),
(73, 'Aubergine', 'Aubergine violette, idéale pour les plats méditerranéens', 2.10, 70, 'aubergine.jpg', 5, 1, '2025-11-21 00:41:24'),
(74, 'Oignon jaune', 'Oignon jaune, base indispensable en cuisine', 0.80, 300, 'oignon-jaune.jpg', 5, 1, '2025-11-21 00:41:24'),
(75, 'Oignon rouge', 'Oignon rouge, légèrement sucré, parfait pour les salades', 1.20, 150, 'oignon-rouge.jpg', 5, 1, '2025-11-21 00:41:24'),
(76, 'Ail rose', 'Ail rose de Lautrec, très aromatique', 1.50, 200, 'ail-rose.jpg', 5, 1, '2025-11-21 00:41:24'),
(77, 'Petits pois', 'Petits pois frais en cosse, sucrés et tendres', 2.30, 80, 'petits-pois.jpg', 5, 1, '2025-11-21 00:41:24'),
(78, 'Haricot vert', 'Haricot vert extra-fin, croquant', 3.00, 120, 'haricot-vert.jpg', 5, 1, '2025-11-21 00:41:24'),
(79, 'Pomme de terre', 'Pomme de terre à chair ferme, idéale pour les salades', 1.20, 250, 'pomme-de-terre.jpg', 5, 1, '2025-11-21 00:41:24'),
(80, 'Patate douce', 'Patate douce orange, sucrée et nutritive', 2.00, 90, 'patate-douce.jpg', 5, 1, '2025-11-21 00:41:24'),
(81, 'Céleri branche', 'Céleri branche, croquant et rafraîchissant', 1.50, 60, 'celeri-branche.jpg', 5, 1, '2025-11-21 00:41:24'),
(82, 'Radis rose', 'Radis rose à bout blanc, croquant et légèrement piquant', 1.20, 150, 'radis-rose.jpg', 5, 1, '2025-11-21 00:41:24'),
(83, 'Betterave crue', 'Betterave crue à cuire, sucrée et terreuse', 1.60, 75, 'betterave-crue.jpg', 5, 1, '2025-11-21 00:41:24'),
(84, 'Betterave cuite', 'Betterave cuite sous vide, prête à déguster', 2.00, 60, 'betterave-cuite.jpg', 5, 1, '2025-11-21 00:41:24'),
(85, 'Asperge verte', 'Asperge verte primeur, tendre et savoureuse', 3.50, 50, 'asperge-verte.jpg', 5, 1, '2025-11-21 00:41:24'),
(86, 'Champignon de Paris', 'Champignons de Paris frais, parfaits pour les plats cuisinés', 2.50, 100, 'champignon-paris.jpg', 5, 1, '2025-11-21 00:41:24'),
(87, 'Poireau', 'Poireau, idéal pour les soupes et les plats mijotés', 1.70, 85, 'poireau.jpg', 5, 1, '2025-11-21 00:41:24'),
(88, 'Navet', 'Navet blanc et violet, légèrement sucré', 1.40, 70, 'navet.jpg', 5, 1, '2025-11-21 00:41:24'),
(89, 'Artichaut', 'Artichaut frais, délicieux avec une vinaigrette', 2.80, 40, 'artichaut.jpg', 5, 1, '2025-11-21 00:41:24'),
(90, 'Fenouil', 'Fenouil bulbeux, anisé et croquant', 2.20, 65, 'fenouil.jpg', 5, 1, '2025-11-21 00:41:24'),
(91, 'Courge spaghetti', 'Courge spaghetti, originale et ludique', 3.00, 30, 'courge-spaghetti.jpg', 5, 1, '2025-11-21 00:41:24'),
(92, 'Panais', 'Panais, légume ancien au goût sucré', 2.00, 55, 'panais.jpg', 5, 1, '2025-11-21 00:41:24'),
(93, 'Basilic frais', 'Bouquet de basilic frais, parfum incomparable', 1.50, 80, 'basilic.jpg', 5, 1, '2025-11-21 00:41:24'),
(94, 'Persil plat', 'Persil plat frais, pour toutes vos recettes', 1.20, 90, 'persil-plat.jpg', 5, 1, '2025-11-21 00:41:24'),
(95, 'Ciboulette', 'Ciboulette fraîche, pour une touche d\'oignon doux', 1.30, 85, 'ciboulette.jpg', 5, 1, '2025-11-21 00:41:24'),
(96, 'Menthe fraîche', 'Menthe fraîche, rafraîchissante et parfumée', 1.40, 70, 'menthe.jpg', 5, 1, '2025-11-21 00:41:24'),
(97, 'Coriandre fraîche', 'Coriandre fraîche, aromatique et parfumée', 1.50, 65, 'coriandre.jpg', 5, 1, '2025-11-21 00:41:24'),
(98, 'Thym frais', 'Branche de thym frais, pour parfumer vos plats', 1.60, 75, 'thym.jpg', 5, 1, '2025-11-21 00:41:24'),
(99, 'Romarin frais', 'Branche de romarin frais, pour vos plats méditerranéens', 1.70, 60, 'romarin.jpg', 5, 1, '2025-11-21 00:41:24'),
(100, 'Estragon frais', 'Estragon frais, pour vos sauces et plats', 1.80, 55, 'estragon.jpg', 5, 1, '2025-11-21 00:41:24'),
(101, 'Aneth frais', 'Aneth frais, pour vos plats scandinaves', 1.60, 60, 'aneth.jpg', 5, 1, '2025-11-21 00:41:24'),
(102, 'Oseille', 'Oseille, légume-feuille au goût acidulé', 1.90, 50, 'oseille.jpg', 5, 1, '2025-11-21 00:41:24'),
(103, 'Échalote', 'Échalote, pour une saveur subtile dans vos plats', 1.70, 100, 'echalote.jpg', 5, 1, '2025-11-21 00:41:24'),
(104, 'Cresson', 'Cresson frais, légèrement poivré', 1.80, 70, 'cresson.jpg', 5, 1, '2025-11-21 00:41:24');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `role` enum('client','admin') DEFAULT 'client',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `password`, `telephone`, `adresse`, `role`, `date_creation`) VALUES
(1, 'Admin', 'Site', 'admin@maraicher.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', 'Ferme du Maraîcher', 'admin', '2025-11-20 01:36:52');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `commande_produits`
--
ALTER TABLE `commande_produits`
  ADD PRIMARY KEY (`commande_id`,`produit_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `paniers`
--
ALTER TABLE `paniers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `paniers`
--
ALTER TABLE `paniers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `commande_produits`
--
ALTER TABLE `commande_produits`
  ADD CONSTRAINT `commande_produits_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_produits_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `paniers`
--
ALTER TABLE `paniers`
  ADD CONSTRAINT `paniers_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
