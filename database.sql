-- Base de données pour la gestion des événements aéroportuaires
CREATE DATABASE IF NOT EXISTS gestion_evenements_aeroport;
USE gestion_evenements_aeroport;

-- Table des événements
CREATE TABLE evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_aeroport VARCHAR(10) NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    structure VARCHAR(100) NOT NULL,
    titre_evenement VARCHAR(255) NOT NULL,
    date_evenement DATE NOT NULL,
    heure_evenement TIME NOT NULL,
    type_aeronef VARCHAR(100),
    immatriculation VARCHAR(50),
    phase_vol VARCHAR(100),
    type_exploitation VARCHAR(100),
    classe_evenement ENUM('EVENEMENT MINEUR', 'incident', 'acte d intervention illicite') NOT NULL,
    domaine_surete TEXT,
    categories_evenement TEXT,
    description_evenement TEXT NOT NULL,
    analyse_gestion_risque TEXT,
    mesures_prises TEXT NOT NULL,
    probabilite_risque ENUM('1', '2', '3', '4', '5') NOT NULL,
    gravite_risque ENUM('E', 'D', 'C', 'B', 'A') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des aéroports
CREATE TABLE aeroports (
    code VARCHAR(10) PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    ville VARCHAR(100) NOT NULL
);

-- Insertion des aéroports
INSERT INTO aeroports (code, nom, ville) VALUES
('DSS', 'Aéroport International Blaise Diagne', 'Diass'),
('DKR', 'Aéroport Léopold Sédar Senghor', 'Dakar'),
('XLS', 'Aéroport de Saint-Louis', 'Saint-Louis'),
('MAX', 'Aéroport de Matam', 'Matam'),
('TUD', 'Aéroport de Tambacounda', 'Tambacounda'),
('KGG', 'Aéroport de Kédougou', 'Kédougou');

-- Table des structures
CREATE TABLE structures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description VARCHAR(255)
);

-- Insertion des structures
INSERT INTO structures (nom, description) VALUES
('TSA', 'Transportation Security Administration'),
('LAS', 'Local Airport Security'),
('AMARANTE', 'Amarante Security'),
('2AS', '2AS Security'),
('HAAS', 'HAAS Services'),
('AIBD_SA', 'AIBD SA'),
('SMCADDY', 'SMCADDY Services'),
('SERVAIR', 'Servair'),
('AIR SENEGAL INTERNATIONAL', 'Air Sénégal International'),
('ARC CIEL', 'Arc Ciel'),
('SAM AIRWAYS', 'Sam Airways'),
('HELICONIA', 'Heliconia'),
('ASECNA', 'ASECNA'),
('TRANSAIR', 'Transair'),
('POLICE', 'Police'),
('DOUANE', 'Douane'),
('GENDARMERIE', 'Gendarmerie');

-- Table des domaines de sûreté
CREATE TABLE domaines_surete (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

-- Insertion des domaines de sûreté
INSERT INTO domaines_surete (nom) VALUES
('sûreté côté ville'),
('passagers et bagages de cabine'),
('personnel'),
('équipage'),
('contrôle d\'accès'),
('bagages de soute'),
('provisions de bord'),
('fournitures d\'aéroport'),
('sûreté en vol'),
('protection des aéroports'),
('protection des aéronefs au sol'),
('fret et courrier'),
('services de contrôle aérien'),
('technologie et systèmes d\'information'),
('drones'),
('manpads ou autres armes à distance'),
('laser'),
('informations sensibles'),
('aviation générale/AEROCLUB'),
('autres');

-- Table des catégories d'événements
CREATE TABLE categories_evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT
);

-- Insertion des catégories d'événements
INSERT INTO categories_evenements (nom, description) VALUES
('Découverte EEI à bord d\'un véhicule', 'Engin explosif improvisé trouvé dans un véhicule'),
('Découverte EEI porté par une personne', 'Engin explosif improvisé porté par un individu'),
('Découverte EEI dissimulé dans un local ou un endroit', 'Engin explosif improvisé caché dans un lieu'),
('Attaque armée', 'Attaque avec des armes'),
('Articles laissés sans surveillance', 'Objets abandonnés sans surveillance'),
('Cyber Attaque', 'Attaque informatique'),
('Dommages aux infrastructures critiques/points vulnérables', 'Dégâts aux infrastructures importantes'),
('Comportement suspect', 'Comportement anormal ou suspect'),
('Perturbations imprévues', 'Interruptions non planifiées'),
('Mélange de passagers contrôlés et non contrôlés', 'Confusion dans le contrôle des passagers'),
('Passager armé muni d\'une autorisation de port', 'Passager autorisé à porter une arme'),
('Lacune dans le traitement des catégories particulières de passagers', 'Défaillance dans le traitement spécial'),
('Lacune dans le processus d\'inspection-filtrage au point de contrôle', 'Défaillance au contrôle de sécurité'),
('Utilisation d\'objets interdits/EEI', 'Usage d\'objets prohibés'),
('Sabotage/Menace interne', 'Sabotage ou menace de l\'intérieur'),
('Personnel contournant les contrôles de sûreté', 'Personnel évitant les contrôles'),
('Faux ou usage de faux dans le régime de contrôle/vérification des antécédents', 'Falsification de documents'),
('Personnel mal formé', 'Formation insuffisante du personnel'),
('Lacune dans les mesures mises en place par les sous-traitants', 'Défaillance des sous-traitants'),
('Dégradation ou tentative de dégradation de la clôture de sûreté', 'Dommage à la clôture de sécurité'),
('Accès non autorisé à la ZSAR ou à une autre zone de sûreté', 'Intrusion en zone sécurisée'),
('Accès non autorisé/sans escorte dans une ZSAR', 'Accès non accompagné en zone sécurisée'),
('Autorisation non conforme dans le système de contrôle d\'accès', 'Problème d\'autorisation d\'accès'),
('Autorisation non conforme dans le système de délivrance des laissez-passer', 'Problème de laissez-passer'),
('Autorisation non conforme suite à un contrôle d\'accès du véhicule et/ou des occupants', 'Problème de contrôle véhicule'),
('Défaut de protection des bagages de soute contrôlés/preuves de falsification', 'Problème bagages soute'),
('Lacune dans le système d\'inspection-filtrage des bagages de soute', 'Défaillance contrôle bagages'),
('Lacune dans la protection des approvisionnements sécurisés/preuves de falsification', 'Problème approvisionnements'),
('Lacune dans l\'application d\'autres contrôles de sûreté', 'Autres défaillances de contrôle'),
('Passager indiscipliné', 'Passager perturbateur'),
('Lacune dans le processus/la protection de la porte du poste de pilotage', 'Problème cockpit'),
('Découverte d\'un objet interdit/EEI', 'Objet prohibé trouvé'),
('Détournement en vol', 'Hijacking'),
('Alerte à la bombe en vol', 'Menace explosive en vol'),
('Vol d\'objets de valeur, bagages, fret/courrier', 'Vol de biens'),
('Destruction ou endommagement des aides à la navigation aérienne', 'Dommage équipements navigation'),
('Accès non autorisé au système d\'information', 'Intrusion informatique'),
('Attaque contre le(s) système(s) d\'aéronef', 'Cyberattaque avion'),
('Attaque contre le(s) système(s) ATM', 'Cyberattaque contrôle aérien'),
('Attaque contre le(s) système(s) aéroportuaire(s)', 'Cyberattaque aéroport'),
('Attaque contre d\'autres systèmes et données critiques', 'Autres cyberattaques'),
('Incursion non autorisée dans l\'espace aérien contrôlé', 'Intrusion espace aérien'),
('Quasi-accident/Rencontre', 'Incident évité de justesse'),
('Frappe/collision (drones, etc.)', 'Collision avec drone'),
('Menace contre un avion', 'Menace aéronef'),
('Menace contre les infrastructures aéroportuaires', 'Menace infrastructure'),
('Menace contre les passagers', 'Menace voyageurs'),
('Attaque contre un aéronef ou une installation aéroportuaire', 'Attaque physique'),
('Visées de lasers', 'Pointage laser'),
('Autres activités suspectes', 'Activités anormales diverses'),
('Lacune dans la protection des informations sensibles concernant la sûreté de l\'aviation', 'Fuite d\'informations');

-- Table des utilisateurs pour l'authentification
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    role ENUM('admin', 'operateur') DEFAULT 'operateur',
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL
);

-- Insertion de l'utilisateur par défaut
INSERT INTO utilisateurs (email, mot_de_passe, nom, prenom, role) VALUES
('coutay.ba@anacim.sn', '$2y$10$yYYavqk1g3ugqVStWqsn2euBV.HwwViqmlq.hk6hMfikxUWthiAcy', 'BA', 'Coutay', 'admin');
-- Note: Le mot de passe haché correspond à "Anacim2025@#"

-- Insertion des données de test pour les événements
INSERT INTO evenements (titre_evenement, description_evenement, date_evenement, heure_evenement, lieu, nom_aeroport, structure, phase_vol, type_exploitation, domaine_surete, categories_evenement, classe_evenement, probabilite_risque, gravite_risque, mesures_prises) VALUES
('Test Événement Mineur', 'Description de test pour un événement mineur', '2024-01-15', '10:30:00', 'Terminal 1', 'DSS', 'TSA', 'Embarquement', 'Commercial', 'Sûreté côté ville', 'Comportement suspect', 'EVENEMENT MINEUR', 2, 'D', 'Surveillance renforcée'),
('Test Incident', 'Description de test pour un incident', '2024-01-16', '14:15:00', 'Piste', 'DKR', 'LAS', 'Atterrissage', 'Privé', 'Contrôle d\'accès', 'Intrusion', 'incident', 3, 'C', 'Intervention sécurité'),
('Test Acte Illicite', 'Description de test pour un acte d\'intervention illicite', '2024-01-17', '08:45:00', 'Zone de fret', 'DSS', 'AMARANTE', 'Au sol', 'Cargo', 'Sûreté du fret', 'Tentative sabotage', 'acte d intervention illicite', 4, 'B', 'Alerte immédiate');
