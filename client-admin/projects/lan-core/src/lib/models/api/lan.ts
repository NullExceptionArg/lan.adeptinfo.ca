/**
 * Événements qui rassemble joueurs et joueuses.
 */
export class Lan {

  // Id du LAN
  id: number;

  // Nom du LAN
  name: string;

  // Date de début du LAN
  lan_start: Date;

  // Date de fin du LAN
  lan_end: Date;

  // Date de début de réservation des places
  seat_reservation_start: Date;

  // Date de début de d'inscription aux tournois
  tournament_reservation_start: Date;

  // Clé d'événement de seats.io
  event_key: string;

  // Latitude de la position du LAN
  latitude: number;

  // Longitude de la position du LAN
  longitude: number;

  // Nombre de places disponibles
  places: number;

  // Prix d'entrée du LAN
  price: number;

  // Règlements du LAN
  rules: string;

  // Description du LAN
  description: string;

  // S'il s'agit du LAN courant
  is_current: boolean;

  // Nom court du LAN (Mois Année)
  date: string;


  constructor(
    name: string,
    lan_start: Date,
    lan_end: Date,
    seat_reservation_start: Date,
    tournament_reservation_start: Date,
    places: number,
    price: number,
    event_key: string,
    latitude: number,
    longitude: number,
    rules: string,
    description: string
  ) {
    this.name = name;
    this.lan_start = lan_start;
    this.lan_end = lan_end;
    this.seat_reservation_start = seat_reservation_start;
    this.tournament_reservation_start = tournament_reservation_start;
    this.places = places;
    this.price = price;
    this.event_key = event_key;
    this.latitude = latitude;
    this.longitude = longitude;
    this.rules = rules;
    this.description = description;
  }
}
