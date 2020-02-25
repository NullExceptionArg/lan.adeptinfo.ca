// declare const $: any;
import * as $ from 'jquery';
import { Component, OnInit } from '@angular/core';
import { interval } from 'rxjs';
import { fromEvent, Observable, Subscription } from "rxjs";
const ticker = interval(1000);

@Component({
	selector: 'app-home',
	templateUrl: './home.component.html',
	styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
	constructor() { }

	ngOnInit(): void {
		
	}

	ngAfterViewInit(): void {

	}
}
