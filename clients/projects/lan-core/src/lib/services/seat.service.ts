import {Injectable} from '@angular/core';
import {Observable} from 'rxjs';
import {ApiService} from './api.service';
import {HttpClient} from '@angular/common/http';

@Injectable()
/**
 * Actions liées places des joueurs.
 */
export class SeatService {

  constructor(
    private apiService: ApiService,
    private http: HttpClient) {
  }

  /**
   * Obtenir la liste des événements disponibles dans seats.io
   */
  getSeatsioEvents(): Observable<any> {
    return this.apiService.get('/seat/charts');
  }
}
