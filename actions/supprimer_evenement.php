<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Evenement.php';

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    $evenement = new Evenement($db);
    
    $evenement->id = $_GET['id'];
    
    if($evenement->delete()) {
        header("Location: ../index.php?deleted=1");
    } else {
        header("Location: ../index.php?error_delete=1");
    }
} else {
    header("Location: ../index.php");
}
?>
