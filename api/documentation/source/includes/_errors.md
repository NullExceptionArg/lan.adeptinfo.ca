# Erreurs

L'API du LAN de l'ADEPT peut renvoyer les code d'erreur suivants:

Code | Signification
---------- | -------
400 | Bad Request -- Votre reqûete n'est pas valide. Peut contenir des détails sur l'erreur.
401 | Unauthorized -- Votre clé d'API n'est pas valide.
403 | Forbidden -- Cette section est destinée à un autre rôle.
404 | Not Found -- L'URL demandé n'existe pas.
418 | I'm a teapot.
500 | Internal Server Error -- Erreur innatendu. Vous seriez gentil de communiquer avec l'équipe de développement si vous voyez cette erreur.
503 | Service Unavailable -- Nous sommes temporairement hors ligne. Veuillez réessayer plus tard.

### Format d'erreur

Chaque erreur devrait être retourné sous la structure suivante:

> Exemple d'erreur 400 pour une requête de création de compte

```json
{
    "success": false,
    "status": 400,
    "message": {
        "first_name": [
            "The first name field is required."
        ],
        "last_name": [
            "The last name field is required."
        ],
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password field is required."
        ]
    }
}
```

Champ | Description
---------- | -------
success | Bad Request -- Votre reqûete n'est pas valide.
status | Code d'erreurs.
message | Contient une liste d'erreurs sur chaque champs fautifs.
