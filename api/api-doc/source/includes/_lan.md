# LAN

## Créer

Créer un nouveau LAN.

### Requête HTTP

`POST /api/lan`

### Paramètres POST

> Exemple de requȩte

```json
{
  "lan_start": "2100-10-11T12:00:00-05:00",
  "lan_end": "2100-10-12T12:00:00-05:00",
  "seat_reservation_start": "2100-10-04T12:00:00-05:00",
  "tournament_reservation_start": "2100-10-04T00:00:00-05:00",
  "event_key_id":"12345678-1234-1234-1234-123456789123",
  "public_key_id":"12345678-1234-1234-1234-123456789123",
  "secret_key_id": "12345678-1234-1234-1234-123456789123",
  "price":"0"
}

```

Paramètre | Description | Règles de validation
--------- | ----------- | --------------------
lan_start | Date et heure de début du LAN. | Requis, après le début des réservations et après le début des inscriptions aux tournois.
lan_end | Date et heure de fin du LAN. | Requis, après le début du LAN.
seat_reservation_start | Date et heure du début des réservations des places du LAN. | Requis, après maintenant.
tournament_reservation_start| Date et heure du début des inscriptions aux tournois du LAN. | Requis, après maintenant.
event_key_id | Clé de l'événement de seats.io pour le LAN. | Requis, 255 caractères max.
public_key_id | Clé publique de seats.io . | Requis, 255 caractères max.
secret_key_id | Clé secrète de seats.io . | Requis, 255 caractères max.
price | Prix du LAN. | Requis, plus grand ou égale à 0.

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
id | Id du LAN créé.
