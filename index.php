<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Evenement.php';
require_once __DIR__ . '/auth.php';

// Vérifier si l'utilisateur est connecté
requireLogin();

$database = new Database();
$db = $database->getConnection();
$evenement = new Evenement($db);

// Récupération des statistiques
$stats = $evenement->getStatistiques();
$aeroports = $evenement->getAeroports();
$structures = $evenement->getStructures();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Événements Aéroportuaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="header-section fade-in-up">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="logo.jpg" alt="Logo" class="img-fluid" style="max-height: 80px;">
                </div>
                <div class="col-md-8">
                    <h1 class="header-title">
                        <i class="fas fa-plane-departure me-3"></i>
                        Système de suivi et d'analyse des événements de sûrete
                    </h1>
                   
                </div>
                <div class="col-md-2 text-end">
                    <div class="user-info">
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    <?php echo getUserInitials(); ?>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#" onclick="logout()">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <ul class="nav nav-pills justify-content-center fade-in-up">
            <li class="nav-item">
                <a class="nav-link active" href="#dashboard" data-bs-toggle="pill">
                    <i class="fas fa-chart-pie me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#ajouter" data-bs-toggle="pill">
                    <i class="fas fa-plus-circle me-2"></i>Ajouter Événement
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#rechercher" data-bs-toggle="pill">
                    <i class="fas fa-search me-2"></i>Rechercher
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#liste" data-bs-toggle="pill">
                    <i class="fas fa-list me-2"></i>Liste Événements
                </a>
            </li>
        </ul>

        <!-- Content -->
        <div class="tab-content">
            <!-- Dashboard -->
            <div class="tab-pane fade show active" id="dashboard">
                <!-- KPI Cards -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="kpi-container fade-in-up">
                            <div class="kpi-item kpi-success">
                                <div class="kpi-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="kpi-content">
                                    <h3 id="evenementsMineurs">0</h3>
                                    <p>Événements Mineurs</p>
                                </div>
                            </div>
                            <div class="kpi-item kpi-warning">
                                <div class="kpi-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="kpi-content">
                                    <h3 id="incidents">0</h3>
                                    <p>Incidents</p>
                                </div>
                            </div>
                            <div class="kpi-item kpi-danger">
                                <div class="kpi-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="kpi-content">
                                    <h3 id="actesIllicites">0</h3>
                                    <p>Actes Illicites</p>
                                </div>
                            </div>
                            <div class="kpi-item kpi-info">
                                <div class="kpi-icon">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                                <div class="kpi-content">
                                    <h3 id="evenementsRecents">0</h3>
                                    <p>Cette Semaine</p>
                                </div>
                            </div>
                            <div class="kpi-item kpi-primary">
                                <div class="kpi-icon">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="kpi-content">
                                    <h3 id="totalEvenements">0</h3>
                                    <p>Total Événements</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="chart-container fade-in-up">
                            <h5 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Événements par Classe</h5>
                            <canvas id="classeChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="chart-container fade-in-up">
                            <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Événements par Aéroport</h5>
                            <canvas id="aeroportChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="chart-container fade-in-up">
                            <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Évolution Mensuelle</h5>
                            <canvas id="evolutionChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="chart-container fade-in-up">
                            <h5 class="mb-3"><i class="fas fa-exclamation-circle me-2"></i>Evaluation du risque</h5>
                            <canvas id="graviteChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ajouter Événement -->
            <div class="tab-pane fade" id="ajouter">
                <div class="card fade-in-up">
                    <div class="card-header">
                        <i class="fas fa-plus-circle me-2"></i>Nouvel Événement
                    </div>
                    <div class="card-body">
                        <form id="ajouterForm" action="actions/ajouter_evenement.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Aéroport</label>
                                    <select class="form-select" name="nom_aeroport" id="nom_aeroport">
                                        <option value="">Sélectionner un aéroport</option>
                                        <?php foreach($aeroports as $aeroport): ?>
                                            <option value="<?php echo $aeroport['code']; ?>">
                                                <?php echo $aeroport['code'] . ' - ' . $aeroport['nom']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="AUTRES">Autres</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="autre_aeroport_container" style="display: none;">
                                    <label class="form-label">Nom de l'aéroport</label>
                                    <input type="text" class="form-control" name="autre_aeroport" id="autre_aeroport" placeholder="Entrer le nom de l'aéroport">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lieu *</label>
                                    <input type="text" class="form-control" name="lieu" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Structure *</label>
                                    <select class="form-select" name="structure" required>
                                        <option value="">Sélectionner une structure</option>
                                        <?php foreach($structures as $structure): ?>
                                            <option value="<?php echo $structure['nom']; ?>">
                                                <?php echo $structure['nom']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Titre de l'Événement *</label>
                                    <input type="text" class="form-control" name="titre_evenement" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date *</label>
                                    <input type="date" class="form-control" name="date_evenement" id="date_evenement" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Heure *</label>
                                    <input type="time" class="form-control" name="heure_evenement" id="heure_evenement" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type d'Aéronef</label>
                                    <input type="text" class="form-control" name="type_aeronef">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Immatriculation</label>
                                    <input type="text" class="form-control" name="immatriculation">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phase de Vol</label>
                                    <input type="text" class="form-control" name="phase_vol" placeholder="Ex: Décollage, Montée, Croisière, Descente, Approche, Atterrissage, Roulage, Stationnement...">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type d'Exploitation</label>
                                    <input type="text" class="form-control" name="type_exploitation" placeholder="Ex: Commercial, Privé, Militaire, Cargo, Formation...">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Classe de l'Événement *</label>
                                <select class="form-select" name="classe_evenement" required>
                                    <option value="">Sélectionner</option>
                                    <option value="EVENEMENT MINEUR">Événement Mineur</option>
                                    <option value="incident">Incident</option>
                                    <option value="acte d intervention illicite">Acte d'Intervention Illicite</option>
                                </select>
                            </div>

                            <!-- Domaines de Sûreté -->
                            <div class="mb-4">
                                <label class="form-label">Domaine Sûreté *</label>
                                <p class="text-muted small">À quelle(s) catégorie(s) l'événement aurait-il / a-t-il pu conduire :</p>
                                <div class="row">
                                    <div class="col-md-6 checkbox-section">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="surete cote ville" id="ds1">
                                            <label class="form-check-label" for="ds1">Sûreté côté ville</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="passagers et bagages de cabine" id="ds2">
                                            <label class="form-check-label" for="ds2">Passagers et bagages de cabine</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="personnel" id="ds3">
                                            <label class="form-check-label" for="ds3">Personnel</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="equipage" id="ds4">
                                            <label class="form-check-label" for="ds4">Équipage</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="controle d acces" id="ds5">
                                            <label class="form-check-label" for="ds5">Contrôle d'accès</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="bagages de soute" id="ds6">
                                            <label class="form-check-label" for="ds6">Bagages de soute</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="provisions de bord" id="ds7">
                                            <label class="form-check-label" for="ds7">Provisions de bord</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="fournitures d aeroport" id="ds8">
                                            <label class="form-check-label" for="ds8">Fournitures d'aéroport</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="surete en vol" id="ds9">
                                            <label class="form-check-label" for="ds9">Sûreté en vol</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="protection des aeroports" id="ds10">
                                            <label class="form-check-label" for="ds10">Protection des aéroports</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 checkbox-section">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="protection des aeronefs au sol" id="ds11">
                                            <label class="form-check-label" for="ds11">Protection des aéronefs au sol</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="fret et courrier" id="ds12">
                                            <label class="form-check-label" for="ds12">Fret et courrier</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="services de controle aerien" id="ds13">
                                            <label class="form-check-label" for="ds13">Services de contrôle aérien</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="technologie et systemes d information" id="ds14">
                                            <label class="form-check-label" for="ds14">Technologie et systèmes d'information</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="drones" id="ds15">
                                            <label class="form-check-label" for="ds15">Drones</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="manpads ou autres armes a distance" id="ds16">
                                            <label class="form-check-label" for="ds16">Manpads ou autres armes à distance</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="laser" id="ds17">
                                            <label class="form-check-label" for="ds17">Laser</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="informations sensibles" id="ds18">
                                            <label class="form-check-label" for="ds18">Informations sensibles</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="aviation generale/AEROCLUB" id="ds19">
                                            <label class="form-check-label" for="ds19">Aviation générale/AEROCLUB</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="autres" id="ds20">
                                            <label class="form-check-label" for="ds20">Autres (préciser)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catégories d'Événements -->
                            <div class="mb-4">
                                <label class="form-label">Catégorie de l'Événement *</label>
                                <p class="text-muted small">À quelle(s) catégorie(s) se rapporte l'événement ?</p>
                                <div class="row">
                                    <div class="col-md-6 checkbox-section">
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Découverte EEI à bord d'un véhicule" id="cat1">
                                            <label class="form-check-label small" for="cat1">Découverte EEI à bord d'un véhicule</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Découverte EEI porté par une personne" id="cat2">
                                            <label class="form-check-label small" for="cat2">Découverte EEI porté par une personne</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Découverte EEI dissimulé dans un local ou un endroit" id="cat3">
                                            <label class="form-check-label small" for="cat3">Découverte EEI dissimulé dans un local ou un endroit</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Attaque armée" id="cat4">
                                            <label class="form-check-label small" for="cat4">Attaque armée</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Articles laissés sans surveillance" id="cat5">
                                            <label class="form-check-label small" for="cat5">Articles laissés sans surveillance</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Cyber Attaque" id="cat6">
                                            <label class="form-check-label small" for="cat6">Cyber Attaque</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Dommages aux infrastructures critiques/points vulnérables" id="cat7">
                                            <label class="form-check-label small" for="cat7">Dommages aux infrastructures critiques/points vulnérables</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Comportement suspect" id="cat8">
                                            <label class="form-check-label small" for="cat8">Comportement suspect</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Perturbations imprévues" id="cat9">
                                            <label class="form-check-label small" for="cat9">Perturbations imprévues</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Mélange de passagers contrôlés et non contrôlés" id="cat10">
                                            <label class="form-check-label small" for="cat10">Mélange de passagers contrôlés et non contrôlés</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Passager armé muni d'une autorisation de port" id="cat11">
                                            <label class="form-check-label small" for="cat11">Passager armé muni d'une autorisation de port</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans le traitement des catégories particulières de passagers" id="cat12">
                                            <label class="form-check-label small" for="cat12">Lacune dans le traitement des catégories particulières de passagers</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans le processus d'inspection-filtrage au point de contrôle" id="cat13">
                                            <label class="form-check-label small" for="cat13">Lacune dans le processus d'inspection-filtrage au point de contrôle</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Utilisation d'objets interdits/EEI" id="cat14">
                                            <label class="form-check-label small" for="cat14">Utilisation d'objets interdits/EEI</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Sabotage/Menace interne" id="cat15">
                                            <label class="form-check-label small" for="cat15">Sabotage/Menace interne</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Personnel contournant les contrôles de sûreté" id="cat16">
                                            <label class="form-check-label small" for="cat16">Personnel contournant les contrôles de sûreté</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Faux ou usage de faux dans le régime de contrôle/vérification des antécédents" id="cat17">
                                            <label class="form-check-label small" for="cat17">Faux ou usage de faux dans le régime de contrôle/vérification des antécédents</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Personnel mal formé" id="cat18">
                                            <label class="form-check-label small" for="cat18">Personnel mal formé</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans les mesures mises en place par les sous-traitants" id="cat19">
                                            <label class="form-check-label small" for="cat19">Lacune dans les mesures mises en place par les sous-traitants</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Dégradation ou tentative de dégradation de la clôture de sûreté" id="cat20">
                                            <label class="form-check-label small" for="cat20">Dégradation ou tentative de dégradation de la clôture de sûreté</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Accès non autorisé à la ZSAR ou à une autre zone de sûreté (personne autre que le personnel)" id="cat21">
                                            <label class="form-check-label small" for="cat21">Accès non autorisé à la ZSAR ou à une autre zone de sûreté (personne autre que le personnel)</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Accès non autorisé/sans escorte dans une ZSAR (personnel)" id="cat22">
                                            <label class="form-check-label small" for="cat22">Accès non autorisé/sans escorte dans une ZSAR (personnel)</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Autorisation non conforme dans le système de contrôle d'accès" id="cat23">
                                            <label class="form-check-label small" for="cat23">Autorisation non conforme dans le système de contrôle d'accès</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Autorisation non conforme dans le système de délivrance des laissez-passer" id="cat24">
                                            <label class="form-check-label small" for="cat24">Autorisation non conforme dans le système de délivrance des laissez-passer</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 checkbox-section">
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Autorisation non conforme suite à un contrôle d'accès du véhicule et/ou des occupants" id="cat25">
                                            <label class="form-check-label small" for="cat25">Autorisation non conforme suite à un contrôle d'accès du véhicule et/ou des occupants</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Défaut de protection des bagages de soute contrôlés/preuves de falsification" id="cat26">
                                            <label class="form-check-label small" for="cat26">Défaut de protection des bagages de soute contrôlés/preuves de falsification</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans le système d'inspection-filtrage des bagages de soute (y compris le rapprochement des bagages des passagers)" id="cat27">
                                            <label class="form-check-label small" for="cat27">Lacune dans le système d'inspection-filtrage des bagages de soute (y compris le rapprochement des bagages des passagers)</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Accès non autorisé" id="cat28">
                                            <label class="form-check-label small" for="cat28">Accès non autorisé</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans la protection des approvisionnements sécurisés/preuves de falsification" id="cat29">
                                            <label class="form-check-label small" for="cat29">Lacune dans la protection des approvisionnements sécurisés/preuves de falsification</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans l'application d'autres contrôles de sûreté (précisez)" id="cat30">
                                            <label class="form-check-label small" for="cat30">Lacune dans l'application d'autres contrôles de sûreté (précisez)</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Passager indiscipliné (à prendre en compte pour les niveaux 3 et 4 à signaler)" id="cat31">
                                            <label class="form-check-label small" for="cat31">Passager indiscipliné (à prendre en compte pour les niveaux 3 et 4 à signaler)</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans le processus/la protection de la porte du poste de pilotage" id="cat32">
                                            <label class="form-check-label small" for="cat32">Lacune dans le processus/la protection de la porte du poste de pilotage</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Découverte d'un objet interdit/EEI" id="cat33">
                                            <label class="form-check-label small" for="cat33">Découverte d'un objet interdit/EEI</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Cyber-attaque" id="cat34">
                                            <label class="form-check-label small" for="cat34">Cyber-attaque</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Détournement en vol" id="cat35">
                                            <label class="form-check-label small" for="cat35">Détournement en vol</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Alerte à la bombe en vol" id="cat36">
                                            <label class="form-check-label small" for="cat36">Alerte à la bombe en vol</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans le processus de sélection" id="cat37">
                                            <label class="form-check-label small" for="cat37">Lacune dans le processus de sélection</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Carence dans la protection du fret sécurisé/preuve de falsification" id="cat38">
                                            <label class="form-check-label small" for="cat38">Carence dans la protection du fret sécurisé/preuve de falsification</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans le processus d'acceptation du fret et autres" id="cat39">
                                            <label class="form-check-label small" for="cat39">Lacune dans le processus d'acceptation du fret et autres</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Vol d'objets de valeur, bagages, fret/courrier" id="cat40">
                                            <label class="form-check-label small" for="cat40">Vol d'objets de valeur, bagages, fret/courrier</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Destruction ou endommagement des aides à la navigation aérienne" id="cat41">
                                            <label class="form-check-label small" for="cat41">Destruction ou endommagement des aides à la navigation aérienne</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Accès non autorisé au système d'information" id="cat42">
                                            <label class="form-check-label small" for="cat42">Accès non autorisé au système d'information</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Attaque contre le(s) système(s) d'aéronef" id="cat43">
                                            <label class="form-check-label small" for="cat43">Attaque contre le(s) système(s) d'aéronef</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Attaque contre le(s) système(s) ATM" id="cat44">
                                            <label class="form-check-label small" for="cat44">Attaque contre le(s) système(s) ATM</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Attaque contre le(s) système(s) aéroportuaire(s)" id="cat45">
                                            <label class="form-check-label small" for="cat45">Attaque contre le(s) système(s) aéroportuaire(s)</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Attaque contre d'autres systèmes et données critiques" id="cat46">
                                            <label class="form-check-label small" for="cat46">Attaque contre d'autres systèmes et données critiques</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Incursion non autorisée dans l'espace aérien contrôlé" id="cat47">
                                            <label class="form-check-label small" for="cat47">Incursion non autorisée dans l'espace aérien contrôlé</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Quasi-accident/Rencontre" id="cat48">
                                            <label class="form-check-label small" for="cat48">Quasi-accident/Rencontre</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Frappe/collision (drones, etc.)" id="cat49">
                                            <label class="form-check-label small" for="cat49">Frappe/collision (drones, etc.)</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Menace contre un avion" id="cat50">
                                            <label class="form-check-label small" for="cat50">Menace contre un avion</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Menace contre les infrastructures aéroportuaires" id="cat51">
                                            <label class="form-check-label small" for="cat51">Menace contre les infrastructures aéroportuaires</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Menace contre les passagers" id="cat52">
                                            <label class="form-check-label small" for="cat52">Menace contre les passagers</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Attaque contre un aéronef ou une installation aéroportuaire" id="cat53">
                                            <label class="form-check-label small" for="cat53">Attaque contre un aéronef ou une installation aéroportuaire</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Visées de lasers" id="cat54">
                                            <label class="form-check-label small" for="cat54">Visées de lasers</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Autres activités suspectes" id="cat55">
                                            <label class="form-check-label small" for="cat55">Autres activités suspectes</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="categories_evenement[]" value="Lacune dans la protection des informations sensibles concernant la sûreté de l'aviation" id="cat56">
                                            <label class="form-check-label small" for="cat56">Lacune dans la protection des informations sensibles concernant la sûreté de l'aviation</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description de l'Événement *</label>
                                <textarea class="form-control" name="description_evenement" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Analyse et Gestion du Risque</label>
                                <textarea class="form-control" name="analyse_gestion_risque" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mesures Prises *</label>
                                <textarea class="form-control" name="mesures_prises" rows="3" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Probabilité du Risque *</label>
                                    <select class="form-select" name="probabilite_risque" required>
                                        <option value="">Sélectionner</option>
                                        <option value="1">1 - Extrêmement Improbable</option>
                                        <option value="2">2 - Improbable</option>
                                        <option value="3">3 - Éloigné</option>
                                        <option value="4">4 - Occasionnel</option>
                                        <option value="5">5 - Fréquent</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gravité du Risque *</label>
                                    <select class="form-select" name="gravite_risque" required>
                                        <option value="">Sélectionner</option>
                                        <option value="E">E - Négligeable</option>
                                        <option value="D">D - Mineur</option>
                                        <option value="C">C - Majeur</option>
                                        <option value="B">B - Dangereux</option>
                                        <option value="A">A - Catastrophique</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-2"></i>Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer l'Événement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recherche -->
            <div class="tab-pane fade" id="rechercher">
                <div class="card fade-in-up">
                    <div class="card-header">
                        <i class="fas fa-search me-2"></i>Recherche Avancée d'Événements
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            La recherche et le filtrage des événements sont maintenant disponibles dans l'onglet <strong>"Liste Événements"</strong>.
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary" onclick="document.querySelector('a[href=\'#liste\']').click()">
                                <i class="fas fa-list me-2"></i>Aller à la Liste des Événements
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste -->
            <div class="tab-pane fade" id="liste">
                <!-- Filtres de recherche -->
                <div class="card fade-in-up mb-4">
                    <div class="card-header">
                        <i class="fas fa-filter me-2"></i>Filtres et Recherche
                    </div>
                    <div class="card-body">
                        <form id="rechercheForm" class="filter-section">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Aéroport</label>
                                    <select class="form-select" name="aeroport" id="filterAeroport">
                                        <option value="">Tous les aéroports</option>
                                        <?php foreach($aeroports as $aeroport): ?>
                                            <option value="<?php echo $aeroport['code']; ?>">
                                                <?php echo $aeroport['code'] . ' - ' . $aeroport['nom']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Structure</label>
                                    <select class="form-select" name="structure" id="filterStructure">
                                        <option value="">Toutes les structures</option>
                                        <?php foreach($structures as $structure): ?>
                                            <option value="<?php echo $structure['nom']; ?>">
                                                <?php echo $structure['nom']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Classe</label>
                                    <select class="form-select" name="classe" id="filterClasse">
                                        <option value="">Toutes les classes</option>
                                        <option value="EVENEMENT MINEUR">Événement Mineur</option>
                                        <option value="incident">Incident</option>
                                        <option value="acte d intervention illicite">Acte d'Intervention Illicite</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Recherche</label>
                                    <input type="text" class="form-control" name="search" id="filterSearch" placeholder="Titre ou description...">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Période Rapide</label>
                                    <select class="form-select" id="filterPeriode" onchange="appliquerPeriode()">
                                        <option value="">Période personnalisée</option>
                                        <option value="today">Aujourd'hui</option>
                                        <option value="week">Cette semaine</option>
                                        <option value="month">Ce mois</option>
                                        <option value="year">Cette année</option>
                                        <option value="last_week">Semaine dernière</option>
                                        <option value="last_month">Mois dernier</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Date début</label>
                                    <input type="date" class="form-control" name="date_debut" id="filterDateDebut">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Date fin</label>
                                    <input type="date" class="form-control" name="date_fin" id="filterDateFin">
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary me-2" onclick="rechercherEvenements()">
                                        <i class="fas fa-search me-2"></i>Rechercher
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                        <i class="fas fa-undo me-2"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Liste des événements -->
                <div class="card fade-in-up">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-list me-2"></i>Liste des Événements</span>
                        <div>
                            <button class="btn btn-outline-info btn-sm me-2" onclick="afficherStatistiques()">
                                <i class="fas fa-chart-bar me-2"></i>Statistiques
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="chargerTousEvenements()">
                                <i class="fas fa-sync-alt me-2"></i>Actualiser
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="listeEvenements">
                            <div class="text-center">
                                <div class="loading"></div>
                                <p class="mt-2">Chargement des événements...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Graphiques
        document.addEventListener('DOMContentLoaded', function() {
            // Graphique par classe
            const classeCtx = document.getElementById('classeChart').getContext('2d');
            new Chart(classeCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode(array_column($stats['par_classe'], 'classe_evenement')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($stats['par_classe'], 'count')); ?>,
                        backgroundColor: ['#059669', '#d97706', '#dc2626']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Graphique par aéroport
            const aeroportCtx = document.getElementById('aeroportChart').getContext('2d');
            new Chart(aeroportCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($stats['par_aeroport'], 'nom_aeroport')); ?>,
                    datasets: [{
                        label: 'Événements',
                        data: <?php echo json_encode(array_column($stats['par_aeroport'], 'count')); ?>,
                        backgroundColor: '#2563eb'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Graphique évolution
            const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
            new Chart(evolutionCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($stats['par_mois'], 'mois_label')); ?>,
                    datasets: [{
                        label: 'Événements par mois',
                        data: <?php echo json_encode(array_column($stats['par_mois'], 'count')); ?>,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#2563eb',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Graphique évaluation des risques
            const graviteCtx = document.getElementById('graviteChart').getContext('2d');
            new Chart(graviteCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode(array_column($stats['par_gravite'], 'evaluation')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($stats['par_gravite'], 'count')); ?>,
                        backgroundColor: <?php echo json_encode(array_column($stats['par_gravite'], 'color')); ?>,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#2563eb',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '50%'
                }
            });

            // Charger tous les événements au démarrage
            chargerTousEvenements();
            // Charger les statistiques
            chargerStatistiques();
            
            // Recherche automatique lors du changement des filtres
            document.getElementById('filterAeroport').addEventListener('change', rechercherEvenements);
            document.getElementById('filterStructure').addEventListener('change', rechercherEvenements);
            document.getElementById('filterClasse').addEventListener('change', rechercherEvenements);
            document.getElementById('filterSearch').addEventListener('input', function() {
                // Délai pour éviter trop de requêtes
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(rechercherEvenements, 500);
            });
            document.getElementById('filterDateDebut').addEventListener('change', rechercherEvenements);
            document.getElementById('filterDateFin').addEventListener('change', rechercherEvenements);
        });

        // Chargement des statistiques
        function chargerStatistiques() {
            fetch('pages/recherche.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Calculer les statistiques par classe
                        let evenementsMineurs = 0;
                        let incidents = 0;
                        let actesIllicites = 0;
                        
                        data.evenements.forEach(evt => {
                            // Normaliser la classe pour éviter les problèmes de casse et d'espaces
                            const classe = evt.classe_evenement ? evt.classe_evenement.toLowerCase().trim() : '';
                            
                            switch(classe) {
                                case 'evenement mineur':
                                case 'événement mineur':
                                    evenementsMineurs++;
                                    break;
                                case 'incident':
                                    incidents++;
                                    break;
                                case 'acte d intervention illicite':
                                case "acte d'intervention illicite":
                                    actesIllicites++;
                                    break;
                            }
                        });
                        
                        document.getElementById('evenementsMineurs').textContent = evenementsMineurs;
                        document.getElementById('incidents').textContent = incidents;
                        document.getElementById('actesIllicites').textContent = actesIllicites;
                        document.getElementById('totalEvenements').textContent = data.evenements.length;
                        
                        // Calculer les événements de cette semaine
                        const today = new Date();
                        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                        const evenementsRecents = data.evenements.filter(evt => {
                            const dateEvt = new Date(evt.date_evenement);
                            return dateEvt >= weekAgo;
                        });
                        document.getElementById('evenementsRecents').textContent = evenementsRecents.length;
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour mettre à jour les KPI avec les événements filtrés
        function mettreAJourStatistiquesFiltrées(evenements) {
            // Calculer les statistiques par classe
            let evenementsMineurs = 0;
            let incidents = 0;
            let actesIllicites = 0;
            
            console.log('Événements reçus:', evenements.length);
            
            evenements.forEach(evt => {
                console.log('Classe événement:', evt.classe_evenement);
                // Normaliser la classe pour éviter les problèmes de casse et d'espaces
                const classe = evt.classe_evenement ? evt.classe_evenement.toLowerCase().trim() : '';
                
                switch(classe) {
                    case 'evenement mineur':
                    case 'événement mineur':
                        evenementsMineurs++;
                        break;
                    case 'incident':
                        incidents++;
                        break;
                    case 'acte d intervention illicite':
                    case "acte d'intervention illicite":
                        actesIllicites++;
                        break;
                }
            });
            
            console.log('Résultats KPI:', {evenementsMineurs, incidents, actesIllicites});
            
            document.getElementById('evenementsMineurs').textContent = evenementsMineurs;
            document.getElementById('incidents').textContent = incidents;
            document.getElementById('actesIllicites').textContent = actesIllicites;
            document.getElementById('totalEvenements').textContent = evenements.length;
            
            // Calculer les événements de cette semaine
            const today = new Date();
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            const evenementsRecents = evenements.filter(evt => {
                const dateEvt = new Date(evt.date_evenement);
                return dateEvt >= weekAgo;
            });
            document.getElementById('evenementsRecents').textContent = evenementsRecents.length;
        }

        // Fonctions de recherche et gestion
        function rechercherEvenements() {
            const formData = new FormData(document.getElementById('rechercheForm'));
            const params = new URLSearchParams(formData);
            
            fetch('pages/recherche.php?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    // Afficher les résultats dans la liste des événements
                    afficherResultats(data.evenements, 'listeEvenements');
                    // Mettre à jour les statistiques avec les résultats filtrés
                    mettreAJourStatistiquesFiltrées(data.evenements);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('listeEvenements').innerHTML = 
                        '<div class="alert alert-danger">Erreur lors de la recherche</div>';
                });
        }

        function chargerTousEvenements() {
            fetch('pages/recherche.php')
                .then(response => response.json())
                .then(data => {
                    afficherResultats(data.evenements, 'listeEvenements');
                    // Mettre à jour les KPI avec tous les événements
                    mettreAJourStatistiquesFiltrées(data.evenements);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('listeEvenements').innerHTML = 
                        '<div class="alert alert-danger">Erreur lors du chargement</div>';
                });
        }

        function afficherResultats(evenements, containerId) {
            const container = document.getElementById(containerId);
            
            if (evenements.length === 0) {
                container.innerHTML = '<div class="alert alert-info">Aucun événement trouvé</div>';
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-calendar me-1"></i>Date/Heure</th>
                                <th><i class="fas fa-plane me-1"></i>Aéroport</th>
                                <th><i class="fas fa-file-alt me-1"></i>Titre</th>
                                <th><i class="fas fa-tag me-1"></i>Classe</th>
                                <th><i class="fas fa-building me-1"></i>Structure</th>
                                <th><i class="fas fa-exclamation-triangle me-1"></i>Risque</th>
                                <th><i class="fas fa-cogs me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            evenements.forEach(evt => {
                const classeColor = {
                    'EVENEMENT MINEUR': 'success',
                    'incident': 'warning',
                    'acte d intervention illicite': 'danger'
                };

                const graviteColor = {
                    'E': 'success',
                    'D': 'info',
                    'C': 'warning',
                    'B': 'danger',
                    'A': 'danger'
                };

                html += `
                    <tr>
                        <td>
                            <small class="text-muted">${formatDate(evt.date_evenement)}</small><br>
                            <strong>${evt.heure_evenement}</strong>
                        </td>
                        <td>
                            <strong>${evt.nom_aeroport}</strong><br>
                            <small class="text-muted">${evt.lieu}</small>
                        </td>
                        <td>
                            <strong>${evt.titre_evenement}</strong><br>
                            <small class="text-muted">${evt.description_evenement.substring(0, 100)}...</small>
                        </td>
                        <td>
                            <span class="badge bg-${classeColor[evt.classe_evenement] || 'secondary'}">
                                ${evt.classe_evenement}
                            </span>
                        </td>
                        <td>${evt.structure}</td>
                        <td>
                            <span class="badge bg-${graviteColor[evt.gravite_risque] || 'secondary'}">
                                ${evt.gravite_risque}${evt.probabilite_risque}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" onclick="voirDetails(${evt.id})" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" onclick="modifierEvenement(${evt.id})" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="telechargerRapportIndividuel(${evt.id})" title="Rapport PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="supprimerEvenement(${evt.id})" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <small class="text-muted">${evenements.length} événement(s) trouvé(s)</small>
                </div>
            `;

            container.innerHTML = html;
        }

        function resetFilters() {
            document.getElementById('rechercheForm').reset();
            document.getElementById('resultatsRecherche').innerHTML = '';
        }

        function supprimerEvenement(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
                window.location.href = 'actions/supprimer_evenement.php?id=' + id;
            }
        }

        function voirDetails(id) {
            window.location.href = 'pages/details_evenement.php?id=' + id;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }

        // Gestion des messages de succès/erreur
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.get('success') === '1') {
                showAlert('Événement ajouté avec succès !', 'success');
            } else if (urlParams.get('error') === '1') {
                showAlert('Erreur lors de l\'ajout de l\'événement', 'danger');
            } else if (urlParams.get('deleted') === '1') {
                showAlert('Événement supprimé avec succès !', 'success');
            } else if (urlParams.get('error_delete') === '1') {
                showAlert('Erreur lors de la suppression', 'danger');
            }

            // Initialiser la date et l'heure actuelles dans le formulaire d'ajout
            initializeDateTimeFields();
        });

        // Fonction pour initialiser les champs date et heure avec les valeurs actuelles
        function initializeDateTimeFields() {
            const now = new Date();
            
            // Format de date YYYY-MM-DD pour input type="date"
            const dateString = now.toISOString().split('T')[0];
            
            // Format d'heure HH:MM pour input type="time"
            const timeString = now.toTimeString().split(' ')[0].substring(0, 5);
            
            // Définir les valeurs par défaut
            const dateField = document.getElementById('date_evenement');
            const timeField = document.getElementById('heure_evenement');
            
            if (dateField && !dateField.value) {
                dateField.value = dateString;
            }
            
            if (timeField && !timeField.value) {
                timeField.value = timeString;
            }
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.header-section'));
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Validation du formulaire
        document.getElementById('ajouterForm').addEventListener('submit', function(e) {
            // Vérifier les domaines de sûreté
            const domainesChecked = document.querySelectorAll('input[name="domaine_surete[]"]:checked');
            if (domainesChecked.length === 0) {
                e.preventDefault();
                showAlert('Veuillez sélectionner au moins un domaine de sûreté', 'danger');
                return false;
            }

            // Vérifier les catégories d'événements
            const categoriesChecked = document.querySelectorAll('input[name="categories_evenement[]"]:checked');
            if (categoriesChecked.length === 0) {
                e.preventDefault();
                showAlert('Veuillez sélectionner au moins une catégorie d\'événement', 'danger');
                return false;
            }
        });

        // Nouvelles fonctions pour la gestion et rapports
        function modifierEvenement(id) {
            window.location.href = 'pages/modifier_evenement.php?id=' + id;
        }

        function telechargerRapportIndividuel(id) {
            window.location.href = 'pages/generer_rapport.php?id=' + id;
        }

        function telechargerRapportTous() {
            // Récupérer tous les événements affichés actuellement
            const evenements = document.querySelectorAll('tbody tr');
            if (evenements.length === 0) {
                showAlert('Aucun événement à exporter', 'warning');
                return;
            }
            
            // Redirection vers le générateur de rapport pour tous les événements filtrés
            const formData = new FormData(document.getElementById('rechercheForm'));
            const params = new URLSearchParams(formData);
            params.append('export_all', '1');
            window.location.href = 'pages/generer_rapport.php?' + params.toString();
        }

        function afficherStatistiques() {
            // Afficher un modal avec les statistiques des événements affichés
            const evenements = document.querySelectorAll('tbody tr');
            if (evenements.length === 0) {
                showAlert('Aucun événement pour calculer les statistiques', 'info');
                return;
            }
            
            showAlert(`Statistiques: ${evenements.length} événement(s) affiché(s)`, 'info');
        }

        function resetFilters() {
            document.getElementById('rechercheForm').reset();
            document.getElementById('filterPeriode').value = '';
            chargerTousEvenements();
        }

        // Fonction pour appliquer les périodes rapides
        function appliquerPeriode() {
            const periode = document.getElementById('filterPeriode').value;
            const dateDebut = document.getElementById('filterDateDebut');
            const dateFin = document.getElementById('filterDateFin');
            
            const today = new Date();
            let debut, fin;
            
            switch(periode) {
                case 'today':
                    debut = fin = today.toISOString().split('T')[0];
                    break;
                case 'week':
                    const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
                    const endOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                    debut = startOfWeek.toISOString().split('T')[0];
                    fin = endOfWeek.toISOString().split('T')[0];
                    break;
                case 'month':
                    debut = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    fin = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
                    break;
                case 'year':
                    debut = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                    fin = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
                    break;
                case 'last_week':
                    const lastWeekStart = new Date(today.setDate(today.getDate() - today.getDay() - 7));
                    const lastWeekEnd = new Date(today.setDate(today.getDate() - today.getDay() - 1));
                    debut = lastWeekStart.toISOString().split('T')[0];
                    fin = lastWeekEnd.toISOString().split('T')[0];
                    break;
                case 'last_month':
                    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    debut = lastMonth.toISOString().split('T')[0];
                    fin = new Date(today.getFullYear(), today.getMonth(), 0).toISOString().split('T')[0];
                    break;
                default:
                    return;
            }
            
            dateDebut.value = debut;
            dateFin.value = fin;
            
            // Lancer automatiquement la recherche
            rechercherEvenements();
        }

        // Fonction de déconnexion
        function logout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                window.location.href = 'logout.php';
            }
        }

        // Gérer l'affichage du champ personnalisé pour l'aéroport "Autres"
        document.getElementById('nom_aeroport').addEventListener('change', function() {
            const autreContainer = document.getElementById('autre_aeroport_container');
            const autreInput = document.getElementById('autre_aeroport');
            
            if (this.value === 'AUTRES') {
                autreContainer.style.display = 'block';
                autreInput.required = true;
            } else {
                autreContainer.style.display = 'none';
                autreInput.required = false;
                autreInput.value = '';
            }
        });
    </script>
</body>
</html>
