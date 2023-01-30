-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 30 jan. 2023 à 14:27
-- Version du serveur : 8.0.30
-- Version de PHP : 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `chaos_citadel`
--

-- --------------------------------------------------------

--
-- Structure de la table `formula`
--

CREATE TABLE `formula` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `formula_luck` int NOT NULL DEFAULT '0',
  `formula_copy` int NOT NULL DEFAULT '0',
  `formula_life` int NOT NULL DEFAULT '0',
  `formula_weakness` int NOT NULL DEFAULT '0',
  `formula_fire` int NOT NULL DEFAULT '0',
  `formula_force` int NOT NULL DEFAULT '0',
  `formula_ability` int NOT NULL DEFAULT '0',
  `formula_illusion` int NOT NULL DEFAULT '0',
  `formula_levitation` int NOT NULL DEFAULT '0',
  `formula_gold` int NOT NULL DEFAULT '0',
  `formula_protection` int NOT NULL DEFAULT '0',
  `formula_telepathy` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inventory`
--

CREATE TABLE `inventory` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `myriad` int NOT NULL DEFAULT '0',
  `spider_jar` int NOT NULL DEFAULT '0',
  `berry` int NOT NULL DEFAULT '0',
  `enchanted_dagger` int NOT NULL DEFAULT '0',
  `fire_wine` int NOT NULL DEFAULT '0',
  `berce_bottle` int NOT NULL DEFAULT '0',
  `green_liquid_bottle` int NOT NULL DEFAULT '0',
  `golden_fleece` int NOT NULL DEFAULT '0',
  `silver_mirror` int NOT NULL DEFAULT '0',
  `comb` int NOT NULL DEFAULT '0',
  `gold` int NOT NULL DEFAULT '0',
  `enchanted_amulet` int NOT NULL DEFAULT '0',
  `copper_key` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `current_step` int NOT NULL DEFAULT '0',
  `ability_max` int NOT NULL DEFAULT '0',
  `ability_current` int NOT NULL DEFAULT '0',
  `life_max` int NOT NULL DEFAULT '0',
  `life_current` int NOT NULL DEFAULT '0',
  `chance_max` int NOT NULL DEFAULT '0',
  `chance_current` int NOT NULL DEFAULT '0',
  `magic_max` int NOT NULL DEFAULT '0',
  `magic_current` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `formula`
--
ALTER TABLE `formula`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `formula`
--
ALTER TABLE `formula`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
