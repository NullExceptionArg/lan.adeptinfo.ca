import {Component} from '@angular/core';
import {FormBuilder, FormGroup} from '@angular/forms';

@Component({
  selector: 'app-lan-description',
  templateUrl: './create-lan-description.component.html',
  styleUrls: ['./create-lan-description.component.css']
})
/**
 * Dialogue pour entrer la description du LAN.
 */
export class CreateLanDescriptionComponent {

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
