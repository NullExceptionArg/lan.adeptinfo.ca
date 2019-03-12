# LAN

Un LAN est une instance de l'événement organisé.

## Créer un LAN

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>create-lan</code>, can_be_per_lan <code>false</code>
</aside>

### Requête HTTP

`POST /lan`

### Paramètres POST

Si le LAN est le premier à être créé, il sera le LAN courant (is_current dans le BD).
Une liste de rôle de LAN par défaut est créé à la création du LAN. Pour en savoir plus sur la liste de rôle, veuillez vous référer à la section [Rôle de LAN](#role-de-lan)
La liste peut être trouvée dans le projet sous `api/resources/roles.php`

> Exemple de requête

```json
{
  "name": "Révolution Bolshevik",
  "lan_start": "2100-10-11 12:00:00",
  "lan_end": "2100-10-12 12:00:00",
  "seat_reservation_start": "2100-10-04 12:00:00",
  "tournament_reservation_start": "2100-10-04 00:00:00",
  "event_key":"3b8214f6-b0ae-4ed2-98b0-5d54519ccc64",
  "public_key":"19aa9acc-c576-465e-bcbf-28738cb997a4",
  "secret_key": "11cea565-a550-42d4-9b24-3dccd96fc67b",
  "places": 258,
  "latitude": -67.5,
  "longitude": 64.033333,
  "rules": "Règles importantes",
  "description": "Description exhaustive",
  "price":0
}
```

Paramètre | Description | Règles de validation | Défaut
--------- | ----------- | -------------------- | ------
name | Nom du LAN. |  chaîne de caractères, 255 caractères max. |
lan_start | Date et heure de début du LAN. |  après le début des réservations et après le début des inscriptions aux tournois. |
lan_end | Date et heure de fin du LAN. |  après le début du LAN. | 
seat_reservation_start | Date et heure du début des réservations des places du LAN. |  avant le début du LAN. |
tournament_reservation_start| Date et heure du début des inscriptions aux tournois du LAN. |  avant le début du LAN. |
event_key | Clé de l'événement de seats.io pour le LAN. |  255 caractères max. |
public_key | Clé publique de seats.io . |  255 caractères max. |
secret_key | Clé secrète de seats.io . |  255 caractères max. |
latitude | Latitude de la position où se déroule le LAN. |  entre -85 et 85, nombre. |
longitude | Longitude de la position où se déroule le LAN. |  entre -180 et 180, nombre. |
places | Places disponibles pour le LAN. |  int, minimum 1. |
price | Prix du LAN en cent. | Plus grand ou égale à 0. | 0
rules | Texte des règles du LAN. | chaîne de caractères, optionnel. |
description | Texte des description du LAN. | chaîne de caractères, optionnel. |

### Format de réponse

> Exemple de réponse

```json
{
    "id": 5,
    "name": "Révolution Bolshevik",
    "lan_start": "2100-10-11 12:00:00",
    "lan_end": "2100-10-12 12:00:00",
    "seat_reservation_start": "2100-10-04 12:00:00",
    "tournament_reservation_start": "2100-10-04 00:00:00",
    "event_key": "3b8214f6-b0ae-4ed2-98b0-5d54519ccc64",
    "public_key": "19aa9acc-c576-465e-bcbf-28738cb997a4",
    "secret_key": "11cea565-a550-42d4-9b24-3dccd96fc67b",
    "is_current": false,
    "places": 258,
    "longitude": 64.033333,
    "latitude": -67.5,
    "price": 0,
    "rules": "Règles importantes",
    "description": "Description exhaustive"
}
```

Champ | Description
--------- | -----------
id | Id du LAN créé.
name | Nom du LAN créé.
lan_start | Date et heure de début du LAN créé.
lan_end | Date et heure de fin du LAN créé.
seat_reservation_start | Date et heure du début des réservations des places du LAN créé.
tournament_reservation_start | Date et heure du début des inscriptions aux tournois du LAN créé.
event_key | Clé de l'événement de seats.io pour le LAN pour le LAN créé.
public_key | Clé publique de seats.io pour le LAN créé.
secret_key | Clé secrète de seats.io pour le LAN créé.
is_current | Si le LAN créé est le LAN courant.
places | Nombre de places disponibles pour le LAN créé.
longitude | Longitude de la position du LAN créé.
latitude | Latitude de la position du LAN créé.
price| Prix du LAN créé.
rules | Texte des règles du LAN créé.
description | Texte de la description du LAN créé.

## Détails d'un LAN

Obtenir les informations sur un LAN

### Requête HTTP

`GET /lan`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
fields | Liste des champs à obtenir pour le LAN, séparés par des virgules. Si ce paramètre est laissé vide, le LAN au complet sera retourné. Voir champs disponibles. | Aucune.
lan_id | Id du LAN dont l'utilisateur veut obtenir les informations. Si le paramètre n'est pas spécifié, on retourne le LAN courant. | entier.

#### Champs disponibles
Champ | Description
--------- | -----------
name | Nom.
lan_start | Date et heure de début.
lan_end | Date et heure de fin.
seat_reservation_start | Date et heure de début des réservation de places.
tournament_reservation_start | Date et heure de début des inscriptions aux tournois.
latitude | Latitude de la position.
longitude | Longitude de la position.
event_key | Clé de l'événement de seats.io.
public_key | Clé publique de seats.io.
secret_key | Clé secrète de seats.io.
places | Information liée à l'occupation des places.
price | Prix d'entré.
rules | Règlements.
description | Description.
images | Images de présentation.

<aside class="notice">
Pour accéder au champ <code>secret_key</code>, il est nécessaire que l'utilisateur possède la permission <code>edit-lan</code>
</aside>

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "Bolshevik Revolution",
    "lan_start": "2100-10-11 12:00:00",
    "lan_end": "2100-10-12 12:00:00",
    "seat_reservation_start": "2100-10-04 12:00:00",
    "tournament_reservation_start": "2100-10-04 00:00:00",
    "latitude": -67.5,
    "longitude": 64.033333,
    "event_key":"12345678-1234-1234-1234-123456789123",
    "public_key":"12345678-1234-1234-1234-123456789123",
    "secret_key": "12345678-1234-1234-1234-123456789123",
    "places": {
      "reserved": 178,
      "total": 258
    },
    "price": 0,
    "rules": "A spectre is haunting Europe – the spectre of communism.",
    "description": "All the powers of old Europe have entered into a holy alliance to exorcise this spectre.",
    "images": [
            {
                "id": 1,
                "image": "data:image/png;base64,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"
            }
        ]
}

```

Champ | Description
--------- | -----------
id | Id.
name | Nom.
lan_start | Date et heure de début.
lan_end | Date et heure de fin.
seat_reservation_start | Date et heure de début des réservation de places.
tournament_reservation_start | Date et heure de début des inscriptions aux tournois.
longitude | Longitude de la position.
latitude | Latitude de la position.
event_key | Clé de l'événement de seats.io.
public_key | Clé publique de seats.io.
secret_key | Clé secrète de seats.io.
places | Information liée à l'occupation des places. Voir places.
price | Prix d'entré.
rules | Règles.
description | Description.
images | Images de présentation.

#### Champ places
Champ | Description
--------- | -----------
reserved | Nombre de places réservées.
total | Nombre de places au total.

#### Champ image
Champ | Description
--------- | -----------
id | Id de l'image.
image | Contenu en base64 de l'image.

## Lister les LANs

Lister l'ensemble des LANs qui existent dans l'application.

### Requête HTTP

`GET /lan/all`

Cette requête ne nécessite aucun paramètres.

### Format de réponse

> Exemple de réponse

```json
[
    {
        "id": 1,
        "name": "Révolution Bolshevik",
        "is_current": true,
        "date": "October 1917"
    },
    {
        "id": 2,
        "name": "Publication du manifeste du parti communiste",
        "is_current": false,
        "date": "February 1848"
    }
]
```

Champ | Description
--------- | -----------
id | Id du LAN.
name | Nom du LAN.
date | Date du LAN (Mois et année).

## Mettre à jour un LAN

Mettre à jour les attributs d'un LAN

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>edit-lan</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`PUT /lan`

### Query Params

> Exemple de requête

```json
{
	"text": "A spectre is haunting Europe – the spectre of communism."
}

```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut modifier les règles. Si le paramètre n'est pas spécifié, le LAN courant est utilisé | entier.

### Paramètres POST

Paramètre | Description | Règles de validation | Défaut
--------- | ----------- | -------------------- | ------
name | Nom du LAN. | chaîne de caractères, 255 caractères max. |
lan_start | Date et heure de début du LAN. | Après le début des réservations et après le début des inscriptions aux tournois. |
lan_end | Date et heure de fin du LAN. | Après le début du LAN. | 
seat_reservation_start | Date et heure du début des réservations des places du LAN. | Avant le début du LAN. |
tournament_reservation_start| Date et heure du début des inscriptions aux tournois du LAN. | Avant le début du LAN. |
event_key | Clé de l'événement de seats.io pour le LAN. | 255 caractères max. |
public_key | Clé publique de seats.io . | 255 caractères max. |
secret_key | Clé secrète de seats.io . | 255 caractères max. |
latitude | Latitude de la position où se déroule le LAN. | Entre -85 et 85, nombre. |
longitude | Longitude de la position où se déroule le LAN. | Entre -180 et 180, nombre. |
places | Places disponibles pour le LAN. | Int, minimum 1, plus grand que le nombre de places réservées. |
price | Prix du LAN en cent. | Plus grand ou égale à 0. | 0
rules | Texte des règles du LAN. | chaîne de caractères, optionnel. |
description | Texte des descritpion du LAN. | chaîne de caractères, optionnel. |

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "name": "Bolshevik Revolution",
    "lan_start": "2100-10-11 12:00:00",
    "lan_end": "2100-10-12 12:00:00",
    "seat_reservation_start": "2100-10-04 12:00:00",
    "tournament_reservation_start": "2100-10-04 00:00:00",
    "latitude": -67.5,
    "longitude": 64.033333,
    "event_key":"12345678-1234-1234-1234-123456789123",
    "public_key":"12345678-1234-1234-1234-123456789123",
    "secret_key": "12345678-1234-1234-1234-123456789123",
    "places": {
      "reserved": 178,
      "total": 258
    },
    "price": 0,
    "rules": "A spectre is haunting Europe – the spectre of communism.",
    "description": "All the powers of old Europe have entered into a holy alliance to exorcise this spectre.",
    "images": [
        {
            "id": 1,
            "image": "data:image/png;base64,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"
        }
    ]
}

```

Champ | Description
--------- | -----------
name | Nom du LAN mis à jour.
lan_start | Date et heure de début du LAN mis à jour.
lan_end | Date et heure de fin du LAN mise à jour.
seat_reservation_start | Date et heure du début des réservations des places du LAN mise à jour.
tournament_reservation_start| Date et heure du début des inscriptions aux tournois du LAN mise à jour.
event_key | Clé de l'événement de seats.io pour le LAN mise à jour.
public_key | Clé publique de seats.io mise à jour.
secret_key | Clé secrète de seats.io mise à jour.
latitude | Latitude de la position où se déroule le LAN mis à jour.
longitude | Longitude de la position où se déroule le LAN mis à jour.
places | Places disponibles pour le LAN mis à jour.
price | Prix du LAN en cent mis à jour.
rules | Texte des règles du LAN mis à jour.
description | Texte des descritpion du LAN mis à jour.

## Changer de LAN courant

Changer de LAN courant, soit celui qui s'affichera par défaut quand les utilisateurs visiteront le site.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>set-current-lan</code>, can_be_per_lan <code>false</code>
</aside>

### Requête HTTP

`POST /lan/current`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN que l'administrateur veut modifier comme courant. |  entier.

### Format de réponse

> Exemple de réponse

```json
{
  "id": 2,
  "name": "Révolution Bolshevik",
  "lan_start": "2100-10-11 12:00:00",
  "lan_end": "2100-10-12 12:00:00",
  "seat_reservation_start": "2100-10-04 12:00:00",
  "tournament_reservation_start": "2100-10-04 00:00:00",
  "event_key":"3b8214f6-b0ae-4ed2-98b0-5d54519ccc64",
  "public_key":"19aa9acc-c576-465e-bcbf-28738cb997a4",
  "secret_key": "11cea565-a550-42d4-9b24-3dccd96fc67b",
  "places": 258,
  "latitude": -67.5,
  "longitude": 64.033333,
  "rules": "Règles importantes",
  "description": "Description exhaustive",
  "price":0
}
```

Champ | Description
--------- | -----------
id | Id du nouveau LAN courant.
name | Nom du nouveau LAN courant.
lan_start | Date et heure de début du nouveau LAN courant.
lan_end | Date et heure de fin du nouveau LAN courant.
seat_reservation_start | Date et heure du début des réservations des places du nouveau LAN courant
tournament_reservation_start | Date et heure du début des inscriptions aux tournois du nouveau LAN courant
event_key | Clé de l'événement de seats.io du nouveau LAN courant.
public_key | Clé publique de seats.io du nouveau LAN courant.
secret_key | Clé secrète de seats.io du nouveau LAN courant.
is_current | Si le LAN courant est le LAN courant (true).
places | Nombre de places disponibles du nouveau LAN courant.
longitude | Longitude de la position du nouveau LAN courant.
latitude | Latitude de la position du nouveau LAN courant.
price| Prix du nouveau LAN courant.
rules | Texte des règles du nouveau LAN courant.
description | Texte de la description du nouveau LAN courant.

## Ajouter une image

Ajouter une image de présentation à un LAN.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>add-image</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`POST /lan/image`

### Query Params
Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut ajouter une image. Si le paramètre n'est pas spécifié, le LAN courant est utilisé. | entier

### Paramètres POST
Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
image | Image à ajouter, encodé en base64. |  chaîne de caractères

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "image": "data:image/png;base64,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"
}
```

Champ | Description
--------- | -----------
lan_id | Id du LAN où l'image vient d'être ajoutée
image | Contenu de l'image créé, encodé en base64
id | Id de l'image créé.

## Supprimer des images

Supprimer des images de présentation d'un LAN.

<aside class="warning">
<a href="#permission">Permission</a> requise : <code>delete-image</code>, can_be_per_lan <code>true</code>
</aside>

### Requête HTTP

`DELETE /lan/image`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
image_ids | Id des images que l'administrateur veut supprimer. |  chaîne de caractères.

### Format de réponse

> Exemple de réponse

```json
[
    1,
    2
]
```

Champ | Description
--------- | -----------
array | Liste des ids des images qui ont été supprimées.
