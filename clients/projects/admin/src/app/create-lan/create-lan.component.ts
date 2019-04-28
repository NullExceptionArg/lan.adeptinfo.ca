import {Component, ViewChild} from '@angular/core';
import {FormBuilder} from '@angular/forms';
import {MediaMatcher} from '@angular/cdk/layout';
import {LanDetailsComponent} from '../shared/lan/details/lan-details.component';
import {LanSeatsComponent} from '../shared/lan/seats/lan-seats.component';
import {LanCoordinatesComponent} from '../shared/lan/coordinates/lan-coordinates.component';
import {LanRulesComponent} from '../shared/lan/rules/lan-rules.component';
import {LanDescriptionComponent} from '../shared/lan/description/lan-description.component';
import {Errors, LanService, Lan} from 'core';
import {SwalComponent} from '@sweetalert2/ngx-sweetalert2';
import {Router} from '@angular/router';
import {DateUtils} from '../utils/DateUtils';
import {MatDialogRef} from '@angular/material';

@Component({
  selector: 'app-create-lan',
  templateUrl: './create-lan.component.html',
  styleUrls: ['./create-lan.component.css']
})
/**
 * Dialogue de création de LAN.
 */
export class CreateLanComponent {

  // Formulaire des détails du LAN
  @ViewChild(LanDetailsComponent) createLanDetailsComponent: LanDetailsComponent;

  // Formulaire de seats.io
  @ViewChild(LanSeatsComponent) createLanSeatsComponent: LanSeatsComponent;

  // Formulaire des coordonnées
  @ViewChild(LanCoordinatesComponent) createLanCoordinatesComponent: LanCoordinatesComponent;

  // Formulaire des règlements
  @ViewChild(LanRulesComponent) createLanRulesComponent: LanRulesComponent;

  // Formulaire de description
  @ViewChild(LanDescriptionComponent) createLanDescriptionComponent: LanDescriptionComponent;

  // Les communications avec le serveur sont en cours, l'interface est donc désactivée pendant ce temps
  isCreatingLan = false;

  // Surveille la largeur courante de l'écran de l'utilisateur
  mobileQuery: MediaQueryList;

  // Erreurs qui peuvent être retournées par l'API
  errors: Errors;

  // Index courant du stepper
  currentIndex = 0;

  // Fenêtre qui confirme à l'utilisateur que le LAN a bien été créé
  @ViewChild('createLanSuccessSwal') private createLanSuccessSwal: SwalComponent;

  constructor(
    private formBuilder: FormBuilder,
    private media: MediaMatcher,
    private lanService: LanService,
    private router: Router,
    private dialogRef: MatDialogRef<CreateLanComponent>
  ) {
    // Le changement de mobile à plein écran s'effectue lorsque l'écran fait 960 pixels de large
    this.mobileQuery = this.media.matchMedia('(min-width: 960px)');
  }

  get coordinatesLongitude() {
    return this.createLanCoordinatesComponent ? this.createLanCoordinatesComponent.longitude : null;
  }

  get detailsForm() {
    return this.createLanDetailsComponent ? this.createLanDetailsComponent.detailsForm : null;
  }

  get seatsForm() {
    return this.createLanSeatsComponent ? this.createLanSeatsComponent.seatsForm : null;
  }

  get coordinatesLatitude() {
    return this.createLanCoordinatesComponent ? this.createLanCoordinatesComponent.latitude : null;
  }

  get coordinatesForm() {
    return this.createLanCoordinatesComponent ? this.createLanCoordinatesComponent.coordinatesForm : null;
  }

  /**
   * Créer un LAN avec les champs qui ont été remplis.
   */
  createLan(): void {
    this.isCreatingLan = true;
    const lan: Lan = new Lan();
      lan.name = this.detailsForm.controls['name'].value;
      lan.lanStart = DateUtils.getDateFromMomentAndString(
        this.detailsForm.controls['startDate'].value,
        this.detailsForm.controls['startTime'].value
      );
      lan.lanEnd = DateUtils.getDateFromMomentAndString(
        this.detailsForm.controls['endDate'].value,
        this.detailsForm.controls['endTime'].value
      );
      lan.seatReservationStart = DateUtils.getDateFromMomentAndString(
        this.detailsForm.controls['reservationDate'].value,
        this.detailsForm.controls['reservationTime'].value
      );
      lan.tournamentReservationStart = DateUtils.getDateFromMomentAndString(
        this.detailsForm.controls['tournamentDate'].value,
        this.detailsForm.controls['tournamentTime'].value
      );
      lan.places = this.detailsForm.controls['playerCount'].value;
      lan.price = this.detailsForm.controls['price'].value;
      lan.eventKey = this.seatsForm.controls['eventKey'].value;
      lan.latitude = this.coordinatesLatitude;
      lan.longitude = this.coordinatesLongitude;
      lan. rules = this.rulesForm.controls['rules'].value;
      lan.description = this.descriptionForm.controls['description'].value;

    this.lanService.createLan(lan).subscribe(
      () => {
        this.errors = null;
        this.isCreatingLan = false;
        this.createLanSuccessSwal.show().then(() => this.dialogRef.close());
      },
      (err: Errors) => {
        this.errors = err;
        this.isCreatingLan = false;
      }
    );
  }

  getErrorTitle(errorName: string): string {
    switch (errorName) {
      case 'name':
        return 'Nom du LAN';
      case 'lanStart':
        return 'Date et heure de début';
      case 'lanEnd':
        return 'Date et heure de fin';
      case 'seatReservationStart':
        return 'Date et heure de réservation';
      case 'tournamentReservationStart':
        return 'Date et heure des tournois';
      case 'eventKey':
        return 'Places';
      case 'latitude':
      case 'longitude':
        return 'Coordonnées';
      case 'places':
        return 'Nombre de joueurs';
      case 'price':
        return 'Prix d\'entrée';
      case 'rules':
        return 'Règlements';
      case 'description':
        return 'Description';
      default :
        return 'Inconnu';
    }
  }

  gotoError(error: string): void {
    switch (error) {
      case 'name':
      case 'lanStart':
      case 'lanEnd':
      case 'seatReservationStart':
      case 'tournamentReservationStart':
      case 'places':
      case 'price':
        this.currentIndex = 0;
        break;
      case 'eventKey':
        this.currentIndex = 1;
        break;
      case 'latitude':
      case 'longitude':
        this.currentIndex = 2;
        break;
      case 'rules':
        this.currentIndex = 3;
        break;
      case 'description':
        this.currentIndex = 4;
        break;
      default :
        this.currentIndex = 5;
        break;
    }
  }

  get rulesForm() {
    return this.createLanRulesComponent ? this.createLanRulesComponent.rulesForm : null;
  }

  get descriptionForm() {
    return this.createLanDescriptionComponent ? this.createLanDescriptionComponent.descriptionForm : null;
  }
}
