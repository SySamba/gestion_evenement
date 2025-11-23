<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Evenement.php';

$database = new Database();
$db = $database->getConnection();
$evenement = new Evenement($db);

// Récupération des filtres
$filters = [];
if(isset($_GET['aeroport']) && !empty($_GET['aeroport'])) {
    $filters['aeroport'] = $_GET['aeroport'];
}
if(isset($_GET['structure']) && !empty($_GET['structure'])) {
    $filters['structure'] = $_GET['structure'];
}
if(isset($_GET['classe']) && !empty($_GET['classe'])) {
    $filters['classe'] = $_GET['classe'];
}
if(isset($_GET['date_debut']) && !empty($_GET['date_debut'])) {
    $filters['date_debut'] = $_GET['date_debut'];
}
if(isset($_GET['date_fin']) && !empty($_GET['date_fin'])) {
    $filters['date_fin'] = $_GET['date_fin'];
}
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Récupération des événements
$stmt = $evenement->read($filters);
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Données pour les filtres
$aeroports = $evenement->getAeroports();
$structures = $evenement->getStructures();

header('Content-Type: application/json');
echo json_encode([
    'evenements' => $evenements,
    'total' => count($evenements)
]);
?>
