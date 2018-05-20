# Places

## Réserver une place

Réserver une place à un LAN.

### Requête HTTP

`POST /api/lan/{lan_id}/book/{seat_id}`

### Path Params

L'ensemble des paramètres sont dans l'URL. Le corps de la requête est donc vide.

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'utilisateur veut réserver une place. | Requis, string, un seul utilisateur par LAN.
seat_id | Id de la place que l'utilisateur veut réserver. | Requis, integer, un seul Id de place par LAN.

### Format de réponse

> Exemple de réponse

```json
{
    "lan_id": 1,
    "seat_id": "A-1"
}
```

Champ | Description
--------- | -----------
lan_id | Id du LAN où l'utilisateur a réservé une place.
seat_id | Id de la place que l'utilisateur a réservé.