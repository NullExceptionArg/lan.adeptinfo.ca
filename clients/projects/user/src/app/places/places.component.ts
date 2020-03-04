import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-places',
  templateUrl: './places.component.html',
  styleUrls: ['./places.component.css']
})
export class PlacesComponent implements OnInit {

  config = {
    publicKey: "19aa9acc-c576-465e-bcbf-28738cb997a4",
    event: "be1783ea-57eb-48ef-86bc-64fd12fdff1a"
  }

  constructor() { }

  ngOnInit() {
  }

}
