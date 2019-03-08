import {Injectable} from '@angular/core';
import {environment} from '../../../environments/environment';
import {HttpClient, HttpParams} from '@angular/common/http';
import {Observable, throwError} from 'rxjs';
import {catchError} from 'rxjs/operators';

@Injectable()
/**
 * Standardisation des communications HTTP avec les resources externes.
 */
export class ApiService {

  constructor(
    private http: HttpClient
  ) {
  }

  /**
   * Formatage des erreurs retournées par une requête
   * @param error Erreurs
   */
  private static formatErrors(error: any) {
    return throwError(error.error);
  }

  /**
   * Effectuer une requête GET.
   * @param path Chemin de la requête
   * @param params Paramètres de la requête
   */
  get(path: string, params: HttpParams = new HttpParams()): Observable<any> {
    return this.http.get(`${environment.apiUrl}${path}`, {params})
      .pipe(catchError(ApiService.formatErrors));
  }

  /**
   * Effectuer une requête PUT.
   * @param path Chemin de la requête
   * @param body Paramètres du corps de la requête
   */
  put(path: string, body: Object = {}): Observable<any> {
    return this.http.put(
      `${environment.apiUrl}${path}`,
      JSON.stringify(body)
    ).pipe(catchError(ApiService.formatErrors));
  }

  /**
   * Effectuer une requête POST.
   * @param path Chemin de la requête
   * @param body Paramètres du corps de la requête
   */
  post(path: string, body: Object = {}): Observable<any> {
    return this.http.post(
      `${environment.apiUrl}${path}`,
      JSON.stringify(body)
    ).pipe(catchError(ApiService.formatErrors));
  }

  /**
   * Effectuer une requête DELETE.
   * @param path Chemin de la requête
   * @param params Paramètres de la requête
   */
  delete(path: string, params: HttpParams = new HttpParams()): Observable<any> {
    return this.http.delete(
      `${environment.apiUrl}${path}`
    ).pipe(catchError(ApiService.formatErrors));
  }
}
