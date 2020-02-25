import { Component } from '@angular/core';
import { fromEvent, Observable, Subscription } from "rxjs";

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent {
    resizeObservable$: Observable<Event>
    resizeSubscription$: Subscription

    constructor() { }

    ngOnInit(): void {
        
    }
}
