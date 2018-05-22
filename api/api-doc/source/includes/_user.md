# Utilisateur

## Créer un compte

Se créer un compte utilisateur.

### Requête HTTP

`POST /api/user`

### Paramètres POST

> Exemple de requête

```json
{
  "first_name": "Karl",
  "last_name": "Marx",
  "email": "karl.marx@unite.org",
  "password": "C4P1T4L1ST_P1G_01k_01k"
}

```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
first_name | Prénom de l'utilisateur à créer. | Requis, 255 caractères max.
last_name | Nom de l'utilisateur à créer. | Requis, 255 caractères max.
email | Identifiant (courriel) de l'utilisateur à créer. | Requis, courriel valide.
password| Mot de passe de l'utilisateur à créer. | Requis, entre 6 et 20 caratères.

### Format de réponse

> Exemple de réponse

```json
{
    "first_name": "Pierre-Olivier",
    "last_name": "Brillant",
    "email": "pierreolivier@gmail.com"
}

```

Champ | Description
--------- | -----------
first_name | Prénom de l'utilisateur créé.
last_name | Nom de l'utilisateur créé.
email | Identifiant (courriel) de l'utilisateur créé.

## Connection

S'authentifier dans l'application.

### Requête HTTP

`POST /oauth/token`

### Paramètres POST

> Exemple de requête

```json
{
  "grant_type":"password",
  "client_id" : "2",
  "client_secret": "2euundsZe439sGf6M4K6UkzRs7gakncVG0rNEye9",
  "username": "dimitri@bolshevik.ru",
  "password": "Passw0rd!",
  "scope": ""
}
```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
grant_type | Type d'authentification pour oauth2. Nous utilisons "password" pour notre application | Requis
client_id | Identifiant du client d'authentification oauth2. | Requis
client_secret | Mot de passe généré du client oauth2. | Requis
username | Identifiant (courriel) de l'utilisateur. | Requis
password | Mot de passe de l'utilisateur. | Requis

### Format de réponse

> Exemple de réponse

```json
{
    "token_type": "Bearer",
    "expires_in": 31536000,
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImZhNDg(...)",
    "refresh_token": "def50200b2b02aa7c25d452bf2362c5199532ff0fa261b712a(...)"
}

```

Champ | Description
--------- | -----------
token_type | Type de token retourné. Dans le contexte de l'application, le token sera de type 'Bearer'.
expire_in | Temps en seconde avant l'expiration du token. Passé ce délai il sera impossible de l'utiliser.
access_token | Token unique à inclure avec toutes les requêtes nécessitant un authentification.
refresh_token | Token unique à utiliser pour étendre la durée de la validitée du access_token.

## Déconnexion

Se déconnecter dans l'application

### Requête HTTP

`POST /api/user/logout`

Cette requête ne nécessite aucun paramètre. Nous retrouvons simplement l'utilisateur à partir du token d'authentication.

### Format de réponse

> Exemple de réponse

```json
[]
```

La réponse de la suppression de utilisateur est vide.


## Supprimer

Supprimer un utilisateur authentifié

### Requête HTTP

`DELETE /api/user`

Cette requête ne nécessite aucun paramètre. Nous retrouvons simplement l'utilisateur à partir du token d'authentication.

### Format de réponse

> Exemple de réponse

```json
[]
```

La réponse de la suppression de utilisateur est vide.
