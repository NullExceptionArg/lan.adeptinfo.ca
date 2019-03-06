import {ChangeDetectorRef, Component, OnInit} from '@angular/core';
import {MediaMatcher} from '@angular/cdk/layout';
import {UserService} from './core/services/user.service';
import {of} from 'rxjs';
import {Router} from '@angular/router';
import {User} from './core/models/user';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {

  mobileQuery: MediaQueryList;
  currentUser: User;

  constructor(private userService: UserService, changeDetectorRef: ChangeDetectorRef, private media: MediaMatcher, private router: Router) {
  }

  ngOnInit(): void {
    this.mobileQuery = this.media.matchMedia('(min-width: 600px)');
    this.userService.populate();
    this.userService.isAuthenticated.subscribe(
      (authenticated) => {
        // Redirection vers l'écran de connection si aucuns utilisateur n'est connecté
        if (!authenticated) {
          this.router.navigateByUrl('/login');
          return of(null);
        }
      }
    );

    this.userService.currentUser.subscribe(
      (userData) => {
        this.currentUser = userData;
      }
    );
  }
}
