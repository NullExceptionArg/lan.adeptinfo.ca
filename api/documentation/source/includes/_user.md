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

## Se connecter avec Facebook

Se connecter ou créer un compte utilisateur en se connectant avec Facebook.
Il est à noter qu'un compte facebook peut s'ajouter à un compte qui a été créé avec Laravel ou Google. 
Aucune manipulation supplémentaire n'est nécessaire pour agencer des méthodes de connection. 
Tout est géré par l'API.

### Requête HTTP

`POST /api/user/facebook`

### Paramètres POST

> Exemple de requête

```json
{
  "access_token": "EAAe1dhSpTRoBAJx1pm9uYg52QxZBkPZC7ACtLg2XWkFLBttr2MnTqxXr5tLeZACcZB7MjZCTKfgXZDGhUgQDpxM1iieJGarqOQiZCOB3kKgOHpEa5Ucp2UzOJRGwNww3srmR4rpYwJg7CrrbECmAXnD6DwczeJhhLFhdg4rTZB8agswdQEGTdabxfDzABuMo4ZAPMxhkKxakdvNwZDZD"
}

```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
access_token | Token retourné par une authentification côté client, avec Facebook. | Requis.

### Format de réponse

> Exemple de réponse

```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjMxNWFlMmE0NWJiNmRjYTQ3MGI2OGY0YThmYzU3ZWI5IThhN2NiY2YzNzNjYTNiZmRkNWMyMmRjMmM5MzA3M2Q4MjU5MWRhNTg0YjJhZThkIn0.eyJhdWQiOiIxIiwianRpOjoiMzE1YWUyYTQ0MmI2ZGNhNDcwYjY4ZjRhOGZjNTdlYjkxOGE3Y2JjZjM3M2NhM2JmZGQ1YzIyZGMyYzkzMDczZDgyNTkxZGE1ODRiMmFlOGQiLCJpYXQiOjE1MzM2MDU5OTYsIm5iZiI6MTUzMzYwNTk5NiwiZXhwIjoxNTY1MTQxOTk2LCJzdWIiOiI1Iiwic2NvcGVzIjpbXX0.PL0YprwvBISWFHDw8fATGU5utLfShxeEqEmfbI6gzdESw59EitzlgwU6aQkrY-2v7SI0V8tg7EpeQga42HJlQbw2LylpLX-jdVyvbFp6fXNFkUI_vRhrKV9n0S-mc-iluN4Px7PfZnVofa4vDyinhW2SP9MnrnISrPVEmqOpePvOIf2q5WfqmKve7LexGioqVAHk1EefgV4ySTQVUHRbwA9NQA1-sVJ1TQz3ZfBuTQDR7Zq5y9m9XOrPIipIKawGad_wJ6eS5oCpWIf4UlWlzwg72YwiNf_EjnHNwSNnceuj7xvDcQ1khyNo7XVrkT_xTdRbv774tEUxi_z2Ktw8h-aHYnSEuw6AtPwNRJjq_7ubTk_3yyXYq2Fk30lGp_o7zlJN6vGPDVsKNV-oixJVj58f3F4gSeHxbUVj8ukvMg7n786swRw22iaVFLTMV3RemrWhqEtLGeue15apYAq_dqApuhIzCK24DhCobRiLbyEotpyTNaXJtdBRTqv77W-vf7ySemajsgIiNasiDpHm_P-eC8DYIoMSqrofWBitquKl5tmAfT_UCaZvj-z2AzQteBmql3rySJNAh_Ot8aapJOF1XLamFpybffB1faL7NP30isNG0rZe6jpBPwU5D-S0lUeUPjwod2OO7SeoMMWZoi0HcsLYN_uAyJOCTNgvOy4"
}
```

Champ | Description
--------- | -----------
token | Token unique à inclure avec toutes les requêtes nécessitant un authentification.

## Se connecter avec Google

Se connecter ou créer un compte utilisateur en se connectant avec Google.
Il est à noter qu'un compte google peut s'ajouter à un compte qui a été créé avec Laravel ou Facebook. 
Aucune manipulation supplémentaire n'est nécessaire pour agencer des méthodes de connection. 
Tout est géré par l'API.

### Requête HTTP

`POST /api/user/google`

### Paramètres POST

> Exemple de requête

```json
{
  "access_token": "EAAe1dhSpTRoBAJx1pm9uYg52QxZBkPZC7ACtLg2XWkFLBttr2MnTqxXr5tLeZACcZB7MjZCTKfgXZDGhUgQDpxM1iieJGarqOQiZCOB3kKgOHpEa5Ucp2UzOJRGwNww3srmR4rpYwJg7CrrbECmAXnD6DwczeJhhLFhdg4rTZB8agswdQEGTdabxfDzABuMo4ZAPMxhkKxakdvNwZDZD"
}

```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
access_token | Token retourné par une authentification côté client, avec Google. | Requis.

### Format de réponse

> Exemple de réponse

```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjMxNWFlMmE0NWJiNmRjYTQ3MGI2OGY0YThmYzU3ZWI5IThhN2NiY2YzNzNjYTNiZmRkNWMyMmRjMmM5MzA3M2Q4MjU5MWRhNTg0YjJhZThkIn0.eyJhdWQiOiIxIiwianRpOjoiMzE1YWUyYTQ0MmI2ZGNhNDcwYjY4ZjRhOGZjNTdlYjkxOGE3Y2JjZjM3M2NhM2JmZGQ1YzIyZGMyYzkzMDczZDgyNTkxZGE1ODRiMmFlOGQiLCJpYXQiOjE1MzM2MDU5OTYsIm5iZiI6MTUzMzYwNTk5NiwiZXhwIjoxNTY1MTQxOTk2LCJzdWIiOiI1Iiwic2NvcGVzIjpbXX0.PL0YprwvBISWFHDw8fATGU5utLfShxeEqEmfbI6gzdESw59EitzlgwU6aQkrY-2v7SI0V8tg7EpeQga42HJlQbw2LylpLX-jdVyvbFp6fXNFkUI_vRhrKV9n0S-mc-iluN4Px7PfZnVofa4vDyinhW2SP9MnrnISrPVEmqOpePvOIf2q5WfqmKve7LexGioqVAHk1EefgV4ySTQVUHRbwA9NQA1-sVJ1TQz3ZfBuTQDR7Zq5y9m9XOrPIipIKawGad_wJ6eS5oCpWIf4UlWlzwg72YwiNf_EjnHNwSNnceuj7xvDcQ1khyNo7XVrkT_xTdRbv774tEUxi_z2Ktw8h-aHYnSEuw6AtPwNRJjq_7ubTk_3yyXYq2Fk30lGp_o7zlJN6vGPDVsKNV-oixJVj58f3F4gSeHxbUVj8ukvMg7n786swRw22iaVFLTMV3RemrWhqEtLGeue15apYAq_dqApuhIzCK24DhCobRiLbyEotpyTNaXJtdBRTqv77W-vf7ySemajsgIiNasiDpHm_P-eC8DYIoMSqrofWBitquKl5tmAfT_UCaZvj-z2AzQteBmql3rySJNAh_Ot8aapJOF1XLamFpybffB1faL7NP30isNG0rZe6jpBPwU5D-S0lUeUPjwod2OO7SeoMMWZoi0HcsLYN_uAyJOCTNgvOy4"
}
```

Champ | Description
--------- | -----------
token | Token unique à inclure avec toutes les requêtes nécessitant un authentification.



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

## Lister les utilisateurs

Lister l'ensemble des utilisateurs selon des filtres, un ordre et de la pagination

### Requête HTTP

`GET /api/user`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
query_string | Terme à rechercher parmis le nom, le prénom, et le courriel de l'utilisateur | string, 255 caractères max.
order_column | Colonne selon laquelle les résultats seront ordonnés. Par défaut: last_name | Doit être l'une des entrées suivantes: last_name, first_name, email.
order_direction | Ordre de classement selon lequel les résultats seront ordonnés, soit en ordre croissant (asc), soit en ordre décroissant (desc). Par défaut asc | Doit être l'une des entrées suivantes: asc, desc.
items_per_page | Nombre de résultats à inclure par page | Nombre, minimum: 1, maximum: 75.
current_page | Page courante de recherche | Nombre, minimum: 1.

### Format de réponse

> Exemple de réponse

```json
{
    "data": [
        {
            "first_name": "Wilfrid",
            "last_name": "Oberbrunner",
            "email": "dayton47@hermiston.com"
        },
        {
            "first_name": "Wilfred",
            "last_name": "Altenwerth",
            "email": "kariane.glover@wyman.com"
        },
        {
            "first_name": "Wilfred",
            "last_name": "Ferry",
            "email": "romaguera.ocie@hackett.com"
        },
        {
            "first_name": "Wiley",
            "last_name": "Morar",
            "email": "lschultz@ernser.com"
        },
        {
            "first_name": "Weston",
            "last_name": "Hoppe",
            "email": "ullrich.florencio@yahoo.com"
        }
    ],
    "pagination": {
        "total": 1001,
        "count": 5,
        "per_page": 5,
        "current_page": 4,
        "total_pages": 201
    }
}

```

Champ | Description
--------- | -----------
data | Liste des utilisateurs retournées par la recherche. Voir Data.
pagination | Informations liées à la pagination. Voir Pagination.

#### Data
Champ | Description
--------- | -----------
first_name | Prénom de l'utilisateur.
last_name | Nom de famille de l'utilisateur.
email | Courriel de l'utilisateur.

#### Pagination
Champ | Description
--------- | -----------
total | Nombre d'utilisateurs trouvés.
count | Nombre d'utilisateur sur la page courante.
per_page | Nombre d'utilisateurs par page.
current_page | Page courante.
total_pages | Nombre total de pages.

## Détails d'un utilisateur

Détails d'un utilisateur et son historique pour un LAN.

### Requête HTTP

`POST /api/user/details`

### Paramètres POST

> Exemple de requête

```json
{
  "email": "karl.marx@unite.org",
  "lan_id": 1 
}
```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
email | Courriel de l'utilisateur à rechercher | integer.
lan_id | Lan dans lequel on souhaite trouver les détails de l'utilisateur. Par défaut: lan courant | requis.

### Format de réponse

> Exemple de réponse

```json
{
    "full_name": "Karl Marx",
    "email": "karl.marx@unite.org",
    "current_place": null,
    "place_history": [
        {
            "seat_id": "A-1",
            "lan": "Bolshevik Revolution",
            "reserved_at": "2018-07-10 22:06:10",
            "arrived_at": null,
            "left_at": null,
            "canceled_at": "2018-07-11 05:47:10"
        },
        {
            "seat_id": "A-1",
            "lan": "Bolshevik Revolution",
            "reserved_at": "2018-07-11 11:16:05",
            "arrived_at": "2018-07-11 06:22:27",
            "left_at": "2018-07-11 07:23:42",
            "canceled_at": "2018-07-12 15:42:11"
        }
    ]
}

```

Champ | Description
--------- | -----------
full_name | Nom complet de l'utilisateur.
email | Courriel de l'utilisateur.
current_place | Place courante de l'utilisateur.
place_history | Historique des places de l'utilisateur. Voir Historique des places.

#### Historique des places
Champ | Description
--------- | -----------
seat_id | Place de l'historique.
lan | Nom du LAN de l'historique.
reserved_at | Moment où l'utilisateur a réservé sa place.
arrived_at | Moment où l'utilisateur est arrivé sur place.
left_at | Moment où l'utilisateur a quitté.
canceled_at | Moment où l'utilisateur a annulé sa réservation.


## Sommaire d'un utilisateur

Informations sommaires d'un utilisateur.

### Requête HTTP

`GET /api/user/summary`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN pour lequel on souhaite obtenir les informations de l'utilisateur. Si paramètre n'est pas spécifié, on retourne le LAN courant. | integer.

### Format de réponse

> Exemple de réponse

```json
{
    "first_name": "Karl",
    "last_name": "Marx",
    "request_count": 5
}
```

Champ | Description
--------- | -----------
first_name | Prénom de l'utilisateur.
last_name | Nom de l'utilisateur.
request_count | Demandes cummulées pour entrer dans les équipes d'un utilisateur (qui est chef).