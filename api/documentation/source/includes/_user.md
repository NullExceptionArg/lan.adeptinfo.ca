# Utilisateur

Un utilisateur peut réserver une place, faire partie d'équipes pour participer à des tournoi, et consulter les détails du LAN.
Il peut aussi administrer l'API s'il possède des [permissions](#permission) d'administration.

## Créer un compte utilisateur

Créer un compte utilisateur dans l'API. 

Un courriel de confirmation sera envoyé au nouvel utilisateur. Le courriel devrait contenir un lien pour [confirmer le compte](#confirmer-un-compte).

Un compte de l'API peut s'ajouter à un compte qui a été créé avec [Facebook](#se-connecter-avec-facebook) ou [Google](#se-connecter-avec-google). 
Aucune manipulation supplémentaire n'est nécessaire pour agencer des méthodes de connexion. 
Tout est géré par l'API.

<aside class="notice">
Tant que le compte n'est pas confirmé, l'utilisateur ne peut pas recevoir de token pour intéragir avec l'API.
</aside>

### Requête HTTP

`POST /user`

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
first_name | Prénom de l'utilisateur à créer. |  255 caractères max.
last_name | Nom de l'utilisateur à créer. |  255 caractères max.
email | Identifiant (courriel) de l'utilisateur à créer. |  courriel valide.
password| Mot de passe de l'utilisateur à créer. |  entre 6 et 20 caratères.

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

## Confirmer un compte

Confirmation que le courriel utilisé par l'utilisateur lui appartient bel et bien. Devrait être utilisé en envoyant un courriel à l'utilisateur. 

<aside class="notice">
Tant que le compte n'est pas confirmé, l'utilisateur ne peut pas recevoir de token pour intéragir avec l'API.
</aside>

### Requête HTTP

`GET /user/confirm/{confirmation_code}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
confirmation_code | Code de confirmation secret qui est communiqué directement à l'utilisateur. 

La réponse est vide, mais retourne un statut 200.

## Connexion avec Facebook

Connexion ou création d'un compte utilisateur avec Facebook.

Un compte facebook peut s'ajouter à un compte qui a été créé avec l'[API](#creer-un-compte) ou [Google](#se-connecter-avec-google). 
Aucune manipulation supplémentaire n'est nécessaire pour agencer des méthodes de connexion. 
Tout est géré par l'API.

### Requête HTTP

`POST /user/facebook`

### Paramètres POST

> Exemple de requête

```json
{
  "access_token": "EAAe1dhSpTRoBAJx1pm9uYg52QxZBkPZC7ACtLg2XWkFLBttr2MnTqxXr5tLeZACcZB7MjZCTKfgXZDGhUgQDpxM1iieJGarqOQiZCOB3kKgOHpEa5Ucp2UzOJRGwNww3srmR4rpYwJg7CrrbECmAXnD6DwczeJhhLFhdg4rTZB8agswdQEGTdabxfDzABuMo4ZAPMxhkKxakdvNwZDZD"
}

```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
access_token | Token retourné par une authentification côté client, avec Facebook. |

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

## Connexion avec Google

Connexion ou création d'un compte utilisateur avec Google.

Un compte Google peut s'ajouter à un compte qui a été créé avec l'[API](#creer-un-compte) ou [Facebook](#se-connecter-avec-facebook). 
Aucune manipulation supplémentaire n'est nécessaire pour agencer des méthodes de connexion. 
Tout est géré par l'API.

### Requête HTTP

`POST /user/google`

### Paramètres POST

> Exemple de requête

```json
{
  "access_token": "EAAe1dhSpTRoBAJx1pm9uYg52QxZBkPZC7ACtLg2XWkFLBttr2MnTqxXr5tLeZACcZB7MjZCTKfgXZDGhUgQDpxM1iieJGarqOQiZCOB3kKgOHpEa5Ucp2UzOJRGwNww3srmR4rpYwJg7CrrbECmAXnD6DwczeJhhLFhdg4rTZB8agswdQEGTdabxfDzABuMo4ZAPMxhkKxakdvNwZDZD"
}

```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
access_token | Token retourné par une authentification côté client, avec Google. | 

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


## Connexion

Permet d'obtenir l'accès aux requêtes qui nécessitent que l'utilisateur soit authentifié.

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
grant_type | Type d'authentification pour oauth2. Nous utilisons "password" pour notre application | 
client_id | Identifiant du client d'authentification oauth2. | 
client_secret | Mot de passe généré du client oauth2. | 
username | Identifiant (courriel) de l'utilisateur. | 
password | Mot de passe de l'utilisateur. | 

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

Déconnecter l'utilisateur en rendant invalide le token retourné à lors de la connexion.

### Requête HTTP

`POST /user/logout`

Cette requête ne nécessite aucun paramètre. Nous retrouvons l'utilisateur à partir du token d'authentication.

### Format de réponse

> Exemple de réponse

```json
[]
```

La réponse de la déconnexion de utilisateur est vide.


## Supprimer l'utilisateur

Supprimer un utilisateur authentifié, ainsi que tout ses liens aux autres entités du LAN.

### Requête HTTP

`DELETE /user`

Cette requête ne nécessite aucun paramètre. Nous retrouvons l'utilisateur à partir du token d'authentication.

### Format de réponse

> Exemple de réponse

```json
[]
```

La réponse de la suppression de utilisateur est vide.

## Lister les utilisateurs

Lister l'ensemble des utilisateurs selon des filtres, un ordre et de la pagination

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>get-users</code>, can_be_per_lan <code>true</code>
</aside>


### Requête HTTP

`GET /user`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
query_string | Terme à rechercher parmis le nom, le prénom, et le courriel de l'utilisateur | chaîne de caractères, 255 caractères max.
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
        "total": 5,
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

Détails d'un utilisateur ainsi que son historique pour un LAN.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>get-user-details</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`POST /user/details`

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
email | Courriel de l'utilisateur à rechercher | chaîne de caractères.
lan_id | Lan dans lequel on souhaite trouver les détails de l'utilisateur. Par défaut: lan courant | 

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


## Sommaire de l'utilisateur

Informations sommaires de l'utilisateur courant. (Identité et nombre de requête en attente d'approbation pour les équipes que le joueur dirige)

### Requête HTTP

`GET /user/summary`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN pour lequel on souhaite obtenir les informations de l'utilisateur. Si le paramètre n'est pas spécifié, on retourne le LAN courant. | entier.

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

## Sommaire de l'administrateur

Informations sommaires de l'administrateur. (Identité, s'il administre un ou des tournois, et les permissions qu'il possède)

### Requête HTTP

`GET /admin/summary`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN pour lequel on souhaite obtenir les informations de l'administrateur. Si le paramètre n'est pas spécifié, on retourne le LAN courant. | entier.

### Format de réponse

> Exemple de réponse

```json
{
    "first_name": "Karl",
    "last_name": "Marx",
    "has_tournaments": true,
    "permissions": [
        {
            "id": 39,
            "name": "create-lan"
        }
    ]
}
```

Champ | Description
--------- | -----------
first_name | Prénom de l'utilisateur.
last_name | Nom de l'utilisateur.
has_tournaments | Si l'utilisateur peut modifier certain tournois.
permissions | Permissions administratives que possède l'administrateur pour le LAN. Voir Permissions.

#### Permissions
Champ | Description
--------- | -----------
id | Id de la permission.
name | Nom de la permission.

## Roles d'un administrateur

Rôles globaux et de LAN d'un administrateur.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>get-admin-roles</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`GET /admin/roles`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
email | Courriel de l'utilisateur dont on veut connaître les rôles. Si ce paramètre n'est pas spécifié, on retourne les rôles de l'utilisateur qui fait la requête (L'utilisateur n'a pas besoin d'avoir cette permission à ce moment). | chaîne de caractères.
lan_id | Id du LAN pour lequel l'administrateur souhaite connaître ses rôle. Si le paramètre n'est pas spécifié, on utilise le LAN courant. | entier.

### Format de réponse

> Exemple de réponse

```json
{
    "global_roles": [
        {
            "id": 1,
            "name": "general-admin",
            "display_name": "Administrateur général",
            "description": "Possède toutes les permissions (LAN et globales)"
        }
    ],
    "lan_roles": [
        {
            "id": 1,
            "name": "lan-general-admin",
            "display_name": "Administrateur général de LAN",
            "description": "Possède toutes les permissions pour un LAN"
        }
    ]
}
```

Champ | Description
--------- | -----------
global_roles | Rôles globaux de l'utilisateur. Voir Roles.
lan_roles | Rôles de LAN de l'utilisateur. Voir Roles.

#### Roles
Champ | Description
--------- | -----------
id | Id du rôle.
name | Nom du rôle.
display_name | Nom d'affichage du rôle.
description | Description du rôle.