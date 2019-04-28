/**
 * Fonctions pratique sur les dates.
 */
import {Moment} from 'moment';

export class DateUtils {

  /**
   * Obtenir une date et heure à partir d'une date et une heure
   * @param date Date
   * @param time Heure
   * @return Date et heure
   */
  public static getDateFromMomentAndString(date: Moment, time: string): Date {
    const t = (<string>time).split(':');
    if (date.toString() === '' || time === '') {
      return null;
    } else {
      return new Date(date.year(), date.month(), date.date(), +t[0], +t[1]);
    }
  }
}
