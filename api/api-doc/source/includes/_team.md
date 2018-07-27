# Équipe

## Créer une équipe

Créer une nouvelle équipe pour participer à un tournoi.

### Requête HTTP

`POST /api/team`

### POST Params

> Exemple de requête

```json
{
	"tournament_id": 1,
	"user_tag_id": 1,
	"name": "WorkersUnite",
	"tag": "PRO"
}
```

L'utilisateur qui créer une équipe en devient le chef automatiquement.

### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
tournament_id | Id du tournoi dans lequel l'utilisateur veut créer son équipe | Requis
user_tag_id | Id du tag sous lequel l'utilisateur souhaite créer et rejoindre l'équipe. | Requis, l'utilisateur peut seulement être dans un tournoi une fois.
name | Nom de l'équipe. | Requis, string, 255 caractères max, le nom doit être unique pour le tournoi.
tag | Nom du tag. | String, 5 caractères max, le tag doit être unique pour le tournoi.

### Format de réponse

> Exemple de réponse

```json
{
	"id": 1,
	"tournament_id": 1,
	"name": "WorkersUnite",
	"tag": "PRO"
}
```

Paramètre | Description
--------- | -----------
tournament_id | Id du tournoi dans lequel l'utilisateur a créer son équipe
user_tag_id | Id du tag sous lequel l'utilisateur a créer et rejoint l'équipe.
name | Nom de l'équipe créée.
tag | Nom du tag créée.