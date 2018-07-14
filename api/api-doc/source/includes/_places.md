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
seat_id | Id de la place que l'utilisateur veut réserver. | Requis, string, un seul Id de place par LAN.

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

## Assigner une place

Assigner une place à un un utilisateur pour un LAN.

### Requête HTTP

`POST /api/seat/assign`

### POST Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut assigner une place. Si ce paramètre n'est pas spécifié, on prend le LAN courant. | int, un seul utilisateur par LAN.
seat_id | Id de la place que l'administrateur veut assigner. | Requis, string, un seul Id de place par LAN.
user_email | Courriel de l'utilisateur auquel on veut assigner une place. | Requis, string.

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
lan_id | Id du LAN où l'administrateur a assigné une place.
seat_id | Id de la place que l'administrateur a assigné.


## Confirmer une place

Confirmer l'arrivée d'un joueur au LAN.

### Requête HTTP

`POST /api/lan/{lan_id}/confirm/{seat_id}`

### Path Params

L'ensemble des paramètres sont dans l'URL. Le corps de la requête est donc vide.

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut confirmer une place. | Requis, string.
seat_id | Id de la place que l'administrateur confirmer. | Requis, integer.

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
lan_id | Id du LAN où l'administrateur a confirmé une place.
seat_id | Id de la place que l'administrateur a confirmé.

## Déconfirmer une place

Départ de l'un des joueurs déjà marqué comme arrivé à un LAN.

### Requête HTTP

`DELETE /api/lan/{lan_id}/confirm/{seat_id}`

### Path Params

L'ensemble des paramètres sont dans l'URL. Le corps de la requête est donc vide.

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut déconfirmer une place. | Requis, string.
seat_id | Id de la place que l'administrateur déconfirmer. | Requis, integer.

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
lan_id | Id du LAN où l'administrateur a déconfirmé une place.
seat_id | Id de la place que l'administrateur a déconfirmé.