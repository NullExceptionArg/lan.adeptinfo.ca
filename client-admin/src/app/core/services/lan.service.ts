import {Injectable} from '@angular/core';
import {Observable, ReplaySubject} from 'rxjs';
import {ApiService} from './api.service';
import {Lan} from '../models/lan';

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
   * Obtenir le LAN courant.
   */
  // getCurrentLan(): Lan {
  //   return this.currentLanSubject.value;
  // }
}
