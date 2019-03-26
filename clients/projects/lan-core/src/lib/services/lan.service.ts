import {Injectable} from '@angular/core';
import {Observable, ReplaySubject} from 'rxjs';
import {ApiService} from './api.service';
import {Lan} from '../models/api/lan';
import {map} from 'rxjs/operators';

@Injectable()
/**
 * Actions liées LANs.
 */
export class LanService {

  // Observables de l'utilisateur courant
  private currentLanSubject = new ReplaySubject<Lan>(1);
  public currentLan = this.currentLanSubject.asObservable();

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
   * @param lan LAN à rendre courant
   */
  setCurrentLan(lan: Lan): void {
    // Rendre un LAN observable
    this.currentLanSubject.next(lan);
  }

  /**
   * Créer un LAN
   * @param lan LAN à rendre courant
   */
  createLan(lan: Lan): Observable<Lan> {
    return this.apiService.post('/lan', {
      name: lan.name,
      lan_start: lan.lan_start,
      lan_end: lan.lan_end,
      seat_reservation_start: lan.seat_reservation_start,
      tournament_reservation_start: lan.tournament_reservation_start,
      places: lan.places,
      price: lan.price,
      event_key: lan.event_key,
      latitude: lan.latitude,
      longitude: lan.longitude,
      rules: lan.rules,
      description: lan.description
    })
      .pipe(map(data => data));
  }

}
