# Place

Des places peuvent être occupées par les joueurs qui emmènent leur équipement. 

L'affichage des places est pris en charge par seats.io, qui offre des outil de modélisation de plan de salle très accessibles.

<aside class="notice">
Le nom complet de l'utilisateur ainsi que son courriel sont stockés dans le champ ExtraData de la place dans l'API seats.io.
Ces informations deviennent donc accessible à l'affichage du plan.  
Pour plus de détails sur ExtraData, consulter la page suivante: <a href="https://docs.seats.io/docs/api-extra-data">https://docs.seats.io/docs/api-extra-data</a>.
</aside>

## Réserver une place

Un joueur effectue une réservation à un LAN.

### Requête HTTP

`POST /seat/book/{seat_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'utilisateur veut réserver. |  chaîne de caractères, un seul Id de place par LAN.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'utilisateur veut réserver une place. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier, un seul utilisateur par LAN.

### Format de réponse

> Exemple de réponse

```json
{
    "seat_id": "A-1"
}
```

Champ | Description
--------- | -----------
seat_id | Id de la place que l'utilisateur a réservé.

## Annuler une réservation

Un joueur annule une réservation qu'il avait fait à un LAN.

### Requête HTTP

`DELETE /seat/book/{seat_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'utilisateur veut annuler. |  chaîne de caractères.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'utilisateur veut annuler une place. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier.

### Format de réponse

> Exemple de réponse

```json
{
    "seat_id": "A-1"
}
```

Champ | Description
--------- | -----------
seat_id | Id de la place que l'utilisateur a annulé.

## Assigner une place

Un administrateur assigne une place à un un joueur à un LAN.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>assign-seat</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`POST /seat/assign/{seat_id}`

### Body Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
user_email | Courriel de l'utilisateur auquel l'administrateur veut assigner une place. |  chaîne de caractères.

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'administrateur veut assigner. |  chaîne de caractères, un seul Id de place par LAN.

### Query Params
Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut assigner une place. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier, un seul utilisateur par LAN.

### Format de réponse

> Exemple de réponse

```json
{
    "seat_id": "A-1"
}
```

Champ | Description
--------- | -----------
seat_id | Id de la place que l'administrateur a assigné.

## Annuler une assignation

Un administrateur annule la réservation d'un joueur à un LAN.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>unassign-seat</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`DELETE /seat/assign/{seat_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'administrateur veut assigner. |  chaîne de caractères, un seul Id de place par LAN.

### Query Params
Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut annuler l'assignation à une place. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier.
user_email | Courriel de l'utilisateur auquel on veut annuler la réservation. |  chaîne de caractères.

### Format de réponse

> Exemple de réponse

```json
{
    "seat_id": "A-1"
}
```

Champ | Description
--------- | -----------
seat_id | Id de la place dont l'administrateur a annulé l'assignation.


## Confirmer l'arrivée d'un joueur

Un administrateur confirmer l'arrivée d'un joueur au LAN.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>confirm-arrival</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`POST /seat/confirm/{seat_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'administrateur confirmer. |  entier.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut confirmer une place. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | chaîne de caractères.

### Format de réponse

> Exemple de réponse

```json
{
    "seat_id": "A-1"
}
```

Champ | Description
--------- | -----------
seat_id | Id de la place que l'administrateur a confirmé.

## Déconfirmer une place

Un administrateur marque le départ de l'un des joueurs déjà marqué comme arrivé à un LAN.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>unconfirm-arrival</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`DELETE /seat/confirm/{seat_id}`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
seat_id | Id de la place que l'administrateur déconfirmer. |  entier.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut déconfirmer une place. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier.

### Format de réponse

> Exemple de réponse

```json
{
    "seat_id": "A-1"
}
```

Champ | Description
--------- | -----------
seat_id | Id de la place que l'administrateur a déconfirmé.