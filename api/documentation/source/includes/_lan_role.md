# Rôle de LAN

Les rôles de LAN sont des groupes de [permissions](#obtenir-les-permissions) qui peuvent être attribués à des utilisateurs.
Les rôles de LAN sont des rôles qui se sont effectif que sur un LAN spécifique. Ils ne peuvent contenir que des permissions qui ont l'attribut 'can_be_per_lan'.

## Créer un rôle de LAN

Créer un rôle de lan, contenant des permissions.

### Requête HTTP

`POST /role/lan`

> Exemple de requête

```json
{
	"name": "comrade",
	"en_display_name" : "Comrade",
	"en_description" : "Our equal",
	"fr_display_name" : "Camarade",
	"fr_description" : "Notre égal.",
	"permissions" : [
		39, 23
	]
}
```

### Body Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
name | Nom unique du rôle à créer. |  chaîne de caractères, max:50, unique.
en_display_name | Nom du rôle à afficher, en anglais. |  chaîne de caractères, max:50.
en_description | Description du rôle à afficher, en anglais. |  chaîne de caractères, max:1000.
fr_display_name | Nom du rôle à afficher, en français. |  chaîne de caractères, max:50.
fr_description | Description du rôle à afficher, en français. |  chaîne de caractères, max:1000.
permissions | Id des permissions |  tableau d'Id des permissions à intégrer dans la permission.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut créer un role. Si le paramètre n'est pas spécifié, on retourne le LAN courant. | entier.

### Format de réponse

> Exemple de réponse

```json
{
    "lan_id": 1,
    "name": "comrade",
    "en_display_name": "Comrade",
    "en_description": "Our equal",
    "fr_display_name": "Camarade",
    "fr_description": "Notre égal."
}
```

Champ | Description
--------- | -----------
lan_id | Id du LAN sur lequel le rôle a été ajouté.
name | Nom unique du rôle créé.
en_display_name | Nom du rôle créé, en anglais.
en_description | Description du rôle créé, en anglais.
fr_display_name | Nom du rôle créé, en français.
fr_description | Description du rôles créé, en français.


## Modifier un rôle de LAN

Modifier les détails d'un rôle de lan.
D'autres appels sont nécessaires pour ajouter ou supprimer des permissions au rôle.

### Requête HTTP

`PUT /role/lan`

> Exemple de requête

```json
{
	"role_id" : 1,
	"name" : "comrades",
	"en_display_name" : "new english name",
	"en_description" : "new english description",
	"fr_display_name" : "nouveau nom francophone",
	"fr_description" : "nouvelle description francophone"
}
```

### Body Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle à modifier | 
name | Nom unique du rôle à modifier. | chaîne de caractères, max:50, unique.
en_display_name | Nom du rôle à afficher, en anglais. | chaîne de caractères, max:50.
en_description | Description du rôle à afficher, en anglais. | chaîne de caractères, max:1000.
fr_display_name | Nom du rôle à afficher, en français. | chaîne de caractères, max:50.
fr_description | Description du rôle à afficher, en français. | chaîne de caractères, max:1000.

### Format de réponse

> Exemple de réponse

```json
{
    "name": "comrades",
    "en_display_name": "new english name",
    "en_description": "new english description",
    "fr_display_name": "nouveau nom francophone",
    "fr_description": "nouvelle description francophone",
    "lan_id": 1
}
```

Champ | Description
--------- | -----------
lan_id | Id du LAN du rôle de LAN modifié.
name | Nom unique du rôle modifié.
en_display_name | Nom du rôle modifié, en anglais.
en_description | Description du rôle modifié, en anglais.
fr_display_name | Nom du rôle modifié, en français.
fr_description | Description du rôles modifié, en français.


## Assigner un rôle de LAN

Assigner un rôle de LAN à un utilisateur.

### Requête HTTP

`POST /role/lan/assign`

> Exemple de requête

```json
{
	"email" : "karl.marx@unite.org",
	"role_id" : 1
}
```

### Body Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
email | Courriel de l'utilisateur à qui on veut attribuer le rôle. | 
role_id | Id du rôle à attribuer à l'utilisateur. | entier.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "comrades",
    "display_name": "Comrade",
    "description": "Our equal",
    "lan_id": 1
}
```

Champ | Description
--------- | -----------
id | Id du rôle.
lan_id | Id du LAN attribué.
name | Nom unique du rôle attribué.
display_name | Nom du rôle attribué.
description | Description du rôle attribué.

## Ajouter des permissions à un role de LAN

### Requête HTTP

`POST /role/lan/permissions`

> Exemple de requête

```json
{
	"role_id" : 1,
	"permissions" : [
		10, 11	
	]
}
```

### Body Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle pour lequel les permissions seront ajoutées. | entier.
permissions | Permissions à ajouter au rôle. |  liste de entier.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "lan-general-admin",
    "display_name": "LAN General admin",
    "description": "Has every permissions for a LAN",
    "lan_id": 1
}
```

Champ | Description
--------- | -----------
id | Id du rôle.
lan_id | Id du LAN du rôle de LAN pour lequel les permissions ont été ajoutées.
name | Nom unique du rôle où les permissions ont été ajoutées.
display_name | Nom du rôle où les permissions ont été ajoutées.
description | Description du rôle où les permissions ont été ajoutées.


## Supprimer des permissions à un rôle de LAN

Supprimer des permissions d'un rôle de LAN.

### Requête HTTP

`DELETE /role/lan/permissions`

> Exemple de requête

```json
{
	"role_id" : 1,
	"permissions" : [
		10, 11	
	]
}
```

### Body Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle pour lequel les permissions seront supprimées. | entier.
permissions | Permissions à supprimer au rôle. |  liste de entier.


### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "lan-general-admin",
    "display_name": "LAN General admin",
    "description": "Has every permissions for a LAN",
    "lan_id": 1
}
```

Champ | Description
--------- | -----------
id | Id du rôle.
lan_id | Id du LAN où les permissions ont été supprimées.
name | Nom unique du rôle où les permissions ont été supprimées.
display_name | Nom du rôle où les permissions ont été supprimées.
description | Description du rôle où les permissions ont été supprimées.


## Supprimer un rôle de LAN

### Requête HTTP

`DELETE /role/lan`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle à supprimer. | entier.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "lan-general-admin",
    "display_name": "LAN General admin",
    "description": "Has every permissions for a LAN",
    "lan_id": 1
}
```

Champ | Description
--------- | -----------
id | Id du rôle supprimé.
lan_id | Id du LAN du rôle de LAN pour lequel le rôle a été supprimé.
name | Nom unique du rôle qui a été supprimé.
display_name | Nom du rôle qui a été supprimé.
description | Description du rôle qui a été supprimé.


## Obtenir les rôles de LAN

Obtenir les rôles de LAN du LAN.

### Requête HTTP

`GET /role/lan`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN duquel on veut obtenir les rôles. | entier.

### Format de réponse

> Exemple de réponse

```json
[
  {
    "id": 1,
    "name": "lan-general-admin",
    "display_name": "LAN General admin",
    "description": "Has every permissions for a LAN",
    "lan_id": 1
  },
  {
    "id": 2,
    "name": "seat-admin",
    "display_name": "Seat admin",
    "description": "Can manage places",
    "lan_id": 1
  },
  {
    "id": 3,
    "name": "tournament-admin",
    "display_name": "Tournament admin",
    "description": "Can manage tournaments et les équipes",
    "lan_id": 1
  }
]
```

Champ | Description
--------- | -----------
id | Id du rôle.
lan_id | Id du LAN du rôle.
name | Nom unique rôle.
display_name | Nom du rôle.
description | Description du rôle.


## Obtenir les permissions d'un rôle de LAN

Obtenir les permissions contenues dans un rôle de LAN.

### Requête HTTP

`GET /role/lan/permissions`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle pour lequel les permissions seront obtenues.  | entier.

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
  },
  {
    "id": 4,
    "name": "create-contribution-category",
    "can_be_per_lan": true,
    "display_name": "Create contribution category",
    "description": "Create new contribution categories."
  }
]
```

Champ | Description
--------- | -----------
id | Id de la permission.
name | Nom unique de la permission.
can_be_per_lan | Si la permission peut être par LAN.
display_name | Nom de la permission.
description | Description de la permission.


## Obtenir les utilisateurs possédants un rôle de LAN

Obtenir les utilisateurs possédants un certain rôle de LAN.

### Requête HTTP

`GET /role/lan/users`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle pour lequel les utilisateurs seront obtenus. | entier.

### Format de réponse

> Exemple de réponse

```json
[
  {
    "email": "karl.marx@unite.org",
    "first_name": "Karl",
    "last_name": "Marx"
  }
]
```

Champ | Description
--------- | -----------
email | Courriel de l'utilisateur.
first_name | Prénom de l'utilisateur.
last_name | Nom de famille de l'utilisateur.
