// Gestion du formulaire de connexion et d'inscription

document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loggedInArea = document.getElementById('loggedInArea');
    const statusMessage = document.getElementById('statusMessage');
    
    // Boutons
    const resetBtn = document.getElementById('resetBtn');
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const cancelRegisterBtn = document.getElementById('cancelRegisterBtn');
    const logoutBtn = document.getElementById('logoutBtn');
    
    // Champs
    const regPassword = document.getElementById('reg_password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    // Vérifier si le user est déjà connecté
    checkSession();
    
    // Événements des boutons
    resetBtn.addEventListener('click', resetForm);
    registerBtn.addEventListener('click', showRegisterForm);
    cancelRegisterBtn.addEventListener('click', showLoginForm);
    logoutBtn.addEventListener('click', logout);
    
    // Soumission du formulaire de connexion
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        login();
    });
    
    // Soumission du formulaire d'inscriptio
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        register();
    });
    
    // Indicateur de force du mot de passe
    if (regPassword) {
        regPassword.addEventListener('input', checkPasswordStrength);
    }
    
    // Fonctions
    
    function showMessage(message, type) {
        statusMessage.textContent = message;
        statusMessage.className = 'message show ' + type;
        
        // Masquer après 5 secondes
        setTimeout(() => {
            statusMessage.classList.remove('show');
        }, 5000);
    }
    
    function resetForm() {
        loginForm.reset();
        showMessage('Formulaire réinitialisé', 'info');
    }
    
    function showRegisterForm() {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        statusMessage.classList.remove('show');
    }
    
    function showLoginForm() {
        registerForm.style.display = 'none';
        loginForm.style.display = 'block';
        registerForm.reset();
        strengthBar.className = 'strength-bar';
        strengthText.textContent = '';
        statusMessage.classList.remove('show');
    }
    
    function checkPasswordStrength() {
        const password = regPassword.value;
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        
        strengthBar.className = 'strength-bar';
        
        if (strength <= 2) {
            strengthBar.classList.add('weak');
            strengthText.textContent = 'Faible';
            strengthText.style.color = '#f44336';
        } else if (strength <= 3) {
            strengthBar.classList.add('medium');
            strengthText.textContent = 'Moyen';
            strengthText.style.color = '#ff9800';
        } else {
            strengthBar.classList.add('strong');
            strengthText.textContent = 'Fort';
            strengthText.style.color = '#4CAF50';
        }
    }
    
    async function login() {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const csrfToken = loginForm.querySelector('input[name="csrf_token"]').value;
        
        if (!username || !password) {
            showMessage('Veuillez remplir tous les champs', 'error');
            return;
        }
        
        loginBtn.classList.add('loading');
        
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=login&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&csrf_token=${encodeURIComponent(csrfToken)}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => {
                    showLoggedInArea(data.username);
                }, 1000);
            } else {
                showMessage(data.message, 'error');
            }
        } catch (error) {
            showMessage('Erreur de connexion au serveur', 'error');
            console.error('Erreur:', error);
        } finally {
            loginBtn.classList.remove('loading');
        }
    }
    
    async function register() {
        const username = document.getElementById('reg_username').value;
        const password = document.getElementById('reg_password').value;
        const email = document.getElementById('reg_email').value;
        const csrfToken = registerForm.querySelector('input[name="csrf_token"]').value;
        
        if (!username || !password) {
            showMessage('Veuillez remplir tous les champs obligatoires', 'error');
            return;
        }
        
        if (password.length < 8) {
            showMessage('Le mot de passe doit contenir au moins 8 caractères', 'error');
            return;
        }
        
        const submitBtn = document.getElementById('submitRegisterBtn');
        submitBtn.classList.add('loading');
        
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=register&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&email=${encodeURIComponent(email)}&csrf_token=${encodeURIComponent(csrfToken)}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => {
                    showLoginForm();
                }, 2000);
            } else {
                showMessage(data.message, 'error');
            }
        } catch (error) {
            showMessage('Erreur de connexion au serveur', 'error');
            console.error('Erreur:', error);
        } finally {
            submitBtn.classList.remove('loading');
        }
    }
    
    async function logout() {
        const csrfToken = loginForm.querySelector('input[name="csrf_token"]').value;
        
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=logout&csrf_token=${encodeURIComponent(csrfToken)}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => {
                    hideLoggedInArea();
                }, 1000);
            }
        } catch (error) {
            showMessage('Erreur de déconnexion', 'error');
            console.error('Erreur:', error);
        }
    }
    
    async function checkSession() {
        const csrfToken = loginForm.querySelector('input[name="csrf_token"]').value;
        
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=checkSession&csrf_token=${encodeURIComponent(csrfToken)}`
            });
            
            const data = await response.json();
            
            if (data.logged_in) {
                showLoggedInArea(data.username);
            }
        } catch (error) {
            console.error('Erreur de vérification de session:', error);
        }
    }
    
    function showLoggedInArea(username) {
        loginForm.style.display = 'none';
        registerForm.style.display = 'none';
        loggedInArea.style.display = 'block';
        document.getElementById('displayUsername').textContent = username;
    }
    
    function hideLoggedInArea() {
        loggedInArea.style.display = 'none';
        loginForm.style.display = 'block';
        loginForm.reset();
    }
});
