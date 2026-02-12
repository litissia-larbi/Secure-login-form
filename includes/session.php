<?php
/**
 * Gestion des sessions sécurisées
 */


// Démarrer une session sécurisée
function secureSessionStart() {
    $session_name = 'secure_session_id';
    $secure = false; 
    $httponly = true; 
    
    // Forcer les cookies de session à être sécurisés
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        die("Impossible de garantir la sécurité de la session");
    }
    
    // Paramètres du cookie de session
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        3600, 
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly
    );
    
    session_name($session_name);
    session_start();
    
    // Régénération de  l'ID de session pour éviter la fixation de session
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

// Générationn du  token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifcation du token CSRF
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Vérifier si le user est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
} 

// Déconnexion du user
function logout() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}
?>
