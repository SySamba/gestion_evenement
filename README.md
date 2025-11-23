# Système de Gestion des Événements Aéroportuaires

## 📋 Description

Système complet de gestion et suivi des événements de sûreté aéroportuaire développé en PHP/MySQL avec une interface moderne Bootstrap 5.

## ✨ Fonctionnalités

### 🎯 Dashboard avec KPI
- **Statistiques en temps réel** : Total événements, événements récents, nombre d'aéroports et structures
- **Graphiques interactifs** : 
  - Répartition par classe d'événement (Doughnut Chart)
  - Événements par aéroport (Bar Chart)
  - Évolution mensuelle (Line Chart)
  - Gravité des risques (Pie Chart)

### ➕ Gestion des Événements
- **Ajout d'événements** avec formulaire complet incluant :
  - Informations obligatoires : Aéroport, lieu, structure, titre, date, heure, classe, description, mesures prises, probabilité et gravité du risque
  - Informations optionnelles : Type d'aéronef, immatriculation, phase de vol, type d'exploitation, analyse du risque
- **Recherche avancée** avec filtres multiples :
  - Par aéroport, structure, classe d'événement
  - Par période (date début/fin)
  - Recherche textuelle dans titre et description
- **Suppression d'événements** avec confirmation
- **Liste complète** des événements avec pagination

### 🏢 Données de Référence
- **6 Aéroports** : DSS (Diass), DKR (Dakar), XLS (Saint-Louis), MAX (Matam), TUD (Tambacounda), KGG (Kédougou)
- **17 Structures** : TSA, LAS, AMARANTE, 2AS, HAAS, AIBD_SA, SMCADDY, SERVAIR, AIR SENEGAL INTERNATIONAL, etc.
- **20 Domaines de sûreté** : Sûreté côté ville, passagers et bagages, personnel, équipage, etc.
- **45+ Catégories d'événements** : Découverte EEI, attaques, cyber-attaques, etc.

### 🎨 Interface Utilisateur
- **Design moderne** avec dégradés et animations CSS
- **Responsive** adapté mobile, tablette et desktop
- **Navigation par onglets** intuitive
- **Cartes KPI** avec icônes et couleurs thématiques
- **Tableaux interactifs** avec tri et filtrage
- **Messages de feedback** pour les actions utilisateur

## 🛠️ Technologies Utilisées

- **Backend** : PHP 8+ avec POO et PDO
- **Base de données** : MySQL 8+
- **Frontend** : HTML5, CSS3, JavaScript ES6+
- **Framework CSS** : Bootstrap 5.3
- **Icônes** : Font Awesome 6.4
- **Graphiques** : Chart.js
- **Polices** : Google Fonts (Inter)

## 📁 Structure du Projet

```
gestion_accident/
├── index.php                 # Page principale avec dashboard
├── database.sql             # Script de création de la base de données
├── README.md                # Documentation
├── config/
│   └── database.php         # Configuration base de données
├── classes/
│   └── Evenement.php        # Classe métier Événement
├── actions/
│   ├── ajouter_evenement.php # Traitement ajout événement
│   └── supprimer_evenement.php # Traitement suppression
├── pages/
│   └── recherche.php        # API de recherche et filtrage
└── assets/
    └── css/
        └── style.css        # Styles personnalisés
```

## 🚀 Installation

### Prérequis
- XAMPP/WAMP/LAMP avec PHP 8+ et MySQL 8+
- Navigateur web moderne

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone [url-du-repo]
   cd gestion_accident
   ```

2. **Configurer la base de données**
   - Démarrer MySQL dans XAMPP
   - Importer le fichier `database.sql` dans phpMyAdmin
   - Vérifier la configuration dans `config/database.php`

3. **Démarrer le serveur**
   - Démarrer Apache dans XAMPP
   - Accéder à `http://localhost/gestion_accident`

## 📊 Base de Données

### Tables principales

#### `evenements`
- **id** : Identifiant unique auto-incrémenté
- **nom_aeroport** : Code aéroport (DSS, DKR, XLS, MAX, TUD, KGG)
- **lieu** : Lieu précis de l'événement
- **structure** : Structure responsable
- **titre_evenement** : Titre descriptif
- **date_evenement** / **heure_evenement** : Horodatage
- **classe_evenement** : EVENEMENT MINEUR | incident | acte d intervention illicite
- **probabilite_risque** : 1-5 (Extrêmement Improbable à Fréquent)
- **gravite_risque** : E-A (Négligeable à Catastrophique)
- **description_evenement** : Description détaillée
- **mesures_prises** : Actions correctives

#### Tables de référence
- **aeroports** : Codes et noms des aéroports
- **structures** : Liste des structures
- **domaines_surete** : Domaines de sûreté
- **categories_evenements** : Catégories d'événements

## 🎯 Utilisation

### Dashboard
- Visualisation des KPI en temps réel
- Graphiques interactifs pour l'analyse des tendances
- Navigation rapide vers les autres fonctionnalités

### Ajouter un Événement
1. Cliquer sur l'onglet "Ajouter Événement"
2. Remplir le formulaire (champs obligatoires marqués *)
3. Sélectionner la probabilité et gravité du risque
4. Cliquer "Enregistrer l'Événement"

### Rechercher des Événements
1. Utiliser l'onglet "Rechercher"
2. Appliquer les filtres souhaités
3. Cliquer "Rechercher" pour afficher les résultats
4. Utiliser "Reset" pour effacer les filtres

### Gérer les Événements
- **Voir** : Bouton œil pour consulter les détails
- **Supprimer** : Bouton corbeille avec confirmation

## 🔧 Configuration

### Base de données
Modifier `config/database.php` pour adapter les paramètres :
```php
private $host = 'localhost';
private $db_name = 'gestion_evenements_aeroport';
private $username = 'root';
private $password = '';
```

### Personnalisation CSS
Variables CSS dans `assets/css/style.css` :
```css
:root {
    --primary-color: #2563eb;
    --secondary-color: #1e40af;
    --success-color: #059669;
    --warning-color: #d97706;
    --danger-color: #dc2626;
}
```

## 📈 Métriques et KPI

### Indicateurs disponibles
- **Total événements** : Compteur global
- **Événements récents** : 7 derniers jours
- **Répartition par classe** : Mineur/Incident/Illicite
- **Répartition par aéroport** : Performance par site
- **Évolution mensuelle** : Tendances sur 12 mois
- **Matrice de risque** : Probabilité × Gravité

### Graphiques
- **Chart.js** pour les visualisations interactives
- **Responsive design** adapté à tous les écrans
- **Couleurs thématiques** selon la criticité

## 🛡️ Sécurité

- **Validation des données** côté client et serveur
- **Protection XSS** avec htmlspecialchars()
- **Requêtes préparées** PDO contre l'injection SQL
- **Nettoyage des entrées** avec strip_tags()

## 🔄 Évolutions Futures

### Fonctionnalités à développer
- [ ] Authentification et gestion des utilisateurs
- [ ] Export des données (PDF, Excel)
- [ ] Notifications automatiques
- [ ] Workflow de validation
- [ ] API REST pour intégrations
- [ ] Module de reporting avancé
- [ ] Géolocalisation des événements
- [ ] Pièces jointes aux événements

### Améliorations techniques
- [ ] Cache Redis pour les performances
- [ ] Tests unitaires PHPUnit
- [ ] Documentation API
- [ ] Logs d'audit
- [ ] Sauvegarde automatique

## 📞 Support

Pour toute question ou problème :
- Consulter cette documentation
- Vérifier les logs d'erreur PHP
- Tester la connexion base de données

## 📄 Licence

Projet développé pour la gestion des événements aéroportuaires.
Tous droits réservés.
