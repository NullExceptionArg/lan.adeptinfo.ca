import { Component, OnInit } from '@angular/core';
import { UserService } from 'projects/core/src/public_api';
import { Router } from '@angular/router';
import { ApiService } from 'projects/core/src/lib/services/api.service';
import { NgForm, Form, FormGroup, FormBuilder, Validators } from '@angular/forms';

@Component({
  selector: 'app-places',
  templateUrl: './places.component.html',
  styleUrls: ['./places.component.css']
})
export class PlacesComponent{

  config = {
    publicKey: "19aa9acc-c576-465e-bcbf-28738cb997a4",
    event: "a2e58f40-980f-4b4f-9c1c-ec7098727e7a",
    fitTo: 'width',
    style: { font: 'Roboto', border: 'max', padding: 'spacious' },
    features: { 
      disabled: ['booths', 'tables']
    },
    maxSelectedObjects: 1,
    selectedObjectsInputName: 'selectedSeat'
  }

  constructor(private formBuilder:FormBuilder,private userService: UserService, private router: Router, private apiService : ApiService) {
    if(!this.userService.isAuthenticated)
    {
      router.navigate(['auth/login']);
    }
   }

   onSubmit(event: any){
     return this.apiService.post('/seat/book/', event.target.selectedSeat.value).subscribe(
       () => {this.router.navigate(["/Home"]);},
       (error) =>{console.log(error);}
     )
   }

}
