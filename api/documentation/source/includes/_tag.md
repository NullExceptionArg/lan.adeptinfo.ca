# Tag

Un utilisateur peut se créer des tags avec lesquels il peut faire partie d'équipes qui participeront à un tournoi.

## Créer un tag

L'utilisateur courant se créer un tag.

### Requête HTTP

`POST /tag`

### Body Params

> Exemple de requête

```json
{
	"name": "PRO"
}
```

### Paramètres POST

Paramètre | Description | Règles de validation | Défaut
--------- | ----------- | -------------------- | ------
name | Nom du tag de joueur à créer. |  chaîne de caract, 5 caractères max, unique. |

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "PRO"
}
```

Champ | Description
--------- | -----------
id | Id du tag de joueur créé. 
name | Nom du tag de joueur créé. 