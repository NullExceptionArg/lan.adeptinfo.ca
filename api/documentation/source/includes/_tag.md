# Tag

## Créer un tag

Créer un nouveau tag pour un utilisateur.

### Requête HTTP

`POST /api/tag`

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
name | Nom du tag. | Requis, string, 5 caractères max, unique. |

### Format de réponse

> Exemple de réponse

```json
{
    "lan_id": 1,
    "name": "PRO"
}
```

Champ | Description
--------- | -----------
name | Nom du tournoi. 