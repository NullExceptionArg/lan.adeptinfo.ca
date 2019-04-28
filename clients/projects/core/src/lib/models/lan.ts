/**
 * Événements qui rassemble joueurs et joueuses.
 */
export class Lan {

  // Id du LAN
  id: number;

  // Nom du LAN
  name: string;

  // Date de début du LAN
  lanStart: Date;

  // Date de fin du LAN
  lanEnd: Date;

  // Date de début de réservation des places
  seatReservationStart: Date;

  // Date de début de d'inscription aux tournois
  tournamentReservationStart: Date;

  // Clé d'événement de seats.io
  eventKey: string;

  // Latitude de la position du LAN
  latitude: number;

  // Longitude de la position du LAN
  longitude: number;

  // Nombre de places disponibles
  places: number;

  // Nombre de places réservées
  reserved: number;

  // Prix d'entrée du LAN
  price: number;

  // Règlements du LAN
  rules: string;

  // Description du LAN
  description: string;

  // S'il s'agit du LAN courant
  isCurrent: boolean;

  // Nom court du LAN (Mois Année)
  date: string;

}
