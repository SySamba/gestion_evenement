<?php
require_once __DIR__ . '/../config/database.php';

class Evenement {
    private $conn;
    private $table_name = "evenements";

    public $id;
    public $nom_aeroport;
    public $autre_aeroport;
    public $lieu;
    public $structure;
    public $titre_evenement;
    public $date_evenement;
    public $heure_evenement;
    public $type_aeronef;
    public $immatriculation;
    public $phase_vol;
    public $type_exploitation;
    public $classe_evenement;
    public $domaine_surete;
    public $categories_evenement;
    public $description_evenement;
    public $analyse_gestion_risque;
    public $mesures_prises;
    public $probabilite_risque;
    public $gravite_risque;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un événement
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nom_aeroport=:nom_aeroport, autre_aeroport=:autre_aeroport, lieu=:lieu, structure=:structure, 
                     titre_evenement=:titre_evenement, date_evenement=:date_evenement, 
                     heure_evenement=:heure_evenement, type_aeronef=:type_aeronef, 
                     immatriculation=:immatriculation, phase_vol=:phase_vol, 
                     type_exploitation=:type_exploitation, classe_evenement=:classe_evenement, 
                     domaine_surete=:domaine_surete, categories_evenement=:categories_evenement, 
                     description_evenement=:description_evenement, analyse_gestion_risque=:analyse_gestion_risque, 
                     mesures_prises=:mesures_prises, probabilite_risque=:probabilite_risque, 
                     gravite_risque=:gravite_risque";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->nom_aeroport = htmlspecialchars(strip_tags($this->nom_aeroport));
        $this->autre_aeroport = $this->autre_aeroport ? htmlspecialchars(strip_tags($this->autre_aeroport)) : null;
        $this->lieu = htmlspecialchars(strip_tags($this->lieu));
        $this->structure = htmlspecialchars(strip_tags($this->structure));
        $this->titre_evenement = htmlspecialchars(strip_tags($this->titre_evenement));
        $this->description_evenement = htmlspecialchars(strip_tags($this->description_evenement));
        $this->mesures_prises = htmlspecialchars(strip_tags($this->mesures_prises));

        // Liaison des paramètres
        $stmt->bindParam(":nom_aeroport", $this->nom_aeroport);
        $stmt->bindParam(":autre_aeroport", $this->autre_aeroport);
        $stmt->bindParam(":lieu", $this->lieu);
        $stmt->bindParam(":structure", $this->structure);
        $stmt->bindParam(":titre_evenement", $this->titre_evenement);
        $stmt->bindParam(":date_evenement", $this->date_evenement);
        $stmt->bindParam(":heure_evenement", $this->heure_evenement);
        $stmt->bindParam(":type_aeronef", $this->type_aeronef);
        $stmt->bindParam(":immatriculation", $this->immatriculation);
        $stmt->bindParam(":phase_vol", $this->phase_vol);
        $stmt->bindParam(":type_exploitation", $this->type_exploitation);
        $stmt->bindParam(":classe_evenement", $this->classe_evenement);
        $stmt->bindParam(":domaine_surete", $this->domaine_surete);
        $stmt->bindParam(":categories_evenement", $this->categories_evenement);
        $stmt->bindParam(":description_evenement", $this->description_evenement);
        $stmt->bindParam(":analyse_gestion_risque", $this->analyse_gestion_risque);
        $stmt->bindParam(":mesures_prises", $this->mesures_prises);
        $stmt->bindParam(":probabilite_risque", $this->probabilite_risque);
        $stmt->bindParam(":gravite_risque", $this->gravite_risque);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lire tous les événements
    public function read($filters = []) {
        $query = "SELECT e.*, a.nom as nom_aeroport_complet, a.ville 
                 FROM " . $this->table_name . " e 
                 LEFT JOIN aeroports a ON e.nom_aeroport = a.code 
                 WHERE 1=1";

        // Application des filtres
        if(!empty($filters['aeroport'])) {
            $query .= " AND e.nom_aeroport = :aeroport";
        }
        if(!empty($filters['structure'])) {
            $query .= " AND e.structure = :structure";
        }
        if(!empty($filters['classe'])) {
            $query .= " AND e.classe_evenement = :classe";
        }
        if(!empty($filters['date_debut'])) {
            $query .= " AND e.date_evenement >= :date_debut";
        }
        if(!empty($filters['date_fin'])) {
            $query .= " AND e.date_evenement <= :date_fin";
        }
        if(!empty($filters['search'])) {
            $query .= " AND (e.titre_evenement LIKE :search OR e.description_evenement LIKE :search)";
        }

        $query .= " ORDER BY e.date_evenement DESC, e.heure_evenement DESC";

        $stmt = $this->conn->prepare($query);

        // Liaison des paramètres de filtre
        if(!empty($filters['aeroport'])) {
            $stmt->bindParam(':aeroport', $filters['aeroport']);
        }
        if(!empty($filters['structure'])) {
            $stmt->bindParam(':structure', $filters['structure']);
        }
        if(!empty($filters['classe'])) {
            $stmt->bindParam(':classe', $filters['classe']);
        }
        if(!empty($filters['date_debut'])) {
            $stmt->bindParam(':date_debut', $filters['date_debut']);
        }
        if(!empty($filters['date_fin'])) {
            $stmt->bindParam(':date_fin', $filters['date_fin']);
        }
        if(!empty($filters['search'])) {
            $search_term = "%" . $filters['search'] . "%";
            $stmt->bindParam(':search', $search_term);
        }

        $stmt->execute();
        return $stmt;
    }

    // Lire un événement par ID
    public function readOne() {
        $query = "SELECT e.*, a.nom as nom_aeroport_complet, a.ville 
                 FROM " . $this->table_name . " e 
                 LEFT JOIN aeroports a ON e.nom_aeroport = a.code 
                 WHERE e.id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nom_aeroport = $row['nom_aeroport'];
            $this->autre_aeroport = $row['autre_aeroport'];
            $this->lieu = $row['lieu'];
            $this->structure = $row['structure'];
            $this->titre_evenement = $row['titre_evenement'];
            $this->date_evenement = $row['date_evenement'];
            $this->heure_evenement = $row['heure_evenement'];
            $this->type_aeronef = $row['type_aeronef'];
            $this->immatriculation = $row['immatriculation'];
            $this->phase_vol = $row['phase_vol'];
            $this->type_exploitation = $row['type_exploitation'];
            $this->classe_evenement = $row['classe_evenement'];
            $this->domaine_surete = $row['domaine_surete'];
            $this->categories_evenement = $row['categories_evenement'];
            $this->description_evenement = $row['description_evenement'];
            $this->analyse_gestion_risque = $row['analyse_gestion_risque'];
            $this->mesures_prises = $row['mesures_prises'];
            $this->probabilite_risque = $row['probabilite_risque'];
            $this->gravite_risque = $row['gravite_risque'];
            return true;
        }
        return false;
    }

    // Modifier un événement
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET nom_aeroport=:nom_aeroport, autre_aeroport=:autre_aeroport, lieu=:lieu, structure=:structure, 
                     titre_evenement=:titre_evenement, date_evenement=:date_evenement, 
                     heure_evenement=:heure_evenement, type_aeronef=:type_aeronef, 
                     immatriculation=:immatriculation, phase_vol=:phase_vol, 
                     type_exploitation=:type_exploitation, classe_evenement=:classe_evenement, 
                     domaine_surete=:domaine_surete, categories_evenement=:categories_evenement, 
                     description_evenement=:description_evenement, analyse_gestion_risque=:analyse_gestion_risque, 
                     mesures_prises=:mesures_prises, probabilite_risque=:probabilite_risque, 
                     gravite_risque=:gravite_risque, updated_at=CURRENT_TIMESTAMP
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->nom_aeroport = htmlspecialchars(strip_tags($this->nom_aeroport));
        $this->autre_aeroport = $this->autre_aeroport ? htmlspecialchars(strip_tags($this->autre_aeroport)) : null;
        $this->lieu = htmlspecialchars(strip_tags($this->lieu));
        $this->structure = htmlspecialchars(strip_tags($this->structure));
        $this->titre_evenement = htmlspecialchars(strip_tags($this->titre_evenement));
        $this->description_evenement = htmlspecialchars(strip_tags($this->description_evenement));
        $this->mesures_prises = htmlspecialchars(strip_tags($this->mesures_prises));

        // Liaison des paramètres
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nom_aeroport", $this->nom_aeroport);
        $stmt->bindParam(":autre_aeroport", $this->autre_aeroport);
        $stmt->bindParam(":lieu", $this->lieu);
        $stmt->bindParam(":structure", $this->structure);
        $stmt->bindParam(":titre_evenement", $this->titre_evenement);
        $stmt->bindParam(":date_evenement", $this->date_evenement);
        $stmt->bindParam(":heure_evenement", $this->heure_evenement);
        $stmt->bindParam(":type_aeronef", $this->type_aeronef);
        $stmt->bindParam(":immatriculation", $this->immatriculation);
        $stmt->bindParam(":phase_vol", $this->phase_vol);
        $stmt->bindParam(":type_exploitation", $this->type_exploitation);
        $stmt->bindParam(":classe_evenement", $this->classe_evenement);
        $stmt->bindParam(":domaine_surete", $this->domaine_surete);
        $stmt->bindParam(":categories_evenement", $this->categories_evenement);
        $stmt->bindParam(":description_evenement", $this->description_evenement);
        $stmt->bindParam(":analyse_gestion_risque", $this->analyse_gestion_risque);
        $stmt->bindParam(":mesures_prises", $this->mesures_prises);
        $stmt->bindParam(":probabilite_risque", $this->probabilite_risque);
        $stmt->bindParam(":gravite_risque", $this->gravite_risque);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Supprimer un événement
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obtenir les statistiques
    public function getStatistiques() {
        $stats = [];

        // Total des événements
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Événements par classe
        $query = "SELECT classe_evenement, COUNT(*) as count FROM " . $this->table_name . " GROUP BY classe_evenement";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['par_classe'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Événements par aéroport
        $query = "SELECT e.nom_aeroport, a.nom, COUNT(*) as count 
                 FROM " . $this->table_name . " e 
                 LEFT JOIN aeroports a ON e.nom_aeroport = a.code 
                 GROUP BY e.nom_aeroport ORDER BY count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['par_aeroport'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Événements par mois (12 derniers mois) - Générer tous les mois même ceux sans événements
        $stats['par_mois'] = [];
        
        // Générer les 12 derniers mois
        for ($i = 11; $i >= 0; $i--) {
            $date = new DateTime();
            $date->sub(new DateInterval("P{$i}M"));
            $mois = $date->format('Y-m');
            $moisLabel = $date->format('M Y'); // Format plus lisible
            
            // Compter les événements pour ce mois
            $query = "SELECT COUNT(*) as count 
                     FROM " . $this->table_name . " 
                     WHERE DATE_FORMAT(date_evenement, '%Y-%m') = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$mois]);
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $stats['par_mois'][] = [
                'mois' => $mois,
                'mois_label' => $moisLabel,
                'count' => (int)$count
            ];
        }

        // Évaluation des risques (matrice probabilité x gravité)
        $query = "SELECT 
                    CONCAT(probabilite_risque, gravite_risque) as evaluation_risque,
                    probabilite_risque,
                    gravite_risque,
                    COUNT(*) as count 
                  FROM " . $this->table_name . " 
                  WHERE probabilite_risque IS NOT NULL AND gravite_risque IS NOT NULL
                  GROUP BY probabilite_risque, gravite_risque";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Classifier selon la matrice de risques
        $risque_rouge = ['5A', '5B', '5C', '4A', '4B', '3A'];
        $risque_jaune = ['5D', '5E', '4C', '4D', '4E', '3B', '3C', '3D', '2A', '2B', '2C', '1A'];
        $risque_vert = ['3E', '2D', '2E', '1B', '1C', '1D', '1E'];
        
        $stats['par_gravite'] = [
            ['evaluation' => 'Risque Élevé (Rouge)', 'count' => 0, 'color' => '#dc2626'],
            ['evaluation' => 'Risque Modéré (Jaune)', 'count' => 0, 'color' => '#fbbf24'],
            ['evaluation' => 'Risque Faible (Vert)', 'count' => 0, 'color' => '#10b981']
        ];
        
        foreach ($evaluations as $eval) {
            $code = $eval['evaluation_risque'];
            if (in_array($code, $risque_rouge)) {
                $stats['par_gravite'][0]['count'] += $eval['count'];
            } elseif (in_array($code, $risque_jaune)) {
                $stats['par_gravite'][1]['count'] += $eval['count'];
            } elseif (in_array($code, $risque_vert)) {
                $stats['par_gravite'][2]['count'] += $eval['count'];
            }
        }

        // Événements récents (7 derniers jours)
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE date_evenement >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['recents'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return $stats;
    }

    // Obtenir les aéroports
    public function getAeroports() {
        $query = "SELECT * FROM aeroports ORDER BY nom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les structures
    public function getStructures() {
        $query = "SELECT * FROM structures ORDER BY nom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les domaines de sûreté
    public function getDomainesSuprete() {
        $query = "SELECT * FROM domaines_surete ORDER BY nom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les catégories d'événements
    public function getCategoriesEvenements() {
        $query = "SELECT * FROM categories_evenements ORDER BY nom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
