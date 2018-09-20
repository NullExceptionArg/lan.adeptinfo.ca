# API LAN de l'ADEPT

Cet api représente le backend complet du site web du LAN de l'ADEPT. Il rassemble donc le côté utilisateur, ainsi que le côté administrateur du site.

## Sommaire
0. [Sommaire](#sommaire)
1. [Information général](#information-générale)
2. [Développer en local](#développer-en-local)
3. [Lignes directrices du pour le développement de l'API](#lignes-directrices-pour-le-développement-de-lapi)

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
  - Une instance de MySQL server, un utilisateur qui possède tous les droits, ainsi que deux bases de données: `lanadept` et `lanadepttest`.

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
    - Username: `karl.marx@unite.org`
    - Password: `Passw0rd!`
    - Client ID: `2`
    - Client Secret: `{{client-secret}}`
    - Scope: 
    - Client Authentication: `Send as Basic Auth header`
 - Créer un environnement pour le projet avec les paramètres suivants
    - server-address: `http://localhost:8000`
    - client-secret: La clé qui a été généré après avoir entré la commande `php artisan passport:install`. La clé est aussi dans la base de donnée sous la table `oauth_clients`.
    
 ## Lignes directrices pour le développement de l'API.
 - Chaques accès HTTP doit être au minimum testé selon l'ensemble de ses limites, et ses différents cas fonctionnels (Ex: paramètre absent et présent). Voir le dossier `Unit/Controller`.
 - Chaques services doit être au minimum testé selon ses limites, et ses différents cas fonctionnels (Ex: paramètre absent et présent). Voir le dossier `Unit/Services`. (Devrait être relativement similaire aux tests de l'accès HTTP).
 - Chaques repository doit être au minimum testé selon sa fonctionnalité principale.
 - Pour chaques accès administrateur:
     - Le nom et la description doivent être définis dans les fichiers de ressource `resource/lang/en/permission` et `resource/lang/fr/permission`, selon la convention de nommage suivante pour le nom à afficher pour la permission: `display-name-"nom-de-la-permission"` et pour la description: `description-"nom-de-la-permission"`.
     - Le nom interne (unique) doit être ajouté dans `app/Console/Commands/GeneratePermissions.php`, sous la fonction `getPermissions()`, en [kebab case](http://wiki.c2.com/?KebabCase).
     - Pour créer les permissions de l'API dans la base de donnée, exécuter la commande `php artisan lan:permissions`. Il est à noter que l'application des deux étapes précédentes est cruciale à ce que le système de permission puisse être fonctionnel. La permière étape permet permet l'affichage pour le client des permissions, alors que la deuxième étape permet la dispinibilité  l'interne, soit dans l'API de la permission.
     - La vérification de la permission doit être faite dans le controlleur.
