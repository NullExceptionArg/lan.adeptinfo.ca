# Contribution

Les organisateurs peuvent remercier ceux qui ont donnés de leur temps à l'aide de cette liste de contributeurs

## Ajouter une contribution

Ajoute une contribution à un LAN

### Requête HTTP

`POST /contribution`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut ajouter une contribution. Si le paramètre n'est pas spécifié, on retourne le LAN courant | integer.

> Exemple de requête

```json
{
	"contribution_category_id": 1,
	"user_full_name": null,
	"user_email": "karl.marx@unite.org"
}
```
### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
contribution_category_id | Id de la catégorie de la contribution à créer. | Requis, integer.
user_full_name | Nom complet du contributeur. | string.
user_email | Adresse courriel du contributeur. | string.

<aside class="notice">
Les paramètres <code>user_full_name</code> et <code>user_email</code> ne peuvent pas être utilisés en même temps. Il est cependant requis d'envoyer un de ces deux champs.
</aside>

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "user_full_name": "Karl Marx",
    "contribution_category_id": 1
}
```

Champ | Description
--------- | -----------
id | Id de la contribution créée.
user_full_name | Nom complet du contributeur créé.
contribution_category_id | Id de la catégorie de contribution du contributeur créé.

## Lister les contributions

Liste l'ensemble des contributions, groupées par catégories, pour un LAN

### Requête HTTP

`GET /contribution`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN d'où l'utilisateur veut lister les contributions. Si le paramètre n'est pas spécifié, on retourne le LAN courant | integer.

### Format de réponse

> Exemple de réponse

```json
[
    {
        "category_id": 1,
        "category_name": "Programmeur",
        "contributions": [
            {
                "id": 9,
                "user_full_name": "Karl Marx"
            }
        ]
    },
    {
        "category_id": 1,
        "category_name": "Réseau",
        "contributions": [
            {
                "id": 7,
                "user_full_name": "Vladimir Lenin"
            }
        ]
    }
]
```

Champ | Description
--------- | -----------
category_id | Id de la catégorie du groupe de contribution.
category_name | Nom de la catégorie du groupe de contribution.
contribution | Voir contribution

#### Contribution
Champ | Description
--------- | -----------
id | Id de la contribution.
user_full_name | Nom complet du contributeur


## Supprimer une contribution

Supprime une contribution d'un LAN

### Requête HTTP

`DELETE /contribution`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN d'où l'administrateur veut supprimer une contribution. Si le paramètre n'est pas spécifié, on retourne le LAN courant | integer.
contribution_id | Id de la contribution que l'administrateur veut supprimer. Si le paramètre n'est pas spécifié, on retourne le LAN courant | integer.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 10,
    "user_full_name": "Leon Trotsky"
}
```

Champ | Description
--------- | -----------
id | Id de la contribution supprimé.
user_full_name | Nom complet du contributeur supprimé.

## Ajouter une catégorie de contribution

Ajoute une catégorie de contribution à un LAN

### Requête HTTP

`POST /contribution/category`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut ajouter une catégorie de contribution. Si le paramètre n'est pas spécifié, on retourne le LAN courant | integer.

> Exemple de requête

```json
{
    "name": "Programmeur"
}
```
### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
name | Nom de la catégorie contribution à créer. | Requis, integer.

### Format de réponse

> Exemple de réponse

```json
{
    "name": "Programmeur",
    "id": 1
}
```

Champ | Description
--------- | -----------
id | Id de la catégorie de contribution créée.
name | Nom de la catégorie de contribution créé.


## Lister les catégories de contribution

Liste les catégories de contribution d'un LAN

### Requête HTTP

`GET /contribution/category`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut ajouter une catégorie de contribution. Si le paramètre n'est pas spécifié, on retourne le LAN courant | integer.

### Format de réponse

> Exemple de réponse

```json
[
    {
        "id": 1,
        "name": "Programmeur"
    },
    {
        "id": 4,
        "name": "Réseau"
    }
]
```

Champ | Description
--------- | -----------
id | Id de la catégorie de contribution listée.
name | Nom de la catégorie de contribution listée.


## Supprimer une catégorie de contribution

Supprime une catégorie de contribution d'un LAN

### Requête HTTP

`DELETE /contribution/category`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN d'où l'administrateur veut supprimer une catégorie de contribution. Si le paramètre n'est pas spécifié, on retourne le LAN courant | integer.
contribution_category_id | Id de la catégorie de contribution que l'administrateur veut supprimer. | Requis, integer.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 4,
    "name": "Réseau"
}
```

Champ | Description
--------- | -----------
id | Id de la catégorie de contribution supprimée.
name | Nom de la catégorie de contribution supprimée.
