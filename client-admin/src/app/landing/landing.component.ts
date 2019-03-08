import {Component} from '@angular/core';
import {environment} from '../../environments/environment';

@Component({
  selector: 'app-landing',
  templateUrl: 'landing.component.html',
  styleUrls: ['landing.component.css']
})
/**
 * Page principale après la connexion
 */
export class LandingComponent {

  getPlayerUrl() {
    return environment.playerUrl;
  }
}
