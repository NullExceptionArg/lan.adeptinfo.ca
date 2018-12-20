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
en_description | Description du rôle à afficher, en anglais. | requis, string, max:50.
fr_description | Description du rôle à afficher, en français. | requis, string, max:1000.
fr_description | Description du rôle à afficher, en français. | requis, string, max:50.
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
fr_display_name | Description du rôle créé, en français.
fr_description | Nom du rôles créé, en français.


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
en_description | Description du rôle à afficher, en anglais. | string, max:50.
fr_description | Description du rôle à afficher, en français. | string, max:1000.
fr_description | Description du rôle à afficher, en français. | string, max:50.

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
fr_display_name | Description du rôle modifié, en français.
fr_description | Nom du rôles modifié, en français.


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
fr_display_name | Description du rôle attribué, en français.
fr_description | Nom du rôles attribué, en français.

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
fr_display_name | Description du rôle où les permissions ont été ajoutées, en français.
fr_description | Nom du rôles où les permissions ont été ajoutées, en français.

### Requête HTTP

`DELETE /api/role/lan/permissions`


### Requête HTTP

`DELETE /api/role/lan`


### Requête HTTP

`GET /api/role/lan`


### Requête HTTP

`GET /api/role/lan/permissions`


### Requête HTTP

`GET /api/role/lan/users`