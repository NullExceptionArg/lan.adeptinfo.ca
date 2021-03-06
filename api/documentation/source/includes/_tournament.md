# Tournoi

Des tournois où plusieurs équipes s'affrontent peuvent être organisés dans un LAN. 

## Créer un tournoi

Créer un nouveau tournoi pour un LAN. L'administrateur qui créer le tournoi en devient automatiquement l'administrateur.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>create-tournament</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`POST /tournament`

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
lan_id | Id du LAN où le tournoi aura lieu. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier.

### Paramètres POST

Paramètre | Description | Règles de validation | Défaut
--------- | ----------- | -------------------- | ------
name | Nom du tournoi. |  chaîne de caractères, 255 caractères max. |
price | Prix d'entrée du tournoi. | int, min: 0. | 0
tournament_start | Date et heure de début du tournoi. |  après le début du LAN. | 
tournament_end | Date et heure de fin du tournoi. |  date, avant la fin du LAN., après le début du tournoi. |
players_to_reach| Nombre de joueur à atteindre par équipe. |  min: 1, int. |
teams_to_reach |Nombre d'équipes à atteindre pour que le tournoi ait lieu.|  min: 1, int. |
rules | Règlements du tournoi. | chaîne de caractères |

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
    "teams": []
}
```

Champ | Description
--------- | -----------
id | Id du tournoi créé. 
name | Nom du tournoi créé. 
rules | Règlements du tournoi créé.
price | Prix d'entrée du tournoi créé.
tournament_start | Date et heure de début du tournoi créé. 
tournament_end | Date et heure de fin du tournoi créé.
teams_to_reach | Nombre d'équipes à atteindre pour que le tournoi ait lieu.
teams_reaches | Nombre d'équipes atteintes du tournoi créé.
players_to_reach| Nombre de joueur à atteindre par équipe du tournoi créé.
state| État courant du tournoi créé. Voir État pour les états possibles.
teams | Voir Team.


#### État
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

## Supprimer un tournoi

Supprimer un tournoi.

Les équipes, les requêtes pour entrer dans les équipes du tournois et les liaisons entre les tag et les équipes du tournoi seront aussi supprimés.

### Requête HTTP

`DELETE /tournament/{tournament_id}`

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>delete-tournament</code>, can_be_per_lan <code>true</code>
</aside>

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi que l'administrateur veut supprimer. | entier.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 2,
    "name": "October",
    "tournament_start": "2100-10-11 14:00:00",
    "tournament_end": "2100-10-11 18:00:00",
    "state": "hidden",
    "teams_reached": 0,
    "teams_to_reach": 6
}
```

Champ | Description
--------- | -----------
id | Id du tournoi supprimé. 
name | Nom du tournoi supprimé. 
tournament_start | Date et heure de début du tournoi supprimé. 
tournament_end | Date et heure de fin du tournoi supprimé.
state | État courant du tournoi supprimé. Voir État pour les états possibles.
teams_reached | Nombre d'équipes atteintes pour que le tournoi supprimé ait lieu.
teams_to_reach | Nombre d'équipes à atteindre pour que le tournoi supprimé ait lieu.

#### État
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


## Tournois d'un organisateur

Obtenir les tournois d'un organisateur.
Si l'organisateur possède les permissions pour modifier, et supprimer, et ajouter un organisateur à un tournoi, tous les tournois s'affichent. 
Sinon on retourne uniquement les tournois qui sont organisés par l'utilisateur.

### Requête HTTP

`GET /tournament/all/organizer`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN dans lequel l'organisateur souhaite trouver ses tournois. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier.

### Format de réponse

> Exemple de réponse

```json
[
    {
        "id": 1,
        "name": "October",
        "tournament_start": "2100-10-11 14:00:00",
        "tournament_end": "2100-10-11 18:00:00",
        "state": "hidden",
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
state| État courant du tournoi. Voir État courant.
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


## Obtenir tous les tournois

Obtenir tous les tournois d'un LAN.

### Requête HTTP

`GET /tournament/all`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN dans lequel on souhaite trouver les tournois. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier.

### Format de réponse

> Exemple de réponse

```json
[
    {
        "id": 1,
        "name": "October",
        "tournament_start": "2100-10-11 14:00:00",
        "tournament_end": "2100-10-11 18:00:00",
        "state": "hidden",
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
state| État courant du tournoi. Voir État courant.
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

Modifier les informations d'un tournoi.

### Requête HTTP

`PUT /tournament/{tournament_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi que l'administrateur veut modifier. | entier.

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

### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
name | Nom du tournoi. |  chaîne de caractères, 255 caractères max.
price | Prix d'entrée du tournoi. | int, min: 0.
state | État courant du LAN. Voir État Courant. | hidden, visible, started, ou finished
tournament_start | Date et heure de début du tournoi. |  après le début du LAN. 
tournament_end | Date et heure de fin du tournoi. |  date, avant la fin du LAN, après le début du tournoi.
players_to_reach| Nombre de joueur à atteindre par équipe. |  min: 1, int.
teams_to_reach |Nombre d'équipes à atteindre pour que le tournoi ait lieu.|  min: 1, int.
rules | Règlements du tournoi. | chaîne de caractères

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
    "rules": "The Bolsheviks seize control of Petrograd.",
    "price": 0,
    "tournament_start": "2100-10-11 14:00:00",
    "tournament_end": "2100-10-11 18:00:00",
    "teams_to_reach": 6,
    "teams_reached": 0,
    "players_to_reach": 5,
    "state": "hidden",
    "teams": []
}
```

Paramètre | Description
--------- | -----------
id | Id du tournoi.
name | Nom du tournoi.
rules | Règlements du tournoi.
price | Prix d'entrée du tournoi. 
tournament_start | Date et heure de début du tournoi.
tournament_end | Date et heure de fin du tournoi.
teams_to_reach |Nombre d'équipes à atteindre pour que le tournoi ait lieu.
teams_reached |Nombre d'équipes atteintes.
players_to_reach| Nombre de joueur à atteindre par équipe.
state | État courant du LAN. Voir État Courant. 
teams | Voir Team.

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

## Détails d'un tournoi

Détails d'un tournoi. Comprends aussi les équipes ainsi que les tags de joueur qui en font parti.

### Requête HTTP

`GET /tournament/details/{tournament_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi que l'on veut consulter. | entier.

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
finished | Le tournoi est terminé.
fourthcoming | Le tournoi est à venir.
late | Le tournoi est en retard.
outguessed | Le tournoi est devancé.
running | Le tournoi est en cours.
behindhand | Le tournoi s'éternise (Après l'heure de fin prévue).
unknown | État inconnu. Si jamais vous ou un utilisateur obtient cette réponse, il serait bien de le communiquer à un développeur de l'API.

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


## Quitter l'organisation d'un tournoi

Un administrateur quitte l'organisation du tournoi.

S'il est le dernier à quitter, le tournoi est supprimé.

### Requête HTTP

`POST /tournament/{tournament_id}/quit`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi l'administrateur veut quitter. | entier.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "Octobers",
    "tournament_start": "2100-10-11 14:00:00",
    "tournament_end": "2100-10-11 18:00:00",
    "state": "hidden",
    "teams_reached": 0,
    "teams_to_reach": 6
}
```

Paramètre | Description
--------- | -----------
id | Id du tournoi.
name | Nom du tournoi.
tournament_start | Date et heure de début du tournoi.
tournament_end | Date et heure de fin du tournoi.
state | État courant du LAN. Voir État Courant. 
teams_reached |Nombre d'équipes atteintes.
teams_to_reach |Nombre d'équipes à atteindre pour que le tournoi ait lieu.

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


## Ajouter un organisateur à un tournoi

Un administrateur ou un organisateur d'un tournoi peut en ajouter un autre, pour l'aider à l'organisation du tournoi.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>add-organizer</code>, can_be_per_lan <code>true</code>
</aside>

<aside class="notice">
La permission n'est pas requise si l'utilisateur est organisateur du tournoi.
</aside>

### Requête HTTP

`POST /tournament/{tournament_id}/organizer`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi pour lequel l'organisateur ou l'administrateur souhaite ajouter un autre organisateur. | entier.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
email | Courriel de l'utilisateur à ajouter comme nouvel organisateur du tournoi. | chaîne de caractères.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 3,
    "name": "October",
    "tournament_start": "2100-10-11 14:00:00",
    "tournament_end": "2100-10-11 18:00:00",
    "state": "hidden",
    "teams_reached": 0,
    "teams_to_reach": 6
}
```

Paramètre | Description
--------- | -----------
id | Id du tournoi dans lequel le l'organisateur a été ajouté.
name | Nom du tournoi dans lequel le l'organisateur a été ajouté.
tournament_start | Date et heure de début du tournoi dans lequel le l'organisateur a été ajouté.
tournament_end | Date et heure de fin du tournoi dans lequel le l'organisateur a été ajouté.
state | État courant du tournoi dans lequel le l'organisateur a été ajouté. Voir État Courant. 
teams_reached | Nombre d'équipes atteintes pour que le tournoi dans lequel le l'organisateur a été ajouté ait lieu.
teams_to_reach | Nombre d'équipes à atteindre pour que le tournoi dans lequel le l'organisateur a été ajouté ait lieu.

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

## Retirer un organisateur d'un tournoi

Un administrateur ou un organisateur d'un tournoi peut en retirer un autre.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>remove-organizer</code>, can_be_per_lan <code>true</code>
</aside>

<aside class="notice">
La permission n'est pas requise si l'utilisateur est organisateur du tournoi.
</aside>

### Requête HTTP

`POST /tournament/{tournament_id}/organizer`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi pour lequel l'organisateur ou l'administrateur souhaite retirer un organisateur. | entier.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
email | Courriel de l'utilisateur à retirer de l'organisation du tournoi. | chaîne de caractères.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 3,
    "name": "October",
    "tournament_start": "2100-10-11 14:00:00",
    "tournament_end": "2100-10-11 18:00:00",
    "state": "hidden",
    "teams_reached": 0,
    "teams_to_reach": 6
}
```

Paramètre | Description
--------- | -----------
id | Id du tournoi pour lequel le l'organisateur a été retiré.
name | Nom du tournoi pour lequel le l'organisateur a été retiré.
tournament_start | Date et heure de début du tournoi pour lequel le l'organisateur a été retiré.
tournament_end | Date et heure de fin du tournoi pour lequel le l'organisateur a été retiré.
state | État courant du tournoi pour lequel le l'organisateur a été retiré. Voir État Courant. 
teams_reached | Nombre d'équipes atteintes pour que le tournoi dans lequel le l'organisateur a été retiré ait lieu.
teams_to_reach | Nombre d'équipes à atteindre pour que le tournoi dans lequel le l'organisateur a été retiré ait lieu.

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
