import {Permission} from './permission';

/**
 * Utilisateur de l'application.
 */
export class User {

  firstName: string;
  lastName: string;
  hasTournaments: string;
  email: string;
  accessToken: string;
  permissions: Permission[];
}
