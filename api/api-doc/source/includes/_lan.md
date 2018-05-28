# LAN

## Créer un LAN

Créer un nouveau LAN.

### Requête HTTP

`POST /api/lan`

### Paramètres POST

> Exemple de requête

```json
{
  "name": "Bolshevik Revolution",
  "lan_start": "2100-10-11 12:00:00-05:00",
  "lan_end": "2100-10-12 12:00:00-05:00",
  "seat_reservation_start": "2100-10-04 12:00:00-05:00",
  "tournament_reservation_start": "2100-10-04 00:00:00-05:00",
  "event_key_id":"12345678-1234-1234-1234-123456789123",
  "public_key_id":"12345678-1234-1234-1234-123456789123",
  "secret_key_id": "12345678-1234-1234-1234-123456789123",
  "places": "258",
  "latitude": -67.5,
  "longitude": 64.033333,
  "price":"0",
  "rules":"A spectre is haunting Europe – the spectre of communism.",
  "description":"All the powers of old Europe have entered into a holy alliance to exorcise this spectre."
}

```

Paramètre | Description | Règles de validation | Defaut
--------- | ----------- | -------------------- | ------
name | Nom du LAN. | Requis, string, 255 caractères max. |
lan_start | Date et heure de début du LAN. | Requis, après le début des réservations et après le début des inscriptions aux tournois. |
lan_end | Date et heure de fin du LAN. | Requis, après le début du LAN. | 
seat_reservation_start | Date et heure du début des réservations des places du LAN. | Requis, après maintenant. |
tournament_reservation_start| Date et heure du début des inscriptions aux tournois du LAN. | Requis, après maintenant. |
event_key_id | Clé de l'événement de seats.io pour le LAN. | Requis, 255 caractères max. |
public_key_id | Clé publique de seats.io . | Requis, 255 caractères max. |
secret_key_id | Clé secrète de seats.io . | Requis, 255 caractères max. |
latitude | Latitude de la position où se déroule le LAN. | Requis, entre -85 et 85, nombre. |
longitude | Longitude de la position où se déroule le LAN. | Requis, entre -180 et 180, nombre. |
places | Places disponibles pour le LAN. | Requis, int, minimum 1. |
price | Prix du LAN. | Plus grand ou égale à 0. | 0
rules | Texte des règles du LAN. | String, optionnel. |
description | Texte des descritpion du LAN. | String, optionnel. |

### Format de réponse

> Exemple de réponse

```json
{
    "name": "Bolshevik Revolution",
    "lan_start": "2100-10-11 12:00:00",
    "lan_end": "2100-10-12 12:00:00",
    "seat_reservation_start": "2100-10-04 12:00:00",
    "tournament_reservation_start": "2100-10-04 00:00:00",
    "event_key_id": "12345678-1234-1234-1234-123456789123",
    "public_key_id": "12345678-1234-1234-1234-123456789123",
    "secret_key_id": "12345678-1234-1234-1234-123456789123",
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
event_key_id | Clé de l'événement de seats.io pour le LAN pour le LAN créé.
public_key_id | Clé publique de seats.io pour le LAN créé.
secret_key_id | Clé secrète de seats.io pour le LAN créé.
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

`GET /api/lan/<lan_id>`

### Path Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN dont l'utilisateur veut obtenir les informations. | Requis, string.

### Query Params

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
fields | Liste des champs à obtenir pour le LAN, séparés par des virgules. Si ce paramètre est laissé vide, le LAN au complet sera retourné. | Aucune.

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
price | Prix d'entré au LAN.
rules | Règles.

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
    "places": {
      "reserved": 178,
      "total": 258
    },
    "price": 0,
    "rules": "A spectre is haunting Europe – the spectre of communism.",
    "description": "All the powers of old Europe have entered into a holy alliance to exorcise this spectre."
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
description | Description du LAN.

#### places
Champ | Description
--------- | -----------
reserved | Places réservées
total | Nombre de places total

## Mettre à jour les règles

Mettre à jour les règles d'un LAN

### Requête HTTP

`POST /api/lan/<lan_id>/rules`

### Path Params

> Exemple de requête

```json
{
	"text": "A spectre is haunting Europe – the spectre of communism."
}

```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_id | Id du LAN où l'administrateur veut modifier les règles. | Requis, string.

### Paramètres POST

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
text | Texte des règles du LAN à mettre à jour. | Requis, string.

### Format de réponse

> Exemple de réponse

```json
{
    "text": "A spectre is haunting Europe – the spectre of communism."
}

```

Champ | Description
--------- | -----------
text | Texte des nouvelles règles du LAN.
