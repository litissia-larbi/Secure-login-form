<?php
require_once 'includes/session.php';
secureSessionStart();
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <!-- Logo -->
            <div class="logo">
                <div class="logo-circle">
                <img src="images/logo.png" alt="Logo" style="width: 92px; height: auto;">
            </div>
                <h1>Connexion</h1>
            </div>
            
            <div id="statusMessage" class="message"></div>

            <!-- Formulaire de connexion -->
            <form id="loginForm" class="form-container">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="input-group">
                    <label for="username">Identifiant</label>
                    <input type="text" id="username" name="username" placeholder="Entrez votre identifiant" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                </div>

                <div class="button-group">
                    <button type="button" id="resetBtn" class="btn btn-secondary">Réinitialisé</button>
                    <button type="submit" id="loginBtn" class="btn btn-primary">Se connecter</button>
                </div>

                <div class="form-footer">
                    <p class="form-footer-text">Pas encore de compte ?</p>
                    <a id="registerBtn" class="form-link">Créer un compte</a>
                </div>
            </form>

            <!-- Formulaire d'inscription -->
            <form id="registerForm" class="form-container" style="display: none;">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="input-group">
                    <label for="reg_username">Identifiant</label>
                    <input type="text" id="reg_username" name="username" placeholder="Choisissez un identifiant" required>
                </div>

                <div class="input-group">
                    <label for="reg_email">Email (optionnel)</label>
                    <input type="email" id="reg_email" name="email" placeholder="votre@email.com">
                </div>

                <div class="input-group">
                    <label for="reg_password">Mot de passe</label>
                    <input type="password" id="reg_password" name="password" placeholder="Minimum 8 caractères" required>
                </div>

                <div class="password-strength">
                    <div id="strengthBar" class="strength-bar"></div>
                    <small id="strengthText"></small>
                </div>

                <div class="button-group">
                    <button type="button" id="cancelRegisterBtn" class="btn btn-secondary">Retour</button>
                    <button type="submit" id="submitRegisterBtn" class="btn btn-primary">Créer mon compte</button>
                </div>
            </form>

            <!-- Zone POUR connectée  -->
            <div id="loggedInArea" style="display: none;">
                <div class="welcome-message">
                    <h2>Bienvenue <span id="displayUsername"></span></h2>
                    <p>Vous êtes connecté avec succès</p>
                    <button id="logoutBtn" class="btn btn-danger">Déconnexion</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>