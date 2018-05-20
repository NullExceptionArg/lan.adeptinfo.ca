# Contribution

Les organisateurs peuvent remercier ceux qui ont donnés de leur temps à l'aide de cette liste de contributeurs

## Ajouter une contribution

Ajouter une contribution à un LAN

### Requête HTTP

`POST /api/lan/{lan_id}/contribution`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut ajouter une contribution. | Requis, string.

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