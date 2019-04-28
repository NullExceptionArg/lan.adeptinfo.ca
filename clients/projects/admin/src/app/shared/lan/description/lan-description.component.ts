import {Component} from '@angular/core';
import {FormBuilder, FormGroup} from '@angular/forms';

@Component({
  selector: 'app-lan-description',
  templateUrl: './lan-description.component.html',
  styleUrls: ['./lan-description.component.css']
})
/**
 * Dialogue pour entrer la description du LAN.
 */
export class LanDescriptionComponent {

  // Formulaire de description du LAN
  descriptionForm: FormGroup;

  constructor(
    private formBuilder: FormBuilder
  ) {
    // Instantiation du formulaire
    this.descriptionForm = this.formBuilder.group({
      description: []
    });
  }

}
