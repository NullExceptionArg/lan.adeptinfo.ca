# Authentification

Notre API utilise un système de token basé sur oauth2 pour s'authentifier à l'API.

Pour chaques requêtes qui nécessite d'être autorisée, veuillez inclure dans le header de votre requête le token qui vous a été remis avec la requête d'autentification. Le header devrait ressembler à ceci:

`Authorization: Bearer prolétaire`

<aside class="notice">
Vous devez remplacer <code>prolétaire</code> par votre token d'authentification.
</aside>
