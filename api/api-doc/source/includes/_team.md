# Équipe

## Créer une équipe

Créer une nouvelle équipe pour participer à un tournoi.

### Requête HTTP

`POST /api/team`

### POST Params

> Exemple de requête

```json
{
	"tournament_id": 1,
	"user_tag_id": 1,
	"name": "WorkersUnite",
	"tag": "PRO"
}
```

L'utilisateur qui créer une équipe en devient le chef automatiquement.

### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi dans lequel l'utilisateur veut créer son équipe. | Requis
user_tag_id | Id du tag sous lequel l'utilisateur souhaite créer et rejoindre l'équipe. | Requis, l'utilisateur peut seulement être dans un tournoi une fois.
name | Nom de l'équipe. | Requis, string, 255 caractères max, le nom doit être unique pour le tournoi.
tag | Nom du tag. | String, 5 caractères max, le tag doit être unique pour le tournoi.

### Format de réponse

> Exemple de réponse

```json
{
	"id": 1,
	"tournament_id": 1,
	"name": "WorkersUnite",
	"tag": "PRO"
}
```

Paramètre | Description
--------- | -----------
tournament_id | Id du tournoi dans lequel l'utilisateur a créer son équipe
user_tag_id | Id du tag sous lequel l'utilisateur a créer et rejoint l'équipe.
name | Nom de l'équipe créée.
tag | Nom du tag créée.

## Créer une demande pour joindre une équipe

Créer une demande pour joindre une équipe

### Requête HTTP

`POST /api/team/request`

### POST Params

> Exemple de requête

```json
{
	"team_id": 1,
	"tag_id": 1
}
```

### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
team_id | Id de l'équipe que l'utilisateur souhaite rejoindre | Requis
tag_id | Id du tag sous lequel l'utilisateur souhaite rejoindre l'équipe. | Requis, l'utilisateur peut seulement être dans un tournoi une fois.

### Format de réponse

> Exemple de réponse

```json
{
	"id": 1,
	"tournament_id": 1,
	"name": "WorkersUnite",
	"tag": "PRO"
}
```

Paramètre | Description
--------- | -----------
team_id | Id de l'équipe dans laquelle l'utilisateur a créé sa demande.
tag_id | Id du tag sous lequel l'utilisateur a créer sa demande.

## Obtenir les équipes d'un utilisateur

Obtenir les équipes d'un utilisateur.

### Requête HTTP

`GET /api/team/user`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN d'où l'utilisateur veut obtenir ses tournois. Si paramètre n'est pas spécifié, on retourne le LAN courant. | Integer.

### Format de réponse

> Exemple de réponse

```json
[
    {
        "id": 1,
        "name": "WorkersUnite",
        "tag": "PRO",
        "players_reached": 1,
        "players_to_reach": 5,
        "tournament_name": "October",
        "requests": 13,
        "player_state": "not-confirmed"
    }
]
```

Paramètre | Description
--------- | -----------
id | Id de l'équipe.
name | Nom de l'équipe.
tag | Tag de l'équipe.
players_reached | Nombre de joueurs dans l'équipe.
players_to_reach | Nombre de joueurs à atteindre (Propriété du tournoi).
tournament_name | Nom du tournoi.
requests | Nombre de demandes pour faire parti de l'équipe.
player_state | État du joueur dans l'équipe. Voir Player State

#### Player State
Paramètre | Description
--------- | -----------
leader | Le joueur est le chef de l'équipe.
confirmed | Le joueur est dans l'équipe.
not-confirmed | La requête du joueur est en attente de confirmation.

## Obtenir les détails d'une équipe

Obtenir les détails d'une équipe.

### Requête HTTP

`GET /api/team/members`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
team_id | Id de l'équipe dont on cherche les détails. | Integer.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "WorkersUnite",
    "team_tag": "PRO",
    "user_tags": [
        {
            "id": 1,
            "tag": "PROL",
            "first_name": "Karl",
            "last_name": "Marx",
            "is_leader": true
        },
        {
            "id": 2,
            "tag": "KEK",
            "first_name": "Vladimir",
            "last_name": "Lenin",
            "is_leader": false
        }
    ],
    "requests": [
        {
            "id": 3,
            "tag": "EXDE",
            "first_name": "Leon",
            "last_name": "Trotsky"
        }
    ]
}
```

Paramètre | Description
--------- | -----------
id | Id de l'équipe.
name | Nom de l'équipe.
tag_team | Tag de l'équipe.
user_tags | Joueurs qui sont dans l'équipe. Voir User Tags
requests | Requêtes pour entrer dans l'équipe. (Seulement visible pour le chef de l'équipe). Voir Requests.

#### User Tags
Paramètre | Description
--------- | -----------
id | Id de la relation entre le tag et l'équipe.
tag_id | Id du tag du joueur.
tag_name | Nom du tag du joueur.
first_name | Prénom du joueur.
last_name | Nom de famille du joueur.
is_leader | Si le joueur est le chef de l'équipe.

#### Requests
Paramètre | Description
--------- | -----------
id | Id de la requête.
tag_id | Id du tag du joueur qui demande à entrer dans l'équipe.
tag_name | Nom du tag du joueur qui demande à entrer dans l'équipe.
first_name | Prénom du joueur qui demande à entrer dans l'équipe.
last_name | Nom de famille du joueur qui demande à entrer dans l'équipe.

## Changer de chef

Le chef donne son titre à un autre joueur de l'équipe.

### Requête HTTP

`PUT /api/team/leader`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
team_id | Id de l'équipe dans laquelle le chef veut donner son titre. | Integer.
tag_id | Id du tag du joueur à qui le chef veut donner son titre. | Integer.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 2,
    "name": "PRO"
}
```

Paramètre | Description
--------- | -----------
id | Id du tag du nouveau chef.
name | Nom du tag du nouveau chef.

## Accepter une requête

Le chef accepte une requête pour entrer dans l'équipe.

### Requête HTTP

`POST /api/team/accept`

### POST Params

> Exemple de requête

```json
{
	"request_id": 1,
	"team_id": 1
}
```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
request_id | Id de la requête pour joindre l'équipe. | Integer.
team_id | Id de l'équipe dans laquelle le chef veut accepter la requête. | Integer.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 2,
    "name": "PRO"
}
```

Paramètre | Description
--------- | -----------
id | Id du tag du nouveau membre de l'équipe.
name | Nom du tag du nouveau membre de l'équipe.