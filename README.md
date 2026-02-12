# Formulaire d'Identification Sécurisé

Devoir 1 - Système d'authentification + pratiques de sécurité

## Description

Ce devoir implémente un système complet d'authentification sécurisé avec :
- Connexion utilisateur
- Création de compte
- Gestion de session
- Protection contre les attaques courantes

## Mesures de Sécurité Implémentées

### 1. **Hashage des mots de passe (Bcrypt)**
- Les mots de passe ne sont JAMAIS stockés en clair
- Utilisation de `password_hash()` et `password_verify()` de PHP
- Algorithme bcrypt avec salage automatique

### 2. **Protection contre les injections SQL**
- Utilisation de requêtes préparées (PDO)
- Paramètres bindés pour toutes les requêtes
- Aucune concaténation de chaînes dans les requêtes SQL

### 3. **Protection CSRF (Cross-Site Request Forgery)**
- Token CSRF unique par session
- Vérification du token pour chaque action
- Régénération régulière des tokens

### 4. **Limitation des tentatives de connexion**
- Maximum 5 tentatives de connexion échouées
- Verrouillage du compte pour 15 minutes après 5 échecs
- Enregistrement de toutes les tentatives avec IP

### 5. **Sessions sécurisées**
- Cookies HTTPOnly (protection contre XSS)
- Régénération de l'ID de session
- Timeout de session
- Paramètres de sécurité stricts

### 6. **Validation des entrées**
- Sanitisation de toutes les entrées utilisateur
- Validation côté client ET serveur
- Filtrage des caractères spéciaux

### 7. **Autres mesures**
- Indicateur de force du mot de passe
- Minimum 8 caractères pour les mots de passe
- Gestion des erreurs sans divulgation d'informations sensibles

## Installation

### Prérequis
- Serveur web (Apache/Nginx)
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- phpMyAdmin (optionnel, pour la gestion de la base de données)

### Étape 1 : Télécharger le projet

```bash
git clone [URL_DE_VOTRE_REPO]
cd secure-login-form
```

### Étape 2 : Créer la base de données

1. Ouvrez phpMyAdmin
2. Créez une nouvelle base de données nommée `secure_login`
3. Importez le fichier `database.sql` :
   - Cliquez sur la base de données `secure_login`
   - Allez dans l'onglet "Importer"
   - Sélectionnez le fichier `database.sql`
   - Cliquez sur "Exécuter"

**Alternative en ligne de commande :**
```bash
mysql -u root -p < database.sql
```

### Étape 3 : Configuration

Modifiez le fichier `config/database.php` avec vos paramètres :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'secure_login');
define('DB_USER', 'root');           // Votre user MySQL
define('DB_PASS', '');               // Votre password  MySQL
```

### Étape 4 : Déployer sur le serveur web

- **XAMPP/WAMP** : Copiez le dossier dans `htdocs/`
- **Serveur Linux** : Copiez dans `/var/www/html/`

### Étape 5 : Accéder à l'application

Ouvrez votre navigateur et allez à :
```
http://localhost/secure-login-form/
```

##  Compte de Test

Un compte administrateur est créé automatiquement :

- **Identifiant** : `admin`
- **Mot de passe** : `Admin123!`


##  Structure du Projet

```
secure-login-form/
│
├── config/
│   └── database.php          # Configuration de la base de données
│
├── includes/
│   └── session.php           # Gestion des sessions sécurisées
│
├── css/ 
| └── style.css            # Styles de l'interface
│
|── images/
|  └── logo.png
│
├── js/
│   └── script.js            # Logique JavaScript
│
├── index.php                # Page principale
├── auth.php                 # API d'authentification
├── database.sql             # Script de création de la BDD
└── README.md                # Ce fichier
```

## Utilisation

### Connexion
1. Entrez votre identifiant et mot de passe
2. Cliquez sur "Se connecter"
3. En cas de succès, vous serez connecté

### Créer un compte
1. Cliquez sur "Créer un compte"
2. Remplissez le formulaire
3. Le mot de passe doit contenir au moins 8 caractères
4. Cliquez sur "Créer le compte"

### Reset
Le bouton "Réinitialisé" efface tous les champs du formulaire.

### Déconnexion
Cliquez sur "Déconnexion" pour terminer votre session.

##  Fonctionnalités de Sécurité en Action

### Protection contre les tentatives multiples
- Après 5 tentatives échouées, le compte est verrouillé pendant 15 minutes
- Un message indique le temps restant avant de pouvoir réessayer

### Indicateur de force du mot de passe
- **Faible** (rouge) : Moins de 8 caractères ou simple
- **Moyen** (orange) : 8+ caractères avec quelques critères
- **Fort** (vert) : 8+ caractères avec majuscules, minuscules, chiffres et caractères spéciaux

##  Tests

### Test de connexion
1. Utilisez le compte de test : `admin` / `Admin123!`
2. Vérifiez que la connexion fonctionne
3. Testez la déconnexion

### Test de création de compte
1. Créez un nouveau compte avec un identifiant unique
2. Essayez de créer un compte avec le même identifiant (devrait échouer)
3. Testez avec un mot de passe trop court (devrait échouer)

### Test de sécurité
1. Essayez de vous connecter 6 fois avec un mauvais mot de passe
2. Vérifiez que le compte est verrouillé
3. Attendez 15 minutes ou réinitialisez manuellement dans la base de données

## Technologies Utilisées

- **Frontend** :
  - HTML5
  - CSS3 
  - JavaScript (ES6+)
  - Fetch API pour les requêtes AJAX

- **Backend** :
  - PHP 7.4+
  - PDO pour la base de données
  - Sessions PHP

- **Base de données** :
  - MySQL 5.7+
  - InnoDB Engine
  - Charset UTF8MB4

##  Auteur
Litissia LARBI 
Devoir réalisé dans le cadre du module " Théorie de l'information appliquée à la 
sécurité des systèmes"

##  Support

En cas de problème :

1. Vérifiez que PHP et MySQL sont bien installés
2. Vérifiez les permissions des fichiers
3. Consultez les logs d'erreurs PHP
4. Vérifiez la connexion à la BDD

