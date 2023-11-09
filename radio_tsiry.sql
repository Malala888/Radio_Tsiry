-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 09 nov. 2023 à 07:38
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `radio_tsiry`
--

-- --------------------------------------------------------

--
-- Structure de la table `achat_produits`
--

CREATE TABLE `achat_produits` (
  `numAchat` varchar(255) NOT NULL,
  `nom` varchar(225) NOT NULL,
  `nbr` int(11) NOT NULL,
  `date_achat` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `achat_produits`
--

INSERT INTO `achat_produits` (`numAchat`, `nom`, `nbr`, `date_achat`) VALUES
('A001', 'Spiriline', 9, '2023-09-22'),
('A002', 'Menaka mahagaga', 5, '2023-10-31'),
('A003', 'Spiriline', 2, '2023-11-06');

--
-- Déclencheurs `achat_produits`
--
DELIMITER $$
CREATE TRIGGER `UpdateProductStockAfterInsert` AFTER INSERT ON `achat_produits` FOR EACH ROW BEGIN
    -- Mettre à jour le stock dans la table "produits" après chaque achat
    UPDATE produits
    SET stock = stock - NEW.nbr
    WHERE nom = NEW.nom;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `UpdateProductStockAfterUpdate` AFTER UPDATE ON `achat_produits` FOR EACH ROW BEGIN
    -- Mettre à jour le stock dans la table "produits" après chaque mise à jour de la colonne "nbr"
    DECLARE diff INT;
    SET diff = NEW.nbr - OLD.nbr;

    UPDATE produits
    SET stock = stock - diff
    WHERE nom = NEW.nom;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `archives`
--

CREATE TABLE `archives` (
  `nom` varchar(225) NOT NULL,
  `type` varchar(225) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `situation` varchar(225) NOT NULL,
  `type_payement` varchar(225) NOT NULL,
  `montant` int(11) NOT NULL,
  `matin` varchar(225) NOT NULL,
  `midi` varchar(225) NOT NULL,
  `soir` varchar(225) NOT NULL,
  `nbr_diffusion` int(11) NOT NULL,
  `audio` longblob NOT NULL,
  `DatePaye` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `archives`
--

INSERT INTO `archives` (`nom`, `type`, `date_debut`, `date_fin`, `situation`, `type_payement`, `montant`, `matin`, `midi`, `soir`, `nbr_diffusion`, `audio`, `DatePaye`) VALUES
('CAMPS', 'pub', '2023-10-26', '2023-10-28', 'Terminé', 'Chèque', 5000, 'non', 'oui', 'non', 6, 0x617564696f2d36353337373734313066336433362e35353931343330302e6d7033, '2023-10-25'),
('Cours Manambina BACC 2023', 'Annonce', '2023-11-06', '2023-11-11', 'Terminé', 'En espèce', 120000, 'oui', 'oui', 'oui', 12, 0x617564696f2d36353435653863333632326532362e36323233333439302e6d7033, '2023-11-04'),
('FANOVO', 'PUB', '2023-10-25', '2023-11-02', 'Terminé', 'en espèce', 10000, 'non', 'oui', 'non', 10, 0x617564696f2d36353337383537323462313663362e35353934323331302e6d7033, '2023-10-24');

-- --------------------------------------------------------

--
-- Structure de la table `entre_produits`
--

CREATE TABLE `entre_produits` (
  `numEntree` varchar(99) NOT NULL,
  `nom` varchar(99) NOT NULL,
  `stock_entree` int(11) NOT NULL,
  `date_entree` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `entre_produits`
--

INSERT INTO `entre_produits` (`numEntree`, `nom`, `stock_entree`, `date_entree`) VALUES
('2', 'Menaka mahagaga', 5, '2023-10-31'),
('3', 'Spiriline', 2, '2023-11-06');

--
-- Déclencheurs `entre_produits`
--
DELIMITER $$
CREATE TRIGGER `update_stock_after_insert` AFTER INSERT ON `entre_produits` FOR EACH ROW BEGIN
    DECLARE product_stock INT;

    -- Sélectionnez le stock actuel du produit
    SELECT stock INTO product_stock
    FROM produits
    WHERE nom = NEW.nom;

    -- Mettez à jour le stock du produit
    UPDATE produits
    SET stock = product_stock + NEW.stock_entree
    WHERE nom = NEW.nom;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `medias`
--

CREATE TABLE `medias` (
  `nom` varchar(225) NOT NULL,
  `type` varchar(225) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `situation` varchar(225) NOT NULL,
  `type_payement` varchar(225) NOT NULL,
  `montant` int(11) NOT NULL,
  `matin` varchar(225) NOT NULL,
  `midi` varchar(225) NOT NULL,
  `soir` varchar(225) NOT NULL,
  `nbr_diffusion` int(11) NOT NULL,
  `audio` longblob NOT NULL,
  `DatePaye` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `medias`
--

INSERT INTO `medias` (`nom`, `type`, `date_debut`, `date_fin`, `situation`, `type_payement`, `montant`, `matin`, `midi`, `soir`, `nbr_diffusion`, `audio`, `DatePaye`) VALUES
('MAGASIN', 'PUB', '2023-10-26', '2023-10-28', 'En cours', 'En espèce', 20000, 'oui', 'non', 'oui', 5, 0x617564696f2d36353462346666386162373437372e38363939383532305f456c746f6e204a6f686e202d205361637269666963652e6d7033, '2023-10-25');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `nom` varchar(225) NOT NULL,
  `producteurs` varchar(225) NOT NULL,
  `prix` int(11) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`nom`, `producteurs`, `prix`, `stock`) VALUES
('Menaka mahagaga', 'Masera', 1500, 10),
('Spiriline', 'Masera', 1000, 22);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `achat_produits`
--
ALTER TABLE `achat_produits`
  ADD PRIMARY KEY (`numAchat`),
  ADD KEY `fk_nom` (`nom`);

--
-- Index pour la table `archives`
--
ALTER TABLE `archives`
  ADD PRIMARY KEY (`nom`);

--
-- Index pour la table `entre_produits`
--
ALTER TABLE `entre_produits`
  ADD PRIMARY KEY (`numEntree`),
  ADD KEY `faux` (`nom`);

--
-- Index pour la table `medias`
--
ALTER TABLE `medias`
  ADD PRIMARY KEY (`nom`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`nom`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `achat_produits`
--
ALTER TABLE `achat_produits`
  ADD CONSTRAINT `fk_nom` FOREIGN KEY (`nom`) REFERENCES `produits` (`nom`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `entre_produits`
--
ALTER TABLE `entre_produits`
  ADD CONSTRAINT `faux` FOREIGN KEY (`nom`) REFERENCES `produits` (`nom`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
