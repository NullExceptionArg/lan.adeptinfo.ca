# API LAN de l'ADEPT

Cet api représente le backend complet du site web du LAN de l'ADEPT. Il rassemble donc le côté utilisateur, ainsi que le côté administrateur du site.

## Information générale

 - Version de Lumen: 5.6
 - Documentation Lumen: https://lumen.laravel.com/docs/5.6 
 - Documentation Laravel: https://laravel.com/docs/5.6
 - Documentation de l'API: https://adept-informatique.github.io/lan.adeptinfo.ca/


## Développer en local

### Outils recommandés

 - Un IDE polyvalent pour développer en PHP (ex: atom, sublime, PhpStorm, VsCode, etc...)
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
    - Veuillez contacter un administrateur du projet pour avoir une configuration de .env préremplie.
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
 - Configurez PHP Storm pour écouter le débogeur (Bouton  côté de démarrer).
 - Démarrez le serveur.
 
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
    
## Développer avec Homestead (vagrant)
Homestead est un environnement de développement fourni par les développeurs de Laravel. L'objectif de homestead est de fournir un environement de développement standardisé qui est garanti de fonctionner avec Laravel (et Lumen). Ce qui signifie qu'aucune configuration ou installation de package n'est nécessaire pour commencer à développer une fois que l'environnement est lancé. Pour plus d'information sur Homestead et vagrant, vous pouvez lire les ressources suivantes:
  - [Homestead](https://laravel.com/docs/5.6/homestead)
  - [Vagrant](https://www.vagrantup.com/docs/index.html)

 ### Outils requis
   - PHP 7.2
  - [Composer](https://getcomposer.org/)
  - [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
  - [Vagrant](https://www.vagrantup.com/downloads.html)
  
### Installation de Homestead
Les configurations de la VM sont déjà dans le projet, à la racine sous `Vagrantfile` et `after.sh`. Cependant certaines informations doivent être fournies par l'utilisateur.
  - *N'oubliez pas d'activer les technologies de virtualisation dans votre BIOS: vt-x pour Intel, et amd-v pour AMD.*
  - Si vous n'avez pas encore de clé ssh, vous devez en générer une. (Si vous ne savez pas ce que c'est, c'est probablement que vous n'en avez pas)
    - Voici les instructions sous linux (et probablement mac)
    - Dans un terminal, exécutez `ssh-keygen -t rsa -b 4096 -C "votre_courriel@example.com"
    - Exécutez eval `"$(ssh-agent -s)"`
    - Exécutez `ssh-add -k ~/.ssh/id_rsa`
  - Avec un terminal de commande, se placer à la racine du projet API
  - Exécuter `composer install`
  - Exécuter `php vendor/bin/homestead make`. Un fichier nommé Homestead.yaml devrait avoir été généré. Si vous ouvrez ce fichier, vous devriez voir quelques informations sur la configuration de votre projet.
  - Vous ne devriez pas en avoir besoin, mais si vous désirez accéder à la machine virtuelle créée, simplement taper  `vagrant ssh`.
  
  ### Déboguer avec PhpStorm
  - Configurez PHP Storm pour écouter le débogeur (Bouton  côté de démarrer)
  - Ajoutez et faites le point d'arrêt que vous voulez atteindre et votre navigateur ou depuis Postman, accédez à l'adresse qui attendra le point d'arrêt
  - Une fenêtre contextuelle devrait apparaître. Dans la section en bas, sélectionnez la première option ((...)`/api/public/index.php`) et appuyez sur ACCEPT
  - Veuillez suivre les prochaines étapes uniquement si le point de d'arrêt n'a pas été atteint.
  - Une erreur devrait appraître dans le log d'événements, avec des liens. Sélectionnez `PHP|Server`. Si l'erreur ne s'est pas affiché, simplement naviguer vers `Settings/Language & Framework/PHP/Servers`
  - Dans la hiérarchie de fichiers qui s'affichent, à droite de l'entrée qui indique ((...)`lan.adeptinfo.ca/api`), cliquer, et entrer `home/vagrant/code`
  - Cliquez sur APPLIQUER et fermez la fenêtre.
  - Vos points d'arrêt devraient maintenant être atteints
