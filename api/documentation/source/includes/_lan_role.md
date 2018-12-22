# Rôle de LAN

Les rôles de LAN sont des groupes de permissions qui peuvent être attribués à des utilisateurs.
Les rôles de LAN sont des rôles qui se sont effectif que sur un LAN spécifique. Ils ne peuvent contenir que des permissions qui ont l'attribut 'can_be_per_lan'.

## Créer un rôle de LAN

Créer un rôle de lan.

### Requête HTTP

`POST /api/role/lan`

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

### POST Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
name | Nom unique du rôle à créer. | requis, string, max:50, unique.
en_display_name | Nom du rôle à afficher, en anglais. | requis, string, max:50.
en_description | Description du rôle à afficher, en anglais. | requis, string, max:1000.
fr_display_name | Nom du rôle à afficher, en français. | requis, string, max:50.
fr_description | Description du rôle à afficher, en français. | requis, string, max:1000.
permissions | Id des permissions | requis, tableau d'Id des permissions à intégrer dans la permission.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut créer un role. Si paramètre n'est pas spécifié, on retourne le LAN courant. | integer.

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

Modifier un rôle de lan.
D'autres appels sont nécessaires pour ajouter ou supprimer des permissions au rôle.

### Requête HTTP

`PUT /api/role/lan`

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

### POST Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle à modifier | requis.
name | Nom unique du rôle à modifier. | string, max:50, unique.
en_display_name | Nom du rôle à afficher, en anglais. | string, max:50.
en_description | Description du rôle à afficher, en anglais. | string, max:1000.
fr_display_name | Nom du rôle à afficher, en anglais. | string, max:50.
fr_description | Description du rôle à afficher, en français. | string, max:1000.

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
lan_id | Id du LAN sur lequel le rôle a été modifié.
name | Nom unique du rôle modifié.
en_display_name | Nom du rôle modifié, en anglais.
en_description | Description du rôle modifié, en anglais.
fr_display_name | Nom du rôle modifié, en français.
fr_description | Description du rôles modifié, en français.


## Assigner un rôle de LAN

Assigner un rôle de LAN à un utilisateur

### Requête HTTP

`POST /api/role/lan/assign`

> Exemple de requête

```json
{
	"email" : "karl.marx@unite.org",
	"role_id" : 1
}
```

### POST Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
email | Courriel de l'utilisateur à qui on veut attribuer le rôle. | requis.
role_id | Id du rôle à attribuer à l'utilisateur | integer.

### Format de réponse

> Exemple de réponse

```json
{
    "name": "comrades",
    "en_display_name": "Comrade",
    "en_description": "Our equal",
    "fr_display_name": "Camarade",
    "fr_description": "Notre égal.",
    "lan_id": 1
}
```

Champ | Description
--------- | -----------
lan_id | Id du LAN attribué.
name | Nom unique du rôle attribué.
en_display_name | Nom du rôle attribué, en anglais.
en_description | Description du rôle attribué, en anglais.
fr_display_name | Nom du rôle attribué, en français.
fr_description | Description du rôles attribué, en français.

## Ajouter des permissions à un role de LAN

Ajouter des permissions à un role de LAN

### Requête HTTP

`POST /api/role/lan/permissions`

> Exemple de requête

```json
{
	"role_id" : 1,
	"permissions" : [
		10, 11	
	]
}
```

### POST Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle où l'on veut ajouter des permissions | integer.
permissions | Permissions à ajouter au rôle. | requis, liste de integer.

### Format de réponse

> Exemple de réponse

```json
{
    "name": "lan-general-admin",
    "en_display_name": "LAN General admin",
    "en_description": "Has every permissions for a LAN",
    "fr_display_name": "Administrateur général de LAN",
    "fr_description": "Possède toutes les permissions pour un LAN",
    "lan_id": 1
}
```

Champ | Description
--------- | -----------
lan_id | Id du LAN où les permissions ont été ajoutées.
name | Nom unique du rôle où les permissions ont été ajoutées.
en_display_name | Nom du rôle où les permissions ont été ajoutées, en anglais.
en_description | Description du rôle où les permissions ont été ajoutées, en anglais.
fr_display_name | Nom du rôle où les permissions ont été ajoutées, en français.
fr_description | Description du rôles où les permissions ont été ajoutées, en français.


## Supprimer des permissions d'un rôle de LAN

Supprimer des permissions d'un rôle de LAN.

### Requête HTTP

`DELETE /api/role/lan/permissions`

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
role_id | Id du rôle où l'on veut supprimer des permissions | integer.
permissions | Permissions à supprimer au rôle. | requis, liste de integer.


### Format de réponse

> Exemple de réponse

```json
{
    "name": "lan-general-admin",
    "en_display_name": "LAN General admin",
    "en_description": "Has every permissions for a LAN",
    "fr_display_name": "Administrateur général de LAN",
    "fr_description": "Possède toutes les permissions pour un LAN",
    "lan_id": 1
}
```

Champ | Description
--------- | -----------
lan_id | Id du LAN où les permissions ont été supprimées.
name | Nom unique du rôle où les permissions ont été supprimées.
en_display_name | Nom du rôle où les permissions ont été supprimées, en anglais.
en_description | Description du rôle où les permissions ont été supprimées, en anglais.
fr_display_name | Nom du rôle où les permissions ont été supprimées, en français.
fr_description | Description du rôles où les permissions ont été supprimées, en français.


## Supprimer un rôle de LAN

Supprimer un rôle de LAN.

### Requête HTTP

`DELETE /api/role/lan`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle que l'on veut supprimer. | integer.

### Format de réponse

> Exemple de réponse

```json
{
    "name": "lan-general-admin",
    "en_display_name": "LAN General admin",
    "en_description": "Has every permissions for a LAN",
    "fr_display_name": "Administrateur général de LAN",
    "fr_description": "Possède toutes les permissions pour un LAN",
    "lan_id": 1
}
```

Champ | Description
--------- | -----------
lan_id | Id du LAN où le rôle a été supprimé.
name | Nom unique du rôle qui a été supprimé.
en_display_name | Nom du rôle qui a été supprimé, en anglais.
en_description | Description du rôle qui a été supprimé, en anglais.
fr_display_name | Nom du rôle qui a été supprimé, en français.
fr_description | Description du rôles qui a été supprimé, en français.


## Obtenir les rôles de LAN

Obtenir les rôles de LAN.

### Requête HTTP

`GET /api/role/lan`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN duquel on veut obtenir les rôles. | integer.

### Format de réponse

> Exemple de réponse

```json
[
  {
    "name": "lan-general-admin",
    "en_display_name": "LAN General admin",
    "en_description": "Has every permissions for a LAN",
    "fr_display_name": "Administrateur général de LAN",
    "fr_description": "Possède toutes les permissions pour un LAN",
    "lan_id": 1
  },
  {
    "name": "seat-admin",
    "en_display_name": "Seat admin",
    "en_description": "Can manage places",
    "fr_display_name": "Administrateur des places",
    "fr_description": "Peut gérer les places",
    "lan_id": 1
  },
  {
    "name": "tournament-admin",
    "en_display_name": "Tournament admin",
    "en_description": "Can manage tournaments et les équipes",
    "fr_display_name": "Administrateur des tournois",
    "fr_description": "Peut gérer les tournois et les équipes",
    "lan_id": 1
  }
]
```

Champ | Description
--------- | -----------
lan_id | Id du LAN du rôle.
name | Nom unique rôle.
en_display_name | Nom du rôle, en anglais.
en_description | Description du rôle, en anglais.
fr_display_name | Nom du rôle, en français.
fr_description | Description du rôles, en français.


## Obtenir les permissions d'un rôle de LAN

Obtenir les permissions d'un rôle de LAN.

### Requête HTTP

`GET /api/role/lan/permissions`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
role_id | Id du rôle duquel on souhaite trouver les permissions. | integer.

### Format de réponse

> Exemple de réponse

```json
[
  {
    "id": 1,
    "name": "create-lan",
    "can_be_per_lan": 0,
    "display_name": "Create a new LAN",
    "description": "Create a new LAN. Careful, this permission should not be given to anyone..."
  },
  {
    "id": 2,
    "name": "set-current-lan",
    "can_be_per_lan": 0,
    "display_name": "Set current LAN",
    "description": "Set the LAN that will be shown on the LAN website. Careful, this permission should not be given to anyone..."
  },
  {
    "id": 3,
    "name": "edit-lan",
    "can_be_per_lan": 1,
    "display_name": "Edit LAN",
    "description": "Edit the name, the starting date, the ending date, the tournament reservation start date, the seat reservation start date, the seats.io keys, the position (Lat, Lng), the number of available places, the price, the rules, and the description of the LAN. Careful, this permission should not be given to anyone..."
  },
  {
    "id": 4,
    "name": "create-contribution-category",
    "can_be_per_lan": 1,
    "display_name": "Create contribution category",
    "description": "Create new contribution categories."
  }
]
```

Champ | Description
--------- | -----------
id | Id de la permission.
name | Nom unique de la permission.
en_display_name | Nom de la permission, en anglais.
en_description | Description de la permission, en anglais.
fr_display_name | Nom de la permission, en français.
fr_description | Description de la permission, en français.


## Obtenir les utilisateurs possédants un rôle de LAN

Obtenir les utilisateurs possédants un rôle de LAN.

### Requête HTTP

`GET /api/role/lan/users`

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
