# API LAN de l'ADEPT

Cet api représente le backend complet du site web du LAN de l'ADEPT. Il rassemble donc le côté utilisateur, ainsi que le côté administrateur du site.

## Information générale

 - Version de Lumen: 5.6
 - Documentation Lumen: https://lumen.laravel.com/docs/5.6 
 - Documentation Laravel: https://laravel.com/docs/5.6
 - Documentation de l'API: https://adept-informatique.github.io/lan.adeptinfo.ca/


## Développer en local

### Outils recommandés

 - Un IDE polyvalent pour développer en PHP (ex: atom, sublime, PhpStorm, etc...)
 - Postman
 - Xdebug
 
 ### Outils requis
  - PHP 7.2
  - [Composer](https://getcomposer.org/)
  - Une instance de MySQL server ainsi qu'un utilisateur qui possède tous les droits.

### Exécuter pour la première fois

 - Avec un terminal de commande, se placer à la racine du projet API
 - Exécuter `composer install` (prend un certain temps)
 - Copier le fichier .env.example pour .env et informer les champs.
    - Les champs avec le préfix `DB_` sont les informations liées  MySQL
    - Veuillez contacter un administrateur du projet pour avoir les clés secrètes de seats.io .
 - Exécuter `php artisan key:generate`
 - Exécuter `php artisan migrate`
 - Exécuter `php artisan passport:install`
 - Exécuter `php -S localhost:8000 -t public`
 - Ouvrir un navigateur à l'URL suivante: [http://localhost:8000](http://localhost:8000)

### Exécuter
 - Avec un terminal de commande, se placer à la racine du projet API
 - Exécuter `php -S localhost:8000 -t public`
 - Ouvrir un navigateur à l'URL suivante: [http://localhost:8000](http://localhost:8000)

### Déboguer avec PhpStorm

 - Sous `Settings/Language & Framework/PHP`:
    - À côté de CLI interpreter, cliquer sur les [...]
    - Cliquer sur + et entrez le chemin vers votre interpreteur PHP. Sur linux ce sera `usr/bin/php` la plupart du temps.
    - Cliquer sur OK.
 - Sous `Settings/Language & Framework/PHP/Debug/DBGp Proxy`
    - IDE key: `PHPSTORM`
    - Host: `127.0.0.1`
    - Port: `9000`
 - Sous `Settings/Language & Framework/PHP/Test Frameworks`:
    - Sous la section `Test Runner`, cocher `Default configuration file:`
    - Sur la ligne `Default configuration file:`, sélectionner le chemin vers le fichier `phpunit.xml` du projet.
 - Configuration Xdebug. Sur linux le chemin est `/etc/php/7.2/cli/conf.d/20-xdebug.ini`
    - [Xdebug]
    - zend_extension=xdebug.so
    - xdebug.remote_autostart=1
    - xdebug.default_enable=1
    - xdebug.remote_port=9001
    - xdebug.remote_host=127.0.0.1
    - xdebug.remote_connect_back=1
    - xdebug.remote_enable=1
    - xdebug.idekey=PHPSTORM
 - Créer une nouvelle configuration "PHP Built-in Web Server"
    - Host: `localhost`
    - Document root: `[...]/lanadept.com/api`
    - Use router script (coché): `[...]/lanadept.com/api/public/index.php`
    - Interpreter options: `-dxdebug.remote_enable=1 -dxdebug.remote_mode=req -dxdebug.remote_port=9000 -dxdebug.remote_host=127.0.0.1`
    - (Optionnel) "Cocher Single Instance Only"
    - Cliquer sur "Apply"
 - Configurer PHP Storm pour écouter le débogeur (Bouton  côté de démarrer).
 - Démarrer le serveur.
 
 ### Utiliser Postman
 Une liste de requête a déjà été montée par le créateur du reposiory. Pour obtenir cette liste simplement contacter [Pierre-Olivier Brillant](https://github.com/PierreOlivierBrillant).
 - Configuration de la fenêtre Get new access token
    - Token name: `Lumen`
    - Grant Type: `Password Credential`
    - Access Token URL `{{server-address}}/oauth/token`
    - Username: `karl.marx@unite.com`
    - Password: `Passw0rd!`
    - Client ID: `2`
    - Client Secret: `{{client-secret}}`
    - Scope: 
    - Client Authentication: `Send as Basic Auth header`
 - Créer un environnement pour le projet avec les paramètres suivants
    - server-address: `http://localhost:8000`
    - client-secret: La clé qui a été généré après avoir entré la commande `php artisan passport:install`. La clé est aussi dans la base de donnée sous la table `oauth_clients`.
