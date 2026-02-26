-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 25 fév. 2026 à 11:25
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_evenements_aeroport`
--

-- --------------------------------------------------------

--
-- Structure de la table `aeroports`
--

CREATE TABLE `aeroports` (
  `code` varchar(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `ville` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `aeroports`
--

INSERT INTO `aeroports` (`code`, `nom`, `ville`) VALUES
('DKR', 'Aéroport Léopold Sédar Senghor', 'Dakar'),
('DSS', 'Aéroport International Blaise Diagne', 'Diass'),
('KGG', 'Aéroport de Kédougou', 'Kédougou'),
('MAX', 'Aéroport de Matam', 'Matam'),
('TUD', 'Aéroport de Tambacounda', 'Tambacounda'),
('XLS', 'Aéroport de Saint-Louis', 'Saint-Louis');

-- --------------------------------------------------------

--
-- Structure de la table `categories_evenements`
--

CREATE TABLE `categories_evenements` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories_evenements`
--

INSERT INTO `categories_evenements` (`id`, `nom`, `description`) VALUES
(1, 'Découverte EEI à bord d\'un véhicule', 'Engin explosif improvisé trouvé dans un véhicule'),
(2, 'Découverte EEI porté par une personne', 'Engin explosif improvisé porté par un individu'),
(3, 'Découverte EEI dissimulé dans un local ou un endroit', 'Engin explosif improvisé caché dans un lieu'),
(4, 'Attaque armée', 'Attaque avec des armes'),
(5, 'Articles laissés sans surveillance', 'Objets abandonnés sans surveillance'),
(6, 'Cyber Attaque', 'Attaque informatique'),
(7, 'Dommages aux infrastructures critiques/points vulnérables', 'Dégâts aux infrastructures importantes'),
(8, 'Comportement suspect', 'Comportement anormal ou suspect'),
(9, 'Perturbations imprévues', 'Interruptions non planifiées'),
(10, 'Mélange de passagers contrôlés et non contrôlés', 'Confusion dans le contrôle des passagers'),
(11, 'Passager armé muni d\'une autorisation de port', 'Passager autorisé à porter une arme'),
(12, 'Lacune dans le traitement des catégories particulières de passagers', 'Défaillance dans le traitement spécial'),
(13, 'Lacune dans le processus d\'inspection-filtrage au point de contrôle', 'Défaillance au contrôle de sécurité'),
(14, 'Utilisation d\'objets interdits/EEI', 'Usage d\'objets prohibés'),
(15, 'Sabotage/Menace interne', 'Sabotage ou menace de l\'intérieur'),
(16, 'Personnel contournant les contrôles de sûreté', 'Personnel évitant les contrôles'),
(17, 'Faux ou usage de faux dans le régime de contrôle/vérification des antécédents', 'Falsification de documents'),
(18, 'Personnel mal formé', 'Formation insuffisante du personnel'),
(19, 'Lacune dans les mesures mises en place par les sous-traitants', 'Défaillance des sous-traitants'),
(20, 'Dégradation ou tentative de dégradation de la clôture de sûreté', 'Dommage à la clôture de sécurité'),
(21, 'Accès non autorisé à la ZSAR ou à une autre zone de sûreté', 'Intrusion en zone sécurisée'),
(22, 'Accès non autorisé/sans escorte dans une ZSAR', 'Accès non accompagné en zone sécurisée'),
(23, 'Autorisation non conforme dans le système de contrôle d\'accès', 'Problème d\'autorisation d\'accès'),
(24, 'Autorisation non conforme dans le système de délivrance des laissez-passer', 'Problème de laissez-passer'),
(25, 'Autorisation non conforme suite à un contrôle d\'accès du véhicule et/ou des occupants', 'Problème de contrôle véhicule'),
(26, 'Défaut de protection des bagages de soute contrôlés/preuves de falsification', 'Problème bagages soute'),
(27, 'Lacune dans le système d\'inspection-filtrage des bagages de soute', 'Défaillance contrôle bagages'),
(28, 'Lacune dans la protection des approvisionnements sécurisés/preuves de falsification', 'Problème approvisionnements'),
(29, 'Lacune dans l\'application d\'autres contrôles de sûreté', 'Autres défaillances de contrôle'),
(30, 'Passager indiscipliné', 'Passager perturbateur'),
(31, 'Lacune dans le processus/la protection de la porte du poste de pilotage', 'Problème cockpit'),
(32, 'Découverte d\'un objet interdit/EEI', 'Objet prohibé trouvé'),
(33, 'Détournement en vol', 'Hijacking'),
(34, 'Alerte à la bombe en vol', 'Menace explosive en vol'),
(35, 'Vol d\'objets de valeur, bagages, fret/courrier', 'Vol de biens'),
(36, 'Destruction ou endommagement des aides à la navigation aérienne', 'Dommage équipements navigation'),
(37, 'Accès non autorisé au système d\'information', 'Intrusion informatique'),
(38, 'Attaque contre le(s) système(s) d\'aéronef', 'Cyberattaque avion'),
(39, 'Attaque contre le(s) système(s) ATM', 'Cyberattaque contrôle aérien'),
(40, 'Attaque contre le(s) système(s) aéroportuaire(s)', 'Cyberattaque aéroport'),
(41, 'Attaque contre d\'autres systèmes et données critiques', 'Autres cyberattaques'),
(42, 'Incursion non autorisée dans l\'espace aérien contrôlé', 'Intrusion espace aérien'),
(43, 'Quasi-accident/Rencontre', 'Incident évité de justesse'),
(44, 'Frappe/collision (drones, etc.)', 'Collision avec drone'),
(45, 'Menace contre un avion', 'Menace aéronef'),
(46, 'Menace contre les infrastructures aéroportuaires', 'Menace infrastructure'),
(47, 'Menace contre les passagers', 'Menace voyageurs'),
(48, 'Attaque contre un aéronef ou une installation aéroportuaire', 'Attaque physique'),
(49, 'Visées de lasers', 'Pointage laser'),
(50, 'Autres activités suspectes', 'Activités anormales diverses'),
(51, 'Lacune dans la protection des informations sensibles concernant la sûreté de l\'aviation', 'Fuite d\'informations');

-- --------------------------------------------------------

--
-- Structure de la table `domaines_surete`
--

CREATE TABLE `domaines_surete` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `domaines_surete`
--

INSERT INTO `domaines_surete` (`id`, `nom`) VALUES
(1, 'sûreté côté ville'),
(2, 'passagers et bagages de cabine'),
(3, 'personnel'),
(4, 'équipage'),
(5, 'contrôle d\'accès'),
(6, 'bagages de soute'),
(7, 'provisions de bord'),
(8, 'fournitures d\'aéroport'),
(9, 'sûreté en vol'),
(10, 'protection des aéroports'),
(11, 'protection des aéronefs au sol'),
(12, 'fret et courrier'),
(13, 'services de contrôle aérien'),
(14, 'technologie et systèmes d\'information'),
(15, 'drones'),
(16, 'manpads ou autres armes à distance'),
(17, 'laser'),
(18, 'informations sensibles'),
(19, 'aviation générale/AEROCLUB'),
(20, 'autres');

-- --------------------------------------------------------

--
-- Structure de la table `evenements`
--

CREATE TABLE `evenements` (
  `id` int(11) NOT NULL,
  `nom_aeroport` varchar(10) NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `structure` varchar(100) NOT NULL,
  `titre_evenement` varchar(255) NOT NULL,
  `date_evenement` date NOT NULL,
  `heure_evenement` time NOT NULL,
  `type_aeronef` varchar(100) DEFAULT NULL,
  `immatriculation` varchar(50) DEFAULT NULL,
  `phase_vol` varchar(100) DEFAULT NULL,
  `type_exploitation` varchar(100) DEFAULT NULL,
  `classe_evenement` enum('EVENEMENT MINEUR','incident','acte d intervention illicite') NOT NULL,
  `domaine_surete` text DEFAULT NULL,
  `categories_evenement` text DEFAULT NULL,
  `description_evenement` text NOT NULL,
  `analyse_gestion_risque` text DEFAULT NULL,
  `mesures_prises` text NOT NULL,
  `probabilite_risque` enum('1','2','3','4','5') NOT NULL,
  `gravite_risque` enum('E','D','C','B','A') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenements`
--

INSERT INTO `evenements` (`id`, `nom_aeroport`, `lieu`, `structure`, `titre_evenement`, `date_evenement`, `heure_evenement`, `type_aeronef`, `immatriculation`, `phase_vol`, `type_exploitation`, `classe_evenement`, `domaine_surete`, `categories_evenement`, `description_evenement`, `analyse_gestion_risque`, `mesures_prises`, `probabilite_risque`, `gravite_risque`, `created_at`, `updated_at`) VALUES
(1, 'KGG', 'Dakar', 'AIR SENEGAL INTERNATIONAL', 'accident', '2025-11-21', '12:00:00', '', '', '', '', 'EVENEMENT MINEUR', 'surete cote ville,passagers et bagages de cabine', NULL, 'TEST DESC', '', 'MESURES PRISES', '2', 'C', '2025-11-21 23:40:28', '2025-11-21 23:53:27'),
(2, 'DSS', 'Dakar', 'POLICE', 'accident', '2025-11-22', '12:00:00', '', '', '', '', 'incident', 'surete cote ville,passagers et bagages de cabine,protection des aeronefs au sol', 'Découverte EEI à bord d\'un véhicule,Découverte EEI porté par une personne,Autorisation non conforme suite à un contrôle d\'accès du véhicule et/ou des occupants', 'DESC', '', 'RISQUES', '3', 'D', '2025-11-22 00:31:43', '2025-11-22 00:31:43'),
(3, 'MAX', 'Dakar', 'AIR SENEGAL INTERNATIONAL', 'accident', '2025-11-22', '12:09:00', '', '', '', '', 'acte d intervention illicite', 'controle d acces,bagages de soute,services de controle aerien', 'Autorisation non conforme suite à un contrôle d\'accès du véhicule et/ou des occupants,Défaut de protection des bagages de soute contrôlés/preuves de falsification', 'DESC', '', 'DESC', '3', 'B', '2025-11-22 00:37:34', '2025-11-22 00:37:34'),
(4, 'TUD', 'Tambacounda', 'ASECNA', 'accident', '2025-11-23', '12:00:00', '', '', '', '', 'incident', 'surete cote ville,passagers et bagages de cabine', 'Découverte EEI à bord d\'un véhicule,Découverte EEI porté par une personne', 'DESC', '', 'DESC', '3', 'C', '2025-11-23 13:47:48', '2025-11-23 13:47:48'),
(5, 'KGG', 'PIF Central', 'TSA', 'evenement de surete survenu a  aeroport de kedouguou', '2025-11-23', '14:01:00', '', '', '', '', 'EVENEMENT MINEUR', 'passagers et bagages de cabine,controle d acces,laser', NULL, 'l operateur TSA du nom de Coutaille BA installe sur l appareil RX a decouvert un EEI sur le sac d un passager nomme  Adama Niang. Ce passager voulait se rendre  a dakar  . ', '', 'l operateur TSA a avise la police et son supérieur hiérarchique', '4', 'A', '2025-11-23 14:26:10', '2025-11-23 14:29:18');

-- --------------------------------------------------------

--
-- Structure de la table `structures`
--

CREATE TABLE `structures` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `structures`
--

INSERT INTO `structures` (`id`, `nom`, `description`) VALUES
(1, 'TSA', 'Transportation Security Administration'),
(2, 'LAS', 'Local Airport Security'),
(3, 'AMARANTE', 'Amarante Security'),
(4, '2AS', '2AS Security'),
(5, 'HAAS', 'HAAS Services'),
(6, 'AIBD_SA', 'AIBD SA'),
(7, 'SMCADDY', 'SMCADDY Services'),
(8, 'SERVAIR', 'Servair'),
(9, 'AIR SENEGAL INTERNATIONAL', 'Air Sénégal International'),
(10, 'ARC CIEL', 'Arc Ciel'),
(11, 'SAM AIRWAYS', 'Sam Airways'),
(12, 'HELICONIA', 'Heliconia'),
(13, 'ASECNA', 'ASECNA'),
(14, 'TRANSAIR', 'Transair'),
(15, 'POLICE', 'Police'),
(16, 'DOUANE', 'Douane'),
(17, 'GENDARMERIE', 'Gendarmerie');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `role` enum('admin','operateur') DEFAULT 'operateur',
  `actif` tinyint(1) DEFAULT 1,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `derniere_connexion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `email`, `mot_de_passe`, `nom`, `prenom`, `role`, `actif`, `date_creation`, `derniere_connexion`) VALUES
(1, 'coutay.ba@anacim.sn', '$2y$10$yYYavqk1g3ugqVStWqsn2euBV.HwwViqmlq.hk6hMfikxUWthiAcy', 'BA', 'Coutay', 'admin', 1, '2025-11-22 01:19:05', '2025-11-23 19:00:59');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `aeroports`
--
ALTER TABLE `aeroports`
  ADD PRIMARY KEY (`code`);

--
-- Index pour la table `categories_evenements`
--
ALTER TABLE `categories_evenements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `domaines_surete`
--
ALTER TABLE `domaines_surete`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `evenements`
--
ALTER TABLE `evenements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `structures`
--
ALTER TABLE `structures`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories_evenements`
--
ALTER TABLE `categories_evenements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `domaines_surete`
--
ALTER TABLE `domaines_surete`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `evenements`
--
ALTER TABLE `evenements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `structures`
--
ALTER TABLE `structures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
