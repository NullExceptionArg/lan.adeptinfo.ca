import {Component, ViewChild} from '@angular/core';
import {FormBuilder} from '@angular/forms';
import {MediaMatcher} from '@angular/cdk/layout';
import {CreateLanDetailsComponent} from './details/create-lan-details.component';

@Component({
  selector: 'app-create-lan',
  templateUrl: './create-lan.component.html',
  styleUrls: ['./create-lan.component.css']
})
/**
 * Dialogue de création de LAN.
 */
export class CreateLanComponent {

  mainForm: Array<string>;

  @ViewChild(CreateLanDetailsComponent) createLanComponent: CreateLanDetailsComponent;

  // Surveille la largeur courante de l'écran de l'utilisateur
  mobileQuery: MediaQueryList;

  constructor(
    // public createLanService: CreateLanService,
    private formBuilder: FormBuilder,
    private media: MediaMatcher
  ) {
    // Le changement de mobile à plein écran s'effectue lorsque l'écran fait 960 pixels de large
    this.mobileQuery = this.media.matchMedia('(min-width: 960px)');

    // Obtient les valeurs du formulaire de création du LAN
    // this.mainForm = this.createLanService.mainForm.value;
  }

  get detailsForm() {
    return this.createLanComponent ? this.createLanComponent.detailsForm : null;
  }

  keys(): Array<string> {
    return Object.keys(this.mainForm);
  }
}
