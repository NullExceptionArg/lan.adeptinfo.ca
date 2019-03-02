# Contribution

Les organisateurs peuvent remercier ceux qui ont donné de leur temps à un LAN à l'aide de cette liste de contributeurs.

## Créer une contribution

Un administrateur créer une contribution à un LAN

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>create-contribution</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`POST /contribution`

> Exemple de requête

```json
{
	"contribution_category_id": 1,
	"user_email": "karl.marx@unite.org"
}
```
### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
contribution_category_id | Id de la catégorie de la contribution à créer. |  entier.
user_full_name | Nom complet du contributeur. | chaîne de caractères.
user_email | Adresse courriel du contributeur. | chaîne de caractères.

<aside class="notice">
Les paramètres <code>user_full_name</code> et <code>user_email</code> ne peuvent pas être utilisés en même temps. Il est cependant requis d'envoyer un de ces deux champs.
</aside>

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "user_full_name": "Karl Marx"
}
```

Champ | Description
--------- | -----------
id | Id de la contribution créée.
user_full_name | Nom complet du contributeur créé.

## Lister les contributions

Lister l'ensemble des contributions, groupées par catégories, pour un LAN.

### Requête HTTP

`GET /contribution`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN d'où l'utilisateur veut lister les contributions. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier.

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

Un administrateur supprime une contribution d'un LAN.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>delete-contribution</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`DELETE /contribution`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
contribution_id | Id de la contribution que l'administrateur veut supprimer. | entier.

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

## Créer une catégorie de contribution

Un administrateur créer une catégorie de contribution à un LAN.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>create-contribution-category</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`POST /contribution/category`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut ajouter une catégorie de contribution. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier.

> Exemple de requête

```json
{
    "name": "Programmeur"
}
```
### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
name | Nom de la catégorie contribution à créer. |  entier.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "Programmeur"
}
```

Champ | Description
--------- | -----------
id | Id de la catégorie de contribution créée.
name | Nom de la catégorie de contribution créé.


## Lister les catégories de contribution

Liste les catégories de contribution d'un LAN.

### Requête HTTP

`GET /contribution/category`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut ajouter une catégorie de contribution. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier.

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

Un administrateur supprime une catégorie de contribution d'un LAN.

### Requête HTTP

`DELETE /contribution/category`

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>delete-contribution-category</code>, can_be_per_lan <code>true</code>
</aside>

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
contribution_category_id | Id de la catégorie de contribution que l'administrateur veut supprimer. |  entier.

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
