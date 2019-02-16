# Tests fonctionnels

Les tests programmés assurent une qualité et une standardisation dans les données retournées par l'API.
Cependant, certaines fonctionnalités sont difficiles, voir impossibles à tester à l'aide de tests programmés. 
C'est pourquoi il est recommandé que la personne qui installe l'API teste certaines fonctionnalités manuellement, avec Postamn.

## Création de compte

À la création d'un compte, un courriel de confirmation devrait être envoyé au courriel spécifié dans le champs `email` du chemin HTTP `POST /user`.

1. S'assurer que les informations sur le serveur de courriel sont bien spécifiés dans le fichier .env de l'application.
2. Créer un utilisateur en utilisant le chemin HTTP suivant: [Créer un compte](/#creer-un-compte).
3. Copier le lien de confirmation de courriel dans Postman, et y accéder.
4. Le corp de réponse devrait être vide, et le statut HTTP 200.

## Compte Google

Google n'offre pas de service de test pour les utilisateurs comme Facebook le fait.

### Nouvel utilisateur
1. Obtenir un token utilisateur auprès de google.
2. Créer un utilisateur en utilisant le chemin HTTP suivant: [Se connecter avec Google](/#se-connecter-avec-google). Le code HTTP devrait être 201.

### Utilisateur existant (Google)
1. Obtenir un token utilisateur auprès de google, d'un utilisateur déjà existant dans l'application, créé avec [Google](/#se-connecter-avec-google), par exemple celui créé au test précédent.
2. Se connecter à l'application en utilisant le chemin HTTP suivant: [Se connecter avec Google](/#se-connecter-avec-google). Le code HTTP devrait être 200.

### Utilisateur existant (Facebook)
1. Obtenir un token utilisateur auprès de google, d'un utilisateur déjà existant dans l'application, créé avec [Facebook](/#se-connecter-avec-facebook).
2. Se connecter à l'application en utilisant le chemin HTTP suivant: [Se connecter avec Google](/#se-connecter-avec-google). Le code HTTP devrait être 200.

### Utilisateur existant (LAN)
1. Obtenir un token utilisateur auprès de google, d'un utilisateur déjà existant dans l'application, créé avec le site du [LAN](/#creer-un-compte).
2. Se connecter à l'application en utilisant le chemin HTTP suivant: [Se connecter avec Google](/#se-connecter-avec-google). Le code HTTP devrait être 200.

