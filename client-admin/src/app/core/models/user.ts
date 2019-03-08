import {Permission} from './permission';

/**
 * Utilisateur de l'application.
 */
export class User {

  first_name: string;
  last_name: string;
  has_tournaments: string;
  email: string;
  access_token: string;
  permissions: Permission[];
}
