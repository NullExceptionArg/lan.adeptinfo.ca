import {Component} from '@angular/core';
import {UserService} from '../core/services/user.service';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {ActivatedRoute, Router} from '@angular/router';
import {MediaMatcher} from '@angular/cdk/layout';

@Component({
  selector: 'app-auth-page',
  templateUrl: './auth.component.html',
  styleUrls: ['./auth.component.css']
})
export class AuthComponent {

  // Champs utilisés pour la connexion
  authForm: FormGroup;

  // Erreurs retournées par le serveur pour le champ du courriel
  emailServerError = '';

  // Erreurs retournées par le serveur pour le champ du mot de passe
  passwordServerError = '';

  // Si des communications avec le serveur sont en cours
  isSubmitting = false;

  mobileQuery: MediaQueryList;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private userService: UserService,
    private formBuilder: FormBuilder,
    private media: MediaMatcher
  ) {
    // use FormBuilder to create a form group
    this.mobileQuery = this.media.matchMedia('(min-width: 960px)');
    this.authForm = this.formBuilder.group({
      'email': ['', Validators.required, Validators.email],
      'password': ['', Validators.required]
    });
  }

  login() {
    // Si le courriel et le mot de passe sont valides
    if (this.authForm.valid) {
      this.isSubmitting = true;

      const credentials = this.authForm.value;
      this.userService
        .attemptAuth(credentials)
        .subscribe(
          data => this.router.navigateByUrl('/'),
          err => {
            this.isSubmitting = false;
          }
        );
    }
  }

  /**
   * Obtenir l'erreur du champ du courriel.
   */
  getEmailErrorMessage() {
    if (this.passwordServerError !== '') {
      return this.passwordServerError;
    } else if (this.authForm.controls['email'].hasError('required')) {
      return 'Le courriel est requis.';
    } else if (this.authForm.controls['email'].hasError('email')) {
      return 'Courriel non valide.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur du champ du mot de passe.
   * @return Chaîne de caractère de l'erreur courante
   */
  getPasswordErrorMessage() {
    if (this.emailServerError !== '') {
      return this.emailServerError;
    } else if (this.authForm.controls['password'].hasError('required')) {
      return 'Le mot de passe est requis.';
    } else {
      return '';
    }
  }

}
