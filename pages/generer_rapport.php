<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Evenement.php';
require_once __DIR__ . '/../auth.php';

// Vérifier si l'utilisateur est connecté
requireLogin();

$database = new Database();
$db = $database->getConnection();
$evenement = new Evenement($db);

// Récupération des événements selon les paramètres
$evenements = [];

if (isset($_GET['export_all']) && $_GET['export_all'] == '1') {
    // Export de tous les événements avec filtres
    $where_conditions = [];
    $params = [];
    
    if (!empty($_GET['aeroport'])) {
        $where_conditions[] = "e.nom_aeroport = ?";
        $params[] = $_GET['aeroport'];
    }
    
    if (!empty($_GET['structure'])) {
        $where_conditions[] = "e.structure = ?";
        $params[] = $_GET['structure'];
    }
    
    if (!empty($_GET['classe'])) {
        $where_conditions[] = "e.classe_evenement = ?";
        $params[] = $_GET['classe'];
    }
    
    if (!empty($_GET['search'])) {
        $where_conditions[] = "(e.titre_evenement LIKE ? OR e.description_evenement LIKE ?)";
        $search_term = '%' . $_GET['search'] . '%';
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    if (!empty($_GET['date_debut'])) {
        $where_conditions[] = "e.date_evenement >= ?";
        $params[] = $_GET['date_debut'];
    }
    
    if (!empty($_GET['date_fin'])) {
        $where_conditions[] = "e.date_evenement <= ?";
        $params[] = $_GET['date_fin'];
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    $query = "SELECT e.*, a.nom as nom_aeroport_complet, a.ville 
             FROM evenements e 
             LEFT JOIN aeroports a ON e.nom_aeroport = a.code 
             $where_clause
             ORDER BY e.date_evenement DESC, e.heure_evenement DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    // Export d'un événement spécifique
    $id = intval($_GET['id']);
    $query = "SELECT e.*, a.nom as nom_aeroport_complet, a.ville 
             FROM evenements e 
             LEFT JOIN aeroports a ON e.nom_aeroport = a.code 
             WHERE e.id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} elseif (isset($_GET['ids']) && !empty($_GET['ids'])) {
    // Export d'événements sélectionnés (ancienne méthode)
    $ids = explode(',', $_GET['ids']);
    $ids = array_map('intval', $ids);
    
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $query = "SELECT e.*, a.nom as nom_aeroport_complet, a.ville 
             FROM evenements e 
             LEFT JOIN aeroports a ON e.nom_aeroport = a.code 
             WHERE e.id IN ($placeholders)
             ORDER BY e.date_evenement DESC, e.heure_evenement DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($ids);
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($evenements)) {
    header("Location: ../index.php?error=no_events");
    exit;
}

// Génération du rapport HTML/PDF
$titre = count($evenements) > 1 ? 'compte rendu d’événement de sûreté ' : 'compte rendu d’événement de sûreté ';
$date_generation = date('d/m/Y H:i:s');

// Labels pour l'affichage
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titre; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding: 20px 0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            border: none;
        }
        
        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            body { font-size: 12px; background: white !important; }
            .table { font-size: 11px; }
            @page {
                margin: 1cm;
                @top-left { content: ""; }
                @top-right { content: ""; }
                @bottom-left { content: ""; }
                @bottom-right { content: ""; }
            }
        }
        .header-rapport {
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 20px;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .event-section {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .badge-custom {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Actions (non imprimées) -->
        <div class="no-print mb-4">
            <div class="d-flex justify-content-end align-items-center">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i>Imprimer
                </button>
            </div>
        </div>

        <!-- En-tête du rapport -->
        <div class="header-rapport">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="../logo.jpg" alt="Logo" style="max-height: 80px;">
                </div>
                <div class="col-md-8 text-center">
                    <h1 class="mb-2"><?php echo $titre; ?></h1>
                    <p class="text-muted mb-1">Système de Gestion des Événements de Sûreté Aéroportuaire</p>
                    <p class="text-muted"><strong>Généré le :</strong> <?php echo $date_generation; ?></p>
                </div>
                <div class="col-md-2 text-end">
                    <div class="badge bg-primary fs-6">
                        <?php echo count($evenements); ?> événement(s)
                    </div>
                </div>
            </div>
        </div>

        <!-- Résumé statistique -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h3>Résumé Statistique</h3>
                <div class="row">
                    <?php
                    // Calcul des statistiques
                    $stats_classe = [];
                    $stats_gravite = [];
                    $stats_aeroport = [];
                    
                    foreach($evenements as $evt) {
                        $stats_classe[$evt['classe_evenement']] = ($stats_classe[$evt['classe_evenement']] ?? 0) + 1;
                        $stats_gravite[$evt['gravite_risque']] = ($stats_gravite[$evt['gravite_risque']] ?? 0) + 1;
                        $stats_aeroport[$evt['nom_aeroport']] = ($stats_aeroport[$evt['nom_aeroport']] ?? 0) + 1;
                    }
                    ?>
                    <div class="col-md-4">
                        <h6>Par Classe d'Événement</h6>
                        <?php foreach($stats_classe as $classe => $count): ?>
                            <div class="d-flex justify-content-between">
                                <span><?php echo $classe; ?></span>
                                <span class="badge bg-secondary"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-md-4">
                        <h6>Par Gravité</h6>
                        <?php foreach($stats_gravite as $gravite => $count): ?>
                            <div class="d-flex justify-content-between">
                                <span><?php echo $gravite_labels[$gravite] ?? $gravite; ?></span>
                                <span class="badge bg-secondary"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-md-4">
                        <h6>Par Aéroport</h6>
                        <?php foreach($stats_aeroport as $aeroport => $count): ?>
                            <div class="d-flex justify-content-between">
                                <span><?php echo $aeroport; ?></span>
                                <span class="badge bg-secondary"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails des événements -->
        <h3>Détails des Événements</h3>
        
        <?php foreach($evenements as $index => $evt): ?>
            <?php if($index > 0): ?><div class="page-break"></div><?php endif; ?>
            
            <div class="event-section">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <h4><?php echo htmlspecialchars($evt['titre_evenement']); ?></h4>
                        <p class="text-muted mb-1">
                            <strong>ID:</strong> <?php echo $evt['id']; ?> | 
                            <strong>Date:</strong> <?php echo date('d/m/Y', strtotime($evt['date_evenement'])); ?> à <?php echo date('H:i', strtotime($evt['heure_evenement'])); ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge-custom bg-primary"><?php echo $evt['nom_aeroport']; ?></span>
                        <span class="badge-custom bg-<?php 
                            echo $evt['classe_evenement'] == 'EVENEMENT MINEUR' ? 'success' : 
                                ($evt['classe_evenement'] == 'incident' ? 'warning' : 'danger'); 
                        ?>"><?php echo $evt['classe_evenement']; ?></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><th>Aéroport:</th><td><?php echo $evt['nom_aeroport_complet']; ?> (<?php echo $evt['nom_aeroport']; ?>)</td></tr>
                            <tr><th>Lieu:</th><td><?php echo htmlspecialchars($evt['lieu']); ?></td></tr>
                            <tr><th>Structure:</th><td><?php echo htmlspecialchars($evt['structure']); ?></td></tr>
                            <?php if(!empty($evt['type_aeronef'])): ?>
                            <tr><th>Type Aéronef:</th><td><?php echo htmlspecialchars($evt['type_aeronef']); ?></td></tr>
                            <?php endif; ?>
                            <?php if(!empty($evt['immatriculation'])): ?>
                            <tr><th>Immatriculation:</th><td><?php echo htmlspecialchars($evt['immatriculation']); ?></td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <?php if(!empty($evt['phase_vol'])): ?>
                            <tr><th>Phase de Vol:</th><td><?php echo htmlspecialchars($evt['phase_vol']); ?></td></tr>
                            <?php endif; ?>
                            <?php if(!empty($evt['type_exploitation'])): ?>
                            <tr><th>Type Exploitation:</th><td><?php echo htmlspecialchars($evt['type_exploitation']); ?></td></tr>
                            <?php endif; ?>
                            <tr><th>Probabilité:</th><td><?php echo $probabilite_labels[$evt['probabilite_risque']] ?? $evt['probabilite_risque']; ?></td></tr>
                            <tr><th>Gravité:</th><td><?php echo $gravite_labels[$evt['gravite_risque']] ?? $evt['gravite_risque']; ?></td></tr>
                            <tr><th>Risque:</th><td><span class="badge bg-danger"><?php echo $evt['gravite_risque'] . $evt['probabilite_risque']; ?></span></td></tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h6>Description de l'Événement</h6>
                        <p><?php echo nl2br(htmlspecialchars($evt['description_evenement'])); ?></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h6>Mesures Prises</h6>
                        <p><?php echo nl2br(htmlspecialchars($evt['mesures_prises'])); ?></p>
                    </div>
                </div>

                <?php if(!empty($evt['analyse_gestion_risque'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h6>Analyse et Gestion du Risque</h6>
                        <p><?php echo nl2br(htmlspecialchars($evt['analyse_gestion_risque'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!empty($evt['domaine_surete'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h6>Domaines de Sûreté</h6>
                        <p>
                            <?php 
                            $domaines = explode(',', $evt['domaine_surete']);
                            foreach($domaines as $domaine): ?>
                                <span class="badge bg-secondary me-1"><?php echo trim($domaine); ?></span>
                            <?php endforeach; ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!empty($evt['categories_evenement'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h6>Catégories d'Événements</h6>
                        <p>
                            <?php 
                            $categories = explode(',', $evt['categories_evenement']);
                            foreach($categories as $categorie): ?>
                                <span class="badge bg-info me-1 mb-1 small"><?php echo trim($categorie); ?></span>
                            <?php endforeach; ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Pied de page -->
        <div class="text-center mt-5 pt-3 border-top">
            <p class="text-muted">
                <small>
                    Rapport généré automatiquement par le Système de Gestion des Événements Aéroportuaires<br>
                    Date de génération: <?php echo $date_generation; ?> | 
                    Nombre d'événements: <?php echo count($evenements); ?>
                </small>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function telechargerPDF() {
            // Simulation du téléchargement PDF
            // Dans un environnement réel, vous pourriez utiliser une bibliothèque comme jsPDF ou wkhtmltopdf
            window.print();
        }
    </script>
</body>
</html>
