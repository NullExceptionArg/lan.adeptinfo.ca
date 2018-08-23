# Tournoi

## Créer un tournoi

Créer un nouveau tournoi.

### Requête HTTP

`POST /api/tournament`

### Query Params

> Exemple de requête

```json
{
	"lan_id": 1,
	"name": "October",
	"tournament_start": "2100-10-11T14:00:00-05:00",
	"tournament_end": "2100-10-11T18:00:00-05:00",
	"players_to_reach": 5,
	"teams_to_reach": 6,
	"rules": "The Bolsheviks seize control of Petrograd.",
	"price": 0
}
```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où le tournoi aura lieu. Si paramètre n'est pas spécifié, on retourne le LAN courant. | integer.

### Paramètres POST

Paramètre | Description | Règles de validation | Défaut
--------- | ----------- | -------------------- | ------
name | Nom du tournoi. | Requis, string, 255 caractères max. |
price | Prix d'entrée du tournoi. | int, min: 0. | 0
tournament_start | Date et heure de début du tournoi. | Requis, après le début du LAN. | 
tournament_end | Date et heure de fin du tournoi. | Requis, date, avant la fin du LAN., après le début du tournoi. |
players_to_reach| Nombre de joueur à atteindre par équipe. | Requis, min: 1, int. |
teams_to_reach |Nombre d'équipes à atteindre pour que le tounoi ait lieu.| Requis, min: 1, int. |
rules | Règlements du tournoi. | String, requis. |

### Format de réponse

> Exemple de réponse

```json
{
    "lan_id": 1,
    "name": "October",
    "tournament_start": "2100-10-11 14:00:00",
    "tournament_end": "2100-10-11 18:00:00",
    "players_to_reach": 5,
    "teams_to_reach": 6,
    "rules": "The Bolsheviks seize control of Petrograd.",
    "price": 0,
    "id": 1
}
```

Champ | Description
--------- | -----------
name | Nom du tournoi. 
price | Prix d'entrée du tournoi.
tournament_start | Date et heure de début du tournoi. 
tournament_end | Date et heure de fin du tournoi.
players_to_reach| Nombre de joueur à atteindre par équipe.
teams_to_reach |Nombre d'équipes à atteindre pour que le tounoi ait lieu.
rules | Règlements du tournoi.

## Supprimer un tournoi

Supprimer un tournoi.

Les équipes, les requêtes pour entrer dans les équipes du tournois et les liaisons entre les tag et les équipes du tournoi seront aussi supprimés.

### Requête HTTP

`DELETE /api/tournament/{tournament_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi que l'administrateur veut supprimer. | integer.

### Format de réponse

> Exemple de réponse

```json
{
    "lan_id": 1,
    "name": "October",
    "tournament_start": "2100-10-11 14:00:00",
    "tournament_end": "2100-10-11 18:00:00",
    "players_to_reach": 5,
    "teams_to_reach": 6,
    "rules": "The Bolsheviks seize control of Petrograd.",
    "price": 0,
    "id": 1
}
```

Champ | Description
--------- | -----------
name | Nom du tournoi. 
price | Prix d'entrée du tournoi.
tournament_start | Date et heure de début du tournoi. 
tournament_end | Date et heure de fin du tournoi.
players_to_reach| Nombre de joueur à atteindre par équipe.
teams_to_reach |Nombre d'équipes à atteindre pour que le tounoi ait lieu.
rules | Règlements du tournoi.

## Tournois d'un organisateur

Obtenir les tournois d'un organisateur.

### Requête HTTP

`GET /api/tournament/all`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN dans lequel l'organisateur souhaite trouver ses LANs. | integer.

### Format de réponse

> Exemple de réponse

```json
[
    {
        "id": 1,
        "name": "October Revolution",
        "tournament_start": "October 2100",
        "tournament_end": "October 2100",
        "current_state": "hidden",
        "teams_reached": 0,
        "teams_to_reach": 6
    }
]
```

Champ | Description
--------- | -----------
id | Id du tournoi. 
name | Nom du tournoi.
tournament_start | Date et heure de début du tournoi. 
tournament_end | Date et heure de fin du tournoi.
current_state| État courant du tournoi. Voir État courant.
teams_reached |Nombre d'équipes complètes atteintes.
teams_to_reach | Nombre d'équipes à atteindre pour que le tournoi ait lieux.

#### État courant
Champ | Description
--------- | -----------
hidden | Caché, est seulement visible pour les organisateurs.
finished | Le tournoi est terminé.
fourthcoming | Le tournoi est à venir.
late | Le tournoi est en retard.
outguessed | Le tournoi est devancé.
running | Le tournoi est en cours.
behindhand | Le tournoi s'éternise (Après l'heure de fin prévue).
unknown | État inconnu. Si jamais vous ou un utilisateur obtient cette réponse, il serait bien de le communiquer à un développeur de l'API.

## Modifier un tournoi

Modifie un tournoi.

### Requête HTTP

`PUT /api/tournament/{tournament_id}`

### Path Params

> Exemple de requête

```json
{
	"name": "Octobers",
	"tournament_start": "2100-10-11T14:00:00-05:00",
	"tournament_end": "2100-10-11T18:00:00-05:00",
	"players_to_reach": 5,
	"teams_to_reach": 6,
	"rules": "The Bolsheviks seize control of Petrograd.",
	"price": 0
}
```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi que l'administrateur veut modifier. | integer.

### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
name | Nom du tournoi. | Requis, string, 255 caractères max.
price | Prix d'entrée du tournoi. | int, min: 0.
state | État courant du LAN. Voir État Courant. | hidden, visible, started, ou finished
tournament_start | Date et heure de début du tournoi. | Requis, après le début du LAN. 
tournament_end | Date et heure de fin du tournoi. | Requis, date, avant la fin du LAN, après le début du tournoi.
players_to_reach| Nombre de joueur à atteindre par équipe. | Requis, min: 1, int.
teams_to_reach |Nombre d'équipes à atteindre pour que le tounoi ait lieu.| Requis, min: 1, int.
rules | Règlements du tournoi. | String, requis.

#### État courant
Champ | Description
--------- | -----------
hidden | Caché, est seulement visible pour les organisateurs.
visible | Est visible pour les utilisateurs.
started | Le tournoi est commencé.
finished | Le tournoi est terminé.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "Octobers",
    "price": 0,
    "tournament_start": "2100-10-11 14:00:00",
    "tournament_end": "2100-10-11 18:00:00",
    "players_to_reach": 5,
    "teams_to_reach": 6,
    "state": "hidden",
    "rules": "The Bolsheviks seize control of Petrograd.",
    "lan_id": 1
}
```

Paramètre | Description
--------- | -----------
name | Nom du tournoi.
price | Prix d'entrée du tournoi. 
state | État courant du LAN. Voir État Courant. 
tournament_start | Date et heure de début du tournoi.
tournament_end | Date et heure de fin du tournoi.
players_to_reach| Nombre de joueur à atteindre par équipe.
teams_to_reach |Nombre d'équipes à atteindre pour que le tounoi ait lieu.
rules | Règlements du tournoi.
lan_id | LAN auquel le tournoi est associé.

#### État courant
Champ | Description
--------- | -----------
hidden | Caché, est seulement visible pour les organisateurs.
visible | Est visible pour les utilisateurs.
started | Le tournoi est commencé.
finished | Le tournoi est terminé.

## Détails d'un tournoi

Détails d'un tournoi

### Requête HTTP

`GET /api/tournament/details/{tournament_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi que l'on veut consulter. | integer.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "October",
    "rules": "The Bolsheviks seize control of Petrograd.",
    "price": 0,
    "tournament_start": "2100-10-11 14:00:00",
    "tournament_end": "2100-10-11 18:00:00",
    "teams_to_reach": 6,
    "teams_reached": 0,
    "players_to_reach": 5,
    "state": "hidden",
    "teams": [
        {
            "id": 1,
            "name": "WorkersUnite",
            "tag": "PRO",
            "players_reached": 1,
            "players": [
                {
                    "tag_id": 2,
                    "tag_name": "PRO",
                    "first_name": "Karl",
                    "last_name": "Marx",
                    "is_leader": false,
                    "reservation_id": 1,
                    "seat_id": "A-1"
                }
            ]
        }
    ]
}
```

Paramètre | Description
--------- | -----------
id | Id du tournoi.
name | Nom du tournoi.
rules | Règles du tournoi. 
price | Prix du tournoi en cents. 
tournament_start | Date et heure du début du tournoi. 
tournament_end | Date et heure de fin du tournoi. 
team_to_reach | Nombre d'équipes complètes à atteindre pour que le tournoi puisse commencer.
team_reached | Nombre d'équipes complètes atteintes.
players_to_reach| Nombre de joueurs à atteindre pour qu'une équipe soit complète.
state | État courant du tournoi. Voir État Courant pour les possibilités et leurs significations.
teams | Voir Team.

#### État courant
Champ | Description
--------- | -----------
hidden | Caché, est seulement visible pour les organisateurs.
visible | Est visible pour les utilisateurs.
started | Le tournoi est commencé.
finished | Le tournoi est terminé.

#### Team
Champ | Description
--------- | -----------
id | Id de l'équipe.
name | Nom de l'équipe.
tag | Tag de l'équipe.
players_reached | Nombre de joueurs atteint.
players | Voir Player.

#### Player
Champ | Description
--------- | -----------
tag_id | Id du tag du joueur.
tag_name | Nom du tag du joueur.
first_name | Prénom du joueur.
last_name | Nom de famille du joueur.
is_leader | Si le joueur est chef de l'équipe.
reservation_id | Id de la réservation, si le joueur en a une pour le LAN.
seat_id | Id de la place du joueur, si le joueur a un réservation pour le LAN.