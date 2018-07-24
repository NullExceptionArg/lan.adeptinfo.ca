# LAN

## Créer un LAN

Créer un nouveau LAN.

### Requête HTTP

`POST /api/lan`

### Paramètres POST

Si le LAN est le premier à être créé, il sera le LAN courant (is_current dans le BD).

> Exemple de requête

```json
{
  "name": "Bolshevik Revolution",
  "lan_start": "2100-10-11 12:00:00-05:00",
  "lan_end": "2100-10-12 12:00:00-05:00",
  "seat_reservation_start": "2100-10-04 12:00:00-05:00",
  "tournament_reservation_start": "2100-10-04 00:00:00-05:00",
  "event_key":"12345678-1234-1234-1234-123456789123",
  "public_key":"12345678-1234-1234-1234-123456789123",
  "secret_key": "12345678-1234-1234-1234-123456789123",
  "places": "258",
  "latitude": -67.5,
  "longitude": 64.033333,
  "price":"0",
  "rules":"A spectre is haunting Europe – the spectre of communism.",
  "description":"All the powers of old Europe have entered into a holy alliance to exorcise this spectre."
}

```

Paramètre | Description | Règles de validation | Défaut
--------- | ----------- | -------------------- | ------
name | Nom du LAN. | Requis, string, 255 caractères max. |
lan_start | Date et heure de début du LAN. | Requis, après le début des réservations et après le début des inscriptions aux tournois. |
lan_end | Date et heure de fin du LAN. | Requis, après le début du LAN. | 
seat_reservation_start | Date et heure du début des réservations des places du LAN. | Requis, avant le début du LAN. |
tournament_reservation_start| Date et heure du début des inscriptions aux tournois du LAN. | Requis, avant le début du LAN. |
event_key | Clé de l'événement de seats.io pour le LAN. | Requis, 255 caractères max. |
public_key | Clé publique de seats.io . | Requis, 255 caractères max. |
secret_key | Clé secrète de seats.io . | Requis, 255 caractères max. |
latitude | Latitude de la position où se déroule le LAN. | Requis, entre -85 et 85, nombre. |
longitude | Longitude de la position où se déroule le LAN. | Requis, entre -180 et 180, nombre. |
places | Places disponibles pour le LAN. | Requis, int, minimum 1. |
price | Prix du LAN en cent. | Plus grand ou égale à 0. | 0
rules | Texte des règles du LAN. | String, optionnel. |
description | Texte des description du LAN. | String, optionnel. |

### Format de réponse

> Exemple de réponse

```json
{
    "name": "Bolshevik Revolution",
    "lan_start": "2100-10-11 12:00:00",
    "lan_end": "2100-10-12 12:00:00",
    "seat_reservation_start": "2100-10-04 12:00:00",
    "tournament_reservation_start": "2100-10-04 00:00:00",
    "event_key": "12345678-1234-1234-1234-123456789123",
    "public_key": "12345678-1234-1234-1234-123456789123",
    "secret_key": "12345678-1234-1234-1234-123456789123",
    "places": "258",
    "latitude": -67.5,
    "longitude": 64.033333,
    "price": 0,
    "rules": "A spectre is haunting Europe – the spectre of communism.",
    "description": "All the powers of old Europe have entered into a holy alliance to exorcise this spectre.",
    "id": 1
}

```

Champ | Description
--------- | -----------
name | Nom du LAN créé.
lan_start | Date et heure de début du LAN créé.
lan_end | Date et heure de fin du LAN créé.
seat_reservation_start | Date et heure du début des réservations des places du LAN créé.
tournament_reservation_start | Date et heure du début des inscriptions aux tournois du LAN créé.
event_key | Clé de l'événement de seats.io pour le LAN pour le LAN créé.
public_key | Clé publique de seats.io pour le LAN créé.
secret_key | Clé secrète de seats.io pour le LAN créé.
latitude | Latitude de la position du LAN créé.
longitude | Longitude de la position du LAN créé.
places | Places disponibles pour le LAN créé
price| Prix du LAN créé.
rules | Texte des règles du LAN créé.
description | Texte de la description du LAN créé.
id | Id du LAN créé.

## Obtenir un LAN

Obtenir les informations sur un LAN

### Requête HTTP

`GET /api/lan`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
fields | Liste des champs à obtenir pour le LAN, séparés par des virgules. Si ce paramètre est laissé vide, le LAN au complet sera retourné. Voir champs disponibles | Aucune.
lan_id | Id du LAN dont l'utilisateur veut obtenir les informations. Si paramètre n'est pas spécifié, on retourne le LAN courant| integer.

#### Champs disponibles
Champ | Description
--------- | -----------
name | Nom du LAN.
lan_start | Date et heure de début du LAN.
lan_end | Date et heure de fin du LAN.
seat_reservation_start | Date et heure de début des réservation de places.
tournament_reservation_start | Date et heure de début des inscriptions aux tournois.
places | Information liée à l'occupation des places
latitude | Latitude de la position du LAN.
longitude | Longitude de la position du LAN
event_key | Clé de l'événement de seats.io pour le LAN.
public_key | Clé publique de seats.io .
secret_key | Clé secrète de seats.io .
price | Prix d'entré au LAN.
rules | Règles du LAN.
images | Images de présentation du LAN.

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
                "lan_id": 1,
                "image": "data:image/png;base64,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"
            }
        ]
}

```

Champ | Description
--------- | -----------
name | Nom du LAN.
lan_start | Date et heure de début du LAN.
lan_end | Date et heure de fin du LAN.
seat_reservation_start | Date et heure de début des réservation de places.
tournament_reservation_start | Date et heure de début des inscriptions aux tournois.
latitude | Latitude de la position du LAN.
longitude | Longitude de la position du LAN 
places | Voir places
price | Prix d'entré au LAN.
rules | Règles du LAN.
event_key | Clé de l'événement de seats.io pour le LAN.
public_key | Clé publique de seats.io .
secret_key | Clé secrète de seats.io .
description | Description du LAN.
images | Liste d'images. Voir image.

#### Champ places
Champ | Description
--------- | -----------
reserved | Places réservées
total | Nombre de places total

#### Champ image
Champ | Description
--------- | -----------
id | Id de l'image.
lan_id | Id du LAN auquel appartient l'image.
image | Contenu en base64 de l'image.

## Obtenir les LAN

Obtenir l'ensemble des LANs

### Requête HTTP

`GET /api/lan/all`

Cette requête ne nécessite aucuns paramètres

### Format de réponse

> Exemple de réponse

```json
[
    {
        "id": 1,
        "name": "Bolshevik Revolution",
        "date": "October 1917"
    },
    {
        "id": 2,
        "name": "Communist Manifesto publication",
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

### Requête HTTP

`PUT /api/lan`

### Query Params

> Exemple de requête

```json
{
	"text": "A spectre is haunting Europe – the spectre of communism."
}

```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut modifier les règles. Si paramètre n'est pas spécifié, on retourne le LAN courant | integer.

### Paramètres POST

Paramètre | Description | Règles de validation | Defaut
--------- | ----------- | -------------------- | ------
name | Nom du LAN. | String, 255 caractères max. |
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
rules | Texte des règles du LAN. | String, optionnel. |
description | Texte des descritpion du LAN. | String, optionnel. |

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
            "lan_id": 1,
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

## Changer le LAN courant

Changer le LAN courant, soit celui qui s'affichera par défaut quand les utilisateurs visiteront le site.

### Requête HTTP

`POST /api/lan/current`

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN que l'administrateur veut modifier comme courant. | Requis, integer.

### Format de réponse

> Exemple de réponse

```json

1

```

Champ | Description
--------- | -----------
int | Id du LAN qui vient d'être modifié comme courant.
