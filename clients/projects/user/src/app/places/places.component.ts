import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-places',
  templateUrl: './places.component.html',
  styleUrls: ['./places.component.css']
})
export class PlacesComponent implements OnInit {

  config = {
    publicKey: "19aa9acc-c576-465e-bcbf-28738cb997a4",
    event: "f1b13b46-abf7-469c-8663-7a0422384e8a",
    fitTo: 'width',
    style: { font: 'Roboto', border: 'max', padding: 'spacious' }
  }

  constructor() { }

  ngOnInit() {
  }

}
