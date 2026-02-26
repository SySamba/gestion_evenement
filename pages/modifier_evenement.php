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

// Récupération des données pour les formulaires
$aeroports = $evenement->getAeroports();
$structures = $evenement->getStructures();

$domaines_array = !empty($evenement->domaine_surete) ? explode(',', $evenement->domaine_surete) : [];
$categories_array = !empty($evenement->categories_evenement) ? explode(',', $evenement->categories_evenement) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Événement - <?php echo htmlspecialchars($evenement->titre_evenement); ?></title>
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
            padding: 20px 0;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color)) !important;
            color: white !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            border: none;
        }
        
        .btn-outline-secondary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-secondary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .header-section, .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
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
                        <i class="fas fa-edit me-3"></i>
                        Modifier l'Événement
                    </h1>
                    <p class="header-subtitle"><?php echo htmlspecialchars($evenement->titre_evenement); ?></p>
                </div>
                <div class="col-md-2 text-end">
                    <a href="details_evenement.php?id=<?php echo $evenement->id; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Formulaire de modification -->
        <div class="card fade-in-up">
            <div class="card-header">
                <i class="fas fa-edit me-2"></i>Modifier l'Événement
            </div>
            <div class="card-body">
                <form id="modifierForm" action="../actions/modifier_evenement.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $evenement->id; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Aéroport</label>
                            <select class="form-select" name="nom_aeroport" id="nom_aeroport_edit">
                                <option value="">Sélectionner un aéroport</option>
                                <?php foreach($aeroports as $aeroport): ?>
                                    <option value="<?php echo $aeroport['code']; ?>" <?php echo ($aeroport['code'] == $evenement->nom_aeroport) ? 'selected' : ''; ?>>
                                        <?php echo $aeroport['code'] . ' - ' . $aeroport['nom']; ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="AUTRES" <?php echo ($evenement->nom_aeroport == 'AUTRES') ? 'selected' : ''; ?>>Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="autre_aeroport_container_edit" style="display: <?php echo ($evenement->nom_aeroport == 'AUTRES') ? 'block' : 'none'; ?>;">
                            <label class="form-label">Nom de l'aéroport</label>
                            <input type="text" class="form-control" name="autre_aeroport" id="autre_aeroport_edit" placeholder="Entrer le nom de l'aéroport" value="<?php echo ($evenement->nom_aeroport == 'AUTRES' && !empty($evenement->autre_aeroport)) ? htmlspecialchars($evenement->autre_aeroport) : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lieu *</label>
                            <input type="text" class="form-control" name="lieu" value="<?php echo htmlspecialchars($evenement->lieu); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Structure *</label>
                            <select class="form-select" name="structure" required>
                                <option value="">Sélectionner une structure</option>
                                <?php foreach($structures as $structure): ?>
                                    <option value="<?php echo $structure['nom']; ?>" <?php echo ($structure['nom'] == $evenement->structure) ? 'selected' : ''; ?>>
                                        <?php echo $structure['nom']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Titre de l'Événement *</label>
                            <input type="text" class="form-control" name="titre_evenement" value="<?php echo htmlspecialchars($evenement->titre_evenement); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" class="form-control" name="date_evenement" value="<?php echo $evenement->date_evenement; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Heure *</label>
                            <input type="time" class="form-control" name="heure_evenement" value="<?php echo $evenement->heure_evenement; ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type d'Aéronef</label>
                            <input type="text" class="form-control" name="type_aeronef" value="<?php echo htmlspecialchars($evenement->type_aeronef); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Immatriculation</label>
                            <input type="text" class="form-control" name="immatriculation" value="<?php echo htmlspecialchars($evenement->immatriculation); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phase de Vol</label>
                            <input type="text" class="form-control" name="phase_vol" value="<?php echo htmlspecialchars($evenement->phase_vol); ?>" placeholder="Ex: Décollage, Montée, Croisière...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type d'Exploitation</label>
                            <input type="text" class="form-control" name="type_exploitation" value="<?php echo htmlspecialchars($evenement->type_exploitation); ?>" placeholder="Ex: Commercial, Privé, Militaire...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Classe de l'Événement *</label>
                        <select class="form-select" name="classe_evenement" required>
                            <option value="">Sélectionner</option>
                            <option value="EVENEMENT MINEUR" <?php echo ($evenement->classe_evenement == 'EVENEMENT MINEUR') ? 'selected' : ''; ?>>Événement Mineur</option>
                            <option value="incident" <?php echo ($evenement->classe_evenement == 'incident') ? 'selected' : ''; ?>>Incident</option>
                            <option value="acte d intervention illicite" <?php echo ($evenement->classe_evenement == 'acte d intervention illicite') ? 'selected' : ''; ?>>Acte d'Intervention Illicite</option>
                        </select>
                    </div>

                    <!-- Domaines de Sûreté -->
                    <div class="mb-4">
                        <label class="form-label">Domaine Sûreté *</label>
                        <p class="text-muted small">À quelle(s) catégorie(s) l'événement aurait-il / a-t-il pu conduire :</p>
                        <div class="row">
                            <div class="col-md-6 checkbox-section">
                                <?php 
                                $domaines_surete = [
                                    'surete cote ville' => 'Sûreté côté ville',
                                    'passagers et bagages de cabine' => 'Passagers et bagages de cabine',
                                    'personnel' => 'Personnel',
                                    'equipage' => 'Équipage',
                                    'controle d acces' => 'Contrôle d\'accès',
                                    'bagages de soute' => 'Bagages de soute',
                                    'provisions de bord' => 'Provisions de bord',
                                    'fournitures d aeroport' => 'Fournitures d\'aéroport',
                                    'surete en vol' => 'Sûreté en vol',
                                    'protection des aeroports' => 'Protection des aéroports'
                                ];
                                $i = 1;
                                foreach($domaines_surete as $value => $label): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="<?php echo $value; ?>" id="ds<?php echo $i; ?>" <?php echo in_array($value, $domaines_array) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="ds<?php echo $i; ?>"><?php echo $label; ?></label>
                                    </div>
                                <?php $i++; endforeach; ?>
                            </div>
                            <div class="col-md-6 checkbox-section">
                                <?php 
                                $domaines_surete2 = [
                                    'protection des aeronefs au sol' => 'Protection des aéronefs au sol',
                                    'fret et courrier' => 'Fret et courrier',
                                    'services de controle aerien' => 'Services de contrôle aérien',
                                    'technologie et systemes d information' => 'Technologie et systèmes d\'information',
                                    'drones' => 'Drones',
                                    'manpads ou autres armes a distance' => 'Manpads ou autres armes à distance',
                                    'laser' => 'Laser',
                                    'informations sensibles' => 'Informations sensibles',
                                    'aviation generale/AEROCLUB' => 'Aviation générale/AEROCLUB',
                                    'autres' => 'Autres (préciser)'
                                ];
                                foreach($domaines_surete2 as $value => $label): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="domaine_surete[]" value="<?php echo $value; ?>" id="ds<?php echo $i; ?>" <?php echo in_array($value, $domaines_array) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="ds<?php echo $i; ?>"><?php echo $label; ?></label>
                                    </div>
                                <?php $i++; endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description de l'Événement *</label>
                        <textarea class="form-control" name="description_evenement" rows="4" required><?php echo htmlspecialchars($evenement->description_evenement); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Analyse et Gestion du Risque</label>
                        <textarea class="form-control" name="analyse_gestion_risque" rows="3"><?php echo htmlspecialchars($evenement->analyse_gestion_risque); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mesures Prises *</label>
                        <textarea class="form-control" name="mesures_prises" rows="3" required><?php echo htmlspecialchars($evenement->mesures_prises); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Probabilité du Risque *</label>
                            <select class="form-select" name="probabilite_risque" required>
                                <option value="">Sélectionner</option>
                                <option value="1" <?php echo ($evenement->probabilite_risque == '1') ? 'selected' : ''; ?>>1 - Extrêmement Improbable</option>
                                <option value="2" <?php echo ($evenement->probabilite_risque == '2') ? 'selected' : ''; ?>>2 - Improbable</option>
                                <option value="3" <?php echo ($evenement->probabilite_risque == '3') ? 'selected' : ''; ?>>3 - Éloigné</option>
                                <option value="4" <?php echo ($evenement->probabilite_risque == '4') ? 'selected' : ''; ?>>4 - Occasionnel</option>
                                <option value="5" <?php echo ($evenement->probabilite_risque == '5') ? 'selected' : ''; ?>>5 - Fréquent</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gravité du Risque *</label>
                            <select class="form-select" name="gravite_risque" required>
                                <option value="">Sélectionner</option>
                                <option value="E" <?php echo ($evenement->gravite_risque == 'E') ? 'selected' : ''; ?>>E - Négligeable</option>
                                <option value="D" <?php echo ($evenement->gravite_risque == 'D') ? 'selected' : ''; ?>>D - Mineur</option>
                                <option value="C" <?php echo ($evenement->gravite_risque == 'C') ? 'selected' : ''; ?>>C - Majeur</option>
                                <option value="B" <?php echo ($evenement->gravite_risque == 'B') ? 'selected' : ''; ?>>B - Dangereux</option>
                                <option value="A" <?php echo ($evenement->gravite_risque == 'A') ? 'selected' : ''; ?>>A - Catastrophique</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="details_evenement.php?id=<?php echo $evenement->id; ?>" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gérer l'affichage du champ personnalisé pour l'aéroport "Autres"
        document.getElementById('nom_aeroport_edit').addEventListener('change', function() {
            const autreContainer = document.getElementById('autre_aeroport_container_edit');
            const autreInput = document.getElementById('autre_aeroport_edit');
            
            if (this.value === 'AUTRES') {
                autreContainer.style.display = 'block';
                autreInput.required = true;
            } else {
                autreContainer.style.display = 'none';
                autreInput.required = false;
                autreInput.value = '';
            }
        });

        // Validation du formulaire
        document.getElementById('modifierForm').addEventListener('submit', function(e) {
            // Vérifier les domaines de sûreté
            const domainesChecked = document.querySelectorAll('input[name="domaine_surete[]"]:checked');
            if (domainesChecked.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins un domaine de sûreté');
                return false;
            }
        });
    </script>
</body>
</html>
