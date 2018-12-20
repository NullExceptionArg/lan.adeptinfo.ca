# Role de LAN

## Créer un rôle de LAN

Créer un rôle pour un lan à partir des permissions d'administration de l'API.

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
name | Nom unique du rôle créé.
en_display_name | Nom du rôle créé, en anglais.
en_description | Description du rôle créé, en anglais.
fr_display_name | Description du rôle créé, en français.
fr_description | Nom du rôles créé, en français.


### Requête HTTP

`PUT /api/role/lan`


### Requête HTTP

`POST /api/role/lan/assign`


### Requête HTTP

`POST /api/role/lan/permissions`


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