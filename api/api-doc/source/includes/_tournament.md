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
lan_id | Id du LAN où le tournoi aura lieu. | integer.

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
