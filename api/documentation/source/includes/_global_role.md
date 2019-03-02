# Rôle global

Les rôles globaux sont des groupes de [permissions](#obtenir-les-permissions) qui peuvent être attribués à des utilisateurs.
Les rôles globaux sont des rôles qui se sont effectif que sur l'ensemble de l'application. Ils peuvent contenir n'importe laquelle des permissions disponibles.

## Créer un rôle global

Créer un rôle global, contenant des permissions.

### Requête HTTP

`POST /role/global`

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
en_display_name | Nom du rôle à créer, en anglais. |  chaîne de caractères, max:50.
en_description | Description du rôle à créer, en anglais. |  chaîne de caractères, max:1000.
fr_display_name | Nom du rôle à créer, en français. |  chaîne de caractères, max:50.
fr_description | Description du rôle à créer, en français. |  chaîne de caractères, max:1000.
permissions | Id des permissions. |  tableau d'Id des permissions à intégrer dans la permission.

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

Modifier les détails d'un rôle global.
D'autres appels sont nécessaires pour ajouter ou supprimer des permissions au rôle.

### Requête HTTP

`PUT /role/global`

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
role_id | Id du rôle à modifier. | 
name | Nom unique du rôle à modifier. | chaîne de caractères, max:50, unique.
en_display_name | Nom du rôle à modifier, en anglais. | chaîne de caractères, max:50.
en_description | Description du rôle à modifier, en anglais. | chaîne de caractères, max:1000.
en_description | Nom du rôle à modifier, en anglais. | chaîne de caractères, max:50.
fr_description | Description du rôle à modifier, en français. | chaîne de caractères, max:1000.

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
name | Nom unique du rôle modifié.
en_display_name | Nom du rôle modifié, en anglais.
en_description | Description du rôle modifié, en anglais.
fr_display_name | Nom du rôle modifié, en français.
fr_description | Description du rôles modifié, en français.


## Assigner un rôle global

Assigner un rôle global à un utilisateur.

### Requête HTTP

`POST /role/global/assign`

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
    "description": "Our equal"
}
```

Champ | Description
--------- | -----------
id | Id du rôle.
name | Nom unique du rôle attribué.
display_name | Nom du rôle attribué.
description | Description du rôle attribué.


## Ajouter des permissions à un role global

### Requête HTTP

`POST /role/global/permissions`

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
permissions | Permissions à ajouter au rôle. | liste d'entiers.

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
display_name | Nom du rôle où les permissions ont été ajoutées.
description | Description du rôle où les permissions ont été ajoutées.


## Supprimer des permissions à un rôle global

### Requête HTTP

`DELETE /role/global/permissions`

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
role_id | Id du rôle pour lequel les permissions seront supprimées. | entier.
permissions | Id des permissions à supprimer au rôle. |  liste de entier.


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
display_name | Nom du rôle où les permissions ont été supprimées.
description | Description du rôle où les permissions ont été supprimées.


## Supprimer un rôle global

### Requête HTTP

`DELETE /role/global`

> Exemple de requête

```json
{
	"role_id" : 1
}
```

### Body Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle à supprimer. | entier.

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
id | Id du rôle supprimé.
name | Nom unique du rôle supprimé.
display_name | Nom du rôle supprimé.
description | Description du rôle supprimé.


## Obtenir les rôles globaux

Obtenir les rôles globaux de l'application.

### Requête HTTP

`GET /role/global`

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
display_name | Nom du rôle.
description | Description du rôle.



## Obtenir les permissions d'un rôle global

Obtenir les permissions contenues dans un rôle global.

### Requête HTTP

`GET /role/role/permissions`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle pour lequel les permissions seront obtenues. | entier.

### Format de réponse

> Exemple de réponse

```json
[
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
can_be_per_lan | Nom de la permission.
description | Description de la permission.


## Obtenir les utilisateurs possédants un rôle global

Obtenir les utilisateurs possédants un certain rôle global.

### Requête HTTP

`GET /role/global/users`

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
