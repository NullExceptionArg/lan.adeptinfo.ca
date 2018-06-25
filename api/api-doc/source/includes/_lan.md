# LAN

## Créer un LAN

Créer un nouveau LAN.

### Requête HTTP

`POST /api/lan`

### Paramètres POST

> Exemple de requête

```json
{
  "lan_start": "2100-10-11T12:00:00-05:00",
  "lan_end": "2100-10-12T12:00:00-05:00",
  "seat_reservation_start": "2100-10-04T12:00:00-05:00",
  "tournament_reservation_start": "2100-10-04T00:00:00-05:00",
  "event_key_id":"12345678-1234-1234-1234-123456789123",
  "public_key_id":"12345678-1234-1234-1234-123456789123",
  "secret_key_id": "12345678-1234-1234-1234-123456789123",
  "price":"0",
  "rules":"A spectre is haunting Europe – the spectre of communism."
}

```

Paramètre | Description | Règles de validation | Defaut
--------- | ----------- | -------------------- | ------
lan_start | Date et heure de début du LAN. | Requis, après le début des réservations et après le début des inscriptions aux tournois. |
lan_end | Date et heure de fin du LAN. | Requis, après le début du LAN. | 
seat_reservation_start | Date et heure du début des réservations des places du LAN. | Requis, après maintenant. |
tournament_reservation_start| Date et heure du début des inscriptions aux tournois du LAN. | Requis, après maintenant. |
event_key_id | Clé de l'événement de seats.io pour le LAN. | Requis, 255 caractères max. |
public_key_id | Clé publique de seats.io . | Requis, 255 caractères max. |
secret_key_id | Clé secrète de seats.io . | Requis, 255 caractères max. |
price | Prix du LAN. | Plus grand ou égale à 0. | 0
rules | Texte des règles du LAN. | String. |

### Format de réponse

> Exemple de réponse

```json
{
    "lan_start": "2100-10-11T12:00:00",
    "lan_end": "2100-10-12T12:00:00",
    "seat_reservation_start": "2100-10-04T12:00:00",
    "tournament_reservation_start": "2100-10-04T00:00:00",
    "event_key_id": "12345678-1234-1234-1234-123456789123",
    "public_key_id": "12345678-1234-1234-1234-123456789123",
    "secret_key_id": "12345678-1234-1234-1234-123456789123",
    "price": 0,
    "rules": "A spectre is haunting Europe – the spectre of communism.",
    "id": 1
}

```

Champ | Description
--------- | -----------
lan_start | Date et heure de début du LAN créé.
lan_end | Date et heure de fin du LAN créé.
seat_reservation_start | Date et heure du début des réservations des places du LAN créé.
tournament_reservation_start | Date et heure du début des inscriptions aux tournois du LAN créé.
event_key_id | Clé de l'événement de seats.io pour le LAN pour le LAN créé.
public_key_id | Clé publique de seats.io pour le LAN créé.
secret_key_id | Clé secrète de seats.io pour le LAN créé.
price| Prix du LAN créé.
rules | Texte des règles du LAN créé.
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
fields | Liste des champs à obtenir pour le LAN. Si ce paramètre est laissé vide, le LAN au complet sera retourné. Voir champs disponibles | Aucune.

#### Champs disponibles
Champ | Description
--------- | -----------
lan_start | Date et heure de début du LAN.
lan_end | Date et heure de fin du LAN.
seat_reservation_start | Date et heure de début des réservation de places.
tournament_reservation_start | Date et heure de début des inscriptions aux tournois.
price | Prix d'entré au LAN.
rules | Règles.

### Format de réponse

> Exemple de réponse

```json
{
    "id": 1,
    "lan_start": "2100-10-11 12:00:00",
    "lan_end": "2100-10-12 12:00:00",
    "seat_reservation_start": "2100-10-04 12:00:00",
    "tournament_reservation_start": "2100-10-04 00:00:00",
    "price": 0,
    "rules": "A spectre is haunting Europe – the spectre of communism."
}

```

Champ | Description
--------- | -----------
lan_start | Date et heure de début du LAN.
lan_end | Date et heure de fin du LAN.
seat_reservation_start | Date et heure de début des réservation de places.
tournament_reservation_start | Date et heure de début des inscriptions aux tournois.
price | Prix d'entré au LAN.
rules | Règles.

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
