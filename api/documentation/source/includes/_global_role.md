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
fr_display_name | Description du rôle créé, en français.
fr_description | Nom du rôles créé, en français.


## Modifier un rôle de LAN

Modifier un rôle pour un lan.
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
}
```

Champ | Description
--------- | -----------
name | Nom unique du rôle modifié.
en_display_name | Nom du rôle modifié, en anglais.
en_description | Description du rôle modifié, en anglais.
fr_display_name | Description du rôle modifié, en français.
fr_description | Nom du rôles modifié, en français.


### Requête HTTP

`POST /api/role/global/assign`


### Requête HTTP

`POST /api/role/global/permissions`


### Requête HTTP

`DELETE /api/role/global/permissions`


### Requête HTTP

`DELETE /api/role/global`


### Requête HTTP

`GET /api/role/global`


### Requête HTTP

`GET /api/role/global/permissions`


### Requête HTTP

`GET /api/role/global/users`