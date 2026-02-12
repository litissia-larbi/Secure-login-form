<?php
require_once 'config/database.php';
require_once 'includes/session.php';

secureSessionStart();

header('Content-Type: application/json');

// Fonction pour nettoyer les inputs 
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Vérifier si le compte est verrouillé
function isAccountLocked($pdo, $username) {
    $stmt = $pdo->prepare("SELECT account_locked_until FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && $user['account_locked_until']) {
        $lockTime = strtotime($user['account_locked_until']);
        if ($lockTime > time()) {
            $minutesLeft = ceil(($lockTime - time()) / 60);
            return [
                'locked' => true,
                'minutes' => $minutesLeft
            ];
        } else {
            // Débloquer le compte
            $stmt = $pdo->prepare("UPDATE users SET account_locked_until = NULL, failed_login_attempts = 0 WHERE username = ?");
            $stmt->execute([$username]);
        }
    }
    
    return ['locked' => false];
}

// Enregistrer une tentative de connexion
function recordLoginAttempt($pdo, $username, $success) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, ?)");
    $stmt->execute([$username, $ip, $success ? 1 : 0]);
    
    if (!$success) {
        // Incrémenter le compteur d'échecs
        $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = failed_login_attempts + 1 WHERE username = ?");
        $stmt->execute([$username]);
        
        // Vérifier si on doit verrouiller le compte (5 tentatives)
        $stmt = $pdo->prepare("SELECT failed_login_attempts FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && $user['failed_login_attempts'] >= 5) {
            // Verrouiller pour 15 minutes
            $lockUntil = date('Y-m-d H:i:s', time() + (15 * 60));
            $stmt = $pdo->prepare("UPDATE users SET account_locked_until = ? WHERE username = ?");
            $stmt->execute([$lockUntil, $username]);
            return ['locked' => true];
        }
    } else {
        // Réinitialiser le compteur en cas de succès
        $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = 0, account_locked_until = NULL, last_login = NOW() WHERE username = ?");
        $stmt->execute([$username]);
    }
    
    return ['locked' => false];
}

// Traitement des requêtes
$action = $_POST['action'] ?? '';

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalide']);
    exit;
}

try {
    $pdo = getDatabase();
    
    switch ($action) {
        case 'login':
            $username = sanitizeInput($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Identifiant et mot de passe requis']);
                exit;
            }
            
            // Vérifier si le compte est verrouillé
            $lockStatus = isAccountLocked($pdo, $username);
            if ($lockStatus['locked']) {
                echo json_encode([
                    'success' => false, 
                    'message' => "Compte verrouillé. Réessayez dans {$lockStatus['minutes']} minute(s)"
                ]);
                exit;
            }
            
           
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie
                recordLoginAttempt($pdo, $username, true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Connexion réussie!',
                    'username' => $user['username']
                ]);
            } else {
                // Échec de connexion
                $lockResult = recordLoginAttempt($pdo, $username, false);
                
                if ($lockResult['locked']) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Trop de tentatives échouées. Compte verrouillé pour 15 minutes.'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Identifiant ou mot de passe incorrect'
                    ]);
                }
            }
            break;
            
        case 'register':
            $username = sanitizeInput($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $email = sanitizeInput($_POST['email'] ?? '');
            
            // Validation
            if (empty($username) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Identifiant et mot de passe requis']);
                exit;
            }
            
            if (strlen($username) < 3 || strlen($username) > 50) {
                echo json_encode(['success' => false, 'message' => 'L\'identifiant doit contenir entre 3 et 50 caractères']);
                exit;
            }
            
            if (strlen($password) < 8) {
                echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
                exit;
            }
            
            // Vérifier si le user existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Cet identifiant existe déjà']);
                exit;
            }
            
            // Hashage  du  passwird  avec bcrypt 
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            // Insérer le nouvel user dans la BDD
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $email]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Compte créé avec succès! Vous pouvez maintenant vous connecter.'
            ]);
            break;
            
        case 'logout':
            logout();
            echo json_encode(['success' => true, 'message' => 'Déconnexion réussie']);
            break;
            
        case 'checkSession':
            if (isLoggedIn()) {
                echo json_encode([
                    'success' => true, 
                    'logged_in' => true,
                    'username' => $_SESSION['username']
                ]);
            } else {
                echo json_encode(['success' => true, 'logged_in' => false]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
    }
    
} catch (Exception $e) {
    error_log("Erreur d'authentification: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur s\'est produite']);
}
?>
