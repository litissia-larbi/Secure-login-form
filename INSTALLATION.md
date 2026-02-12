
### 1. Prérequis
- XAMPP, WAMP, MAMP ou serveur LAMP
- Navigateur web 

### 2. Installation

1. **Téléchargez et installez XAMPP** : https://www.apachefriends.org/

2. **Démarrez Apache et MySQL** dans le panneau de contrôle XAMPP

3. **Copiez le projet** :
   ```
   Copiez le dossier "secure-login-form" dans :
   C:\xampp\htdocs\  (Windows)
   /Applications/XAMPP/htdocs/  (Mac) 
   ```

4. **Créez la base de données** :
   - Allez sur http://localhost/phpmyadmin
   - Cliquez sur "Nouveau" pour créer une base de données
   - Nommez-la `secure_login`
   - Cliquez sur l'onglet "Importer"
   - Sélectionnez le fichier `database.sql` du projet
   - Cliquez sur "Exécuter"

5. **Configurez la connexion** :
   - Ouvrez `config/database.php`
   - Modifiez si nécessaire (par défaut ça marche avec XAMPP)

6. **Testez** :
   - Allez sur http://localhost/secure-login-form/
   - Connectez-vous avec : admin / Admin123!

### 3. Compte de test

- **Identifiant** : `admin`
- **Mot de passe** : `Admin123!`

### 4. Résolution de problèmes

**Erreur "Unable to connect to database"**
- Vérifiez que MySQL est démarré
- Vérifiez les paramètres dans `config/database.php`

**Page blanche**
- Activez l'affichage des erreurs dans php.ini
- Vérifiez les logs : `xampp/apache/logs/error.log`

**Erreur 404**
- Vérifiez que le dossier est au bon endroit
- Vérifiez que Apache est démarré



