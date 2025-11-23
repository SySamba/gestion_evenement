<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Evenement.php';

if($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    $evenement = new Evenement($db);

    // Assignation des valeurs
    $evenement->nom_aeroport = $_POST['nom_aeroport'];
    $evenement->lieu = $_POST['lieu'];
    $evenement->structure = $_POST['structure'];
    $evenement->titre_evenement = $_POST['titre_evenement'];
    $evenement->date_evenement = $_POST['date_evenement'];
    $evenement->heure_evenement = $_POST['heure_evenement'];
    $evenement->type_aeronef = $_POST['type_aeronef'] ?? null;
    $evenement->immatriculation = $_POST['immatriculation'] ?? null;
    $evenement->phase_vol = $_POST['phase_vol'] ?? null;
    $evenement->type_exploitation = $_POST['type_exploitation'] ?? null;
    $evenement->classe_evenement = $_POST['classe_evenement'];
    $evenement->domaine_surete = isset($_POST['domaine_surete']) ? implode(',', $_POST['domaine_surete']) : null;
    $evenement->categories_evenement = isset($_POST['categories_evenement']) ? implode(',', $_POST['categories_evenement']) : null;
    $evenement->description_evenement = $_POST['description_evenement'];
    $evenement->analyse_gestion_risque = $_POST['analyse_gestion_risque'] ?? null;
    $evenement->mesures_prises = $_POST['mesures_prises'];
    $evenement->probabilite_risque = $_POST['probabilite_risque'];
    $evenement->gravite_risque = $_POST['gravite_risque'];

    // Création de l'événement
    if($evenement->create()) {
        header("Location: ../index.php?success=1");
    } else {
        header("Location: ../index.php?error=1");
    }
} else {
    header("Location: ../index.php");
}
?>
