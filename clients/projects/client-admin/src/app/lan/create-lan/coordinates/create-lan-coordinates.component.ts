import {Component, ElementRef, NgZone, OnInit, ViewChild} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {MapsAPILoader} from '@agm/core';
// @ts-ignore
import {Map} from 'googlemaps';

@Component({
  selector: 'app-create-lan-coordinates',
  templateUrl: './create-lan-coordinates.component.html',
  styleUrls: ['./create-lan-coordinates.component.css']
})
/**
 * Dialogue pour choisir les coordonnées du LAN.
 */
export class CreateLanCoordinatesComponent implements OnInit {

  // Formulaire des détails du LAN
  coordinatesForm: FormGroup;

  // Latitude centrale affichée sur la carte
  public latitude: number;

  // Longitude centrale affichée sur la carte
  public longitude: number;

  // Taille de la carte
  public zoom: number;

  // Référence au champ de recherche d'addresse
  @ViewChild('search')
  public searchElementRef: ElementRef;

  constructor(
    private formBuilder: FormBuilder,
    private mapsAPILoader: MapsAPILoader,
    private ngZone: NgZone
  ) {
    // Instantiation du formulaire
    this.coordinatesForm = this.formBuilder.group({
      address: ['', Validators.required]
    });
  }

  ngOnInit() {
    // Taille de la carte par défaut
    this.zoom = 4;

    // Tenter de positionner la carte sur l'utilisateur courant comme point de départ
    this.setCurrentPosition();

    // Chargement de l'API de google maps
    this.mapsAPILoader.load().then(() => {

      // Attribution de la responsabilité du champs pour afficher les suggestions
      const autocomplete = new google.maps.places.Autocomplete(this.searchElementRef.nativeElement, {
        types: ['address']
      });

      // À la sélection d'une option de la liste de suggestions
      autocomplete.addListener('place_changed', () => {
        this.ngZone.run(() => {
          // Obtenir les informations de la place
          const place: google.maps.places.PlaceResult = autocomplete.getPlace();

          // Vérifier les résultats
          if (place.geometry === undefined || place.geometry === null) {
            return;
          }

          // Ajuster la latitude pour celle de l'entrée sélectionnée
          this.latitude = place.geometry.location.lat();

          // Ajuster la longitude pour celle de l'entrée sélectionnée
          this.longitude = place.geometry.location.lng();

          // Agrandir la carte sur le point sélectionné
          this.zoom = 12;
        });
      });
    });
  }

  /**
   * Obtenir l'erreur de l'adresse du LAN.
   * @return string Texte de l'erreur
   */
  getAddressError(): string {
    if (this.coordinatesForm.controls['address'].hasError('required')) {
      return 'L\'adresse du LAN est requise.';
    } else if (this.coordinatesForm.controls['address'].hasError('address')) {
      return 'L\'adresse du LAN est requise.';
    } else {
      return '';
    }
  }

  /**
   * Valider que des coordonnées sont sélectionées.
   */
  checkAddress(): void {
    if (!this.longitude || !this.latitude || this.coordinatesForm.controls['address'].value === '') {
      this.coordinatesForm.controls['address'].setErrors({'address': true});
    } else {
      this.coordinatesForm.controls['address'].setErrors(null);
    }
  }

  /**
   * Obtenir la position courante de l'utilisateur.
   */
  private setCurrentPosition(): void {
    // Si le navigateur permet la géolocalisation
    if ('geolocation' in navigator) {
      // Demander à l'utilisateur la permission d'utiliser sa position courante
      navigator.geolocation.getCurrentPosition((position) => {

        // Ajuster la longitude pour celle de la position courante de l'utilisateur
        this.latitude = position.coords.latitude;

        // Ajuster la latitude pour celle de la position courante de l'utilisateur
        this.longitude = position.coords.longitude;

        // Agrandir la carte sur le point sélectionné
        this.zoom = 12;
      });
    }
  }
}
