# Places

## Réserver une place

Réserver une place à un LAN.

### Requête HTTP

`POST /api/seat/book/{seat_id}`

### Path Params

L'ensemble des paramètres sont dans l'URL. Le corps de la requête est donc vide.

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'utilisateur veut réserver. | Requis, string, un seul Id de place par LAN.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'utilisateur veut réserver une place. Si paramètre n'est pas spécifié, on retourne le LAN courant | integer, un seul utilisateur par LAN.

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

## Annuler une réservation

Annule une réservation à un LAN.

### Requête HTTP

`DELETE /api/seat/book/{seat_id}`

### Path Params

L'ensemble des paramètres sont dans l'URL. Le corps de la requête est donc vide.

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'utilisateur veut annuler. | Requis, string.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'utilisateur veut annuler une place. Si paramètre n'est pas spécifié, on retourne le LAN courant | integer.

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
lan_id | Id du LAN où l'utilisateur a annulé une place.
seat_id | Id de la place que l'utilisateur a annulé.

## Assigner une place

Assigner une place à un un utilisateur pour un LAN.

### Requête HTTP

`POST /api/seat/assign/{seat_id}`

### POST Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
user_email | Courriel de l'utilisateur auquel on veut assigner une place. | Requis, string.

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'administrateur veut assigner. | Requis, string, un seul Id de place par LAN.

### Query Params
Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut assigner une place. Si paramètre n'est pas spécifié, on retourne le LAN courant | integer, un seul utilisateur par LAN.

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

## Annuler une assignation

Annuler l'assignation d'une place à un un utilisateur pour un LAN.

### Requête HTTP

`DELETE /api/seat/assign/{seat_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'administrateur veut assigner. | Requis, string, un seul Id de place par LAN.

### Query Params
Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut annuler l'assignation à une place. Si paramètre n'est pas spécifié, on retourne le LAN courant | integer.
user_email | Courriel de l'utilisateur auquel on veut annuler la réservation. | Requis, string.

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
lan_id | Id du LAN où l'administrateur a annuler l'assignation.
seat_id | Id de la place dont l'administrateur a annulé l'assignation.


## Confirmer une place

Confirmer l'arrivée d'un joueur au LAN.

### Requête HTTP

`POST /api/seat/confirm/{seat_id}`

### Path Params

L'ensemble des paramètres sont dans l'URL. Le corps de la requête est donc vide.

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'administrateur confirmer. | Requis, integer.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut confirmer une place. Si paramètre n'est pas spécifié, on retourne le LAN courant | string.

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

`DELETE /api/seat/confirm/{seat_id}`

### Path Params

L'ensemble des paramètres sont dans l'URL. Le corps de la requête est donc vide.

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'administrateur déconfirmer. | Requis, integer.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut déconfirmer une place. Si paramètre n'est pas spécifié, on retourne le LAN courant | integer.

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