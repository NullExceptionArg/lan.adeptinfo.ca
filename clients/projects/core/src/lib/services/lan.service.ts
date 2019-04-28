import {Injectable} from '@angular/core';
import {Observable, ReplaySubject} from 'rxjs';
import {ApiService} from './api.service';
import {map} from 'rxjs/operators';
import {HttpParams} from '@angular/common/http';
import {Lan} from '../models/lan';

@Injectable()
/**
 * Actions liées aux LANs.
 */
export class LanService {

  // Observables du LAN courant
  private currentLanSubject = new ReplaySubject<Lan>(1);
  public currentLan = this.currentLanSubject.asObservable();

  // Observable du LAN courant existant
  private currentLanReplaySubject = new ReplaySubject<Lan>(1);
  public currentLanReplay = this.currentLanReplaySubject.asObservable();

  constructor(
    private apiService: ApiService) {
  }

  /**
   * Obtenir les LANs de l'application
   */
  getAll(): Observable<Lan[]> {
    return this.apiService.get('/lan/all');
  }

  /**
   * Rendre un LAN courant.
   * @param lanId LAN à rendre courant
   */
  setCurrentLan(lanId: number): Observable<Lan> {
    return this.apiService.post('/lan/current', {
      lan_id: lanId
    });
  }

  /**
   * Obtenir les détails d'un LAN.
   * @param lanId Id du LAN à obtenir. Si nulle, demander le LAN courant.
   */
  getLan(lanId?: number): Observable<Lan> {

    const params = new HttpParams();

    if (lanId != null) {
      params.append('lan_id', lanId.toString());
    }

    return this.apiService.get('/lan', params)
      .pipe(
        map((data: any) => {
          let lan;
          if (data.length !== 0) {
            lan = new Lan();
            lan.id = data.id;
            lan.name = data.name;
            lan.price = data.price;
            lan.places = data.places.total;
            lan.reserved = data.places.reserved;
            lan.lanStart = new Date(data.lan_start);
            lan.lanEnd = new Date(data.lan_end);
            lan.seatReservationStart = new Date(data.seat_reservation_start);
            lan.tournamentReservationStart = new Date(data.tournament_reservation_start);
            lan.eventKey = data.event_key;
            lan.latitude = data.latitude;
            lan.longitude = data.longitude;
            lan.rules = data.rules;
            lan.description = data.description;
            lan.isCurrent = data.is_current;
            lan.date = data.date;
          } else {
            lan = null;
          }
          this.currentLanSubject.next(lan);
          this.currentLanReplaySubject.next(lan);
          return lan;
        })
      );
  }

  /**
   * Créer un LAN
   * @param lan LAN à rendre courant
   */
  createLan(lan: Lan): Observable<Lan> {
    return this.apiService.post('/lan', {
      name: lan.name,
      lan_start: lan.lanStart,
      lan_end: lan.lanEnd,
      seat_reservation_start: lan.seatReservationStart,
      tournament_reservation_start: lan.tournamentReservationStart,
      places: lan.places,
      price: lan.price,
      event_key: lan.eventKey,
      latitude: lan.latitude,
      longitude: lan.longitude,
      rules: lan.rules,
      description: lan.description
    });
  }

}
