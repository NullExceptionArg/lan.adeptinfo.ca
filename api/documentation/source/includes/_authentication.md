# Authentification

Notre API utilise un système de token basé sur oauth2 pour s'authentifier à l'API.

Pour chaques requêtes qui nécessite d'être authentifié, veuillez inclure dans le header de votre requête le token remis à l'authentification, soit avec un compte de l'[API](#connection), un compte [Google](#se-connecter-avec-google), ou un compte [Facebook](#se-connecter-avec-facebook). Le header devrait ressembler à ceci:

`Authorization: Bearer votre-token`

<aside class="notice">
Vous devez remplacer <code>votre-token</code> par votre token d'authentification.
</aside>
