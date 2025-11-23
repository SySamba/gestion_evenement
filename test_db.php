<?php
// Test de connexion à la base de données
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if($db) {
        echo "<h2>✅ Connexion à la base de données réussie !</h2>";
        
        // Vérifier si les tables existent
        $tables = ['evenements', 'aeroports', 'structures', 'domaines_surete', 'categories_evenements'];
        
        foreach($tables as $table) {
            $query = "SHOW TABLES LIKE '$table'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                echo "<p>✅ Table '$table' existe</p>";
            } else {
                echo "<p>❌ Table '$table' n'existe pas</p>";
            }
        }
        
        // Compter les enregistrements dans les tables de référence
        echo "<h3>Données de référence :</h3>";
        
        $query = "SELECT COUNT(*) as count FROM aeroports";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>Aéroports : $count</p>";
        
        $query = "SELECT COUNT(*) as count FROM structures";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>Structures : $count</p>";
        
        $query = "SELECT COUNT(*) as count FROM categories_evenements";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>Catégories d'événements : $count</p>";
        
    } else {
        echo "<h2>❌ Erreur de connexion à la base de données</h2>";
    }
    
} catch(Exception $e) {
    echo "<h2>❌ Erreur : " . $e->getMessage() . "</h2>";
    echo "<p>Veuillez vérifier que :</p>";
    echo "<ul>";
    echo "<li>MySQL est démarré dans XAMPP</li>";
    echo "<li>La base de données 'gestion_evenements_aeroport' existe</li>";
    echo "<li>Le script database.sql a été importé</li>";
    echo "</ul>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
p { margin: 5px 0; }
ul { margin: 10px 0; }
</style>
