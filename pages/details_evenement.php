<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Evenement.php';
require_once __DIR__ . '/../auth.php';

// Vérifier si l'utilisateur est connecté
requireLogin();

if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$evenement = new Evenement($db);

$evenement->id = $_GET['id'];
if(!$evenement->readOne()) {
    header("Location: ../index.php?error=not_found");
    exit;
}

// Récupération des données pour affichage
$aeroports = $evenement->getAeroports();
$aeroport_nom = '';
foreach($aeroports as $aero) {
    if($aero['code'] == $evenement->nom_aeroport) {
        $aeroport_nom = $aero['nom'];
        break;
    }
}

$domaines_array = !empty($evenement->domaine_surete) ? explode(',', $evenement->domaine_surete) : [];
$categories_array = !empty($evenement->categories_evenement) ? explode(',', $evenement->categories_evenement) : [];

$probabilite_labels = [
    '1' => 'Extrêmement Improbable',
    '2' => 'Improbable', 
    '3' => 'Éloigné',
    '4' => 'Occasionnel',
    '5' => 'Fréquent'
];

$gravite_labels = [
    'E' => 'Négligeable',
    'D' => 'Mineur',
    'C' => 'Majeur', 
    'B' => 'Dangereux',
    'A' => 'Catastrophique'
];

$classe_colors = [
    'EVENEMENT MINEUR' => 'success',
    'incident' => 'warning',
    'acte d intervention illicite' => 'danger'
];

$gravite_colors = [
    'E' => 'success',
    'D' => 'info', 
    'C' => 'warning',
    'B' => 'danger',
    'A' => 'danger'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Événement - <?php echo htmlspecialchars($evenement->titre_evenement); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --accent-yellow: #fbbf24;
            --accent-red: #dc2626;
            --success-color: #10b981;
            --info-color: #3b82f6;
        }
        
        body {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #fbbf24 100%);
            min-height: 100vh;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color)) !important;
            color: white !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            border: none;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--accent-yellow), #d97706);
            border: none;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--accent-red), #b91c1c);
            border: none;
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #047857);
            border: none;
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info-color), var(--primary-color));
            border: none;
        }
        
        .header-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { font-size: 12px; color: #000; background: white !important; }
            .card { border: 1px solid #000 !important; box-shadow: none !important; background: white !important; }
            .badge { border: 1px solid #000 !important; }
            .btn { display: none !important; }
        }
        .print-only { display: none; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="header-section fade-in-up">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="../logo.jpg" alt="Logo" class="img-fluid" style="max-height: 80px;">
                </div>
                <div class="col-md-8">
                    <h1 class="header-title">
                        <i class="fas fa-eye me-3"></i>
                        Détails de l'Événement
                    </h1>
                    <p class="header-subtitle"><?php echo htmlspecialchars($evenement->titre_evenement); ?></p>
                </div>
                <div class="col-md-2 text-end no-print">
                    <a href="../index.php#liste" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Informations d'impression (visible seulement à l'impression) -->
        <div class="print-only mb-4">
            <div class="text-center border-bottom pb-2 mb-3">
                <h2>Rapport d'Événement Aéroportuaire</h2>
                <p><strong>ID:</strong> <?php echo $evenement->id; ?> | <strong>Généré le:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
            </div>
        </div>

        <!-- Détails de l'événement -->
        <div class="row">
            <!-- Informations principales -->
            <div class="col-lg-8">
                <div class="card fade-in-up">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>Informations Principales
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Aéroport :</strong><br>
                                <span class="badge bg-primary"><?php echo $evenement->nom_aeroport; ?></span>
                                <?php echo htmlspecialchars($aeroport_nom); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Lieu :</strong><br>
                                <?php echo htmlspecialchars($evenement->lieu); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Structure :</strong><br>
                                <?php echo htmlspecialchars($evenement->structure); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Date et Heure :</strong><br>
                                <?php echo date('d/m/Y', strtotime($evenement->date_evenement)); ?> à 
                                <?php echo date('H:i', strtotime($evenement->heure_evenement)); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Classe :</strong><br>
                                <span class="badge bg-<?php echo $classe_colors[$evenement->classe_evenement] ?? 'secondary'; ?>">
                                    <?php echo htmlspecialchars($evenement->classe_evenement); ?>
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Évaluation du Risque :</strong><br>
                                <span class="badge bg-<?php echo $gravite_colors[$evenement->gravite_risque] ?? 'secondary'; ?>">
                                    <?php echo $evenement->gravite_risque . $evenement->probabilite_risque; ?>
                                </span>
                                (<?php echo $gravite_labels[$evenement->gravite_risque] ?? $evenement->gravite_risque; ?> - 
                                <?php echo $probabilite_labels[$evenement->probabilite_risque] ?? $evenement->probabilite_risque; ?>)
                            </div>
                        </div>

                        <?php if(!empty($evenement->type_aeronef) || !empty($evenement->immatriculation) || !empty($evenement->phase_vol) || !empty($evenement->type_exploitation)): ?>
                        <hr>
                        <h6><i class="fas fa-plane me-2"></i>Informations Aéronef</h6>
                        <div class="row">
                            <?php if(!empty($evenement->type_aeronef)): ?>
                            <div class="col-md-6 mb-2">
                                <strong>Type d'Aéronef :</strong> <?php echo htmlspecialchars($evenement->type_aeronef); ?>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($evenement->immatriculation)): ?>
                            <div class="col-md-6 mb-2">
                                <strong>Immatriculation :</strong> <?php echo htmlspecialchars($evenement->immatriculation); ?>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($evenement->phase_vol)): ?>
                            <div class="col-md-6 mb-2">
                                <strong>Phase de Vol :</strong> <?php echo htmlspecialchars($evenement->phase_vol); ?>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($evenement->type_exploitation)): ?>
                            <div class="col-md-6 mb-2">
                                <strong>Type d'Exploitation :</strong> <?php echo htmlspecialchars($evenement->type_exploitation); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Description -->
                <div class="card fade-in-up mt-4">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i>Description de l'Événement
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($evenement->description_evenement)); ?></p>
                    </div>
                </div>

                <!-- Mesures prises -->
                <div class="card fade-in-up mt-4">
                    <div class="card-header">
                        <i class="fas fa-tasks me-2"></i>Mesures Prises
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($evenement->mesures_prises)); ?></p>
                    </div>
                </div>

                <?php if(!empty($evenement->analyse_gestion_risque)): ?>
                <!-- Analyse du risque -->
                <div class="card fade-in-up mt-4">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i>Analyse et Gestion du Risque
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($evenement->analyse_gestion_risque)); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Actions -->
                <div class="card fade-in-up no-print">
                    <div class="card-header">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </div>
                    <div class="card-body">
                        <a href="modifier_evenement.php?id=<?php echo $evenement->id; ?>" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                        <button onclick="imprimerRapport()" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-print me-2"></i>Imprimer
                        </button>
                        <button onclick="telechargerPDF()" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-download me-2"></i>Télécharger PDF
                        </button>
                        <button onclick="supprimerEvenement(<?php echo $evenement->id; ?>)" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </div>
                </div>

                <!-- Domaines de sûreté -->
                <?php if(!empty($domaines_array)): ?>
                <div class="card fade-in-up mt-4">
                    <div class="card-header">
                        <i class="fas fa-shield-alt me-2"></i>Domaines de Sûreté
                    </div>
                    <div class="card-body">
                        <?php foreach($domaines_array as $domaine): ?>
                            <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars(trim($domaine)); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Catégories -->
                <?php if(!empty($categories_array)): ?>
                <div class="card fade-in-up mt-4">
                    <div class="card-header">
                        <i class="fas fa-tags me-2"></i>Catégories d'Événements
                    </div>
                    <div class="card-body">
                        <?php foreach($categories_array as $categorie): ?>
                            <span class="badge bg-info me-1 mb-1 small"><?php echo htmlspecialchars(trim($categorie)); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function supprimerEvenement(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
                window.location.href = '../actions/supprimer_evenement.php?id=' + id;
            }
        }

        function imprimerRapport() {
            window.print();
        }

        function telechargerPDF() {
            // Redirection vers le générateur de PDF
            window.location.href = 'generer_pdf.php?id=<?php echo $evenement->id; ?>';
        }
    </script>
</body>
</html>
