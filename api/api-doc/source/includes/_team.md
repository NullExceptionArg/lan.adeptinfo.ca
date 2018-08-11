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

Créer une demande pour joindre une équipe

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