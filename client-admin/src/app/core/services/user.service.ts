import {Injectable} from '@angular/core';
import {BehaviorSubject, Observable, ReplaySubject} from 'rxjs';

import {distinctUntilChanged, map} from 'rxjs/operators';
import {User} from '../models/user';
import {ApiService} from './api.service';
import {JwtService} from './jwt.service';

@Injectable()
export class UserService {

  private currentUserSubject = new BehaviorSubject<User>({} as User);
  public currentUser = this.currentUserSubject.asObservable().pipe(distinctUntilChanged());

  private isAuthenticatedSubject = new ReplaySubject<boolean>(1);
  public isAuthenticated = this.isAuthenticatedSubject.asObservable();

  constructor(
    private apiService: ApiService) {
  }

  // Verify JWT in localstorage with server & load user's info.
  // This runs once on application startup.
  populate() {
    // If JWT detected, attempt to get & store user's info
    if (JwtService.getToken()) {
      this.apiService.get('/admin/summary')
        .subscribe(
          data => this.setAuth(data.user),
          err => this.purgeAuth()
        );
    } else {
      // Remove any potential remnants of previous auth states
      this.purgeAuth();

    }
  }

  /**
   * Détails de l'authentification.
   * @param user Utilisateur authentifié
   */
  setAuth(user: User) {
    // Sauvegarder le JWT renvoyé du serveur dans le localstorage
    JwtService.saveToken(user.token);
    // Rendre les données de l'utilisateur courant observables
    this.currentUserSubject.next(user);
    // Mettre isAuthenticated à true
    this.isAuthenticatedSubject.next(true);
  }

  purgeAuth() {
    // Remove JWT from localstorage
    JwtService.destroyToken();
    // Set current user to an empty object
    this.currentUserSubject.next({} as User);
    // Set auth status to false
    this.isAuthenticatedSubject.next(false);
  }

  /**
   * Tentative d'obtention d'un JWT.
   * @param credentials Informations de l'utilisateur qui tente de se connecter
   */
  attemptAuth(credentials): Observable<User> {
    return this.apiService.post('/oauth/token', {user: credentials})
      .pipe(map(
        data => {
          this.setAuth(data.user);
          return data;
        }
      ));
  }

  /**
   * Obtenir les détails de l'utilisateur courant.
   */
  getCurrentUser(): User {
    return this.currentUserSubject.value;
  }
}
