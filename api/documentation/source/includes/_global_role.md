# Rôle global

Les rôles globaux sont des groupes de permissions qui peuvent être attribués à des utilisateurs.
Les rôles globaux sont des rôles qui se sont effectif que sur l'ensemble de l'application. Ils peuvent contenir n'importe laquelle des permissions disponibles.

## Créer un rôle global

Créer un rôle global.

### Requête HTTP

`POST /api/role/global`

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
name | Nom unique du rôle à créer. | requis, string, max:50, unique.
en_display_name | Nom du rôle à créer, en anglais. | requis, string, max:50.
en_description | Description du rôle à créer, en anglais. | requis, string, max:1000.
fr_display_name | Nom du rôle à créer, en français. | requis, string, max:50.
fr_description | Description du rôle à créer, en français. | requis, string, max:1000.
permissions | Id des permissions | requis, tableau d'Id des permissions à intégrer dans la permission.

### Format de réponse

> Exemple de réponse

```json
{
    "name": "comrade",
    "en_display_name": "Comrade",
    "en_description": "Our equal",
    "fr_display_name": "Camarade",
    "fr_description": "Notre égal."
}
```

Champ | Description
--------- | -----------
name | Nom unique du rôle créé.
en_display_name | Nom du rôle créé, en anglais.
en_description | Description du rôle créé, en anglais.
fr_display_name | Nom du rôle créé, en français.
fr_description | Description du rôles créé, en français.


## Modifier un rôle global

Modifier un rôle global.
D'autres appels sont nécessaires pour ajouter ou supprimer des permissions au rôle.

### Requête HTTP

`PUT /api/role/global`

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
role_id | Id du rôle à modifier | requis.
name | Nom unique du rôle à modifier. | string, max:50, unique.
en_display_name | Nom du rôle à modifier, en anglais. | string, max:50.
en_description | Description du rôle à modifier, en anglais. | string, max:1000.
en_description | Nom du rôle à modifier, en anglais. | string, max:50.
fr_description | Description du rôle à modifier, en français. | string, max:1000.

### Format de réponse

> Exemple de réponse

```json
{
    "name": "comrades",
    "en_display_name": "new english name",
    "en_description": "new english description",
    "fr_display_name": "nouveau nom francophone",
    "fr_description": "nouvelle description francophone"
}
```

Champ | Description
--------- | -----------
id | Id du rôle.
name | Nom unique du rôle modifié.
en_display_name | Nom du rôle modifié, en anglais.
en_description | Description du rôle modifié, en anglais.
fr_display_name | Nom du rôle modifié, en français.
fr_description | Description du rôles modifié, en français.


## Assigner un rôle global

Assigner un rôle global à un utilisateur

### Requête HTTP

`POST /api/role/global/assign`

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
email | Courriel de l'utilisateur à qui on veut attribuer le rôle. | requis.
role_id | Id du rôle à attribuer à l'utilisateur | integer.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "comrades",
    "display_name": "Comrade",
    "description": "Our equal"
}
```

Champ | Description
--------- | -----------
id | Id du rôle.
name | Nom unique du rôle attribué.
display_name | Nom du rôle attribué, selon la langue spécifiée.
description | Description du rôle attribué, selon la langue spécifiée.


## Ajouter des permissions à un role global

Ajouter des permissions à un role global

### Requête HTTP

`POST /api/role/global/permissions`

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
role_id | Id du rôle où l'on veut ajouter des permissions | integer.
permissions | Permissions à ajouter au rôle. | requis, liste de integer.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "general-admin",
    "display_name": "General admin",
    "description": "Has every permissions"
}
```

Champ | Description
--------- | -----------
id | Id du rôle.
name | Nom unique du rôle où les permissions ont été ajoutées.
display_name | Nom du rôle où les permissions ont été ajoutées, selon la langue spécifiée.
description | Description du rôle où les permissions ont été ajoutées, selon la langue spécifiée.


## Supprimer des permissions d'un rôle global

Supprimer des permissions d'un rôle global

### Requête HTTP

`DELETE /api/role/global/permissions`

> Exemple de requête

```json
{
	"role_id" : 1,
	"permissions" : [
		10, 11	
	]
}
```

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle où l'on veut supprimer des permissions | integer.
permissions | Permissions à supprimer au rôle. | requis, liste de integer.


### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "general-admin",
    "display_name": "General admin",
    "description": "Has every permissions"
}
```

Champ | Description
--------- | -----------
id | Id du rôle.
name | Nom unique du rôle où les permissions ont été supprimées.
display_name | Nom du rôle où les permissions ont été supprimées, selon la langue spécifiée.
description | Description du rôle où les permissions ont été supprimées, selon la langue spécifiée.


## Supprimer un rôle global

Supprimer un rôle global.

### Requête HTTP

`DELETE /api/role/global`

> Exemple de requête

```json
{
	"role_id" : 1
}
```

### Body Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle que l'on veut supprimer. | integer.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "general-admin",
    "display_name": "General admin",
    "description": "Has every permissions"
}
```

Champ | Description
--------- | -----------
id | Id du rôle.
name | Nom unique du rôle qui a été supprimé.
display_name | Nom du rôle qui a été supprimé, selon la langue spécifiée.
description | Description du rôle qui a été supprimé, selon la langue spécifiée.


## Obtenir les rôle globaux

Obtenir les rôles globaux.

### Requête HTTP

`GET /api/role/global`

Cette requête ne nécessite aucuns paramètres.

### Format de réponse

> Exemple de réponse

```json
[
  {
    "id": 1,
    "name": "comrades",
    "display_name": "new english name",
    "description": "new english description"
  }
]
```

Champ | Description
--------- | -----------
id | Id du rôle.
name | Nom unique rôle.
display_name | Nom du rôle, selon la langue spécifiée.
description | Description du rôle, selon la langue spécifiée.



## Obtenir les permissions d'un rôle global

Obtenir les permissions d'un rôle global.

### Requête HTTP

`GET /api/role/role/permissions`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle duquel on souhaite trouver les permissions. | integer.

### Format de réponse

> Exemple de réponse

```json
[
  {
    "id": 3,
    "name": "edit-lan",
    "can_be_per_lan": true,
    "display_name": "Edit LAN",
    "description": "Edit the name, the starting date, the ending date, the tournament reservation start date, the seat reservation start date, the seats.io keys, the position (Lat, Lng), the number of available places, the price, the rules, and the description of the LAN. Careful, this permission should not be given to anyone..."
  },
  {
    "id": 4,
    "name": "create-contribution-category",
    "can_be_per_lan": true,
    "display_name": "Create contribution category",
    "description": "Create new contribution categories."
  },
  {
    "id": 5,
    "name": "delete-contribution-category",
    "can_be_per_lan": true,
    "display_name": "Delete contribution category",
    "description": "Delete contribution categories."
  }
]
```

Champ | Description
--------- | -----------
id | Id de la permission.
can_be_per_lan | Si la permission peut être par LAN.
name | Nom unique de la permission.
can_be_per_lan | Nom de la permission, selon la langue spécifiée.
description | Description de la permission, selon la langue spécifiée.


## Obtenir les utilisateurs possédants un rôle global

Obtenir les utilisateurs possédants un rôle global.

### Requête HTTP

`GET /api/role/global/users`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle duquel on souhaite obtenir les utilisateurs. | integer.

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