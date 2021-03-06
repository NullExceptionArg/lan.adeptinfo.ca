# Permission

Les permissions protègent les appels destinées à l'administration. 
Les permissions sont attribués à des utilisateurs par le biais de rôles [globaux](#role-global) ou de [LAN](#role-de-lan).

## Obtenir les permissions

Obtenir les permissions disponibles dans l'API. 
Chaque permission est associée à un appel HTTP pour une action d'administration. 
Les administrateur peuvent avoir des permissions par le biais de rôles de LAN ou de rôles globaux.

### Requête HTTP

`GET /role/permissions`

Cette requête ne nécessite aucuns paramètres.

### Format de réponse

> Exemple de réponse

```json
[
    {
        "id": 1,
        "name": "create-lan",
        "can_be_per_lan": false,
        "display_name": "Create a new LAN",
        "description": "Create a new LAN. Careful, this permission should not be given to anyone..."
    },
    {
        "id": 2,
        "name": "set-current-lan",
        "can_be_per_lan": false,
        "display_name": "Set current LAN",
        "description": "Set the LAN that will be shown on the LAN website. Careful, this permission should not be given to anyone..."
    },
    {
        "id": 3,
        "name": "edit-lan",
        "can_be_per_lan": true,
        "display_name": "Edit LAN",
        "description": "Edit the name, the starting date, the closing date, the date start, the seat.io keys, the position (Lat, Lng), the number of available places, the price, the rules, and the description of the LAN. Careful, this permission should not be given to anyone ... "
    }
]
```

Paramètre | Description
--------- | -----------
id | Id de la permission.
name | Nom de la permission.
can_be_per_lan | Si la permission peut être dans un rôle de LAN.
display_name | Nom à afficher de la permission.
description | Nom à description de la permission.