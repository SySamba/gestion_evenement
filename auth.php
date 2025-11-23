<?php
/**
 * Script d'authentification et de gestion des sessions
 * Gestion des Événements Aéroportuaires - ANACIM
 */

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifier si l'utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifier si l'utilisateur a un rôle spécifique
 * @param string $role
 * @return bool
 */
function hasRole($role) {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Vérifier si l'utilisateur est admin
 * @return bool
 */
function isAdmin() {
    return hasRole('admin');
}

/**
 * Obtenir les informations de l'utilisateur connecté
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'],
        'nom' => $_SESSION['user_nom'],
        'prenom' => $_SESSION['user_prenom'],
        'role' => $_SESSION['user_role']
    ];
}

/**
 * Rediriger vers la page de connexion si non connecté
 * @param string $redirect_url URL de redirection après connexion
 */
function requireLogin($redirect_url = null) {
    if (!isLoggedIn()) {
        $login_url = 'login.php';
        if ($redirect_url) {
            $login_url .= '?redirect=' . urlencode($redirect_url);
        }
        header('Location: ' . $login_url);
        exit();
    }
}

/**
 * Rediriger vers la page de connexion si l'utilisateur n'a pas le rôle requis
 * @param string $required_role
 */
function requireRole($required_role) {
    requireLogin();
    
    if (!hasRole($required_role)) {
        header('Location: index.php?error=access_denied');
        exit();
    }
}

/**
 * Déconnecter l'utilisateur
 */
function logout() {
    // Détruire toutes les variables de session
    $_SESSION = array();
    
    // Détruire le cookie de session si il existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Détruire la session
    session_destroy();
    
    // Rediriger vers la page de connexion
    header('Location: login.php?message=logged_out');
    exit();
}

/**
 * Générer un hash sécurisé pour un mot de passe
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Vérifier un mot de passe
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Générer un token CSRF
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier un token CSRF
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Obtenir le nom complet de l'utilisateur
 * @return string
 */
function getUserFullName() {
    if (!isLoggedIn()) {
        return '';
    }
    
    return $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
}

/**
 * Obtenir l'initiale de l'utilisateur pour l'avatar
 * @return string
 */
function getUserInitials() {
    if (!isLoggedIn()) {
        return '';
    }
    
    $prenom = $_SESSION['user_prenom'] ?? '';
    $nom = $_SESSION['user_nom'] ?? '';
    
    return strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
}

/**
 * Enregistrer une action dans les logs (optionnel)
 * @param string $action
 * @param string $details
 */
function logUserAction($action, $details = '') {
    if (!isLoggedIn()) {
        return;
    }
    
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $_SESSION['user_id'],
        'user_email' => $_SESSION['user_email'],
        'action' => $action,
        'details' => $details,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    // Ici vous pouvez ajouter la logique pour enregistrer dans une table de logs
    // ou dans un fichier de log
}

// Protection contre les attaques de fixation de session
if (isLoggedIn()) {
    // Régénérer l'ID de session périodiquement
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}
?>
